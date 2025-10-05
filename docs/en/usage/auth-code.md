---
title: Authorization Code
lang: en-US
---

# Authorization Code

## Route

```php
Route::middleware(['auth:web'])->group(function () {
    Route::get('/auth', [App\Http\Controllers\AuthCodeController::class, 'auth']);
    Route::get('/token', [App\Http\Controllers\AuthCodeController::class, 'token']);
});
```

## Controller

```php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Jundayw\Tokenable\Contracts\Token\Token;
use Jundayw\Tokenable\Support\GuardHelper;

class AuthCodeController
{
    use GuardHelper;

    public function auth(Request $request): ?Token
    {
        return $this->guard()->onceUsingId($request->user()->getKey())->createAuthCode();
    }

    public function token(Request $request): ?Token
    {
        return $this->guard()
            ->fromAuthCode()
            ->createToken(name: 'auth_code', platform: 'pc');
    }
}
```

## Response structure

```json
{
    "auth_code": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiJkMDI2YTViNC1iMGQ2LTRmYmMtOTI0ZC0xZTg1ZjVlYmRhMTAiLCJpc3MiOiJBcHAuTW9kZWxzLlVzZXIiLCJzdWIiOjEsImlhdCI6MTc1OTY3NDk3Mn0.l3KezbE7EST8asqP9LPaovJO589WB_dPwZFakwYforU",
    "code_type": "Bearer",
    "type": "code"
}
```
