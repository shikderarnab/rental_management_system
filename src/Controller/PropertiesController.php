<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;

class PropertiesController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadModel('Properties');
    }

    public function index()
    {
        $user = $this->Authentication->getIdentity();
        
        if ($user->role === 'landlord') {
            $this->loadModel('Landlords');
            $landlord = $this->Landlords->find()
                ->where(['user_id' => $user->id])
                ->first();
            
            if ($landlord) {
                $properties = $this->Properties->find()
                    ->where(['landlord_id' => $landlord->id])
                    ->contain(['Units'])
                    ->toArray();
            } else {
                $properties = [];
            }
        } else {
            // Admin can see all
            $properties = $this->Properties->find()
                ->contain(['Landlords', 'Units'])
                ->toArray();
        }
        
        $this->set(compact('properties'));
    }

    public function view($id = null)
    {
        $property = $this->Properties->get($id, [
            'contain' => ['Landlords', 'Units'],
        ]);
        
        $this->set(compact('property'));
    }

    public function add()
    {
        $user = $this->Authentication->getIdentity();
        
        if ($user->role !== 'landlord' && $user->role !== 'admin') {
            $this->Flash->error('Access denied');
            return $this->redirect(['action' => 'index']);
        }
        
        $this->loadModel('Landlords');
        $landlord = $this->Landlords->find()
            ->where(['user_id' => $user->id])
            ->first();
        
        $property = $this->Properties->newEmptyEntity();
        
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            
            if ($user->role === 'landlord' && $landlord) {
                $data['landlord_id'] = $landlord->id;
            }
            
            $property = $this->Properties->patchEntity($property, $data);
            
            if ($this->Properties->save($property)) {
                $this->Flash->success('Property added successfully');
                return $this->redirect(['action' => 'index']);
            }
            
            $this->Flash->error('Failed to add property');
        }
        
        $landlords = $this->Properties->Landlords->find('list');
        $this->set(compact('property', 'landlords'));
    }

    public function edit($id = null)
    {
        $property = $this->Properties->get($id);
        $user = $this->Authentication->getIdentity();
        
        // Check authorization
        if ($user->role === 'landlord') {
            $this->loadModel('Landlords');
            $landlord = $this->Landlords->find()
                ->where(['user_id' => $user->id])
                ->first();
            
            if (!$landlord || $property->landlord_id !== $landlord->id) {
                $this->Flash->error('Access denied');
                return $this->redirect(['action' => 'index']);
            }
        }
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            $property = $this->Properties->patchEntity($property, $this->request->getData());
            
            if ($this->Properties->save($property)) {
                $this->Flash->success('Property updated successfully');
                return $this->redirect(['action' => 'index']);
            }
            
            $this->Flash->error('Failed to update property');
        }
        
        $landlords = $this->Properties->Landlords->find('list');
        $this->set(compact('property', 'landlords'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        
        $property = $this->Properties->get($id);
        $user = $this->Authentication->getIdentity();
        
        // Check authorization
        if ($user->role === 'landlord') {
            $this->loadModel('Landlords');
            $landlord = $this->Landlords->find()
                ->where(['user_id' => $user->id])
                ->first();
            
            if (!$landlord || $property->landlord_id !== $landlord->id) {
                $this->Flash->error('Access denied');
                return $this->redirect(['action' => 'index']);
            }
        }
        
        if ($this->Properties->delete($property)) {
            $this->Flash->success('Property deleted');
        } else {
            $this->Flash->error('Failed to delete property');
        }
        
        return $this->redirect(['action' => 'index']);
    }
}

