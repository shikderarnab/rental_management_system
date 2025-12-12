<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Signature extends Entity
{
    protected $_accessible = [
        'contract_id' => true,
        'user_id' => true,
        'signature_type' => true,
        'signature_data' => true,
        'signed_at' => true,
        'ip_address' => true,
        'created' => true,
        'modified' => true,
        'contract' => true,
        'user' => true,
    ];
}

