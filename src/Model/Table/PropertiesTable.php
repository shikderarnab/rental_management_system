<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class PropertiesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('properties');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Landlords', [
            'foreignKey' => 'landlord_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Units', [
            'foreignKey' => 'property_id',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('landlord_id')
            ->requirePresence('landlord_id', 'create')
            ->notEmptyString('landlord_id');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('address')
            ->requirePresence('address', 'create')
            ->notEmptyString('address');

        $validator
            ->scalar('city')
            ->maxLength('city', 100)
            ->requirePresence('city', 'create')
            ->notEmptyString('city');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('landlord_id', 'Landlords'), ['errorField' => 'landlord_id']);

        return $rules;
    }
}

