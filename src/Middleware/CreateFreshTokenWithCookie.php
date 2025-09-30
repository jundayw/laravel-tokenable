<?php

namespace Jundayw\Tokenable\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Jundayw\Tokenable\Contracts\Blacklist;
use Jundayw\Tokenable\Contracts\Tokenable;
use Jundayw\Tokenable\Exceptions\AuthenticationException;

class CreateFreshTokenWithCookie
{
    /**
     * The authentication guards.
     *
     * @var array
     */
    protected array $guards = [];

    public function __construct(
        protected Factory $auth,
        protected Blacklist $blacklist,
        protected Tokenable $tokenable,
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
     */
    public function handle(Request $request, Closure $next, string ...$guards): mixed
    {
        $this->guards = empty($guards) ? [null] : $guards;

        return $next($request);
    }

    /**
     * Determine if the given request should receive a fresh token.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function shouldReceiveFreshToken(Request $request): bool
    {
        return false;
    }

    /**
     * Determine if the request should receive a fresh token.
     *
     * @return Tokenable
     * @throws AuthenticationException
     */
    protected function requestShouldReceiveFreshToken(): Tokenable
    {
        throw new AuthenticationException();
    }

    /**
     * Determine if the current request has a valid access token.
     *
     * Check if the access token included in the current request is valid,
     * meaning it is recognized by the system and has not expired.
     *
     * @param Request $request
     *
     * @return string|null
     */
    public function getTokenViaCookie(Request $request): ?string
    {
        return null;
    }

    /**
     * Determine if the response should receive a fresh token.
     *
     * @param mixed $response
     *
     * @return bool
     */
    protected function responseShouldReceiveFreshToken(mixed $response): bool
    {
        return $response instanceof Response || $response instanceof JsonResponse;
    }
}
