# LumenMart POS Foundations

LumenMart POS Foundations is a four-page CodeIgniter 4 application created for the IT0049 technical formative assessment. It demonstrates explicit routing, controllers, views, navigation, and static PHP arrays before database integration.

## Features

- Landing page at `/`
- Project information at `/about`
- Customer account listing at `/customers`
- User and staff account listing at `/users`
- Customer and user records retrieved from a MySQL database
- Responsive navigation and table styling

## Requirements

- PHP 8.2 or newer
- Composer 2
- PHP extensions `intl`, `mbstring`, and `zip`
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

6. Start MySQL.
- Create a database named lumenmart_pos.
- Import database/lumenmart_pos.sql.
- Configure the database.default settings in .env.



7. Start the CodeIgniter development server:

   ```bash
   php spark serve
   ```

8. Open `http://localhost:8080` in a browser.

## Project structure

- `app/Config/Routes.php` defines the four page routes.
- `app/Controllers/Pages.php` serves the landing and about pages.
- `app/Controllers/Customers.php`retrieves customer records through CustomerModel.
- `app/Controllers/Users.php` retrieves staff records through UserModel.
- `app/Models/CustomerModel.php ` connects to the customers table.
- `app/Models/UserModel.php ` connects to the users table.
- `app/Views` contains the page and shared layout views.
- `public/css/style.css` contains the site presentation styles.

## Current data source

CodeIgniter Models and a MySQL database for persistent customer and staff records.
