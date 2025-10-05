---
title: Cookie
lang: zh-CN
---

# Cookie

## 配置

在你的应用程序 `auth.php` 配置文件的 `guards` 配置中使用 `tokenable` 看守器：

```php
'guards' => [
    'api' => [
        'driver' => 'tokenable',
        'provider' => 'users',
    ],
],
```

## 模型

要开始为用户颁发令牌，你的 User 模型应使用 `Jundayw\Tokenable\HasTokenable` trait 并实现 `Jundayw\Tokenable\Contracts\Tokenable` 接口。

```php
namespace App\Models;

use Jundayw\Tokenable\Contracts\Tokenable;
use Jundayw\Tokenable\HasTokenable;

class User extends Authenticatable implements Tokenable
{
    use HasTokenable, HasFactory, Notifiable;
}
```

## 路由

```php
use Jundayw\Tokenable\Middleware\RefreshTokenWithCookie;

Route::get('/login', [App\Http\Controllers\AccountController::class, 'login']);
Route::middleware(RefreshTokenWithCookie::using('web'))->group(function () {
    Route::get('/freeze', [App\Http\Controllers\AccountController::class, 'freeze']);
    Route::get('/revoke', [App\Http\Controllers\AccountController::class, 'revoke']);
});
Route::get('/token', [App\Http\Controllers\AccountController::class, 'token']);
```

::: tip
中间件 `RefreshTokenWithCookie::using('web')` 有自动刷新令牌并设置 `cookie` 的功能。
:::

## 控制器

```php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Jundayw\Tokenable\Contracts\Token\Token;
use Jundayw\Tokenable\Support\GuardHelper;

class AccountController
{
    use GuardHelper;
    
    public function login(Request $request): ?Token
    {
        $user = User::query()->where([
            'email'    => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ])->first();
        
        if(is_null($user)){
            return null;
        }

        return $this->guard('web')
            ->login($user)
            ->createToken(name: 'PC Token', platform: 'pc')
            ->withCookies();
    }

    public function freeze(Request $request): bool
    {
        return $request->user()?->suspendToken();
    }

    public function revoke(Request $request): bool
    {
        return $this->guard()->revokeToken();
    }
    
    public function token(Request $request): ?Token
    {
        return $this->guard('web')
            ->fromAuthCode()
            ?->createToken(name: 'auth_code', platform: 'pc')
            ->withCookies();
    }
}
```
