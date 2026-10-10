# LumenMart POS Foundations

LumenMart POS Foundations is a CodeIgniter 4 application for managing customer and staff accounts.

## Features

- Landing page at `/`
- Project information at `/about`
- Full customer management at `/customers`
- Full staff account management at `/users`, including prepared avatars and hashed passwords
- Product listing and management at `/products`
- Transactional sale recording with stock checks at `/sales/new`
- Customer and user records retrieved from a MySQL database
- Responsive navigation and table styling
- Login required for customer and user account management

## Requirements

- PHP 8.2 or newer
- Composer 2
- PHP extensions `intl`, `mbstring`, `zip`, and `gd`
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
7. Run the migrations to add missing account fields and fill any missing password hashes:

   ```bash
   php spark migrate
   ```

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

## Project structure

- `app/Config/Routes.php` defines public and protected routes.
- `app/Filters/AuthFilter.php` protects customer and user routes.
- `app/Controllers/Auth.php` handles login and logout.
- `app/Controllers/Pages.php` serves the landing and about pages.
- `app/Controllers/Customers.php` manages customer records through CustomerModel.
- `app/Controllers/Products.php` manages product records and prepared product images.
- `app/Controllers/Sales.php` validates and records sales through an atomic stock update.
- `app/Controllers/Users.php` retrieves staff records through UserModel.
- `app/Models/CustomerModel.php` connects to the customers table.
- `app/Models/UserModel.php` connects to the users table.
- `app/Views` contains the page and shared layout views.
- `public/css/style.css` contains the site presentation styles.

## Current data source

CodeIgniter Models and a MySQL database for persistent product, customer, staff, and sales records.

## Database design

The `database/lumenmart_pos.sql` import and CodeIgniter migrations provide four related tables:

- `products` stores the current price, available stock, and prepared image filename.
- `customers` stores optional customer details for a sale.
- `users` stores staff accounts. The existing `role` field is retained by the current interface in addition to the required hashed password and avatar fields.
- `sales` references one product and one selling staff member. Its customer reference is optional.

Deleting a customer keeps historical sales by setting `sales.customer_id` to `NULL`. Products and staff members referenced by sales are restricted from deletion so that transaction history remains complete.
