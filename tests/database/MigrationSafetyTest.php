<?php

use App\Database\Migrations\AddAvatarToUsers;
use App\Database\Migrations\AddPasswordsToUsers;
use App\Database\Migrations\CreateProductsAndSalesTables;
use App\Database\Migrations\RequireUserPasswords;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Test\CIUnitTestCase;
use Config\Database;

require_once APPPATH . 'Database/Migrations/2026-10-03-000001_AddAvatarToUsers.php';
require_once APPPATH . 'Database/Migrations/2026-10-03-000002_AddPasswordsToUsers.php';
require_once APPPATH . 'Database/Migrations/2026-10-10-000001_CreateProductsAndSalesTables.php';
require_once APPPATH . 'Database/Migrations/2026-10-10-000002_RequireUserPasswords.php';

/** @internal */
final class MigrationSafetyTest extends CIUnitTestCase
{
    private BaseConnection $connection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection = Database::connect('tests');
        $this->dropTables();

        $prefix = $this->connection->getPrefix();
        $this->connection->query("CREATE TABLE {$prefix}users (id INTEGER PRIMARY KEY, avatar VARCHAR(255), password VARCHAR(255) NULL)");
        $this->connection->query("CREATE TABLE {$prefix}products (id INTEGER PRIMARY KEY, name VARCHAR(100))");
        $this->connection->query("CREATE TABLE {$prefix}sales (id INTEGER PRIMARY KEY, product_id INTEGER)");
        $this->connection->resetDataCache();
    }

    protected function tearDown(): void
    {
        $this->dropTables();
        parent::tearDown();
    }

    public function testRollbackKeepsSchemaAndDataImportedBeforeMigrations(): void
    {
        $this->connection->table('users')->insert(['id' => 1, 'avatar' => 'portrait.png', 'password' => 'existing-hash']);
        $this->connection->table('products')->insert(['id' => 1, 'name' => 'Coffee']);
        $this->connection->table('sales')->insert(['id' => 1, 'product_id' => 1]);

        $forge = Database::forge('tests');
        (new AddAvatarToUsers($forge))->up();
        (new AddPasswordsToUsers($forge))->up();
        (new CreateProductsAndSalesTables($forge))->up();

        (new CreateProductsAndSalesTables($forge))->down();
        (new AddPasswordsToUsers($forge))->down();
        (new AddAvatarToUsers($forge))->down();

        $this->assertTrue($this->connection->tableExists('products'));
        $this->assertTrue($this->connection->tableExists('sales'));
        $this->assertTrue($this->connection->fieldExists('avatar', 'users'));
        $this->assertTrue($this->connection->fieldExists('password', 'users'));
        $this->assertSame('portrait.png', $this->connection->table('users')->get()->getRow('avatar'));
        $this->assertSame(1, $this->connection->table('sales')->countAllResults());
    }

    public function testUpgradeBackfillsMissingPasswordsAndMakesColumnRequired(): void
    {
        $knownHash = password_hash('existing password', PASSWORD_DEFAULT);
        $this->connection->table('users')->insertBatch([
            ['id' => 1, 'avatar' => null, 'password' => null],
            ['id' => 2, 'avatar' => null, 'password' => ''],
            ['id' => 3, 'avatar' => null, 'password' => $knownHash],
        ]);

        $migration = new RequireUserPasswords(Database::forge('tests'));
        $migration->up();

        $users = $this->connection->table('users')->orderBy('id')->get()->getResultArray();
        $this->assertNotEmpty(password_get_info($users[0]['password'])['algo']);
        $this->assertNotEmpty(password_get_info($users[1]['password'])['algo']);
        $this->assertSame($knownHash, $users[2]['password']);

        $prefix = $this->connection->getPrefix();
        $columns = $this->connection->query("PRAGMA table_info({$prefix}users)")->getResultArray();
        $passwordColumn = array_values(array_filter($columns, static fn (array $column): bool => $column['name'] === 'password'))[0];
        $this->assertSame(1, (int) $passwordColumn['notnull']);

        $migration->down();
        $columns = $this->connection->query("PRAGMA table_info({$prefix}users)")->getResultArray();
        $passwordColumn = array_values(array_filter($columns, static fn (array $column): bool => $column['name'] === 'password'))[0];
        $this->assertSame(1, (int) $passwordColumn['notnull']);
    }

    private function dropTables(): void
    {
        $prefix = $this->connection->getPrefix();
        foreach (['sales', 'products', 'users'] as $table) {
            $this->connection->query('DROP TABLE IF EXISTS ' . $prefix . $table);
        }
        $this->connection->resetDataCache();
    }
}
