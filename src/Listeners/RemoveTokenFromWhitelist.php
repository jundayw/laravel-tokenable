<?php

namespace Jundayw\Tokenable\Listeners;

use Jundayw\Tokenable\Contracts\Whitelist;
use Jundayw\Tokenable\Events\AccessTokenEvent;

class RemoveTokenFromWhitelist extends ShouldQueueable
{
    public function __construct(
        protected Whitelist $whitelist,
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
        $this->whitelist->forget($event->getAuthorization()->getAttribute('access_token'));
        $this->whitelist->forget($event->getAuthorization()->getAttribute('refresh_token'));
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
        return $this->whitelist->isWhitelistEnabled();
    }
}
