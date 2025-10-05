---
title: Token Cache Configuration
lang: en-US
---

# Token Cache Configuration

```php
/*
|--------------------------------------------------------------------------
| Token Cache Configuration
|--------------------------------------------------------------------------
|
| Configure the cache-backed mechanisms used to manage token state,
| such as blacklist and whitelist lookups.
| The cache is used as a fast lookup layer to reduce database hits.
| When both blacklist and whitelist are enabled, blacklist checks take precedence.
|
*/

'cache' => [

    /*
    |--------------------------------------------------------------------------
    | Token Cache Store
    |--------------------------------------------------------------------------
    |
    | The cache store to be used for managing token states (blacklist/whitelist).
    | You may specify any of the stores defined in your cache.php configuration
    | file, such as "redis", "memcached", "file", or "database".
    |
    */

    'driver' => env('TOKEN_CACHE_DRIVER', env('CACHE_DRIVER', 'file')),

    /*
    |--------------------------------------------------------------------------
    | Blacklist Feature
    |--------------------------------------------------------------------------
    |
    | Enable or disable the token blacklist feature.
    | When enabled, tokens added to the blacklist will be considered invalid
    | and denied access, regardless of their expiration status.
    |
    */

    'blacklist_enabled' => env('TOKEN_BLACKLIST_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Whitelist Feature
    |--------------------------------------------------------------------------
    |
    | Enable or disable the token whitelist feature.
    | When enabled, tokens added to the whitelist will always be considered
    | valid and granted access, provided they are not expired or revoked in the database.
    |
    */

    'whitelist_enabled' => env('TOKEN_WHITELIST_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Token Cache Key Prefix
    |--------------------------------------------------------------------------
    |
    | This value will be prepended to all token-related cache keys to prevent
    | naming collisions across different applications or environments.
    |
    */

    'prefix' => env('TOKEN_CACHE_PREFIX', 'tokenable:'),
],
```
