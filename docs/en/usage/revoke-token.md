---
title: Revoke Token
lang: en-US
---

# Revoke Token

## Route

```php
Route::middleware(['auth:web'])->group(function () {
    Route::get('/revoke', [App\Http\Controllers\AccountController::class, 'revoke']);
});
```

## Controller

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
