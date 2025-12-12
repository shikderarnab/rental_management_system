<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;

class DashboardController extends AppController
{
    public function index()
    {
        $user = $this->Authentication->getIdentity();
        
        if (!$user) {
            // If not logged in, always go to the login page.
            return $this->redirect('http://localhost/rental-management/login');
        }
        
        $this->loadModel('Payments');
        $this->loadModel('Contracts');
        $this->loadModel('Properties');
        $this->loadModel('Tenants');
        $this->loadModel('Landlords');
        $this->loadModel('Units');
        
        $data = [];
        $chartData = [];
        $topData = [];
        
        switch ($user->role) {
            case 'admin':
                // Basic stats
                $data = [
                    'total_properties' => $this->Properties->find()->count(),
                    'total_contracts' => $this->Contracts->find()->count(),
                    'total_payments' => $this->Payments->find()->count(),
                    'pending_payments' => $this->Payments->find()->where(['payment_status' => 'pending'])->count(),
                    'verified_payments' => $this->Payments->find()->where(['payment_status' => 'verified'])->count(),
                    'rejected_payments' => $this->Payments->find()->where(['payment_status' => 'rejected'])->count(),
                    'total_revenue' => $this->Payments->find()
                        ->where(['payment_status' => 'verified'])
                        ->select(['total' => 'SUM(amount)'])
                        ->first(),
                    'total_tenants' => $this->Tenants->find()->count(),
                    'total_landlords' => $this->Landlords->find()->count(),
                    'active_contracts' => $this->Contracts->find()->where(['status' => 'active'])->count(),
                ];
                
                // Payment status chart data
                $chartData['payment_status'] = [
                    'pending' => $data['pending_payments'],
                    'verified' => $data['verified_payments'],
                    'rejected' => $data['rejected_payments'],
                ];
                
                // Monthly revenue (last 6 months)
                $chartData['monthly_revenue'] = $this->getMonthlyRevenue(null);
                
                // Contract status chart data
                $chartData['contract_status'] = [
                    'active' => $this->Contracts->find()->where(['status' => 'active'])->count(),
                    'pending_signature' => $this->Contracts->find()->where(['status' => 'pending_signature'])->count(),
                    'expired' => $this->Contracts->find()->where(['status' => 'expired'])->count(),
                    'terminated' => $this->Contracts->find()->where(['status' => 'terminated'])->count(),
                ];
                
                // Top 5 Properties by Revenue - Using manual join
                try {
                    $topPropertiesQuery = $this->Payments->find()
                        ->join([
                            'Contracts' => [
                                'table' => 'contracts',
                                'type' => 'INNER',
                                'conditions' => 'Contracts.id = Payments.contract_id'
                            ],
                            'Units' => [
                                'table' => 'units',
                                'type' => 'INNER',
                                'conditions' => 'Units.id = Contracts.unit_id'
                            ],
                            'Properties' => [
                                'table' => 'properties',
                                'type' => 'INNER',
                                'conditions' => 'Properties.id = Units.property_id'
                            ]
                        ])
                        ->where(['Payments.payment_status' => 'verified'])
                        ->select([
                            'property_id' => 'Properties.id',
                            'property_name' => 'Properties.name',
                            'total_revenue' => 'SUM(Payments.amount)'
                        ])
                        ->group(['Properties.id', 'Properties.name'])
                        ->order(['total_revenue' => 'DESC'])
                        ->limit(5);
                    
                    $topData['top_properties'] = $topPropertiesQuery->toArray();
                } catch (\Exception $e) {
                    \Cake\Log\Log::error('Error fetching top properties: ' . $e->getMessage());
                    $topData['top_properties'] = [];
                }
                
                // Top 5 Tenants by Payment Amount - Using manual join
                try {
                    $topTenantsQuery = $this->Payments->find()
                        ->join([
                            'Tenants' => [
                                'table' => 'tenants',
                                'type' => 'INNER',
                                'conditions' => 'Tenants.id = Payments.tenant_id'
                            ]
                        ])
                        ->where(['Payments.payment_status' => 'verified'])
                        ->select([
                            'tenant_id' => 'Tenants.id',
                            'total_paid' => 'SUM(Payments.amount)',
                            'payment_count' => 'COUNT(Payments.id)'
                        ])
                        ->group(['Tenants.id'])
                        ->order(['total_paid' => 'DESC'])
                        ->limit(5);
                    
                    $topTenantsResults = $topTenantsQuery->toArray();
                    // Load tenant user data separately
                    foreach ($topTenantsResults as &$result) {
                        if (isset($result->tenant_id)) {
                            try {
                                $tenant = $this->Tenants->get($result->tenant_id, ['contain' => ['Users']]);
                                $result->tenant = $tenant;
                            } catch (\Exception $e) {
                                // Skip if tenant not found
                            }
                        }
                    }
                    $topData['top_tenants'] = $topTenantsResults;
                } catch (\Exception $e) {
                    \Cake\Log\Log::error('Error fetching top tenants: ' . $e->getMessage());
                    $topData['top_tenants'] = [];
                }
                
                // Top 5 Recent Payments
                $topData['recent_payments'] = $this->Payments->find()
                    ->contain(['Tenants.Users', 'Contracts.Units.Properties'])
                    ->order(['Payments.created' => 'DESC'])
                    ->limit(5)
                    ->toArray();
                
                // Top 5 Properties by Unit Count
                $topData['top_properties_by_units'] = $this->Properties->find()
                    ->select([
                        'id',
                        'name',
                        'unit_count' => 'COUNT(Units.id)'
                    ])
                    ->leftJoinWith('Units')
                    ->group(['Properties.id', 'Properties.name'])
                    ->order(['unit_count' => 'DESC'])
                    ->limit(5)
                    ->toArray();
                
                break;
                
            case 'landlord':
                $this->loadModel('Landlords');
                $landlord = $this->Landlords->find()
                    ->where(['user_id' => $user->id])
                    ->first();
                
                if ($landlord) {
                    // Basic stats
                    $data = [
                        'total_properties' => $this->Properties->find()
                            ->where(['landlord_id' => $landlord->id])
                            ->count(),
                        'total_contracts' => $this->Contracts->find()
                            ->where(['landlord_id' => $landlord->id])
                            ->count(),
                        'pending_payments' => $this->Payments->find()
                            ->where(['landlord_id' => $landlord->id, 'payment_status' => 'pending'])
                            ->count(),
                        'verified_payments' => $this->Payments->find()
                            ->where(['landlord_id' => $landlord->id, 'payment_status' => 'verified'])
                            ->count(),
                        'total_revenue' => $this->Payments->find()
                            ->where(['landlord_id' => $landlord->id, 'payment_status' => 'verified'])
                            ->select(['total' => 'SUM(amount)'])
                            ->first(),
                        'active_contracts' => $this->Contracts->find()
                            ->where(['landlord_id' => $landlord->id, 'status' => 'active'])
                            ->count(),
                    ];
                    
                    // Payment status chart data
                    $chartData['payment_status'] = [
                        'pending' => $data['pending_payments'],
                        'verified' => $data['verified_payments'],
                        'rejected' => $this->Payments->find()
                            ->where(['landlord_id' => $landlord->id, 'payment_status' => 'rejected'])
                            ->count(),
                    ];
                    
                    // Monthly revenue (last 6 months)
                    $chartData['monthly_revenue'] = $this->getMonthlyRevenue($landlord->id);
                    
                    // Contract status chart data
                    $chartData['contract_status'] = [
                        'active' => $this->Contracts->find()
                            ->where(['landlord_id' => $landlord->id, 'status' => 'active'])
                            ->count(),
                        'pending_signature' => $this->Contracts->find()
                            ->where(['landlord_id' => $landlord->id, 'status' => 'pending_signature'])
                            ->count(),
                        'expired' => $this->Contracts->find()
                            ->where(['landlord_id' => $landlord->id, 'status' => 'expired'])
                            ->count(),
                        'terminated' => $this->Contracts->find()
                            ->where(['landlord_id' => $landlord->id, 'status' => 'terminated'])
                            ->count(),
                    ];
                    
                    // Top 5 Properties by Revenue - Using manual join
                    try {
                        $topPropertiesQuery = $this->Payments->find()
                            ->join([
                                'Contracts' => [
                                    'table' => 'contracts',
                                    'type' => 'INNER',
                                    'conditions' => 'Contracts.id = Payments.contract_id'
                                ],
                                'Units' => [
                                    'table' => 'units',
                                    'type' => 'INNER',
                                    'conditions' => 'Units.id = Contracts.unit_id'
                                ],
                                'Properties' => [
                                    'table' => 'properties',
                                    'type' => 'INNER',
                                    'conditions' => 'Properties.id = Units.property_id'
                                ]
                            ])
                            ->where([
                                'Payments.landlord_id' => $landlord->id,
                                'Payments.payment_status' => 'verified',
                                'Properties.landlord_id' => $landlord->id
                            ])
                            ->select([
                                'property_id' => 'Properties.id',
                                'property_name' => 'Properties.name',
                                'total_revenue' => 'SUM(Payments.amount)'
                            ])
                            ->group(['Properties.id', 'Properties.name'])
                            ->order(['total_revenue' => 'DESC'])
                            ->limit(5);
                        
                        $topData['top_properties'] = $topPropertiesQuery->toArray();
                    } catch (\Exception $e) {
                        \Cake\Log\Log::error('Error fetching top properties: ' . $e->getMessage());
                        $topData['top_properties'] = [];
                    }
                    
                    // Top 5 Tenants by Payment Amount - Using manual join
                    try {
                        $topTenantsQuery = $this->Payments->find()
                            ->join([
                                'Tenants' => [
                                    'table' => 'tenants',
                                    'type' => 'INNER',
                                    'conditions' => 'Tenants.id = Payments.tenant_id'
                                ]
                            ])
                            ->where([
                                'Payments.landlord_id' => $landlord->id,
                                'Payments.payment_status' => 'verified'
                            ])
                            ->select([
                                'tenant_id' => 'Tenants.id',
                                'total_paid' => 'SUM(Payments.amount)',
                                'payment_count' => 'COUNT(Payments.id)'
                            ])
                            ->group(['Tenants.id'])
                            ->order(['total_paid' => 'DESC'])
                            ->limit(5);
                        
                        $topTenantsResults = $topTenantsQuery->toArray();
                        // Load tenant user data separately
                        foreach ($topTenantsResults as &$result) {
                            if (isset($result->tenant_id)) {
                                try {
                                    $tenant = $this->Tenants->get($result->tenant_id, ['contain' => ['Users']]);
                                    $result->tenant = $tenant;
                                } catch (\Exception $e) {
                                    // Skip if tenant not found
                                }
                            }
                        }
                        $topData['top_tenants'] = $topTenantsResults;
                    } catch (\Exception $e) {
                        \Cake\Log\Log::error('Error fetching top tenants: ' . $e->getMessage());
                        $topData['top_tenants'] = [];
                    }
                    
                    // Top 5 Recent Payments
                    $topData['recent_payments'] = $this->Payments->find()
                        ->contain(['Tenants.Users', 'Contracts.Units.Properties'])
                        ->where(['Payments.landlord_id' => $landlord->id])
                        ->order(['Payments.created' => 'DESC'])
                        ->limit(5)
                        ->toArray();
                    
                    // Top 5 Properties by Unit Count
                    $topData['top_properties_by_units'] = $this->Properties->find()
                        ->where(['landlord_id' => $landlord->id])
                        ->select([
                            'id',
                            'name',
                            'unit_count' => 'COUNT(Units.id)'
                        ])
                        ->leftJoinWith('Units')
                        ->group(['Properties.id', 'Properties.name'])
                        ->order(['unit_count' => 'DESC'])
                        ->limit(5)
                        ->toArray();
                }
                break;
                
            case 'tenant':
                $this->loadModel('Tenants');
                $tenant = $this->Tenants->find()
                    ->where(['user_id' => $user->id])
                    ->first();
                
                if ($tenant) {
                    $data = [
                        'active_contracts' => $this->Contracts->find()
                            ->where(['tenant_id' => $tenant->id, 'status' => 'active'])
                            ->count(),
                        'pending_payments' => $this->Payments->find()
                            ->where(['tenant_id' => $tenant->id, 'payment_status' => 'pending'])
                            ->count(),
                        'payment_history' => $this->Payments->find()
                            ->where(['tenant_id' => $tenant->id])
                            ->order(['Payments.created' => 'DESC'])
                            ->limit(5)
                            ->toArray(),
                    ];
                }
                break;
        }
        
        $this->set(compact('data', 'user', 'chartData', 'topData'));
    }
    
    /**
     * Get monthly revenue for the last 6 months
     */
    private function getMonthlyRevenue($landlordId = null)
    {
        $months = [];
        $revenues = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = new \DateTime();
            $date->modify("-{$i} months");
            $monthStart = $date->format('Y-m-01');
            $monthEnd = $date->format('Y-m-t');
            
            $query = $this->Payments->find()
                ->where([
                    'Payments.payment_status' => 'verified',
                    'DATE(Payments.created) >=' => $monthStart,
                    'DATE(Payments.created) <=' => $monthEnd,
                ]);
            
            if ($landlordId !== null) {
                $query->where(['Payments.landlord_id' => $landlordId]);
            }
            
            $revenue = $query->select(['total' => 'SUM(amount)'])->first();
            
            $months[] = $date->format('M Y');
            $revenues[] = $revenue->total ?? 0;
        }
        
        return [
            'months' => $months,
            'revenues' => $revenues,
        ];
    }
}

