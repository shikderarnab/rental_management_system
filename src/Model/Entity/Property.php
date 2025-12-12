<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Property extends Entity
{
    protected $_accessible = [
        'landlord_id' => true,
        'name' => true,
        'address' => true,
        'city' => true,
        'state' => true,
        'zip_code' => true,
        'country' => true,
        'description' => true,
        'is_active' => true,
        'created' => true,
        'modified' => true,
        'landlord' => true,
        'units' => true,
    ];
}

