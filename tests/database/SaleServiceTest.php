<?php

use App\Models\SaleModel;
use App\Services\SaleService;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Test\CIUnitTestCase;
use Config\Database;

/** @internal */
final class SaleServiceTest extends CIUnitTestCase
{
    private BaseConnection $connection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection = Database::connect('tests');
        $prefix = $this->connection->getPrefix();

        foreach (['sales', 'products', 'customers', 'users'] as $table) {
            $this->connection->query('DROP TABLE IF EXISTS ' . $prefix . $table);
        }

        $this->connection->query("CREATE TABLE {$prefix}products (id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR(100) NOT NULL, price DECIMAL(10,2) NOT NULL, stock_quantity INTEGER NOT NULL, image VARCHAR(255), created_at DATETIME NOT NULL)");
        $this->connection->query("CREATE TABLE {$prefix}customers (id INTEGER PRIMARY KEY AUTOINCREMENT, full_name VARCHAR(100) NOT NULL, email VARCHAR(100) NOT NULL, phone VARCHAR(20), created_at DATETIME NOT NULL)");
        $this->connection->query("CREATE TABLE {$prefix}users (id INTEGER PRIMARY KEY AUTOINCREMENT, username VARCHAR(50) NOT NULL, full_name VARCHAR(100) NOT NULL, password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL)");
        $this->connection->query("CREATE TABLE {$prefix}sales (id INTEGER PRIMARY KEY AUTOINCREMENT, product_id INTEGER NOT NULL, customer_id INTEGER, sold_by INTEGER NOT NULL, quantity INTEGER NOT NULL, total_price DECIMAL(10,2) NOT NULL, created_at DATETIME NOT NULL)");
    }

    public function testRecordsSaleAndReducesStock(): void
    {
        $staffId = $this->insertStaff();
        $productId = $this->insertProduct(10, '12.50');

        $result = (new SaleService($this->connection))->record($productId, null, $staffId, 3);

        $this->assertTrue($result['success']);
        $product = $this->connection->table('products')->where('id', $productId)->get()->getRowArray();
        $this->assertSame(7, (int) $product['stock_quantity']);
        $sale = $this->connection->table('sales')->get()->getRowArray();
        $this->assertSame(3, (int) $sale['quantity']);
        $this->assertSame(37.5, (float) $sale['total_price']);
        $this->assertNull($sale['customer_id']);
    }

    public function testRejectsSaleWhenStockIsInsufficient(): void
    {
        $staffId = $this->insertStaff();
        $productId = $this->insertProduct(2, '15.00');

        $result = (new SaleService($this->connection))->record($productId, null, $staffId, 3);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Not enough stock', $result['message']);
        $product = $this->connection->table('products')->where('id', $productId)->get()->getRowArray();
        $this->assertSame(2, (int) $product['stock_quantity']);
        $this->assertSame(0, $this->connection->table('sales')->countAllResults());
    }

    public function testRejectsNonPositiveQuantityWithoutChangingStock(): void
    {
        $staffId = $this->insertStaff();
        $productId = $this->insertProduct(5, '15.00');

        $result = (new SaleService($this->connection))->record($productId, null, $staffId, -2);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Quantity must be at least one', $result['message']);
        $product = $this->connection->table('products')->where('id', $productId)->get()->getRowArray();
        $this->assertSame(5, (int) $product['stock_quantity']);
        $this->assertSame(0, $this->connection->table('sales')->countAllResults());
    }

    public function testInvalidCustomerDoesNotChangeStock(): void
    {
        $staffId = $this->insertStaff();
        $productId = $this->insertProduct(5, '20.00');

        $result = (new SaleService($this->connection))->record($productId, 999, $staffId, 1);

        $this->assertFalse($result['success']);
        $product = $this->connection->table('products')->where('id', $productId)->get()->getRowArray();
        $this->assertSame(5, (int) $product['stock_quantity']);
        $this->assertSame(0, $this->connection->table('sales')->countAllResults());
    }

    public function testSalesHistoryIncludesRelatedNames(): void
    {
        $staffId = $this->insertStaff();
        $productId = $this->insertProduct(4, '25.00');
        $customerId = $this->insertCustomer();
        (new SaleService($this->connection))->record($productId, $customerId, $staffId, 2);

        $history = (new SaleModel($this->connection))->history();

        $this->assertCount(1, $history);
        $this->assertSame('Test Product', $history[0]['product_name']);
        $this->assertSame('Test Customer', $history[0]['customer_name']);
        $this->assertSame('Test Cashier', $history[0]['staff_name']);
        $this->assertSame(2, (int) $history[0]['quantity']);
        $this->assertSame(50.0, (float) $history[0]['total_price']);
    }

    private function insertStaff(): int
    {
        $this->connection->table('users')->insert([
            'username' => 'cashier',
            'full_name' => 'Test Cashier',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'created_at' => '2026-10-10 10:00:00',
        ]);

        return $this->connection->insertID();
    }

    private function insertProduct(int $stock, string $price): int
    {
        $this->connection->table('products')->insert([
            'name' => 'Test Product',
            'price' => $price,
            'stock_quantity' => $stock,
            'created_at' => '2026-10-10 10:00:00',
        ]);

        return $this->connection->insertID();
    }

    private function insertCustomer(): int
    {
        $this->connection->table('customers')->insert([
            'full_name' => 'Test Customer',
            'email' => 'customer@example.com',
            'created_at' => '2026-10-10 10:00:00',
        ]);

        return $this->connection->insertID();
    }
}
