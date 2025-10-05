---
title: 令牌暂停
lang: zh-CN
---

# 令牌暂停

```php
/*
|--------------------------------------------------------------------------
| 是否启用令牌暂停
|--------------------------------------------------------------------------
|
| 此选项决定是否启用令牌暂停功能。
| 设置为 false 时禁用令牌暂停。
|
*/

'suspend_enabled' => env('TOKEN_SUSPEND_ENABLED', true),
```

此选项确定是否启用令牌暂停功能。
设置为 `false` 时，禁用令牌暂停。

::: tip
使用场景：
根据风控或限流，当令牌触发风控时，可以暂停该用户的所有令牌，
或者暂停当前平台的令牌，系统会自动剔除令牌下线，
用户再次登录时，获取到 `auth_code` 跳转至 验证或风控解除页面，完成评估后才能获取普通令牌。
:::

## 令牌暂停

暂停当前令牌（即当前平台的令牌被冻结）

```php
$request->user()->suspendToken();
```

暂停全局令牌（即当前用户的所有平台的令牌都被冻结）

```php
$request->user()->suspendToken(true);
```

## 签发令牌

```php
use Illuminate\Support\Facades\Auth;

Auth::guard('api')->login($user)->createToken('Hash Token');
```

通常情况下，签发令牌会获取到 `access_token` 和 `refresh_token`，但在令牌暂停时，会获取到 `auth_code`，需要使用 `auth_code` 换取 `access_token` 和 `refresh_token`。

## 恢复令牌

```php
Auth::guard('api')->fromAuthCode()->createToken('Token');
```

当获取到 `auth_code` 时，可以调用 `fromAuthCode` 方法生成 `access_token` 和 `refresh_token` 。
