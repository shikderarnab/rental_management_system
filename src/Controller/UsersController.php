<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\EventInterface;

class UsersController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadModel('Users');
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated(['login', 'logout', 'register']);
    }

    public function index()
    {
        $user = $this->Authentication->getIdentity();
        
        // Only admin can view all users
        if ($user->role !== 'admin') {
            $this->Flash->error('Access denied');
            return $this->redirect('/dashboard');
        }
        
        $users = $this->Users->find()
            ->contain(['Tenants', 'Landlords'])
            ->order(['Users.created' => 'DESC'])
            ->toArray();
        
        $this->set(compact('users'));
    }

    public function add()
    {
        $currentUser = $this->Authentication->getIdentity();
        
        // Only admin can create users
        if ($currentUser->role !== 'admin') {
            $this->Flash->error('Access denied');
            return $this->redirect('/dashboard');
        }
        
        $this->request->allowMethod(['get', 'post']);
        
        $user = $this->Users->newEmptyEntity();
        $selectedRole = $this->request->getQuery('role') ?? $this->request->getData('role') ?? null;
        
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            
            // Set default values
            $data['is_active'] = $data['is_active'] ?? true;
            $data['email_verified'] = $data['email_verified'] ?? false;
            $data['phone_verified'] = $data['phone_verified'] ?? false;
            
            $user = $this->Users->patchEntity($user, $data);
            
            if ($this->Users->save($user)) {
                // Create tenant/landlord record based on role
                try {
                    if ($user->role === 'tenant') {
                        $this->loadModel('Tenants');
                        $tenant = $this->Tenants->newEmptyEntity();
                        $tenant->user_id = $user->id;
                        if (!$this->Tenants->save($tenant)) {
                            $errors = $tenant->getErrors();
                            \Cake\Log\Log::error('Tenant save errors: ' . json_encode($errors));
                            $this->Flash->warning('User created but tenant record failed.');
                        }
                    } elseif ($user->role === 'landlord') {
                        $this->loadModel('Landlords');
                        $landlord = $this->Landlords->newEmptyEntity();
                        $landlord->user_id = $user->id;
                        
                        // Handle landlord-specific fields
                        if (!empty($data['company_name'])) {
                            $landlord->company_name = $data['company_name'];
                        }
                        if (!empty($data['tax_id'])) {
                            $landlord->tax_id = $data['tax_id'];
                        }
                        if (!empty($data['address'])) {
                            $landlord->address = $data['address'];
                        }
                        if (!empty($data['bank_account'])) {
                            $landlord->bank_account = $data['bank_account'];
                        }
                        
                        if (!$this->Landlords->save($landlord)) {
                            $errors = $landlord->getErrors();
                            \Cake\Log\Log::error('Landlord save errors: ' . json_encode($errors));
                            $this->Flash->warning('User created but landlord record failed.');
                        }
                    }
                } catch (\Exception $e) {
                    \Cake\Log\Log::error('Exception creating tenant/landlord record: ' . $e->getMessage());
                    $this->Flash->warning('User created but profile record failed.');
                }
                
                $this->Flash->success('User created successfully.');
                return $this->redirect(['action' => 'index']);
            }
            
            // Show validation errors
            $errors = $user->getErrors();
            if (!empty($errors)) {
                $errorMessages = [];
                foreach ($errors as $field => $fieldErrors) {
                    foreach ($fieldErrors as $error) {
                        $errorMessages[] = ucfirst($field) . ': ' . $error;
                    }
                }
                $this->Flash->error('Please fix the following errors: ' . implode(', ', $errorMessages));
            } else {
                $this->Flash->error('User creation failed. Please check your input and try again.');
            }
            
            // Keep selected role for form display
            $selectedRole = $data['role'] ?? null;
        }
        
        $this->set(compact('user', 'selectedRole'));
    }

    public function login()
    {
        $this->request->allowMethod(['get', 'post']);
        
        if ($this->request->is('post')) {
            $result = $this->Authentication->getResult();
            
            if ($result->isValid()) {
                $user = $this->Authentication->getIdentity();

                // After successful login, always redirect to the main dashboard.
                // Use a fixed absolute URL to avoid any base path manipulation issues.
                return $this->redirect('http://localhost/rental-management/dashboard');
            }
            
            $this->Flash->error('Invalid email or password');
        }
    }

    public function logout()
    {
        $this->Authentication->logout();
        // After logout, always go to the login page.
        return $this->redirect('http://localhost/rental-management/login');
    }

    public function register()
    {
        $this->request->allowMethod(['get', 'post']);
        
        $user = $this->Users->newEmptyEntity();
        
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['role'] = $data['role'] ?? 'tenant';
            
            // Set default values
            $data['is_active'] = $data['is_active'] ?? true;
            $data['email_verified'] = $data['email_verified'] ?? false;
            $data['phone_verified'] = $data['phone_verified'] ?? false;
            
            $user = $this->Users->patchEntity($user, $data);
            
            if ($this->Users->save($user)) {
                // Create tenant/landlord record based on role
                try {
                    if ($user->role === 'tenant') {
                        $this->loadModel('Tenants');
                        $tenant = $this->Tenants->newEmptyEntity();
                        $tenant->user_id = $user->id;
                        if (!$this->Tenants->save($tenant)) {
                            // Log validation errors
                            $errors = $tenant->getErrors();
                            \Cake\Log\Log::error('Tenant save errors: ' . json_encode($errors));
                            $this->Flash->warning('User created but tenant record failed. Please contact admin.');
                        }
                    } elseif ($user->role === 'landlord') {
                        $this->loadModel('Landlords');
                        $landlord = $this->Landlords->newEmptyEntity();
                        $landlord->user_id = $user->id;
                        if (!$this->Landlords->save($landlord)) {
                            // Log validation errors
                            $errors = $landlord->getErrors();
                            \Cake\Log\Log::error('Landlord save errors: ' . json_encode($errors));
                            $this->Flash->warning('User created but landlord record failed. Please contact admin.');
                        }
                    }
                } catch (\Exception $e) {
                    \Cake\Log\Log::error('Exception creating tenant/landlord record: ' . $e->getMessage());
                    $this->Flash->warning('User created but profile record failed. Please contact admin.');
                }
                
                $this->Flash->success('Registration successful. Please login.');
                return $this->redirect(['action' => 'login']);
            }
            
            // Show validation errors - entity will be passed to view with errors
            $errors = $user->getErrors();
            if (!empty($errors)) {
                $errorMessages = [];
                foreach ($errors as $field => $fieldErrors) {
                    foreach ($fieldErrors as $error) {
                        $errorMessages[] = ucfirst($field) . ': ' . $error;
                    }
                }
                $this->Flash->error('Please fix the following errors: ' . implode(', ', $errorMessages));
            } else {
                $this->Flash->error('Registration failed. Please check your input and try again.');
            }
        }
        
        $this->set(compact('user'));
    }

    public function view($id = null)
    {
        $currentUser = $this->Authentication->getIdentity();
        
        // Only admin can view user details
        if ($currentUser->role !== 'admin') {
            $this->Flash->error('Access denied');
            return $this->redirect('/dashboard');
        }
        
        try {
            $user = $this->Users->get($id, [
                'contain' => ['Tenants', 'Landlords'],
            ]);
            
            $this->set(compact('user'));
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('User not found');
            return $this->redirect(['action' => 'index']);
        }
    }

    public function edit($id = null)
    {
        $currentUser = $this->Authentication->getIdentity();
        
        // Only admin can edit users
        if ($currentUser->role !== 'admin') {
            $this->Flash->error('Access denied');
            return $this->redirect('/dashboard');
        }
        
        $this->request->allowMethod(['get', 'post', 'put']);
        
        try {
            $user = $this->Users->get($id, [
                'contain' => ['Tenants', 'Landlords'],
            ]);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('User not found');
            return $this->redirect(['action' => 'index']);
        }
        
        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();
            
            // Handle password update - make it optional
            if (empty($data['password']) || trim($data['password']) === '') {
                unset($data['password']);
            }
            
            // Don't allow changing user's own role (prevent self-demotion)
            if ($id == $currentUser->id && isset($data['role']) && $data['role'] !== $user->role) {
                $this->Flash->error('You cannot change your own role');
                return $this->redirect(['action' => 'edit', $id]);
            }
            
            $user = $this->Users->patchEntity($user, $data);
            
            if ($this->Users->save($user)) {
                // Update tenant/landlord record if role changed
                try {
                    if ($user->role === 'tenant') {
                        $this->loadModel('Tenants');
                        $tenant = $this->Tenants->find()
                            ->where(['user_id' => $user->id])
                            ->first();
                        
                        if (!$tenant) {
                            // Create tenant record if it doesn't exist
                            $tenant = $this->Tenants->newEmptyEntity();
                            $tenant->user_id = $user->id;
                            $this->Tenants->save($tenant);
                        }
                        
                        // Remove landlord record if exists
                        $this->loadModel('Landlords');
                        $landlord = $this->Landlords->find()
                            ->where(['user_id' => $user->id])
                            ->first();
                        if ($landlord) {
                            $this->Landlords->delete($landlord);
                        }
                    } elseif ($user->role === 'landlord') {
                        $this->loadModel('Landlords');
                        $landlord = $this->Landlords->find()
                            ->where(['user_id' => $user->id])
                            ->first();
                        
                        if (!$landlord) {
                            // Create landlord record if it doesn't exist
                            $landlord = $this->Landlords->newEmptyEntity();
                            $landlord->user_id = $user->id;
                            
                            // Handle landlord-specific fields if provided
                            if (!empty($data['company_name'])) {
                                $landlord->company_name = $data['company_name'];
                            }
                            if (!empty($data['tax_id'])) {
                                $landlord->tax_id = $data['tax_id'];
                            }
                            if (!empty($data['address'])) {
                                $landlord->address = $data['address'];
                            }
                            if (!empty($data['bank_account'])) {
                                $landlord->bank_account = $data['bank_account'];
                            }
                            
                            $this->Landlords->save($landlord);
                        } else {
                            // Update landlord fields if provided
                            if (!empty($data['company_name'])) {
                                $landlord->company_name = $data['company_name'];
                            }
                            if (!empty($data['tax_id'])) {
                                $landlord->tax_id = $data['tax_id'];
                            }
                            if (!empty($data['address'])) {
                                $landlord->address = $data['address'];
                            }
                            if (!empty($data['bank_account'])) {
                                $landlord->bank_account = $data['bank_account'];
                            }
                            $this->Landlords->save($landlord);
                        }
                        
                        // Remove tenant record if exists
                        $this->loadModel('Tenants');
                        $tenant = $this->Tenants->find()
                            ->where(['user_id' => $user->id])
                            ->first();
                        if ($tenant) {
                            $this->Tenants->delete($tenant);
                        }
                    }
                } catch (\Exception $e) {
                    \Cake\Log\Log::error('Exception updating tenant/landlord record: ' . $e->getMessage());
                    $this->Flash->warning('User updated but profile record update had issues.');
                }
                
                $this->Flash->success('User updated successfully.');
                return $this->redirect(['action' => 'view', $id]);
            }
            
            // Show validation errors
            $errors = $user->getErrors();
            if (!empty($errors)) {
                $errorMessages = [];
                foreach ($errors as $field => $fieldErrors) {
                    foreach ($fieldErrors as $error) {
                        $errorMessages[] = ucfirst($field) . ': ' . $error;
                    }
                }
                $this->Flash->error('Please fix the following errors: ' . implode(', ', $errorMessages));
            } else {
                $this->Flash->error('User update failed. Please check your input and try again.');
            }
        }
        
        // Load landlord data if exists
        $landlordData = null;
        if (isset($user->landlord)) {
            $landlordData = $user->landlord;
        } elseif ($user->role === 'landlord') {
            $this->loadModel('Landlords');
            $landlordData = $this->Landlords->find()
                ->where(['user_id' => $user->id])
                ->first();
        }
        
        $this->set(compact('user', 'landlordData'));
    }
}

