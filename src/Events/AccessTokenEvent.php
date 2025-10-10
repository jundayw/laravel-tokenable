<?php

namespace Jundayw\Tokenable\Events;

use Illuminate\Queue\SerializesModels;
use Jundayw\Tokenable\Contracts\Auth\Authenticable;

abstract class AccessTokenEvent
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param Authenticable $authorization
     */
    public function __construct(
        protected Authenticable $authorization,
    ) {
        //
    }

    /**
     * Get the authorization entity instance.
     *
     * @return Authenticable
     */
    public function getAuthorization(): Authenticable
    {
        return $this->authorization;
    }

    /**
     * Get an attribute from the model.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getAttribute(string $key): mixed
    {
        return $this->authorization->getAttribute($key);
    }
}
