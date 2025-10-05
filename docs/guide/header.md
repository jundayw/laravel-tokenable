---
title: 请求头
lang: zh-CN
---

# 请求头

## 默认响应结构体

令牌结构体：

```json
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiJhNTg5Yjg0Ni1mMjlkLTQ3MDYtYjIyOC1mZjRmYTVhYzZhM2EiLCJpc3MiOiJBcHAuTW9kZWxzLlVzZXIiLCJzdWIiOjEsImF1ZCI6WyIqIl0sImV4cCI6MTc1OTYzMjY4OSwiaWF0IjoxNzU5NjI1NDg5fQ.7kq4DsCJe54g_Q6pMxwI2L913IcdoRDRnE-Ya4TC7Po",
    "token_type": "Bearer",
    "expires_in": "2025-10-05T02:51:29Z",
    "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiIzZDkwYTA1ZS1mNGQxLTQ1YzUtYWFjZS0zMzMxNjkxMzA1MTgiLCJpc3MiOiJBcHAuTW9kZWxzLlVzZXIiLCJzdWIiOjEsImV4cCI6MTc1OTYzMjY4OSwibmJmIjoxNzU5NjI5MDg5LCJpYXQiOjE3NTk2MjU0ODl9.ZzZW-VIMFqIJ5ee_Yw6M4T786bjn0OiBPtYY0chXYHE",
    "type": "token"
}
```

授权码结构体：

```json
{
    "auth_code": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiJkMDI2YTViNC1iMGQ2LTRmYmMtOTI0ZC0xZTg1ZjVlYmRhMTAiLCJpc3MiOiJBcHAuTW9kZWxzLlVzZXIiLCJzdWIiOjEsImlhdCI6MTc1OTY3NDk3Mn0.l3KezbE7EST8asqP9LPaovJO589WB_dPwZFakwYforU",
    "code_type": "Bearer",
    "type": "code"
}
```

::: warning
访问令牌:

Authorization: Bearer <access_token>

刷新令牌:

Authorization: Bearer <refresh_token>

授权码:

Authorization: Bearer <authorization_code>
:::

## 指定响应结构体

使用 `withToken` 方法指定响应结构体类型：

```php
return $this->guard('web')
    ->login($user)
    ->withToken('hash')
    ->createToken(name: 'PC Token', platform: 'pc');
```

令牌结构体：

```json
{
    "access_token": "48bc23ae2198bdcce64fbb8532c0a536ccc84d391ba6c04ec4fe15730029e345",
    "token_type": "hash",
    "expires_in": "2025-10-05T18:50:54Z",
    "refresh_token": "4d5136678843994d8a0e25a3b32a9032eeb89b549579ae91a1487a0b6c7305d6",
    "type": "token"
}
```

使用 `withToken` 方法指定响应结构体类型：

```php
return $this->guard('web')
    ->login($user)
    ->withToken('hash')
    ->createAuthCode();
```

授权码结构体：

```json
{
    "auth_code": "e05364f0bdfcdde296224d538acc6993f8944a76bcef281af291eff4bcfba547",
    "code_type": "hash",
    "type": "code"
}
```

::: warning
访问令牌:

Authorization: Basic <base64(hash:access_token)>

刷新令牌:

Authorization: Basic <base64(hash:refresh_token)>

授权码:

Authorization: Basic <base64(hash:authorization_code)>
:::
