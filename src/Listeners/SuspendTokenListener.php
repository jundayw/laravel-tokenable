<?php

namespace Jundayw\Tokenable\Listeners;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Jundayw\Tokenable\Contracts\Auth\Authenticable;
use Jundayw\Tokenable\Events\AccessTokenRevoked;
use Jundayw\Tokenable\Events\SuspendTokenCreated;

class SuspendTokenListener extends ShouldQueueable
{
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
    }
}
