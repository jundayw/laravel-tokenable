<?php

namespace Jundayw\Tokenable\Listeners;

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
    ) {
        //
    }

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
            ],
            AccessTokenRevoked::class   => 'handleAccessTokenRevoked',
            AccessTokenRefreshed::class => 'handleAccessTokenCreated',
            SuspendTokenCreated::class  => SuspendTokenListener::class,
        ];
    }
}
