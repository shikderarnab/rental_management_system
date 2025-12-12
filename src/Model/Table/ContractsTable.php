<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ContractsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('contracts');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Units', [
            'foreignKey' => 'unit_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Tenants', [
            'foreignKey' => 'tenant_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Landlords', [
            'foreignKey' => 'landlord_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Signatures', [
            'foreignKey' => 'contract_id',
        ]);
        $this->hasMany('Payments', [
            'foreignKey' => 'contract_id',
        ]);
        $this->hasMany('Reminders', [
            'foreignKey' => 'contract_id',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->date('start_date')
            ->requirePresence('start_date', 'create')
            ->notEmptyDate('start_date');

        $validator
            ->decimal('rent_amount')
            ->requirePresence('rent_amount', 'create')
            ->notEmptyString('rent_amount');

        $validator
            ->inList('status', ['draft', 'pending_signature', 'active', 'expired', 'terminated'])
            ->requirePresence('status', 'create')
            ->notEmptyString('status');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('unit_id', 'Units'), ['errorField' => 'unit_id']);
        $rules->add($rules->existsIn('tenant_id', 'Tenants'), ['errorField' => 'tenant_id']);
        $rules->add($rules->existsIn('landlord_id', 'Landlords'), ['errorField' => 'landlord_id']);

        return $rules;
    }
}

