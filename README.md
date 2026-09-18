# LumenMart POS Foundations

LumenMart POS Foundations is a four-page CodeIgniter 4 application created for the IT0049 technical formative assessment. It demonstrates explicit routing, controllers, views, navigation, and static PHP arrays before database integration.

## Features

- Landing page at `/`
- Project information at `/about`
- Customer account listing at `/customers`
- User and staff account listing at `/users`
- Five customer records and five user records stored in PHP arrays
- Responsive navigation and table styling

## Requirements

- PHP 8.2 or newer
- Composer 2
- PHP extensions `intl`, `mbstring`, and `zip`

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

6. Start the CodeIgniter development server:

   ```bash
   php spark serve
   ```

7. Open `http://localhost:8080` in a browser.

## Project structure

- `app/Config/Routes.php` defines the four page routes.
- `app/Controllers/Pages.php` serves the landing and about pages.
- `app/Controllers/Customers.php` prepares temporary customer data.
- `app/Controllers/Users.php` prepares temporary staff data.
- `app/Views` contains the page and shared layout views.
- `public/css/style.css` contains the site presentation styles.

## Current data source

This version intentionally uses static PHP arrays. No database is required for the current activity.
