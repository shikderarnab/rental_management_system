<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Tenant extends Entity
{
    protected $_accessible = [
        'user_id' => true,
        'id_number' => true,
        'emergency_contact' => true,
        'emergency_phone' => true,
        'created' => true,
        'modified' => true,
        'user' => true,
        'contracts' => true,
        'payments' => true,
    ];
}

