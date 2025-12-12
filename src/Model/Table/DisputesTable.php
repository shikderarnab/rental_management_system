<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\Behavior\TimestampBehavior;

class DisputesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('disputes');
        $this->setDisplayField('subject');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Contracts', [
            'foreignKey' => 'contract_id',
        ]);
        $this->belongsTo('Payments', [
            'foreignKey' => 'payment_id',
        ]);
        $this->belongsTo('Tenants', [
            'foreignKey' => 'tenant_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Landlords', [
            'foreignKey' => 'landlord_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('DisputeMessages', [
            'foreignKey' => 'dispute_id',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('subject')
            ->maxLength('subject', 255)
            ->requirePresence('subject', 'create')
            ->notEmptyString('subject');

        $validator
            ->scalar('description')
            ->requirePresence('description', 'create')
            ->notEmptyString('description');

        $validator
            ->inList('status', ['open', 'reviewing', 'resolved', 'closed'])
            ->requirePresence('status', 'create')
            ->notEmptyString('status');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('tenant_id', 'Tenants'), ['errorField' => 'tenant_id']);
        $rules->add($rules->existsIn('landlord_id', 'Landlords'), ['errorField' => 'landlord_id']);

        return $rules;
    }
}

