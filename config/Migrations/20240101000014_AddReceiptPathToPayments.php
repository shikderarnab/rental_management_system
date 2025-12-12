<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddReceiptPathToPayments extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('payments');
        $table->addColumn('receipt_path', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => true,
            'after' => 'proof_path'
        ]);
        $table->update();
    }
}

