---
title: Token Suspension Enabled
lang: en-US
---

# Token Suspension Enabled

```php
/*
|--------------------------------------------------------------------------
| Token Suspension Enabled
|--------------------------------------------------------------------------
|
| This option determines whether the token suspension feature is enabled.
| When set to false disable token suspension.
|
*/

'suspend_enabled' => env('TOKEN_SUSPEND_ENABLED', true),
```

::: tip
**Usage Scenario**

When risk control or rate limiting is triggered, the system can temporarily suspend token access in one of two ways:

**Platform-level suspension:** Only the tokens associated with the current platform are suspended.

**Account-level suspension:** All tokens belonging to the user are suspended.

Once a suspension is applied, the system will automatically revoke and invalidate affected tokens, forcing them offline.

When the user attempts to log in again, they will receive an auth_code instead of a normal access token.
The user must be redirected to a **verification or risk assessment page**,
where they must complete the required evaluation before obtaining new valid access and refresh tokens.
:::

## Suspend Token

Platform-level suspension

```php
$request->user()->suspendToken();
```

Account-level suspension

```php
$request->user()->suspendToken(true);
```

## Create Token

```php
use Illuminate\Support\Facades\Auth;

Auth::guard('api')->login($user)->createToken('Hash Token');
```

## Auth Code

```php
Auth::guard('api')->fromAuthCode()->createToken('Token');
```
