<?php

namespace Jundayw\Tokenable\Events;

use Illuminate\Contracts\Config\Repository;
use Jundayw\Tokenable\Contracts\Auth\Authenticable;
use Jundayw\Tokenable\Contracts\Token\Token;
use Jundayw\Tokenable\Contracts\Tokenable;

class AccessTokenCreated extends AccessTokenEvent
{
    public function __construct(
        protected Repository $config,
        protected Authenticable $authorization,
        protected Tokenable $tokenable,
        protected Token $token,
    ) {
        parent::__construct($authorization, $tokenable, $token);
    }

    /**
     * Get the configuration repository instance.
     *
     * @param string|null $key
     * @param mixed       $default
     *
     * @return mixed
     */
    public function getConfig(string $key = null, mixed $default = null): mixed
    {
        if (is_null($key)) {
            return $this->config;
        }

        return $this->config->get($key, $default);
    }
}
