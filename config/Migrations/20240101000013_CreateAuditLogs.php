<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateAuditLogs extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('audit_logs', [
            'id' => false,
            'primary_key' => ['id']
        ]);
        $table->addColumn('id', 'integer', [
            'autoIncrement' => true,
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('user_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => true,
        ]);
        $table->addColumn('action', 'string', [
            'default' => null,
            'limit' => 100,
            'null' => false,
        ]);
        $table->addColumn('model', 'string', [
            'default' => null,
            'limit' => 100,
            'null' => false,
        ]);
        $table->addColumn('model_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => true,
        ]);
        $table->addColumn('data', 'text', [
            'default' => null,
            'null' => true,
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
        $table->addIndex(['user_id']);
        $table->addIndex(['model', 'model_id']);
        $table->addIndex(['action']);
        $table->addIndex(['created']);
        $table->create();
    }
}

