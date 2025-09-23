<?php

namespace Jundayw\Tokenable\Support;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Jundayw\Tokenable\Contracts\Auth\SupportsTokenable;

class GuardHelper
{
    /**
     * Attempt to get the guard against the local cache.
     *
     * @param string|null $name
     *
     * @return Guard|SupportsTokenable
     */
    public function guard(string|null $name = null): Guard|SupportsTokenable
    {
        return Auth::guard($name);
    }
}
