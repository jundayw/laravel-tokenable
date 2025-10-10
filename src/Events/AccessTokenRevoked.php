<?php

namespace Jundayw\Tokenable\Events;

class AccessTokenRevoked
{
    public function __construct(
        protected array $attributes,
    ) {
        //
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
        return $this->attributes[$key] ?? null;
    }
}
