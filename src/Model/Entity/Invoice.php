<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Invoice extends Entity
{
    protected $_accessible = [
        'payment_id' => true,
        'invoice_number' => true,
        'file_path' => true,
        'created' => true,
        'modified' => true,
        'payment' => true,
    ];
}

