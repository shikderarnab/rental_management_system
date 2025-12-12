<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateDisputes extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('disputes', [
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
            'null' => true,
        ]);
        $table->addColumn('payment_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => true,
        ]);
        $table->addColumn('tenant_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('landlord_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('subject', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('description', 'text', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('status', 'enum', [
            'values' => ['open', 'reviewing', 'resolved', 'closed'],
            'default' => 'open',
            'null' => false,
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
        $table->addIndex(['payment_id']);
        $table->addIndex(['tenant_id']);
        $table->addIndex(['landlord_id']);
        $table->addIndex(['status']);
        $table->create();
    }
}

