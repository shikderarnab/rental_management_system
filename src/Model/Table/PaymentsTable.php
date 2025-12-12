<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\Behavior\TimestampBehavior;

class PaymentsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('payments');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Contracts', [
            'foreignKey' => 'contract_id',
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
        $this->hasOne('Invoices', [
            'foreignKey' => 'payment_id',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->decimal('amount')
            ->requirePresence('amount', 'create')
            ->notEmptyString('amount')
            ->greaterThan('amount', 0);

        $validator
            ->inList('payment_method', ['cash', 'bank', 'online'])
            ->requirePresence('payment_method', 'create')
            ->notEmptyString('payment_method');

        $validator
            ->inList('payment_status', ['pending', 'verified', 'rejected'])
            ->requirePresence('payment_status', 'create')
            ->notEmptyString('payment_status');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('contract_id', 'Contracts'), ['errorField' => 'contract_id']);
        $rules->add($rules->existsIn('tenant_id', 'Tenants'), ['errorField' => 'tenant_id']);
        $rules->add($rules->existsIn('landlord_id', 'Landlords'), ['errorField' => 'landlord_id']);

        return $rules;
    }
}

