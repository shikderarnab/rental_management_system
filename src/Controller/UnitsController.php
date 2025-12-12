<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;

class UnitsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadModel('Units');
    }

    public function index($propertyId = null)
    {
        $query = $this->Units->find()->contain(['Properties']);
        
        if ($propertyId) {
            $query->where(['property_id' => $propertyId]);
        }
        
        $units = $query->toArray();
        $this->set(compact('units', 'propertyId'));
    }

    public function view($id = null)
    {
        try {
            $unit = $this->Units->get($id, [
                'contain' => ['Properties', 'Contracts'],
            ]);
            
            $this->set(compact('unit'));
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Unit not found');
            return $this->redirect(['action' => 'index']);
        }
    }

    public function add($propertyId = null)
    {
        // Get propertyId from route parameter if not passed directly
        if ($propertyId === null) {
            $propertyId = $this->request->getParam('pass.0');
        }
        
        $unit = $this->Units->newEmptyEntity();
        
        if ($propertyId) {
            $unit->property_id = $propertyId;
        }
        
        if ($this->request->is('post')) {
            $unit = $this->Units->patchEntity($unit, $this->request->getData());
            
            if ($this->Units->save($unit)) {
                $this->Flash->success('Unit added successfully');
                return $this->redirect(['action' => 'index', $unit->property_id]);
            }
            
            $this->Flash->error('Failed to add unit');
        }
        
        $properties = $this->Units->Properties->find('list');
        $this->set(compact('unit', 'properties', 'propertyId'));
    }

    public function edit($id = null)
    {
        try {
            $unit = $this->Units->get($id, [
                'contain' => ['Properties'],
            ]);
            
            if ($this->request->is(['patch', 'post', 'put'])) {
                $unit = $this->Units->patchEntity($unit, $this->request->getData());
                
                if ($this->Units->save($unit)) {
                    $this->Flash->success('Unit updated successfully');
                    return $this->redirect(['action' => 'index', $unit->property_id]);
                }
                
                $this->Flash->error('Failed to update unit');
            }
            
            $properties = $this->Units->Properties->find('list');
            $this->set(compact('unit', 'properties'));
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Unit not found');
            return $this->redirect(['action' => 'index']);
        }
    }

    public function assignTenant($id = null)
    {
        try {
            $unit = $this->Units->get($id, [
                'contain' => ['Properties'],
            ]);
            
            // Redirect to contract creation with unit pre-selected.
            // Use a fixed absolute URL to avoid any base path duplication issues.
            $url = 'http://localhost/rental-management/contracts/add?unit_id=' . urlencode((string)$id);
            return $this->redirect($url);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Unit not found');
            return $this->redirect(['action' => 'index']);
        }
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        
        $unit = $this->Units->get($id);
        
        if ($this->Units->delete($unit)) {
            $this->Flash->success('Unit deleted');
        } else {
            $this->Flash->error('Failed to delete unit');
        }
        
        return $this->redirect(['action' => 'index']);
    }
}

