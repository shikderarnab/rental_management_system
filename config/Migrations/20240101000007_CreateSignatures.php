<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateSignatures extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('signatures', [
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
        $table->addColumn('user_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('signature_type', 'enum', [
            'values' => ['typed', 'drawn', 'uploaded'],
            'default' => 'typed',
            'null' => false,
        ]);
        $table->addColumn('signature_data', 'text', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('signed_at', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('ip_address', 'string', [
            'default' => null,
            'limit' => 45,
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
        $table->addIndex(['user_id']);
        $table->create();
    }
}

