---
title: 颁发令牌
lang: zh-CN
---

# 颁发令牌

![认证流程](/assets/zh/auth.svg)

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
            ->createToken(name: 'PC Token', platform: 'pc');
    }
}
```

## 响应结构体

```json
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiJhNTg5Yjg0Ni1mMjlkLTQ3MDYtYjIyOC1mZjRmYTVhYzZhM2EiLCJpc3MiOiJBcHAuTW9kZWxzLlVzZXIiLCJzdWIiOjEsImF1ZCI6WyIqIl0sImV4cCI6MTc1OTYzMjY4OSwiaWF0IjoxNzU5NjI1NDg5fQ.7kq4DsCJe54g_Q6pMxwI2L913IcdoRDRnE-Ya4TC7Po",
    "token_type": "Bearer",
    "expires_in": "2025-10-05T02:51:29Z",
    "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiIzZDkwYTA1ZS1mNGQxLTQ1YzUtYWFjZS0zMzMxNjkxMzA1MTgiLCJpc3MiOiJBcHAuTW9kZWxzLlVzZXIiLCJzdWIiOjEsImV4cCI6MTc1OTYzMjY4OSwibmJmIjoxNzU5NjI5MDg5LCJpYXQiOjE3NTk2MjU0ODl9.ZzZW-VIMFqIJ5ee_Yw6M4T786bjn0OiBPtYY0chXYHE",
    "type": "token"
}
```

[JSON Web Token (JWT) Debugger](https://www.jwt.io/)
