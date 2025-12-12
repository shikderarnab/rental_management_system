<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Reminder extends Entity
{
    protected $_accessible = [
        'contract_id' => true,
        'tenant_id' => true,
        'reminder_type' => true,
        'message' => true,
        'send_sms' => true,
        'send_email' => true,
        'sms_sent' => true,
        'email_sent' => true,
        'scheduled_at' => true,
        'sent_at' => true,
        'created' => true,
        'modified' => true,
        'contract' => true,
        'tenant' => true,
    ];
}

