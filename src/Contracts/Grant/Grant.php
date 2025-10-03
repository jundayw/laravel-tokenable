<?php

namespace Jundayw\Tokenable\Contracts\Grant;

use Illuminate\Contracts\Cache\Repository;
use Jundayw\Tokenable\Contracts\Auth\Authenticable;
use Jundayw\Tokenable\Contracts\Auth\TokenableAuthGuard;
use Jundayw\Tokenable\Contracts\Blacklist;
use Jundayw\Tokenable\Contracts\Token\Factory as TokenFactoryContract;
use Jundayw\Tokenable\Contracts\Token\Token;
use Jundayw\Tokenable\Contracts\Tokenable;
use Jundayw\Tokenable\Contracts\Whitelist;

interface Grant
{
    public function getAuthentication(): Authenticable;

    public function setAuthentication(Authenticable $authentication): static;

    public function getTokenManager(): TokenFactoryContract;

    public function setTokenManager(TokenFactoryContract $tokenManager): static;

    public function getBlacklist(): Blacklist;

    public function setBlacklist(Blacklist $blacklist): static;

    public function getWhitelist(): Whitelist;

    public function setWhitelist(Whitelist $whitelist): static;

    public function getRepository(): Repository;

    public function setRepository(Repository $repository): static;

    public function getTokenable(): Tokenable;

    public function setTokenable(Tokenable $tokenable): static;

    public function getToken(): Token;

    public function setToken(Token $token): static;

    public function withToken(string $token = null): static;

    public function getGuard(): TokenableAuthGuard;

    public function withGuard(TokenableAuthGuard $guard): static;
}
