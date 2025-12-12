<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Payment extends Entity
{
    protected $_accessible = [
        'contract_id' => true,
        'tenant_id' => true,
        'landlord_id' => true,
        'amount' => true,
        'currency' => true,
        'payment_method' => true,
        'payment_status' => true,
        'proof_path' => true,
        'receipt_path' => true,
        'reference' => true,
        'remarks' => true,
        'paid_at' => true,
        'verified_at' => true,
        'verified_by' => true,
        'created' => true,
        'modified' => true,
        'contract' => true,
        'tenant' => true,
        'landlord' => true,
        'invoice' => true,
    ];
}

