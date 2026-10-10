# LumenMart POS tests

The project uses PHPUnit 10 and CodeIgniter's test tools. Run the suite from the project root after `composer install`.

## Run the suite

On this XAMPP for Windows installation, SQLite3 and GD are installed but disabled in CLI PHP. Enable them for one test run:

```powershell
C:\xampp\php\php.exe -d extension=sqlite3 -d extension=gd vendor\bin\phpunit
```

If those extensions are already enabled and `php` is on your PATH, run `php vendor/bin/phpunit` instead.

The `tests` connection in `app/Config/Database.php` uses in-memory SQLite. The suite does not modify the local MySQL or MariaDB database. `phpunit.dist.xml` is the default PHPUnit configuration.

## What is covered

- Guest access to management forms and sales pages, signed-in forms, and sessions for deleted staff
- Sale recording, stock reduction, invalid quantities, insufficient-stock rejection, and an invalid customer
- Sales history joins for product, customer, and staff names
- A stale product edit cannot restore stock after a sale, and customers in sales history cannot be deleted
- Migration rollback preserves imported data; the password upgrade fills missing hashes and requires the column
- CodeIgniter starter example tests

The suite does not currently test every CRUD or image-upload path, simultaneous MySQL transactions, or a fresh MySQL import. Check those separately before deployment. PHPUnit writes test reports under `build/logs` by default.
