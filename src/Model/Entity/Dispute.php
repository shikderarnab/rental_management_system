<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Dispute extends Entity
{
    protected $_accessible = [
        'contract_id' => true,
        'payment_id' => true,
        'tenant_id' => true,
        'landlord_id' => true,
        'subject' => true,
        'description' => true,
        'status' => true,
        'created' => true,
        'modified' => true,
        'contract' => true,
        'payment' => true,
        'tenant' => true,
        'landlord' => true,
        'dispute_messages' => true,
    ];
}

