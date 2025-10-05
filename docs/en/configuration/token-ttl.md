---
title: Token Lifetime
lang: en-US
---

# Token Lifetime

```php
/*
|--------------------------------------------------------------------------
| Access Token Lifetime (TTL)
|--------------------------------------------------------------------------
|
| This value defines the number of seconds an issued access token will
| remain valid before expiring. Once expired, the token can no longer
| be used to access protected resources.
|
*/

'ttl' => env('TOKEN_TTL', 7200),

/*
|--------------------------------------------------------------------------
| Refresh Token Not-Before Time
|--------------------------------------------------------------------------
|
| This value defines the number of seconds after issuance during which
| a refresh token cannot be used. This allows enforcing a minimum wait
| time before a client is allowed to request a new access token using
| the refresh token.
|
*/

'refresh_nbf' => env('TOKEN_REFRESH_NBF', 3600),

/*
|--------------------------------------------------------------------------
| Refresh Token Lifetime
|--------------------------------------------------------------------------
|
| This value controls the total time-to-live of refresh tokens. It may
| be expressed as a relative interval (e.g. "P15D" for 15 days). Once
| a refresh token expires, the client must re-authenticate to obtain
| new tokens.
|
*/

'refresh_ttl' => env('TOKEN_REFRESH_TTL', 'P15D'),

/*
|--------------------------------------------------------------------------
| Authorization Code Lifetime
|--------------------------------------------------------------------------
|
| This value defines the time-to-live of authorization codes. Since these
| codes are intended to be short-lived and exchanged quickly for access
| tokens, their lifetime should remain very short (typically 30–300
| seconds). Once an authorization code expires, the client must restart
| the authorization flow to obtain a new one.
|
*/

'auth_code_ttl' => env('TOKEN_AUTH_CODE_TTL', 60),
```
