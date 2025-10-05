---
title: Token Drivers
lang: en-US
---

# Token Drivers

The extension package has two built-in token drivers: `HashToken` and `JsonWebToken`, which can be registered as needed.

## HashToken

```php
'hash' => [

    /*
    |--------------------------------------------------------------------------
    | Hashing / Signing Algorithm
    |--------------------------------------------------------------------------
    |
    | This value determines the algorithm used to sign or hash tokens.
    | You may adjust this to suit the security requirements of your application.
    |
    | Supported: @link https://php.net/manual/en/function.hash-hmac.php
    |
    */

    'algo' => env('TOKEN_HASH_ALGO', 'sha256'),

    /*
    |--------------------------------------------------------------------------
    | Token Secret Key
    |--------------------------------------------------------------------------
    |
    | This key is used to sign or verify tokens issued by the application.
    | By default, it falls back to the application APP_KEY. You should
    | ensure that this key remains secret and secure at all times.
    |
    */

    'secret_key' => env('TOKEN_SECRET_KEY', env('APP_KEY')),
],
```

### Generate hash key

```shell
php artisan tokenable:secret
```

## JsonWebToken

```php
'jwt' => [

    /*
    |--------------------------------------------------------------------------
    | Cryptographic algorithms for signing and verification
    |--------------------------------------------------------------------------
    |
    | This option determines the cryptographic algorithm used for signing
    | and verifying JWT tokens. You may choose from asymmetric algorithms
    | (RS256, RS384, RS512, ES256, ES384, ES512, EdDSA) or symmetric ones
    | (HS256, HS384, HS512).
    |
    | - RS*, ES*, EdDSA: Require both private_key (signing) and public_key
    |   (verification). Recommended for distributed systems.
    |
    | - HS*: Require only secret_key. Simpler but less flexible, recommended
    |   for single-service deployments.
    |
    | Default is HS256 for compatibility, but you should consider stronger
    | algorithms like RS384 or ES384 for better security.
    |
    */

    'algo' => env('TOKEN_ALGO', 'HS256'),

    /*
    |--------------------------------------------------------------------------
    | Private Key
    |--------------------------------------------------------------------------
    |
    | The private key is used to sign tokens when working with asymmetric
    | algorithms such as RS256, RS512, or ES512. This key must be kept
    | strictly confidential and should never be exposed publicly.
    |
    | You may specify the path to a PEM file, or load the key directly
    | from an environment variable or a secure key manager.
    |
    */

    'private_key' => env('TOKEN_PRIVATE_KEY', 'tokenable-private.key'),

    /*
    |--------------------------------------------------------------------------
    | Public Key
    |--------------------------------------------------------------------------
    |
    | The public key is used to verify tokens that were signed with the
    | corresponding private key when using asymmetric algorithms such
    | as RS256, RS512, or ES512. This key may be safely shared with
    | other services or clients that need to validate tokens.
    |
    | You may specify the path to a PEM file, or load the key directly
    | from an environment variable or a secure key manager.
    |
    */

    'public_key' => env('TOKEN_PUBLIC_KEY', 'tokenable-public.key'),


    /*
    |--------------------------------------------------------------------------
    | Secret Key
    |--------------------------------------------------------------------------
    |
    | This key is used for signing tokens when using symmetric algorithms
    | such as HMAC (e.g., HS256, HS512). It must be provided as plain text
    | and can be set via environment variables for convenience.
    |
    */

    'secret_key' => env('TOKEN_SECRET_KEY', env('APP_KEY')),
],
```

### Generate Keys

```shell
php artisan tokenable:keys
```

Algorithm to generate keys (RS256, RS384, RS512, ES256, ES384, ES512, EdDSA) [default: "RS256"]:

```shell
php artisan tokenable:keys ES384
```

Generate a specified key algorithm, for example: ES384

### Configure the key storage path

```php
'key_path' => env('TOKEN_KEY_PATH', storage_path('keys')),
```

### Specify the key storage path

Manually specify the key storage path in the service provider:

```php
use Jundayw\Tokenable\Tokenable;

Tokenable::loadKeysFrom(storage_path('keys'));
```
