<?php

namespace Jundayw\Tokenable\Listeners;

use Jundayw\Tokenable\Contracts\Whitelist;
use Jundayw\Tokenable\Events\AccessTokenEvent;

class AddTokenToWhitelist extends ShouldQueueable
{
    public function __construct(
        protected Whitelist $whitelist
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
        $this->whitelist->put(
            $event->getAuthorization()->getAttribute('access_token'),
            $event->getAuthorization(),
            $event->getAuthorization()->getAttribute('access_token_expire_at'),
        );
        $this->whitelist->put(
            $event->getAuthorization()->getAttribute('refresh_token'),
            $event->getAuthorization(),
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
        return $this->whitelist->isWhitelistEnabled();
    }
}
