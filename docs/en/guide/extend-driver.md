---
title: Custom Token Driver
lang: en-US
---

# Custom Token Driver

## Configuration

`tokenable.php`

```php
/*
|--------------------------------------------------------------------------
| Token Drivers
|--------------------------------------------------------------------------
|
| Here you may configure all the token drivers used by your application.
| Each driver may have its own set of options passed to its constructor.
|
*/

'drivers' => [
    'md5' => [
        'secret' => env('TOKEN_SECRET_KEY', env('APP_KEY')),
    ],
    // ...
],
```

## Create Custom Token Class

`Md5Token.php`

```php
<?php

namespace App;

use Illuminate\Config\Repository;
use Jundayw\Tokenable\Contracts\Auth\Authenticable;
use Jundayw\Tokenable\Contracts\Tokenable;
use Jundayw\Tokenable\Tokens\Token;

class Md5Token extends Token
{
    public function __construct(string $name, array $config)
    {
        $this->name   = $name;
        $this->config = new Repository($config);
    }

    /**
     * Generate a new access token.
     *
     * This token is typically short-lived and is used to authenticate API requests.
     *
     * @param Authenticable $authentication
     * @param Tokenable     $tokenable
     *
     * @return string
     */
    protected function generateAccessToken(Authenticable $authentication, Tokenable $tokenable): string
    {
        return md5($tokenable->getKey() . $this->ulid() . $this->getConfig()->get('secret'));
    }

    /**
     * Generate a new refresh token.
     *
     * Refresh tokens are long-lived and used to obtain new access tokens.
     *
     * @param Authenticable $authentication
     * @param Tokenable     $tokenable
     *
     * @return string
     */
    protected function generateRefreshToken(Authenticable $authentication, Tokenable $tokenable): string
    {
        return md5($tokenable->getKey() . $this->ulid() . $this->getConfig()->get('secret'));
    }

    /**
     * Generate a new auth code.
     *
     * The authentication code has a short expiration period; it is used to obtain a new token pair.
     *
     * @param Authenticable $authentication
     * @param Tokenable     $tokenable
     *
     * @return string
     */
    protected function generateAuthorizationCode(Authenticable $authentication, Tokenable $tokenable): string
    {
        return md5($tokenable->getKey() . $this->ulid() . $this->getConfig()->get('secret'));
    }

    /**
     * Validate the token using a validator.
     *
     * @param string $token
     *
     * @return bool
     */
    public function validate(string $token): bool
    {
        return true;
    }
}
```

## ServiceProvider

`AppServiceProvider.php`

```php
<?php

namespace App\Providers;

use App\Md5Token;
use Illuminate\Support\ServiceProvider;
use Jundayw\Tokenable\Contracts\Token\Factory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        app(Factory::class)->extend('md5', function (string $name, array $config) {
            return new Md5Token($name, $config);
        });
    }
}
```

## Use Custom Token Driver

```php
return $this->guard('web')
    ->login($user)
    ->withToken('md5')
    ->createToken(name: 'PC Token', platform: 'pc');
```

### Token Structure

```json
{
    "access_token": "ffe4ce9253e47c9f195cf3b49517aea6",
    "token_type": "md5",
    "expires_in": "2025-10-11T12:41:05Z",
    "refresh_token": "2356d366f039a7adfa7d52d1326a74f6",
    "type": "token"
}
```

### Authorization Code Structure

```json
{
    "auth_code": "f9b6f018f03d15ead017bc7f8d101895",
    "code_type": "md5",
    "type": "code"
}
```
