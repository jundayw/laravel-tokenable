---
title: 快速入门
lang: zh-CN
---

# 快速入门

## 安装

您可以通过 `Composer` 安装该软件包：

```bash
composer require jundayw/tokenable
```

## 发布资源

可以使用 `--provider` 标志发布所有可发布文件：

```shell
php artisan vendor:publish --provider="Jundayw\Tokenable\TokenableServiceProvider"
```

您可能希望只发布配置文件：

```shell
php artisan vendor:publish --tag=tokenable-config
```

您可能希望只发布迁移文件：

```shell
php artisan vendor:publish --tag=tokenable-migrations
```

## 执行迁移

```shell
php artisan migrate --path=database/migrations/2025_06_01_000000_create_auth_token_table.php
```

## 生成密钥

```shell
php artisan tokenable:secret
```

此密钥用于在使用对称算法（例如 HMAC，HS256、HS512）签名令牌时使用。

::: tip
扩展包基于兼容性考虑，默认令牌使用算法为 HS256，即可以使用 `php artisan tokenable:secret` 生成秘钥，
但您应该考虑更强大的算法可以使用 RS384 或 ES384 等算法来提高安全性。

如果使用 RS*、ES*、EdDSA 等算法，您需要同时生成私钥和公钥。
:::

支持（RS256、RS384、RS512、ES256、ES384、ES512、EdDSA）[默认值："RS256"]:

```shell
php artisan tokenable:keys
```

私钥用于在使用非对称算法（例如 RS256、RS512 或 ES512）时对令牌进行签名。
