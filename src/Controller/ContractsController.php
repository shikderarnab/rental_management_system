<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;

class ContractsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadModel('Contracts');
    }

    public function index()
    {
        $user = $this->Authentication->getIdentity();
        
        $query = $this->Contracts->find()
            ->contain([
                'Units' => ['Properties'],
                'Tenants' => ['Users'],
                'Landlords' => ['Users']
            ]);
        
        if ($user->role === 'tenant') {
            $this->loadModel('Tenants');
            $tenant = $this->Tenants->find()
                ->where(['user_id' => $user->id])
                ->first();
            
            if ($tenant) {
                $query->where(['tenant_id' => $tenant->id]);
            }
        } elseif ($user->role === 'landlord') {
            $this->loadModel('Landlords');
            $landlord = $this->Landlords->find()
                ->where(['user_id' => $user->id])
                ->first();
            
            if ($landlord) {
                $query->where(['landlord_id' => $landlord->id]);
            }
        }
        
        $contracts = $query->order(['Contracts.created' => 'DESC'])->toArray();
        $this->set(compact('contracts'));
    }

    public function view($id = null)
    {
        $contract = $this->Contracts->get($id, [
            'contain' => [
                'Units' => ['Properties'],
                'Tenants' => ['Users'],
                'Landlords' => ['Users'],
                'Signatures'
            ],
        ]);
        
        $this->set(compact('contract'));
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
        
        $contract = $this->Contracts->newEmptyEntity();
        
        // Pre-select unit if unit_id is provided in query string
        $unitId = $this->request->getQuery('unit_id');
        if ($unitId) {
            $contract->unit_id = $unitId;
        }
        
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            
            if ($user->role === 'landlord' && $landlord) {
                $data['landlord_id'] = $landlord->id;
            }
            
            // Handle agreement file upload - remove from data first to avoid array access error
            $uploadedFile = $this->request->getUploadedFile('agreement_file');
            unset($data['agreement_file']); // Remove the file object from data array
            
            if ($uploadedFile && $uploadedFile->getError() === UPLOAD_ERR_OK) {
                $uploadPath = $this->uploadAgreement($uploadedFile);
                if ($uploadPath) {
                    $data['agreement_file'] = $uploadPath; // Add the file path string instead
                }
            }
            
            $contract = $this->Contracts->patchEntity($contract, $data);
            
            if ($this->Contracts->save($contract)) {
                $this->Flash->success('Contract created successfully');
                return $this->redirect(['action' => 'index']);
            }
            
            $this->Flash->error('Failed to create contract');
        }
        
        // Format units list with property name and unit number
        $unitsQuery = $this->Contracts->Units->find()
            ->contain(['Properties'])
            ->order(['Properties.name' => 'ASC', 'Units.unit_number' => 'ASC']);
        $unitsList = [];
        foreach ($unitsQuery as $unit) {
            $unitsList[$unit->id] = ($unit->property->name ?? 'N/A') . ' - Unit ' . $unit->unit_number;
        }
        
        // Format tenants list with user name
        $this->loadModel('Tenants');
        $this->loadModel('Users');
        
        // First, check if there are any users with tenant role that don't have tenant records
        $tenantUsers = $this->Users->find()
            ->where(['role' => 'tenant'])
            ->toArray();
        
        // Create tenant records for users that don't have them
        $createdCount = 0;
        foreach ($tenantUsers as $user) {
            // Check if tenant record exists
            $existingTenant = $this->Tenants->find()
                ->where(['user_id' => $user->id])
                ->first();
            
            if (!$existingTenant) {
                try {
                    $tenant = $this->Tenants->newEmptyEntity();
                    $tenant->user_id = $user->id;
                    
                    if ($this->Tenants->save($tenant)) {
                        $createdCount++;
                    } else {
                        // Log validation errors
                        $errors = $tenant->getErrors();
                        \Cake\Log\Log::error('Failed to create tenant record for user ' . $user->id . ': ' . json_encode($errors));
                    }
                } catch (\Exception $e) {
                    // Log error but continue
                    \Cake\Log\Log::error('Exception creating tenant record for user ' . $user->id . ': ' . $e->getMessage());
                }
            }
        }
        
        // If we created any tenant records, show a message
        if ($createdCount > 0) {
            $this->Flash->success("Created {$createdCount} tenant record(s) for existing users.");
        }
        
        // Get all tenants - try with Users relationship first
        try {
            $tenantsQuery = $this->Tenants->find()
                ->contain(['Users'])
                ->order(['Tenants.id' => 'ASC']);
            
            $tenantsList = [];
            foreach ($tenantsQuery as $tenant) {
                $name = '';
                if (isset($tenant->user) && $tenant->user) {
                    $firstName = $tenant->user->first_name ?? '';
                    $lastName = $tenant->user->last_name ?? '';
                    $name = trim($firstName . ' ' . $lastName);
                }
                if (empty($name) && $tenant->user_id) {
                    // Try to get user directly if not loaded
                    try {
                        $user = $this->Users->get($tenant->user_id);
                        if ($user) {
                            $name = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                        }
                    } catch (\Exception $e) {
                        // User not found, continue
                    }
                }
                if (empty($name)) {
                    $name = 'Tenant #' . $tenant->id;
                }
                $tenantsList[$tenant->id] = $name;
            }
        } catch (\Exception $e) {
            // Fallback: get tenants without Users relationship
            $tenantsQuery = $this->Tenants->find()
                ->order(['Tenants.id' => 'ASC']);
            
            $tenantsList = [];
            foreach ($tenantsQuery as $tenant) {
                $name = 'Tenant #' . $tenant->id;
                if ($tenant->user_id) {
                    try {
                        $user = $this->Users->get($tenant->user_id);
                        if ($user) {
                            $name = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                            if (empty($name)) {
                                $name = 'Tenant #' . $tenant->id;
                            }
                        }
                    } catch (\Exception $e) {
                        // User not found, use default name
                    }
                }
                $tenantsList[$tenant->id] = $name;
            }
        }
        
        // Sort by name for better UX
        asort($tenantsList);
        
        // Format landlords list with user name or company name
        $landlordsQuery = $this->Contracts->Landlords->find()
            ->contain(['Users'])
            ->order(['Users.first_name' => 'ASC', 'Users.last_name' => 'ASC']);
        $landlordsList = [];
        foreach ($landlordsQuery as $landlord) {
            $name = trim($landlord->company_name ?? '');
            if (!$name) {
                $name = trim(($landlord->user->first_name ?? '') . ' ' . ($landlord->user->last_name ?? ''));
            }
            $landlordsList[$landlord->id] = $name ?: 'Landlord #' . $landlord->id;
        }
        
        $this->set(compact('contract', 'unitsList', 'tenantsList', 'landlordsList', 'unitId'));
    }

    public function edit($id = null)
    {
        $user = $this->Authentication->getIdentity();
        
        if ($user->role !== 'landlord' && $user->role !== 'admin') {
            $this->Flash->error('Access denied');
            return $this->redirect(['action' => 'index']);
        }
        
        try {
            $contract = $this->Contracts->get($id, [
                'contain' => [
                    'Units' => ['Properties'],
                    'Tenants' => ['Users'],
                    'Landlords' => ['Users']
                ],
            ]);
            
            // Check authorization for landlords
            if ($user->role === 'landlord') {
                $this->loadModel('Landlords');
                $landlord = $this->Landlords->find()
                    ->where(['user_id' => $user->id])
                    ->first();
                
                if (!$landlord || $contract->landlord_id !== $landlord->id) {
                    $this->Flash->error('Access denied');
                    return $this->redirect(['action' => 'index']);
                }
            }
            
            if ($this->request->is(['patch', 'post', 'put'])) {
                $data = $this->request->getData();
                
                // Handle agreement file upload - remove from data first to avoid array access error
                $uploadedFile = $this->request->getUploadedFile('agreement_file');
                unset($data['agreement_file']); // Remove the file object from data array
                
                if ($uploadedFile && $uploadedFile->getError() === UPLOAD_ERR_OK) {
                    $uploadPath = $this->uploadAgreement($uploadedFile);
                    if ($uploadPath) {
                        $data['agreement_file'] = $uploadPath; // Add the file path string instead
                    }
                }
                
                $contract = $this->Contracts->patchEntity($contract, $data);
                
                if ($this->Contracts->save($contract)) {
                    $this->Flash->success('Contract updated successfully');
                    return $this->redirect(['action' => 'view', $contract->id]);
                }
                
                $this->Flash->error('Failed to update contract');
            }
            
            // Format units list with property name and unit number
            $unitsQuery = $this->Contracts->Units->find()
                ->contain(['Properties'])
                ->order(['Properties.name' => 'ASC', 'Units.unit_number' => 'ASC']);
            $unitsList = [];
            foreach ($unitsQuery as $unit) {
                $unitsList[$unit->id] = ($unit->property->name ?? 'N/A') . ' - Unit ' . $unit->unit_number;
            }
            
            // Format tenants list with user name
            $this->loadModel('Tenants');
            $this->loadModel('Users');
            $tenantsQuery = $this->Tenants->find()
                ->contain(['Users'])
                ->order(['Tenants.id' => 'ASC']);
            
            $tenantsList = [];
            foreach ($tenantsQuery as $tenant) {
                $name = '';
                if (isset($tenant->user) && $tenant->user) {
                    $firstName = $tenant->user->first_name ?? '';
                    $lastName = $tenant->user->last_name ?? '';
                    $name = trim($firstName . ' ' . $lastName);
                }
                if (empty($name) && $tenant->user_id) {
                    try {
                        $user = $this->Users->get($tenant->user_id);
                        if ($user) {
                            $name = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                        }
                    } catch (\Exception $e) {
                        // User not found, continue
                    }
                }
                if (empty($name)) {
                    $name = 'Tenant #' . $tenant->id;
                }
                $tenantsList[$tenant->id] = $name;
            }
            asort($tenantsList);
            
            // Format landlords list with user name or company name
            $landlordsQuery = $this->Contracts->Landlords->find()
                ->contain(['Users'])
                ->order(['Users.first_name' => 'ASC', 'Users.last_name' => 'ASC']);
            $landlordsList = [];
            foreach ($landlordsQuery as $landlord) {
                $name = trim($landlord->company_name ?? '');
                if (!$name) {
                    $name = trim(($landlord->user->first_name ?? '') . ' ' . ($landlord->user->last_name ?? ''));
                }
                $landlordsList[$landlord->id] = $name ?: 'Landlord #' . $landlord->id;
            }
            
            $this->set(compact('contract', 'unitsList', 'tenantsList', 'landlordsList'));
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Contract not found');
            return $this->redirect(['action' => 'index']);
        }
    }

    public function sign($id = null)
    {
        $user = $this->Authentication->getIdentity();
        
        $contract = $this->Contracts->get($id, [
            'contain' => ['Tenants', 'Signatures'],
        ]);
        
        // Check if user is the tenant
        $this->loadModel('Tenants');
        $tenant = $this->Tenants->find()
            ->where(['user_id' => $user->id])
            ->first();
        
        if (!$tenant || $contract->tenant_id !== $tenant->id) {
            $this->Flash->error('Access denied');
            return $this->redirect(['action' => 'index']);
        }
        
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            
            $this->loadModel('Signatures');
            $signature = $this->Signatures->newEmptyEntity([
                'contract_id' => $contract->id,
                'user_id' => $user->id,
                'signature_type' => $data['signature_type'] ?? 'typed',
                'signature_data' => $data['signature_data'],
                'signed_at' => date('Y-m-d H:i:s'),
                'ip_address' => $this->request->clientIp(),
            ]);
            
            if ($this->Signatures->save($signature)) {
                // Update contract status
                $contract->status = 'active';
                $this->Contracts->save($contract);
                
                // Generate signed PDF
                $signedPath = $this->generateSignedPdf($contract, $signature);
                if ($signedPath) {
                    $contract->signed_file = $signedPath;
                    $this->Contracts->save($contract);
                }
                
                // Send notification
                $this->loadModel('FirebaseService');
                $firebase = new \App\Service\FirebaseService();
                $firebase->sendEmail(
                    $user->email,
                    'Agreement Signed',
                    'Your rental agreement has been signed successfully.',
                    ['contract_id' => $contract->id]
                );
                
                $this->Flash->success('Contract signed successfully');
                return $this->redirect(['action' => 'view', $contract->id]);
            }
            
            $this->Flash->error('Failed to sign contract');
        }
        
        $this->set(compact('contract'));
    }

    private function uploadAgreement($uploadedFile): ?string
    {
        // Check if it's a PSR-7 UploadedFileInterface object
        if (!($uploadedFile instanceof \Psr\Http\Message\UploadedFileInterface)) {
            return null;
        }
        
        if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
            return null;
        }
        
        $clientFilename = $uploadedFile->getClientFilename();
        $clientMediaType = $uploadedFile->getClientMediaType();
        
        // Check file type
        if ($clientMediaType !== 'application/pdf' && pathinfo($clientFilename, PATHINFO_EXTENSION) !== 'pdf') {
            return null;
        }
        
        $filename = 'agreement_' . time() . '_' . uniqid() . '.pdf';
        $uploadDir = WWW_ROOT . 'uploads' . DS . 'agreements' . DS;
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $filePath = $uploadDir . $filename;
        
        try {
            // Move the uploaded file
            $uploadedFile->moveTo($filePath);
            return 'uploads/agreements/' . $filename;
        } catch (\Exception $e) {
            \Cake\Log\Log::error('File upload error: ' . $e->getMessage());
            return null;
        }
    }

    private function generateSignedPdf($contract, $signature): ?string
    {
        // This would merge the agreement PDF with the signature
        // For now, return a placeholder path
        // In production, use a PDF library to merge files
        return null;
    }
}

