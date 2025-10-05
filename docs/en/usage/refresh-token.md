---
title: Refresh Token
lang: en-US
---

# Refresh Token

![Refresh Token](./assets/en/refresh.svg)

## Route

```php
Route::get('/refresh', [App\Http\Controllers\AccountController::class, 'refresh']);
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

    public function refresh(Request $request): ?Token
    {
        return $this->guard('web')->refreshToken();
    }
}
```
