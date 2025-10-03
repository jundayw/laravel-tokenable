<?php

namespace Jundayw\Tokenable\Listeners;

use Jundayw\Tokenable\Contracts\Blacklist;
use Jundayw\Tokenable\Events\AccessTokenEvent;

class AddTokenToBlacklist extends ShouldQueueable
{
    public function __construct(
        protected Blacklist $blacklist
    ) {
        //
    }

    /**
     * @inheritdoc
     *
     * @param AccessTokenEvent $event
     *
     * @return void
     */
    public function handle($event): void
    {
        $this->blacklist->put(
            $event->getAuthorization()->getAttribute('access_token'),
            $event->getAuthorization()->getAttribute('access_token'),
            $event->getAuthorization()->getAttribute('access_token_expire_at'),
        );
        $this->blacklist->put(
            $event->getAuthorization()->getAttribute('refresh_token'),
            $event->getAuthorization()->getAttribute('refresh_token'),
            $event->getAuthorization()->getAttribute('refresh_token_expire_at'),
        );
    }

    /**
     * Determine whether the listener should be queued.
     *
     * @param $event
     *
     * @return bool
     */
    public function shouldQueue($event): bool
    {
        return $this->blacklist->isBlacklistEnabled();
    }
}
