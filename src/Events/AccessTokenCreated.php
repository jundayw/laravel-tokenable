<?php

namespace Jundayw\Tokenable\Events;

use Illuminate\Contracts\Config\Repository;
use Jundayw\Tokenable\Contracts\Auth\Authenticable;

class AccessTokenCreated extends AccessTokenEvent
{
    public function __construct(
        protected Repository $config,
        protected Authenticable $authorization,
    ) {
        parent::__construct($authorization);
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
