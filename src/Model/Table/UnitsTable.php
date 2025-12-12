<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class UnitsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('units');
        $this->setDisplayField('unit_number');
        $this->setPrimaryKey('id');

        $this->belongsTo('Properties', [
            'foreignKey' => 'property_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Contracts', [
            'foreignKey' => 'unit_id',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('property_id')
            ->requirePresence('property_id', 'create')
            ->notEmptyString('property_id');

        $validator
            ->scalar('unit_number')
            ->maxLength('unit_number', 50)
            ->requirePresence('unit_number', 'create')
            ->notEmptyString('unit_number');

        $validator
            ->decimal('rent_amount')
            ->requirePresence('rent_amount', 'create')
            ->notEmptyString('rent_amount');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('property_id', 'Properties'), ['errorField' => 'property_id']);

        return $rules;
    }
}

