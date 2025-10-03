<?php

namespace Jundayw\Tokenable\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Jundayw\Tokenable\Contracts\Auth\TokenableAuthGuard;
use Jundayw\Tokenable\Exceptions\AuthenticationException;

class RefreshTokenWithCookie
{
    public function __construct(
        protected Factory $auth,
    ) {
        //
    }

    /**
     * Specify the guards for the middleware.
     *
     * @param array|string $guards
     *
     * @return string
     */
    public static function using(array|string ...$guards): string
    {
        if (is_array($guards[0])) {
            return static::class . ':' . implode(',', $guards[0]);
        }

        return static::class . ':' . implode(',', $guards);
    }

    /**
     * Handle the incoming request.
     *
     * @param Request  $request
     * @param Closure  $next
     * @param string[] $guards
     *
     * @return Response
     * @throws AuthenticationException
     */
    public function handle(Request $request, Closure $next, string ...$guards): mixed
    {
        $this->authenticate($guards);

        return $next($request);
    }

    /**
     * Determine if the user is logged in to any of the given guards.
     *
     * @param array $guards
     *
     * @return null
     * @throws AuthenticationException
     */
    protected function authenticate(array $guards)
    {
        if (empty($guards)) {
            $guards = [null];
        }

        foreach ($guards as $guard) {
            $tokenableGuard = $this->auth->guard($guard);
            if ($tokenableGuard->check() || ($tokenableGuard instanceof TokenableAuthGuard &&
                    !is_null($tokenableGuard->refreshToken()?->withCookies()))) {
                return $this->auth->shouldUse($guard);
            }
        }

        throw new AuthenticationException;
    }
}
