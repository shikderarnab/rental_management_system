<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateReminders extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('reminders', [
            'id' => false,
            'primary_key' => ['id']
        ]);
        $table->addColumn('id', 'integer', [
            'autoIncrement' => true,
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('contract_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('tenant_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('reminder_type', 'enum', [
            'values' => ['rent_due', 'agreement_expiry', 'payment_overdue'],
            'default' => 'rent_due',
            'null' => false,
        ]);
        $table->addColumn('message', 'text', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('send_sms', 'boolean', [
            'default' => false,
            'null' => false,
        ]);
        $table->addColumn('send_email', 'boolean', [
            'default' => false,
            'null' => false,
        ]);
        $table->addColumn('sms_sent', 'boolean', [
            'default' => false,
            'null' => false,
        ]);
        $table->addColumn('email_sent', 'boolean', [
            'default' => false,
            'null' => false,
        ]);
        $table->addColumn('scheduled_at', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('sent_at', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addIndex(['contract_id']);
        $table->addIndex(['tenant_id']);
        $table->addIndex(['reminder_type']);
        $table->addIndex(['scheduled_at']);
        $table->create();
    }
}

