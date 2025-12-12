<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Landlord extends Entity
{
    protected $_accessible = [
        'user_id' => true,
        'company_name' => true,
        'tax_id' => true,
        'address' => true,
        'bank_account' => true,
        'created' => true,
        'modified' => true,
        'user' => true,
        'properties' => true,
        'contracts' => true,
        'payments' => true,
    ];
}

