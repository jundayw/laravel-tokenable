---
title: 冻结令牌
lang: zh-CN
---

## 冻结令牌

冻结当前平台令牌

```php
$request->user()->suspendToken();
```

冻结当前用户所有令牌

```php
$request->user()->suspendToken(true);
```

令牌冻结后，控制器颁发令牌不再返回 `AccessToken` 和 `RefreshToken`，而是返回 `AuthCode`

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

## 解冻令牌

### 路由

```php
Route::get('/token', [App\Http\Controllers\AccountController::class, 'token']);
```

### 控制器

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
