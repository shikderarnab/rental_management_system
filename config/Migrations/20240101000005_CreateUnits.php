<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateUnits extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('units', [
            'id' => false,
            'primary_key' => ['id']
        ]);
        $table->addColumn('id', 'integer', [
            'autoIncrement' => true,
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('property_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('unit_number', 'string', [
            'default' => null,
            'limit' => 50,
            'null' => false,
        ]);
        $table->addColumn('type', 'string', [
            'default' => null,
            'limit' => 50,
            'null' => false,
        ]);
        $table->addColumn('bedrooms', 'integer', [
            'default' => 0,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('bathrooms', 'decimal', [
            'default' => 0,
            'precision' => 2,
            'scale' => 1,
            'null' => false,
        ]);
        $table->addColumn('area', 'decimal', [
            'default' => null,
            'precision' => 10,
            'scale' => 2,
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
        $table->addColumn('is_available', 'boolean', [
            'default' => true,
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
        $table->addIndex(['property_id']);
        $table->addIndex(['unit_number', 'property_id'], ['unique' => true]);
        $table->create();
    }
}

