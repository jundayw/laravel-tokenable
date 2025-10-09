---
title: 默认令牌驱动程序
lang: zh-CN
---

# 默认令牌驱动程序

```php
/*
|--------------------------------------------------------------------------
| 默认令牌驱动程序
|---------------------------------------------------------------------------
|
| 此选项控制 TokenManager 使用的默认令牌驱动程序。
| 您可以选择“hash”或“jwt”，或者注册您自己的驱动程序。
|
*/

'default' => [
    'driver' => env('TOKEN_DRIVER', 'jwt'),
],
```

此选项控制 `TokenManager` 使用的默认令牌驱动程序。

::: tip
您可以选择 `hash` 或 `jwt` [令牌驱动](token-driver.md)，也可以[注册您自己的驱动程序](../guide/extend-driver)。
:::

```php
use Illuminate\Support\Facades\Auth;

Auth::guard('api')->login($user)->withToken('hash')->createToken('Hash Token');
```
