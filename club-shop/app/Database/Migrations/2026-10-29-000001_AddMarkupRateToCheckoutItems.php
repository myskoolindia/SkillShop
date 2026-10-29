<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMarkupRateToCheckoutItems extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('checkout_items') && !$this->db->fieldExists('markup_rate', 'checkout_items')) {
            $this->forge->addColumn('checkout_items', [
                'markup_rate' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,2',
                    'default'    => 0.00,
                    'after'      => 'product_commission_rate',
                    'null'       => true,
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('checkout_items') && $this->db->fieldExists('markup_rate', 'checkout_items')) {
            $this->forge->dropColumn('checkout_items', 'markup_rate');
        }
    }
}

