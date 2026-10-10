<?php

use App\Services\SaleService;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Database;

/** @internal */
final class InventoryHistoryGuardTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    private BaseConnection $connection;
    private int $staffId;
    private int $productId;
    private int $customerId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection = Database::connect('tests');
        $this->resetTables();

        $prefix = $this->connection->getPrefix();
        $this->connection->query("CREATE TABLE {$prefix}users (id INTEGER PRIMARY KEY AUTOINCREMENT, username VARCHAR(50) NOT NULL, full_name VARCHAR(100) NOT NULL, role VARCHAR(50) NOT NULL, password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL)");
        $this->connection->query("CREATE TABLE {$prefix}customers (id INTEGER PRIMARY KEY AUTOINCREMENT, full_name VARCHAR(100) NOT NULL, email VARCHAR(100) NOT NULL, phone VARCHAR(20), created_at DATETIME NOT NULL)");
        $this->connection->query("CREATE TABLE {$prefix}products (id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR(100) NOT NULL, price DECIMAL(10,2) NOT NULL, stock_quantity INTEGER NOT NULL, image VARCHAR(255), created_at DATETIME NOT NULL)");
        $this->connection->query("CREATE TABLE {$prefix}sales (id INTEGER PRIMARY KEY AUTOINCREMENT, product_id INTEGER NOT NULL REFERENCES {$prefix}products(id), customer_id INTEGER REFERENCES {$prefix}customers(id) ON DELETE SET NULL, sold_by INTEGER NOT NULL REFERENCES {$prefix}users(id), quantity INTEGER NOT NULL, total_price DECIMAL(10,2) NOT NULL, created_at DATETIME NOT NULL)");
        $this->connection->resetDataCache();

        $this->connection->table('users')->insert([
            'username' => 'feature.staff',
            'full_name' => 'Feature Test Staff',
            'role' => 'Cashier',
            'password' => password_hash('test-password', PASSWORD_DEFAULT),
            'created_at' => '2026-10-10 10:00:00',
        ]);
        $this->staffId = (int) $this->connection->insertID();

        $this->connection->table('products')->insert([
            'name' => 'Test Product',
            'price' => '12.50',
            'stock_quantity' => 5,
            'created_at' => '2026-10-10 10:00:00',
        ]);
        $this->productId = (int) $this->connection->insertID();

        $this->connection->table('customers')->insert([
            'full_name' => 'Named Customer',
            'email' => 'named@example.com',
            'created_at' => '2026-10-10 10:00:00',
        ]);
        $this->customerId = (int) $this->connection->insertID();
    }

    protected function tearDown(): void
    {
        $this->resetTables();
        parent::tearDown();
    }

    public function testStaleProductEditCannotRestoreSoldStock(): void
    {
        $editPage = $this->withSession($this->staffSession())->get('/products/' . $this->productId . '/edit');
        $editPage->assertOK();
        $this->assertStringContainsString('name="original_stock_quantity" value="5"', $editPage->getBody());

        $sale = (new SaleService($this->connection))->record($this->productId, null, $this->staffId, 2);
        $this->assertTrue($sale['success']);

        $this->withSession($this->staffSession())
            ->post('/products/' . $this->productId, [
                csrf_token() => csrf_hash(),
                'original_stock_quantity' => '5',
                'name' => 'Stale Product Name',
                'price' => '12.50',
                'stock_quantity' => '5',
            ])
            ->assertRedirectTo('/products/' . $this->productId . '/edit');

        $product = $this->connection->table('products')->where('id', $this->productId)->get()->getRowArray();
        $this->assertSame(3, (int) $product['stock_quantity']);
        $this->assertSame('Test Product', $product['name']);
        $this->assertSame(1, $this->connection->table('sales')->countAllResults());
        $this->assertStringContainsString('Stock changed', (string) session()->getFlashdata('error'));
    }

    public function testCustomerWithSaleCannotBeDeleted(): void
    {
        $sale = (new SaleService($this->connection))->record($this->productId, $this->customerId, $this->staffId, 1);
        $this->assertTrue($sale['success']);

        $this->withSession($this->staffSession())
            ->post('/customers/' . $this->customerId . '/delete', [csrf_token() => csrf_hash()])
            ->assertRedirectTo('/customers');

        $this->assertNotNull($this->connection->table('customers')->where('id', $this->customerId)->get()->getRowArray());
        $saleRow = $this->connection->table('sales')->get()->getRowArray();
        $this->assertSame($this->customerId, (int) $saleRow['customer_id']);
        $this->assertStringContainsString('sales history', (string) session()->getFlashdata('error'));
        $this->withSession($this->staffSession())->get('/sales')->assertSee('Named Customer');
    }

    private function staffSession(): array
    {
        return ['user_id' => $this->staffId, 'username' => 'feature.staff'];
    }

    private function resetTables(): void
    {
        $prefix = $this->connection->getPrefix();
        foreach (['sales', 'products', 'customers', 'users'] as $table) {
            $this->connection->query('DROP TABLE IF EXISTS ' . $prefix . $table);
        }
        $this->connection->resetDataCache();
    }
}
