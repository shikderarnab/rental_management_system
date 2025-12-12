<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateContracts extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('contracts', [
            'id' => false,
            'primary_key' => ['id']
        ]);
        $table->addColumn('id', 'integer', [
            'autoIncrement' => true,
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('unit_id', 'integer', [
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
        $table->addColumn('agreement_file', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => true,
        ]);
        $table->addColumn('start_date', 'date', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('end_date', 'date', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('rent_amount', 'decimal', [
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
        $table->addColumn('status', 'enum', [
            'values' => ['draft', 'pending_signature', 'active', 'expired', 'terminated'],
            'default' => 'draft',
            'null' => false,
        ]);
        $table->addColumn('signed_file', 'string', [
            'default' => null,
            'limit' => 255,
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
        $table->addIndex(['unit_id']);
        $table->addIndex(['tenant_id']);
        $table->addIndex(['landlord_id']);
        $table->addIndex(['status']);
        $table->create();
    }
}

