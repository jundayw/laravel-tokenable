<?php

namespace Jundayw\Tokenable\Grants;

use Illuminate\Contracts\Cache\Repository;
use Jundayw\Tokenable\Contracts\Auth\Authenticable;
use Jundayw\Tokenable\Contracts\Auth\TokenableAuthGuard;
use Jundayw\Tokenable\Contracts\Blacklist;
use Jundayw\Tokenable\Contracts\Token\Factory as TokenFactoryContract;
use Jundayw\Tokenable\Contracts\Token\Token;
use Jundayw\Tokenable\Contracts\Tokenable;
use Jundayw\Tokenable\Contracts\Whitelist;

abstract class Grant
{
    protected Authenticable        $authentication;
    protected TokenFactoryContract $tokenManager;
    protected Blacklist            $blacklist;
    protected Whitelist            $whitelist;
    protected Repository           $repository;

    public function getAuthentication(): Authenticable
    {
        return $this->authentication;
    }

    public function setAuthentication(Authenticable $authentication): static
    {
        $this->authentication = $authentication;
        return $this;
    }

    public function getTokenManager(): TokenFactoryContract
    {
        return $this->tokenManager;
    }

    public function setTokenManager(TokenFactoryContract $tokenManager): static
    {
        $this->tokenManager = $tokenManager;
        return $this;
    }

    public function getBlacklist(): Blacklist
    {
        return $this->blacklist;
    }

    public function setBlacklist(Blacklist $blacklist): static
    {
        $this->blacklist = $blacklist;
        return $this;
    }

    public function getWhitelist(): Whitelist
    {
        return $this->whitelist;
    }

    public function setWhitelist(Whitelist $whitelist): static
    {
        $this->whitelist = $whitelist;
        return $this;
    }

    public function getRepository(): Repository
    {
        return $this->repository;
    }

    public function setRepository(Repository $repository): static
    {
        $this->repository = $repository;
        return $this;
    }

    protected Tokenable $tokenable;
    protected Token     $token;

    public function getTokenable(): Tokenable
    {
        return $this->tokenable;
    }

    public function setTokenable(Tokenable $tokenable): static
    {
        $this->tokenable = $tokenable;
        return $this;
    }

    public function getToken(): Token
    {
        return $this->token ?? $this->getTokenManager()->driver();
    }

    public function setToken(Token $token): static
    {
        $this->token = $token;
        return $this;
    }

    public function withToken(string $token = null): static
    {
        $this->token = $this->getTokenManager()->driver($token);
        return $this;
    }

    protected TokenableAuthGuard $guard;

    public function getGuard(): TokenableAuthGuard
    {
        return $this->guard;
    }

    public function withGuard(TokenableAuthGuard $guard): static
    {
        $this->guard = $guard;
        return $this;
    }
}
