<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Event\EventInterface;
use ArrayObject;
use Cake\Utility\Security;

class UsersTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('users');
        $this->setDisplayField('email');
        $this->setPrimaryKey('id');

        $this->hasOne('Landlords', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasOne('Tenants', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Payments', [
            'foreignKey' => 'tenant_id',
        ]);
        $this->hasMany('Signatures', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('AuditLogs', [
            'foreignKey' => 'user_id',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmptyString('email')
            ->add('email', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('password')
            ->maxLength('password', 255)
            ->requirePresence('password', 'create')
            ->allowEmptyString('password', 'Password is optional when updating', function ($context) {
                // Allow empty password when updating (id exists)
                return !empty($context['data']['id']);
            })
            ->notEmptyString('password', 'Password is required', function ($context) {
                // Only require password when creating (id is empty)
                return empty($context['data']['id']);
            });

        $validator
            ->scalar('phone')
            ->maxLength('phone', 20)
            ->allowEmptyString('phone');

        $validator
            ->inList('role', ['admin', 'landlord', 'tenant'])
            ->requirePresence('role', 'create')
            ->notEmptyString('role');

        $validator
            ->scalar('first_name')
            ->maxLength('first_name', 100)
            ->requirePresence('first_name', 'create')
            ->notEmptyString('first_name');

        $validator
            ->scalar('last_name')
            ->maxLength('last_name', 100)
            ->requirePresence('last_name', 'create')
            ->notEmptyString('last_name');

        return $validator;
    }

    public function beforeSave(EventInterface $event, $entity, ArrayObject $options)
    {
        if ($entity->isDirty('password') && !empty($entity->password)) {
            $entity->password = password_hash($entity->password, PASSWORD_DEFAULT);
        }
    }
}

