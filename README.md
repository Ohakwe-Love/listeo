# Listeo

A plain PHP directory/listing website starter.

## Setup

1. Create the database by importing `database.sql` in MySQL/phpMyAdmin.
2. Update the database connection with environment variables if your local credentials are not the defaults:
   - `LISTEO_DB_HOST`
   - `LISTEO_DB_PORT`
   - `LISTEO_DB_NAME`
   - `LISTEO_DB_USER`
   - `LISTEO_DB_PASS`
3. Serve the project from a PHP server, then visit `signup.php` to create a user.
4. Visit `services.php`, `listings.php`, and `listing.php?id=1` to see the database-backed listing flow.

The default database settings are `127.0.0.1`, port `3306`, database `listeo`, user `root`, and an empty password.

`database.sql` creates and seeds `users`, `categories`, and `listings`. If the database is not available yet, the listing pages fall back to sample data so the UI still renders.
