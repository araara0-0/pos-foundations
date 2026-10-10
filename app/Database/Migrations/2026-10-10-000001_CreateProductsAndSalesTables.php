<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductsAndSalesTables extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('products')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'name' => ['type' => 'VARCHAR', 'constraint' => 100],
                'price' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
                'stock_quantity' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'image' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'created_at' => ['type' => 'DATETIME'],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('products', true, ['ENGINE' => 'InnoDB']);
        }

        if (! $this->db->tableExists('sales')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'product_id' => ['type' => 'INT', 'constraint' => 11],
                'customer_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
                'sold_by' => ['type' => 'INT', 'constraint' => 11],
                'quantity' => ['type' => 'INT', 'constraint' => 11],
                'total_price' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
                'created_at' => ['type' => 'DATETIME'],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('product_id');
            $this->forge->addKey('customer_id');
            $this->forge->addKey('sold_by');
            $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'RESTRICT');
            $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'SET NULL');
            $this->forge->addForeignKey('sold_by', 'users', 'id', 'CASCADE', 'RESTRICT');
            $this->forge->createTable('sales', true, ['ENGINE' => 'InnoDB']);
        }
    }

    public function down()
    {
        // The SQL import may have supplied both tables before this migration ran.
        // Keep their inventory and transaction history on rollback.
    }
}
