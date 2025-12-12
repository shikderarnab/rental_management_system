<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Unit extends Entity
{
    protected $_accessible = [
        'property_id' => true,
        'unit_number' => true,
        'type' => true,
        'bedrooms' => true,
        'bathrooms' => true,
        'area' => true,
        'rent_amount' => true,
        'currency' => true,
        'is_available' => true,
        'created' => true,
        'modified' => true,
        'property' => true,
        'contracts' => true,
    ];
}

