<?php

namespace Jundayw\Tokenable;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Auth\Factory as AuthFactoryContract;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Jundayw\Tokenable\Contracts\Auth\Authenticable;
use Jundayw\Tokenable\Contracts\Blacklist;
use Jundayw\Tokenable\Contracts\Grant\AccessTokenGrant as AccessTokenGrantContract;
use Jundayw\Tokenable\Contracts\Grant\AuthorizationCodeGrant as AuthorizationCodeGrantContract;
use Jundayw\Tokenable\Contracts\Grant\Factory as GrantFactoryContract;
use Jundayw\Tokenable\Contracts\Grant\Grant;
use Jundayw\Tokenable\Contracts\Token\Factory as TokenFactoryContract;
use Jundayw\Tokenable\Contracts\Token\Token;
use Jundayw\Tokenable\Contracts\Whitelist;
use Jundayw\Tokenable\Events\AccessTokenCreated;
use Jundayw\Tokenable\Events\AccessTokenRefreshed;
use Jundayw\Tokenable\Events\AccessTokenRefreshing;
use Jundayw\Tokenable\Events\AccessTokenRevoked;
use Jundayw\Tokenable\Grants\AccessTokenGrant;
use Jundayw\Tokenable\Grants\AuthorizationCodeGrant;
use Jundayw\Tokenable\Guards\TokenableGuard;
use Jundayw\Tokenable\Middleware\CheckForAnyScope;
use Jundayw\Tokenable\Middleware\CheckScopes;
use Jundayw\Tokenable\Repositories\BlacklistRepository;
use Jundayw\Tokenable\Repositories\WhitelistRepository;

class TokenableServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        config([
            'auth.guards.tokenable' => array_merge([
                'driver'   => 'tokenable',
                'provider' => null,
            ], config('auth.guards.tokenable', [])),
        ]);
        if (!app()->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__ . '/../config/tokenable.php', 'tokenable');
        }

        Tokenable::loadKeysFrom(config('tokenable.key_path'));

        $this->registerAuthRepository();
        $this->registerTokenProvider();
        $this->registerStorageProvider();

        $this->registerGrantProvider();

        $this->aliasMiddleware([
            'scopes' => CheckScopes::class,
            'scope'  => CheckForAnyScope::class,
        ]);
    }

    /**
     * Register the bindings for the Auth repository.
     *
     * @return void
     */
    protected function registerAuthRepository(): void
    {
        $this->app->singleton(Authenticable::class, static function ($app) {
            return $app->make(Tokenable::authenticationModel());
        });
    }

    /**
     * Register the bindings for the Token provider.
     *
     * @return void
     */
    protected function registerTokenProvider(): void
    {
        $this->app->singleton(TokenFactoryContract::class, static fn($app) => new TokenManager);
        $this->app->bind(Token::class, static fn($app) => $app[TokenFactoryContract::class]->driver());
    }

    /**
     * Register the bindings for the Storage provider.
     *
     * @return void
     */
    protected function registerStorageProvider(): void
    {
        $blacklist = config('cache.stores.blacklist', []);
        $whitelist = config('cache.stores.whitelist', []);
        $driver    = config('tokenable.cache.driver');
        $default   = config("cache.stores.{$driver}", []);
        $prefix    = trim(config('tokenable.cache.prefix'), ':');

        config([
            'cache.stores.blacklist' => $blacklist ?: $default + ['prefix' => $prefix . ':blacklist'],
            'cache.stores.whitelist' => $whitelist ?: $default + ['prefix' => $prefix . ':whitelist'],
        ]);

        $this->app->bind(Blacklist::class, static function ($app) {
            $store   = $app['cache']->store('blacklist')->getStore();
            $enabled = config('tokenable.cache.blacklist_enabled', false);
            return tap(new BlacklistRepository($store), static function (Blacklist $blacklist) use ($enabled) {
                $blacklist->setBlacklistEnabled($enabled);
            });
        });
        $this->app->bind(Whitelist::class, static function ($app) {
            $store   = $app['cache']->store('whitelist')->getStore();
            $enabled = config('tokenable.cache.whitelist_enabled', false);
            return tap(new WhitelistRepository($store), static function (Whitelist $whitelist) use ($enabled) {
                $whitelist->setWhitelistEnabled($enabled);
            });
        });
    }

    /**
     * Register the bindings for the grant provider.
     *
     * @return void
     */
    protected function registerGrantProvider(): void
    {
        $this->app->singleton(AccessTokenGrantContract::class, static fn() => new AccessTokenGrant);
        $this->app->singleton(AuthorizationCodeGrantContract::class, static fn() => new AuthorizationCodeGrant);
        $this->app->afterResolving(Grant::class, static fn(Grant $grant, $app) => $grant
            ->setAuthentication($app[Authenticable::class])
            ->setTokenManager($app[TokenFactoryContract::class])
            ->setBlacklist($app[Blacklist::class])
            ->setWhitelist($app[Whitelist::class])
            ->setRepository($app['cache.store'])
        );
        $this->app->singleton(GrantFactoryContract::class, GrantManager::class);
    }

    /**
     * Register the middleware.
     *
     * @param array $middlewares
     *
     * @return void
     */
    protected function aliasMiddleware(array $middlewares = []): void
    {
        $router = $this->app['router'];
        $method = method_exists($router, 'aliasMiddleware') ? 'aliasMiddleware' : 'middleware';

        array_walk($middlewares, static fn(string $class, string $name) => [$router, $method]($name, $class));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->registerMigrations();
            $this->registerPublishing();
            $this->registerCommands();
        }

        $this->registerGuard();
        $this->registerListeners();
    }

    /**
     * Register the migration file.
     *
     * @return void
     */
    protected function registerMigrations(): void
    {
        if (Tokenable::shouldRunMigrations()) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing(): void
    {
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'tokenable-migrations');

        $this->publishes([
            __DIR__ . '/../config/tokenable.php' => config_path('tokenable.php'),
        ], 'tokenable-config');
    }

    /**
     * Register the Authing Artisan commands.
     *
     * @return void
     */
    protected function registerCommands(): void
    {
        $this->commands([
            Console\KeysCommand::class,
            Console\PurgeCommand::class,
            Console\SecretCommand::class,
        ]);
    }

    /**
     * Register the token guard.
     *
     * @return void
     */
    protected function registerGuard(): void
    {
        Auth::resolved(function (AuthFactoryContract $auth) {
            $auth->extend('tokenable', function ($app, string $name, array $config) use ($auth) {
                return tap($this->makeGuard($auth, $name, $config), function (Guard $guard) {
                    app()->refresh('request', $guard, 'setRequest');
                });
            });
        });
    }

    /**
     * Make an instance of the token guard.
     *
     * @param AuthFactoryContract $auth
     * @param string              $name
     * @param array               $config
     *
     * @return Guard
     */
    protected function makeGuard(AuthFactoryContract $auth, string $name, array $config): Guard
    {
        $tokenManagement = config('tokenable.token_management');
        $tokenManagement = array_filter($tokenManagement, static fn($key) => !array_key_exists($key, $config), ARRAY_FILTER_USE_KEY);

        return new TokenableGuard(
            $name,
            new Repository($config + $tokenManagement),
            $this->app[GrantFactoryContract::class],
            $this->app['request'],
            $auth->createUserProvider($config['provider'] ?? null)
        );
    }

    /**
     * Registering event listeners
     *
     * @return void
     */
    protected function registerListeners(): void
    {
        Event::listen(AccessTokenCreated::class, Listeners\TokenManagementListener::class);
        Event::listen(AccessTokenCreated::class, Listeners\AddTokenToWhitelist::class);
        Event::listen(AccessTokenRevoked::class, Listeners\RemoveTokenFromWhitelist::class);
        Event::listen(AccessTokenRevoked::class, Listeners\AddTokenToBlacklist::class);
        Event::listen(AccessTokenRefreshing::class, Listeners\RemoveTokenFromWhitelist::class);
        Event::listen(AccessTokenRefreshing::class, Listeners\AddTokenToBlacklist::class);
        Event::listen(AccessTokenRefreshed::class, Listeners\AddTokenToWhitelist::class);
    }
}
