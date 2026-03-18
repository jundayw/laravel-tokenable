<?php

namespace Jundayw\Tokenable\Concerns\Auth;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Jundayw\Tokenable\Exceptions\AccessTokenExpiredException;
use Jundayw\Tokenable\Exceptions\RefreshTokenExpiredException;
use Jundayw\Tokenable\Exceptions\RefreshTokenNotAvailableException;
use Jundayw\Tokenable\Exceptions\TokenNotFoundException;

trait Authenticatable
{
    /**
     * Get the tokenable model that the access token belongs to.
     *
     * @return MorphTo
     */
    public function tokenable(): MorphTo
    {
        return $this->morphTo('tokenable');
    }

    /**
     * Find a valid access token by its plain-text value.
     *
     *  Loads the related `tokenable` model and ensures the token
     *  has not yet expired. Returns null if no access token exists.
     *
     * @param string $token
     *
     * @return static
     */
    public function findAccessToken(string $token): static
    {
        $token = $this->newQuery()
            ->with('tokenable')
            ->where('access_token', $token)
            ->first();

        if (is_null($token)) {
            throw new TokenNotFoundException;
        }

        if (now()->gt($token->getAttribute('access_token_expire_at'))) {
            throw new AccessTokenExpiredException;
        }

        return $token;
    }

    /**
     * Find a refresh token by its plain-text value.
     *
     *  Loads the related `tokenable` model and ensures the token
     *  has not yet expired. Returns null if no refresh token exists.
     *
     * @param string $token
     *
     * @return static
     */
    public function findRefreshToken(string $token): static
    {
        $token = $this->newQuery()
            ->with('tokenable')
            ->where('refresh_token', $token)
            ->first();

        if (is_null($token)) {
            throw new TokenNotFoundException;
        }

        if (now()->gt($token->getAttribute('refresh_token_expire_at'))) {
            throw new RefreshTokenExpiredException;
        }

        if (now()->lt($token->getAttribute('refresh_token_available_at'))) {
            throw new RefreshTokenNotAvailableException;
        }

        return $token;
    }

    /**
     *  Update the token with the given attributes and return the fresh model.
     *
     *  Persists the changes and reloads the model from the database
     *  to ensure all attributes are up-to-date.
     *
     * @param array<string, mixed> $credentials
     *
     * @return static
     */
    public function updateToken(array $credentials = []): static
    {
        return tap($this, static fn($model) => $model->update($credentials))->refresh();
    }

    /**
     * Determine if the token has a given scope.
     *
     * @param string $scope
     *
     * @return bool
     */
    public function can(string $scope): bool
    {
        if (in_array('*', $this->getAttribute('scopes'))) {
            return true;
        }

        $scopes = [$scope];

        foreach ($scopes as $scope) {
            if (array_key_exists($scope, array_flip($this->getAttribute('scopes')))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the token is missing a given scope.
     *
     * @param string $scope
     *
     * @return bool
     */
    public function cant(string $scope): bool
    {
        return !$this->can($scope);
    }
}
