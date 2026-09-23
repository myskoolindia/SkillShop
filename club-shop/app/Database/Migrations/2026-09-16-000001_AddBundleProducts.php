<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBundleProducts extends Migration
{
    public function up()
    {
        // 1. Add bundle columns to products table if not existing
        $fieldsToAddToProducts = [];
        if (!$this->db->fieldExists('is_bundle', 'products')) {
            $fieldsToAddToProducts['is_bundle'] = [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'product_type'
            ];
        }
        if (!$this->db->fieldExists('bundle_pricing_type', 'products')) {
            $fieldsToAddToProducts['bundle_pricing_type'] = [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'fixed', // 'fixed' or 'dynamic_discount'
                'after'      => 'is_bundle'
            ];
        }
        if (!$this->db->fieldExists('bundle_discount_rate', 'products')) {
            $fieldsToAddToProducts['bundle_discount_rate'] = [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
                'after'      => 'bundle_pricing_type'
            ];
        }
        if (!empty($fieldsToAddToProducts)) {
            $this->forge->addColumn('products', $fieldsToAddToProducts);
        }

        // 2. Create product_bundles table
        if (!$this->db->tableExists('product_bundles')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'BIGINT',
                    'constraint'     => 20,
                    'unsigned'       => true,
                    'auto_increment' => true
                ],
                'bundle_product_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true
                ],
                'component_product_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true
                ],
                'variant_id' => [
                    'type'       => 'BIGINT',
                    'constraint' => 20,
                    'unsigned'   => true,
                    'null'       => true,
                    'default'    => null
                ],
                'quantity' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'default'    => 1
                ],
                'price_override' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,2',
                    'null'       => true,
                    'default'    => null
                ],
                'sort_order' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 0
                ],
                'created_at' => [
                    'type'    => 'DATETIME',
                    'null'    => true
                ],
                'updated_at' => [
                    'type'    => 'DATETIME',
                    'null'    => true
                ]
            ]);

            $this->forge->addKey('id', true);
            $this->forge->addKey('bundle_product_id');
            $this->forge->addKey('component_product_id');
            $this->forge->addKey('variant_id');
            $this->forge->createTable('product_bundles', true);
        }

        // 3. Create order_bundle_items table
        if (!$this->db->tableExists('order_bundle_items')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'BIGINT',
                    'constraint'     => 20,
                    'unsigned'       => true,
                    'auto_increment' => true
                ],
                'order_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true
                ],
                'order_product_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true
                ],
                'component_product_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true
                ],
                'variant_id' => [
                    'type'       => 'BIGINT',
                    'constraint' => 20,
                    'unsigned'   => true,
                    'null'       => true,
                    'default'    => null
                ],
                'product_title' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 500
                ],
                'variant_description' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 500,
                    'null'       => true,
                    'default'    => null
                ],
                'quantity' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'default'    => 1
                ],
                'unit_price' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,2',
                    'default'    => 0.00
                ],
                'created_at' => [
                    'type'    => 'DATETIME',
                    'null'    => true
                ]
            ]);

            $this->forge->addKey('id', true);
            $this->forge->addKey('order_id');
            $this->forge->addKey('order_product_id');
            $this->forge->addKey('component_product_id');
            $this->forge->createTable('order_bundle_items', true);
        }
    }

    public function down()
    {
        $this->forge->dropTable('order_bundle_items', true);
        $this->forge->dropTable('product_bundles', true);
        if ($this->db->fieldExists('is_bundle', 'products')) {
            $this->forge->dropColumn('products', ['is_bundle', 'bundle_pricing_type', 'bundle_discount_rate']);
        }
    }
}
