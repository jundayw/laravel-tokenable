---
title: 刷新令牌
lang: zh-CN
---

# 刷新令牌

![刷新令牌流程](./assets/zh/refresh.svg)

## 路由

```php
Route::get('/refresh', [App\Http\Controllers\AccountController::class, 'refresh']);
```

## 控制器

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
