---
title: Default Token Driver
lang: en-US
---

# Default Token Driver

```php
/*
|--------------------------------------------------------------------------
| Default Token Driver
|--------------------------------------------------------------------------
|
| This option controls the default token driver used by the TokenManager.
| You may choose between "hash" or "jwt" or register your own driver.
|
*/

'default' => [
    'driver' => env('TOKEN_DRIVER', 'jwt'),
],
```

This option controls the default token driver used by the `TokenManager`.

::: tip
You may choose between "hash" or "jwt" or register your own driver.
:::

```php
use Illuminate\Support\Facades\Auth;

Auth::guard('api')->login($user)->withToken('hash')->createToken('Hash Token');
```
