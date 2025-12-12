<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;

class ProfileController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadModel('Users');
    }

    public function index()
    {
        $user = $this->Authentication->getIdentity();
        
        if (!$user) {
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }
        
        // Load user with related data
        $user = $this->Users->get($user->id, [
            'contain' => ['Tenants', 'Landlords'],
        ]);
        
        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();
            
            // Don't allow role changes through profile
            unset($data['role']);
            
            // Handle password update - make it optional
            if (empty($data['password']) || trim($data['password']) === '') {
                unset($data['password']);
                unset($data['password_confirm']); // Remove password confirmation if exists
            }
            
            // Set validation context to 'update' to allow empty password
            $user = $this->Users->patchEntity($user, $data, [
                'validate' => 'default',
                'accessibleFields' => ['first_name' => true, 'last_name' => true, 'email' => true, 'phone' => true, 'password' => true]
            ]);
            
            if ($this->Users->save($user)) {
                // Update authentication identity
                $this->Authentication->setIdentity($user);
                
                $this->Flash->success('Profile updated successfully');
                return $this->redirect(['action' => 'index']);
            }
            
            // Display validation errors if any
            if ($user->hasErrors()) {
                $errors = [];
                foreach ($user->getErrors() as $field => $fieldErrors) {
                    foreach ($fieldErrors as $error) {
                        $errors[] = ucfirst($field) . ': ' . $error;
                    }
                }
                $this->Flash->error('Failed to update profile: ' . implode(', ', $errors));
            } else {
                $this->Flash->error('Failed to update profile. Please try again.');
            }
        }
        
        $this->set(compact('user'));
    }
}

