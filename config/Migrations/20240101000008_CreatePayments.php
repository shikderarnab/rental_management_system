<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreatePayments extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('payments', [
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
        $table->addColumn('landlord_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('amount', 'decimal', [
            'default' => null,
            'precision' => 10,
            'scale' => 2,
            'null' => false,
        ]);
        $table->addColumn('currency', 'string', [
            'default' => 'USD',
            'limit' => 3,
            'null' => false,
        ]);
        $table->addColumn('payment_method', 'enum', [
            'values' => ['cash', 'bank', 'online'],
            'default' => 'cash',
            'null' => false,
        ]);
        $table->addColumn('payment_status', 'enum', [
            'values' => ['pending', 'verified', 'rejected'],
            'default' => 'pending',
            'null' => false,
        ]);
        $table->addColumn('proof_path', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => true,
        ]);
        $table->addColumn('reference', 'string', [
            'default' => null,
            'limit' => 100,
            'null' => true,
        ]);
        $table->addColumn('remarks', 'text', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('paid_at', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('verified_at', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('verified_by', 'integer', [
            'default' => null,
            'limit' => 11,
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
        $table->addIndex(['landlord_id']);
        $table->addIndex(['payment_status']);
        $table->addIndex(['payment_method']);
        $table->create();
    }
}

