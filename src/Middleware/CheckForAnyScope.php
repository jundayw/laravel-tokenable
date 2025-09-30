<?php

namespace Jundayw\Tokenable\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Jundayw\Tokenable\Exceptions\AuthenticationException;
use Jundayw\Tokenable\Exceptions\MissingScopeException;

class CheckForAnyScope
{
    /**
     * Specify the scopes for the middleware.
     *
     * @param array|string $scopes
     *
     * @return string
     */
    public static function using(...$scopes): string
    {
        if (is_array($scopes[0])) {
            return static::class . ':' . implode(',', $scopes[0]);
        }

        return static::class . ':' . implode(',', $scopes);
    }

    /**
     * Handle the incoming request.
     *
     * @param Request  $request
     * @param Closure  $next
     * @param string[] $scopes
     *
     * @return Response
     * @throws AuthenticationException
     * @throws MissingScopeException
     */
    public function handle(Request $request, Closure $next, string ...$scopes): mixed
    {
        if (!$request->user() || !$request->user()->token()) {
            throw new AuthenticationException;
        }

        foreach ($scopes as $scope) {
            if ($request->user()->tokenCan($scope)) {
                return $next($request);
            }
        }

        throw new MissingScopeException($scopes);
    }
}
