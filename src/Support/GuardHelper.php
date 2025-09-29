<?php

namespace Jundayw\Tokenable\Support;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Jundayw\Tokenable\Contracts\Auth\TokenableAuthGuard;

trait GuardHelper
{
    /**
     * Attempt to get the guard against the local cache.
     *
     * @param string|null $name
     *
     * @return Guard|TokenableAuthGuard
     */
    public function guard(string|null $name = null): Guard|TokenableAuthGuard
    {
        return Auth::guard($name);
    }
}
