---
title: Suspend Token
lang: en-US
---

## Suspend Token

Platform-level suspension

```php
$request->user()->suspendToken();
```

Account-level suspension

```php
$request->user()->suspendToken(true);
```

After the token is suspension, the controller no longer returns `AccessToken` and `RefreshToken` when issuing tokens, but returns `AuthCode`

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
}
```

```json
{
    "auth_code": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiJkMDI2YTViNC1iMGQ2LTRmYmMtOTI0ZC0xZTg1ZjVlYmRhMTAiLCJpc3MiOiJBcHAuTW9kZWxzLlVzZXIiLCJzdWIiOjEsImlhdCI6MTc1OTY3NDk3Mn0.l3KezbE7EST8asqP9LPaovJO589WB_dPwZFakwYforU",
    "code_type": "Bearer",
    "type": "code"
}
```

## Create Token

### Route

```php
Route::get('/token', [App\Http\Controllers\AccountController::class, 'token']);
```

### Controller

```php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Jundayw\Tokenable\Contracts\Token\Token;
use Jundayw\Tokenable\Support\GuardHelper;

class AccountController
{
    use GuardHelper;
    
    public function token(Request $request): ?Token
    {
        return $this->guard('web')
            ->fromAuthCode()
            ->createToken(name: 'auth_code', platform: 'pc');
    }
}
```
