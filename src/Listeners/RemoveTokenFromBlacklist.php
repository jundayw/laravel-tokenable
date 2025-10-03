<?php

namespace Jundayw\Tokenable\Listeners;

use Jundayw\Tokenable\Contracts\Blacklist;
use Jundayw\Tokenable\Events\AccessTokenEvent;

class RemoveTokenFromBlacklist extends ShouldQueueable
{
    public function __construct(
        protected Blacklist $blacklist,
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
        $this->blacklist->forget($event->getAuthorization()->getAttribute('access_token'));
        $this->blacklist->forget($event->getAuthorization()->getAttribute('refresh_token'));
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
