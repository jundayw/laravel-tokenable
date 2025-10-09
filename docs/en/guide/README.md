---
title: Guide
lang: en-US
---

# Guide

```php
Tokenable::useTokenable(fn($tokenable) => [
    "access_token"  => $tokenable->accessToken,
    "token_type"    => $tokenable->getTokenType(),
    "expires_in"    => $tokenable->getExpiresIn(),
    "refresh_token" => $tokenable->refreshToken,
    "time"          => now()->toIso8601ZuluString(),
]);
```
