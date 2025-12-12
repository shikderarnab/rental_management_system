<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class User extends Entity
{
    protected $_accessible = [
        'email' => true,
        'password' => true,
        'phone' => true,
        'role' => true,
        'first_name' => true,
        'last_name' => true,
        'is_active' => true,
        'email_verified' => true,
        'phone_verified' => true,
        'firebase_uid' => true,
        'created' => true,
        'modified' => true,
        'landlord' => true,
        'tenant' => true,
        'payments' => true,
        'signatures' => true,
        'audit_logs' => true,
    ];

    protected $_hidden = [
        'password',
    ];
}

