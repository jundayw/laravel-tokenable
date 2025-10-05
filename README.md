<a id="readme-top"></a>

# Laravel Tokenable

[![GitHub Tag][GitHub Tag]][GitHub Tag URL]
[![Total Downloads][Total Downloads]][Packagist URL]
[![Packagist Version][Packagist Version]][Packagist URL]
[![Packagist PHP Version Support][Packagist PHP Version Support]][Repository URL]
[![Packagist License][Packagist License]][Repository URL]

<!-- TABLE OF CONTENTS -->
<details>
    <summary>Table of Contents</summary>
    <ol>
        <li><a href="#introduction">Introduction</a></li>
        <li><a href="#documentation">Documentation</a></li>
        <li><a href="#installation">Installation</a></li>
        <li><a href="#usage">Usage</a></li>
        <li><a href="#contributing">Contributing</a></li>
        <li><a href="#contributors">Contributors</a></li>
        <li><a href="#license">License</a></li>
    </ol>
</details>

## Introduction

**Laravel Tokenable** is a modern authentication package that extends Laravel’s token capabilities beyond Sanctum.

It supports:

* **APIs** for stateless clients.
* **SPAs** (Single Page Applications) for JavaScript-driven frontends.
* **SSR** (Server-Side Rendered apps) with cookie-based authentication.

Key features include:

* **Multi-model support** — authenticate multiple user models (e.g. `User`, `Admin`, `Member`) seamlessly.
* **Multi-platform support** — issue tokens per platform (e.g. `App`, `Web`, `Mini Programs`) with fine-grained control.
* **Refresh tokens** — enable long-lived sessions with secure refresh workflows.
* **Flexible token structure** — customize token structure and lifecycle management.
* **Cookie + API support** — works equally well for session-based SSR and token-based APIs.

Laravel Sanctum provides basic API authentication and multiple model support, but falls short when:

* You need **fine-grained token control per platform** (e.g. App vs Web).
* You require **refresh token flows**.
* You want **customizable token structures** instead of being tied to Sanctum’s defaults.

**Laravel Tokenable** bridges this gap, making token authentication **first-class, flexible, and universal** across your Laravel projects.

<p align="right">[<a href="#readme-top">back to top</a>]</p>

## Documentation

Documentation for Laravel Tokenable can be found on the [documentation](https://jundayw.github.io/laravel-tokenable/).

<p align="right">[<a href="#readme-top">back to top</a>]</p>

<!-- INSTALLATION -->

## Installation

You can install the package via [Composer]:

```bash
composer require jundayw/tokenable
```

### Publish Resources

Your users can also publish all publishable files defined by your package's service provider using the `--provider` flag:

```shell
php artisan vendor:publish --provider="Jundayw\Tokenable\TokenableServiceProvider"
```

You may wish to publish only the configuration files:

```shell
php artisan vendor:publish --tag=tokenable-config
```

You may wish to publish only the migration files:

```shell
php artisan vendor:publish --tag=tokenable-migrations
```

### Run Migrations

```shell
php artisan migrate --path=database/migrations/2025_06_01_000000_create_auth_token_table.php
```

<p align="right">[<a href="#readme-top">back to top</a>]</p>

<!-- USAGE EXAMPLES -->

## Usage

### Configuration

Use the `tokenable` guard in the `guards` configuration of your application's `auth.php` configuration file:

```php
'guards' => [
    'api' => [
        'driver' => 'tokenable',
        'provider' => 'users',
    ],
],
```

### Model

To start issuing tokens for users, your User model should use the `Jundayw\Tokenable\HasTokenable` trait and implement the `Jundayw\Tokenable\Contracts\Tokenable` interface.

```php
namespace App\Models;

use Jundayw\Tokenable\Contracts\Tokenable;
use Jundayw\Tokenable\HasTokenable;

class User extends Authenticatable implements Tokenable
{
    use HasTokenable, HasFactory, Notifiable;
}
```

### Create Token

```php
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
```

### Refresh Token

```php
return $this->guard('web')->refreshToken();
```

### Revoke Token

```php
return $this->guard()->revokeToken();
```

<!-- CONTRIBUTING -->

## Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement".
Don't forget to give the project a star! Thanks again!

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

<p align="right">[<a href="#readme-top">back to top</a>]</p>

<!-- CONTRIBUTORS -->

## Contributors

Thanks goes to these wonderful people:

<a href="https://github.com/jundayw/laravel-tokenable/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=jundayw/laravel-tokenable" alt="contrib.rocks image" />
</a>

Contributions of any kind are welcome!

<p align="right">[<a href="#readme-top">back to top</a>]</p>

<!-- LICENSE -->

## License

Distributed under the MIT License (MIT). Please see [License File] for more information.

<p align="right">[<a href="#readme-top">back to top</a>]</p>

[GitHub Tag]: https://img.shields.io/github/v/tag/jundayw/laravel-tokenable

[Total Downloads]: https://img.shields.io/packagist/dt/jundayw/tokenable?style=flat-square

[Packagist Version]: https://img.shields.io/packagist/v/jundayw/tokenable

[Packagist PHP Version Support]: https://img.shields.io/packagist/php-v/jundayw/tokenable

[Packagist License]: https://img.shields.io/github/license/jundayw/laravel-tokenable

[GitHub Tag URL]: https://github.com/jundayw/laravel-tokenable/tags

[Packagist URL]: https://packagist.org/packages/jundayw/tokenable

[Repository URL]: https://github.com/jundayw/laravel-tokenable

[GitHub Open Issues]: https://github.com/jundayw/laravel-tokenable/issues

[Composer]: https://getcomposer.org

[License File]: https://github.com/jundayw/laravel-tokenable/blob/main/LICENSE
