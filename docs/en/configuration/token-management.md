---
title: Multi-Token Support
lang: en-US
---

# Multi-Token Support

```php
/*
|--------------------------------------------------------------------------
| Multi-Token Support
|--------------------------------------------------------------------------
|
| When enabled, a user can hold multiple active tokens simultaneously,
| for example, one per platform (web, app, API, etc.).
|
*/

'multi_token' => [

    /*
    |--------------------------------------------------------------------------
    | Multi-Tokens Enabled
    |--------------------------------------------------------------------------
    |
    | This option determines whether multiple active tokens can be issued
    | for a single user. When enabled, a user may hold distinct tokens
    | across different platforms (such as Web, App, or API clients),
    | allowing simultaneous sessions under the same account.
    |
    */

    'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Allow Multiple Platforms
    |--------------------------------------------------------------------------
    |
    | When true, tokens for different platforms (e.g., 'pc', 'mobile', 'app')
    | can remain valid at the same time.
    | When false, issuing a new token will invalidate all existing tokens
    | regardless of platform.
    |
    |
    */

    'allow_multi_platforms' => true,

    /*
    |--------------------------------------------------------------------------
    | Multi Platform Tokens
    |--------------------------------------------------------------------------
    |
    | A list of platforms that are allowed to have multiple active tokens
    | simultaneously for the same user.
    | Example: ['pc'] allows multiple tokens for PC platforms at the same time.
    |
    */

    'multi_platform_tokens' => [],
],
```

::: tip
`allow_multi_platforms`
When true, tokens for different platforms (e.g., 'pc', 'mobile', 'app') can remain valid at the same time.
When false, issuing a new token will invalidate all existing tokens regardless of platform.
:::

::: tip
`multi_platform_tokens`
A list of platforms that are allowed to have multiple active tokens simultaneously for the same user.
Example: ['pc'] allows multiple tokens for PC platforms at the same time.
:::

## Priority

The `guards` configuration in `config/auth.php` takes precedence over the `multi_token` configuration in `config/tokenable.php`,
so the configuration in `config/auth.php` can be overwritten in `config/tokenable.php`.

```php
'guards' => [
    'api' => [
        'driver' => 'tokenable',
        'provider' => 'users',
        'allow_multi_platforms' => false,
    ],
    'web' => [
        'driver' => 'tokenable',
        'provider' => 'users',
        'allow_multi_platforms' => true,
        'multi_platform_tokens' => ['pc', 'h5'],
    ],
],
```

The above configuration will allow the same user to issue a token on both `PC` and `H5` platforms,
but only the latest token will be valid on the `API` platform.

```php
use Illuminate\Support\Facades\Auth;
// api
Auth::guard('api')->login($user)->createToken('API Token');
// web
Auth::guard('web')->login($user)->createToken('PC Token', 'pc');
Auth::guard('web')->login($user)->createToken('H5 Token', 'h5');
```
