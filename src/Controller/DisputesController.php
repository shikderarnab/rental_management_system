<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;

class DisputesController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadModel('Disputes');
    }

    public function index()
    {
        $user = $this->Authentication->getIdentity();
        
        $query = $this->Disputes->find()
            ->contain(['Contracts', 'Payments', 'Tenants', 'Landlords', 'DisputeMessages']);
        
        if ($user->role === 'tenant') {
            $this->loadModel('Tenants');
            $tenant = $this->Tenants->find()
                ->where(['user_id' => $user->id])
                ->first();
            
            if ($tenant) {
                $query->where(['Disputes.tenant_id' => $tenant->id]);
            }
        } elseif ($user->role === 'landlord') {
            $this->loadModel('Landlords');
            $landlord = $this->Landlords->find()
                ->where(['user_id' => $user->id])
                ->first();
            
            if ($landlord) {
                $query->where(['Disputes.landlord_id' => $landlord->id]);
            }
        }
        
        $disputes = $query->order(['Disputes.created' => 'DESC'])->toArray();
        $this->set(compact('disputes'));
    }

    public function add()
    {
        $user = $this->Authentication->getIdentity();
        
        if ($user->role !== 'tenant') {
            $this->Flash->error('Only tenants can submit disputes');
            return $this->redirect(['action' => 'index']);
        }
        
        $this->loadModel('Tenants');
        $tenant = $this->Tenants->find()
            ->where(['user_id' => $user->id])
            ->first();
        
        if (!$tenant) {
            $this->Flash->error('Tenant profile not found');
            return $this->redirect('/dashboard');
        }
        
        $dispute = $this->Disputes->newEmptyEntity();
        
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['tenant_id'] = $tenant->id;
            $data['status'] = 'open';
            
            // Get landlord_id from contract or payment
            if (!empty($data['contract_id'])) {
                $this->loadModel('Contracts');
                $contract = $this->Contracts->get($data['contract_id']);
                $data['landlord_id'] = $contract->landlord_id;
            } elseif (!empty($data['payment_id'])) {
                $this->loadModel('Payments');
                $payment = $this->Payments->get($data['payment_id']);
                $data['landlord_id'] = $payment->landlord_id;
            }
            
            $dispute = $this->Disputes->patchEntity($dispute, $data);
            
            if ($this->Disputes->save($dispute)) {
                $this->Flash->success('Dispute submitted successfully');
                return $this->redirect(['action' => 'index']);
            }
            
            $this->Flash->error('Failed to submit dispute');
        }
        
        $this->loadModel('Contracts');
        $contracts = $this->Contracts->find()
            ->where(['tenant_id' => $tenant->id])
            ->contain(['Units.Properties'])
            ->toArray();
        
        $this->set(compact('dispute', 'contracts'));
    }

    public function view($id = null)
    {
        $user = $this->Authentication->getIdentity();
        
        $dispute = $this->Disputes->get($id, [
            'contain' => ['Contracts', 'Payments', 'Tenants', 'Landlords', 'DisputeMessages' => ['Users']],
        ]);
        
        // Check authorization
        if ($user->role === 'tenant') {
            $this->loadModel('Tenants');
            $tenant = $this->Tenants->find()
                ->where(['user_id' => $user->id])
                ->first();
            
            if (!$tenant || $dispute->tenant_id !== $tenant->id) {
                $this->Flash->error('Access denied');
                return $this->redirect(['action' => 'index']);
            }
        }
        
        $this->loadModel('DisputeMessages');
        $message = $this->DisputeMessages->newEmptyEntity();
        
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['dispute_id'] = $dispute->id;
            $data['user_id'] = $user->id;
            
            $message = $this->DisputeMessages->patchEntity($message, $data);
            
            if ($this->DisputeMessages->save($message)) {
                // Update dispute status if needed
                if ($user->role === 'admin' || $user->role === 'landlord') {
                    $status = $this->request->getData('status');
                    if ($status && in_array($status, ['open', 'reviewing', 'resolved', 'closed'])) {
                        $dispute->status = $status;
                        $this->Disputes->save($dispute);
                    }
                }
                
                $this->Flash->success('Message sent');
                return $this->redirect(['action' => 'view', $dispute->id]);
            }
            
            $this->Flash->error('Failed to send message');
        }
        
        $this->set(compact('dispute', 'message'));
    }

    public function updateStatus($id = null)
    {
        $this->request->allowMethod(['post']);
        
        $user = $this->Authentication->getIdentity();
        
        if ($user->role !== 'admin' && $user->role !== 'landlord') {
            $this->Flash->error('Access denied');
            return $this->redirect(['action' => 'index']);
        }
        
        $dispute = $this->Disputes->get($id);
        $status = $this->request->getData('status');
        
        if (in_array($status, ['open', 'reviewing', 'resolved', 'closed'])) {
            $dispute->status = $status;
            
            if ($this->Disputes->save($dispute)) {
                $this->Flash->success('Dispute status updated');
            } else {
                $this->Flash->error('Failed to update status');
            }
        }
        
        return $this->redirect(['action' => 'view', $dispute->id]);
    }
}

