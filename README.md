# LumenMart POS

LumenMart POS is a CodeIgniter 4 application for managing a small store's products, customers, staff, and sales.

## Features

- Landing page at `/`
- Project information at `/about`
- Product management at `/products`, including stock, prices, and uploaded images
- Customer management at `/customers`
- Staff management at `/users`, including avatars and hashed passwords
- Sale recording at `/sales/new`, with stock checks and an atomic stock reduction
- Sales history with product, customer, staff, quantity, total, and date at `/sales`
- Login required for all management pages
- Records stored in MySQL or MariaDB
- Responsive navigation and table styling

## Requirements

- PHP 8.2 or newer
- Composer 2
- PHP extensions `intl`, `mbstring`, `zip`, and `gd`; enable `sqlite3` to run the automated database tests
- MySQL or MariaDB
- XAMPP or another compatible local server environment

## Local setup

1. Clone or download this repository.
2. Open a terminal in the project root.
3. Install the dependencies:

   ```bash
   composer install
   ```

4. Copy `env` to `.env`.
5. Configure the local environment in `.env`:

   ```ini
   CI_ENVIRONMENT = development
   app.baseURL = 'http://localhost:8080/'
   ```

6. Start MySQL, create the `lumenmart_pos` database, import `database/lumenmart_pos.sql`, and configure the `database.default` settings in `.env`.
7. Run the migrations to apply any missing schema changes and fill any missing password hashes:

   ```bash
   php spark migrate
   ```

   The supplied SQL import already owns the base tables. For safety, the compatibility migrations preserve those tables and columns on rollback; `migrate:rollback` and `migrate:refresh` do not remove or rebuild this imported schema. Use a database backup when you need to restore it.

8. The sample SQL accounts use `admin123` for `avery.admin` and `lumen123` for the other four users. Change these passwords before using the application beyond a local demo. To generate a new password for an existing account:

   ```bash
   php spark users:reset-password avery.admin
   ```

   The command displays the new password once. Save it securely. New accounts require a password, and existing accounts can change theirs on the edit page.

   To set a specific password from standard input, run `php spark users:set-password <username|all>` and enter the password when prompted.

9. Start the CodeIgniter development server:

   ```bash
   php spark serve
   ```

10. Open `http://localhost:8080/login` in a browser.

On XAMPP for Windows, use `C:\xampp\php\php.exe` in place of `php` if PHP is not on your PATH. Enable `gd` in `C:\xampp\php\php.ini` so uploaded images are resized for display.

### Optional sample products

Populate the product catalog with editable convenience-store demo inventory:

```bash
php spark db:seed ProductSeeder
```

The seeder skips existing product names, so rerunning it will not duplicate or overwrite those products. Product prices are sample retail values and should be reviewed before real use.

## Tests

The test database is an in-memory SQLite database; running PHPUnit does not change the local MySQL data. From the project root, run:

```powershell
C:\xampp\php\php.exe -d extension=sqlite3 -d extension=gd vendor\bin\phpunit
```

This command enables the SQLite3 and GD extensions for that run if they are installed but disabled in XAMPP's CLI configuration. See [tests/README.md](tests/README.md) for test coverage and other environments.

## Project structure

- `app/Config/Routes.php` defines public and protected routes.
- `app/Filters/AuthFilter.php` protects product, customer, staff, and sales routes.
- `app/Controllers/Auth.php` handles login and logout.
- `app/Controllers/Pages.php` serves the landing and about pages.
- `app/Controllers/Customers.php` manages customer records through CustomerModel.
- `app/Controllers/Products.php` manages product records and prepared product images.
- `app/Controllers/Sales.php` handles sale entry and sales history.
- `app/Services/SaleService.php` records sales and updates stock in one transaction.
- `app/Controllers/Users.php` manages staff accounts and avatars.
- `app/Models` contains the product, customer, staff, and sale models.
- `app/Database/Migrations` and `app/Database/Seeds` contain schema changes and sample products.
- `app/Views` contains the page and shared layout views.
- `public/css/style.css` contains the site presentation styles.

## Database design

The `database/lumenmart_pos.sql` import and CodeIgniter migrations provide four related tables:

- `products` stores the current price, available stock, and prepared image filename.
- `customers` stores optional customer details for a sale.
- `users` stores staff accounts. The existing `role` field is retained by the current interface in addition to the required hashed password and avatar fields.
- `sales` references one product and one selling staff member. Its customer reference is optional.

Products, customers, and staff members referenced by sales cannot be deleted through the application, so names remain available in sales history. Other records can be deleted from their management pages.
