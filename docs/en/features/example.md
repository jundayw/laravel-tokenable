---
title: APIs
lang: en-US
---

# APIs

## Configuration

Use the `tokenable` guard in the `guards` configuration of your application's `auth.php` configuration file:

```php
'guards' => [
    'api' => [
        'driver' => 'tokenable',
        'provider' => 'users',
    ],
],
```

## Model

To start issuing tokens for users, your User model should use the `Jundayw\Tokenable\HasTokenable` trait and implement the `Jundayw\Tokenable\Contracts\Tokenable` interface.

```php
namespace App\Models;

use Jundayw\Tokenable\Contracts\Tokenable;
use Jundayw\Tokenable\HasTokenable;

class User extends Authenticatable implements Tokenable
{
    use HasTokenable, HasFactory, Notifiable;
}
```

## Route

```php
Route::get('/login', [App\Http\Controllers\AccountController::class, 'login']);
Route::get('/refresh', [App\Http\Controllers\AccountController::class, 'refresh']);
Route::middleware(['auth:web'])->group(function () {
    Route::get('/freeze', [App\Http\Controllers\AccountController::class, 'freeze']);
    Route::get('/revoke', [App\Http\Controllers\AccountController::class, 'revoke']);
});
Route::get('/token', [App\Http\Controllers\AccountController::class, 'token']);
```

## Controller

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
            ->createToken(name: 'PC Token', platform: 'pc');
    }

    public function refresh(Request $request): ?Token
    {
        return $this->guard('web')->refreshToken();
    }

    public function freeze(Request $request): bool
    {
        return $request->user()->suspendToken();
    }

    public function revoke(Request $request): bool
    {
        return $this->guard()->revokeToken();
    }
    
    public function token(Request $request): ?Token
    {
        return $this->guard('web')
            ->fromAuthCode()
            ->createToken(name: 'auth_code', platform: 'pc');
    }
}
```
