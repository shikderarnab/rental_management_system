<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class DisputeMessage extends Entity
{
    protected $_accessible = [
        'dispute_id' => true,
        'user_id' => true,
        'message' => true,
        'attachment_path' => true,
        'created' => true,
        'modified' => true,
        'dispute' => true,
        'user' => true,
    ];
}

