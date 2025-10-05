---
title: Database Storage Configuration
lang: en-US
---

# Database Storage Configuration

```php
/*
|--------------------------------------------------------------------------
| Database Storage Configuration
|--------------------------------------------------------------------------
|
| Here you may configure how tokens are persisted in your application's
| database. This section allows you to define which database connection
| should be used and the specific table where all issued tokens will be
| stored. By customizing these settings, you can isolate token storage
| from your primary application tables if desired.
|
*/

'database' => [

    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | This option specifies the database connection that should be used
    | to store and manage issued tokens. By default, it falls back to
    | the application's main database connection if no specific
    | TOKEN_CONNECTION is defined in the environment file.
    |
    */

    'connection' => env('TOKEN_CONNECTION', env('DB_CONNECTION', 'mysql')),

    /*
    |--------------------------------------------------------------------------
    | Token Storage Table
    |--------------------------------------------------------------------------
    |
    | This table is used to persist all generated tokens and their metadata,
    | including access tokens, refresh tokens, expiration times, and
    | revocation status. You can change this table name if you want
    | to store tokens in a custom table.
    |
    */

    'table' => env('TOKEN_TABLE', 'auth_tokens'),
],
```

## Publish Resources

```shell
php artisan vendor:publish --tag=tokenable-migrations
```

## Generating Migrations

```shell
php artisan migrate --path=database/migrations/2025_06_01_000000_create_auth_token_table.php
```
