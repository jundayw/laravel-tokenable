<?php

namespace Jundayw\Tokenable\Listeners;

use Illuminate\Contracts\Cache\Repository;
use Jundayw\Tokenable\Contracts\Blacklist;
use Jundayw\Tokenable\Contracts\Whitelist;
use Jundayw\Tokenable\Events\AccessTokenCreated;
use Jundayw\Tokenable\Events\AccessTokenEvent;
use Jundayw\Tokenable\Events\AccessTokenRefreshed;
use Jundayw\Tokenable\Events\AccessTokenRevoked;
use Jundayw\Tokenable\Events\SuspendTokenCreated;

class TokenableEventSubscriber
{
    public function __construct(
        protected Blacklist $blacklist,
        protected Whitelist $whitelist,
        protected Repository $repository,
    ) {
        //
    }

    /**
     * Handles the creation of an access token by storing it in the whitelist.
     *
     * If whitelist functionality is disabled, the method returns early.
     * Otherwise, both the access token and refresh token are added to the whitelist
     * with their corresponding expiration timestamps.
     *
     * @param AccessTokenEvent $event The event containing token and authorization information
     *
     * @return void
     */
    public function handleAccessTokenCreated(AccessTokenEvent $event): void
    {
        if ($this->whitelist->isWhitelistEnabled() === false) {
            return;
        }

        $this->whitelist->put(
            $event->getAttribute('access_token'),
            $event->getAuthorization(),
            $event->getAttribute('access_token_expire_at'),
        );
        $this->whitelist->put(
            $event->getAttribute('refresh_token'),
            $event->getAuthorization(),
            $event->getAttribute('refresh_token_expire_at'),
        );
    }

    /**
     * Handles the revocation of an access token.
     *
     * If the whitelist is enabled, removes the access token and refresh token from it.
     * If the blacklist is enabled, adds both tokens to the blacklist along with their expiration timestamps.
     *
     * @param AccessTokenRevoked $event The event containing token and authorization information
     *
     * @return void
     */
    public function handleAccessTokenRevoked(AccessTokenRevoked $event): void
    {
        if ($this->whitelist->isWhitelistEnabled()) {
            $this->whitelist->forget($event->getAttribute('access_token'));
            $this->whitelist->forget($event->getAttribute('refresh_token'));
        }

        if ($this->blacklist->isBlacklistEnabled() === false) {
            return;
        }

        $this->blacklist->put(
            $event->getAttribute('access_token'),
            $event->getAttribute('access_token'),
            $event->getAttribute('access_token_expire_at'),
        );
        $this->blacklist->put(
            $event->getAttribute('refresh_token'),
            $event->getAttribute('refresh_token'),
            $event->getAttribute('refresh_token_expire_at'),
        );
    }

    /**
     * Handles the creation of a suspend token.
     *
     * Generates a unique key based on the tokenable type, ID, and optionally the platform
     * (if not a global token) and stores it in the repository indefinitely.
     *
     * @param SuspendTokenCreated $event The event containing tokenable and authorization information
     *
     * @return void
     */
    public function handleSuspendTokenCreated(SuspendTokenCreated $event): void
    {
        $keys = [$event->getAttribute('tokenable_type'), $event->getAttribute('tokenable_id')];

        if (!$event->isGlobal()) {
            $keys[] = $event->getAuthorization()->getAttribute('platform');
        }

        $key = implode('.', $keys);

        $this->repository->forever($key, now()->toIso8601ZuluString());
    }

    /**
     * Handles the revocation of a suspend token.
     *
     * Iterates over the hierarchical key structure derived from tokenable type, ID, and platform,
     * removing each prefixed key from the repository.
     *
     * @param AccessTokenCreated $event The event containing tokenable and authorization information
     *
     * @return void
     */
    public function handleSuspendTokenRevoked(AccessTokenCreated $event): void
    {
        for ($i = 1; $i < count($keys = [
            $event->getAuthorization()->getAttribute('tokenable_type'),
            $event->getAuthorization()->getAttribute('tokenable_id'),
            $event->getAuthorization()->getAttribute('platform'),
        ]); $i++) {
            $this->repository->forget(
                implode('.', array_slice($keys, 0, $i + 1))
            );
        }
    }

    /**
     * Registers a listener for the subscriber.
     *
     * @param $events
     *
     * @return array
     */
    public function subscribe($events): array
    {
        return [
            AccessTokenCreated::class   => [
                TokenManagementListener::class,
                'handleAccessTokenCreated',
                'handleSuspendTokenRevoked',
            ],
            AccessTokenRevoked::class   => 'handleAccessTokenRevoked',
            AccessTokenRefreshed::class => 'handleAccessTokenCreated',
            SuspendTokenCreated::class  => [
                SuspendTokenListener::class,
                'handleSuspendTokenCreated',
            ],
        ];
    }
}
