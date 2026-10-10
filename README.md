# LumenMart POS

LumenMart POS is a CodeIgniter 4 application for managing customer and staff accounts.

## Features

- Landing page at `/`
- Project information at `/about`
- Customer account listing at `/customers`
- User and staff account listing at `/users`
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
- `app/Controllers/Users.php` retrieves staff records through UserModel.
- `app/Models/CustomerModel.php` connects to the customers table.
- `app/Models/UserModel.php` connects to the users table.
- `app/Views` contains the page and shared layout views.
- `public/css/style.css` contains the site presentation styles.

## Current data source

CodeIgniter Models and a MySQL database for persistent customer and staff records.
