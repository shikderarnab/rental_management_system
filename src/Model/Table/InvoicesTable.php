<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class InvoicesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('invoices');
        $this->setDisplayField('invoice_number');
        $this->setPrimaryKey('id');

        $this->belongsTo('Payments', [
            'foreignKey' => 'payment_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('payment_id')
            ->requirePresence('payment_id', 'create')
            ->notEmptyString('payment_id');

        $validator
            ->scalar('invoice_number')
            ->maxLength('invoice_number', 50)
            ->requirePresence('invoice_number', 'create')
            ->notEmptyString('invoice_number')
            ->add('invoice_number', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('file_path')
            ->maxLength('file_path', 255)
            ->requirePresence('file_path', 'create')
            ->notEmptyString('file_path');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('payment_id', 'Payments'), ['errorField' => 'payment_id']);

        return $rules;
    }
}

