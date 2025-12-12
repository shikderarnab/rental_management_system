<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Contract extends Entity
{
    protected $_accessible = [
        'unit_id' => true,
        'tenant_id' => true,
        'landlord_id' => true,
        'agreement_file' => true,
        'start_date' => true,
        'end_date' => true,
        'rent_amount' => true,
        'currency' => true,
        'status' => true,
        'signed_file' => true,
        'created' => true,
        'modified' => true,
        'unit' => true,
        'tenant' => true,
        'landlord' => true,
        'signatures' => true,
        'payments' => true,
        'reminders' => true,
    ];
}

