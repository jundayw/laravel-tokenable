<?php

namespace Jundayw\Tokenable\Listeners;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Jundayw\Tokenable\Contracts\Auth\Authenticable;
use Jundayw\Tokenable\Contracts\Blacklist;
use Jundayw\Tokenable\Events\AccessTokenRevoked;
use Jundayw\Tokenable\Events\SuspendTokenCreated;

class SuspendTokenListener extends ShouldQueueable
{
    public function __construct(
        protected Blacklist $blacklist,
    ) {
        //
    }

    /**
     * @inheritdoc
     *
     * @param SuspendTokenCreated $event
     *
     * @return void
     */
    public function handle($event): void
    {
        $event
            ->getAuthorization()
            ->newQuery()
            ->with('tokenable')
            ->where([
                'tokenable_type' => $event->getAuthorization()->getAttribute('tokenable_type'),
                'tokenable_id'   => $event->getAuthorization()->getAttribute('tokenable_id'),
            ])
            ->unless($event->isGlobal(), function (Builder $builder) use ($event) {
                $builder->where([
                    'platform' => $event->getAuthorization()->getAttribute('platform'),
                ]);
            })
            ->chunkById(10, static fn(Collection $collection) => $collection->each(function (Authenticable $authorization) use ($event) {
                if ($authorization->delete()) {
                    event(new AccessTokenRevoked($authorization->getAttributes()));
                }
            }));

        $keys = [$event->getAttribute('tokenable_type'), $event->getAttribute('tokenable_id')];

        if (!$event->isGlobal()) {
            $keys[] = $event->getAuthorization()->getAttribute('platform');
        }

        $this->blacklist->forever(implode(':', $keys), now()->toIso8601ZuluString());
    }

    /**
     * Determine whether the listener should be queued.
     *
     * @param SuspendTokenCreated $event
     *
     * @return bool
     */
    public function shouldQueue($event): bool
    {
        return $this->blacklist->isBlacklistEnabled();
    }
}
