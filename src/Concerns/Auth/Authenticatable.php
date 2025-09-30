<?php

namespace Jundayw\Tokenable\Concerns\Auth;

use Illuminate\Database\Eloquent\Relations\MorphTo;

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
     * @return static|null
     */
    public function findAccessToken(string $token): ?static
    {
        return $this->newQuery()
            ->with('tokenable')
            ->where('access_token', $token)
            ->where('access_token_expire_at', '>=', now())
            ->first();
    }

    /**
     * Find a refresh token by its plain-text value.
     *
     *  Loads the related `tokenable` model and ensures the token
     *  has not yet expired. Returns null if no refresh token exists.
     *
     * @param string $token
     *
     * @return static|null
     */
    public function findRefreshToken(string $token): ?static
    {
        return $this->newQuery()
            ->with('tokenable')
            ->where('refresh_token', $token)
            ->where('refresh_token_available_at', '<=', now())
            ->where('refresh_token_expire_at', '>=', now())
            ->first();
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
