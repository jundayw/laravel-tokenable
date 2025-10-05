---
title: Quick Start
lang: en-US
---

# Quick Start

## Installation

You can install the package using Composer:

```bash
composer require jundayw/tokenable
```

## Publishing Resources

You can publish all publishable files using the `--provider` flag:

```shell
php artisan vendor:publish --provider="Jundayw\Tokenable\TokenableServiceProvider"
```

You may want to publish only the configuration files:

```shell
php artisan vendor:publish --tag=tokenable-config
```

You may want to publish only the migration files:

```shell
php artisan vendor:publish --tag=tokenable-migrations
```

## Run Migrations

```shell
php artisan migrate --path=database/migrations/2025_06_01_000000_create_auth_token_table.php
```

## Generate a secret key

```shell
php artisan tokenable:secret
```

This secret key is used when signing tokens using a symmetric algorithm (such as HMAC, HS256, or HS512).

::: tip
For compatibility reasons, the default token algorithm used by the extension package is HS256, meaning you can generate a secret key using `php artisan tokenable:secret`.

However, you should consider using a stronger algorithm such as RS384 or ES384 for increased security.

If using algorithms such as RS*, ES*, or EdDSA, you will need to generate both a private and public key.
:::

Supported (RS256, RS384, RS512, ES256, ES384, ES512, EdDSA) [Default: "RS256"]:

```shell
php artisan tokenable:keys
```

The private key is used to sign tokens when using an asymmetric algorithm (such as RS256, RS512, or ES512).
