---
title: 吊销令牌
lang: zh-CN
---

# 吊销令牌

## 路由

```php
Route::middleware(['auth:web'])->group(function () {
    Route::get('/revoke', [App\Http\Controllers\AccountController::class, 'revoke']);
});
```

## 控制器

```php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Jundayw\Tokenable\Support\GuardHelper;

class AccountController
{
    use GuardHelper;

    public function revoke(Request $request): bool
    {
        return $this->guard()->revokeToken();
    }
}
```
