<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class RemindersTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('reminders');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Contracts', [
            'foreignKey' => 'contract_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Tenants', [
            'foreignKey' => 'tenant_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->inList('reminder_type', ['rent_due', 'agreement_expiry', 'payment_overdue'])
            ->requirePresence('reminder_type', 'create')
            ->notEmptyString('reminder_type');

        $validator
            ->scalar('message')
            ->requirePresence('message', 'create')
            ->notEmptyString('message');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('contract_id', 'Contracts'), ['errorField' => 'contract_id']);
        $rules->add($rules->existsIn('tenant_id', 'Tenants'), ['errorField' => 'tenant_id']);

        return $rules;
    }
}

