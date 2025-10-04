<?php

namespace Jundayw\Tokenable\Listeners;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Jundayw\Tokenable\Contracts\Auth\Authenticable;
use Jundayw\Tokenable\Contracts\Blacklist;
use Jundayw\Tokenable\Contracts\Token\Token;
use Jundayw\Tokenable\Events\AccessTokenRevoked;
use Jundayw\Tokenable\Events\SuspendToken;

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
     * @param SuspendToken $event
     *
     * @return void
     */
    public function handle($event): void
    {
        $event
            ->getAuthorization()
            ->newQuery()
            ->with('tokenable')
            ->when($event->isGlobal(), function (Builder $builder) use ($event) {
                $builder->where([
                    'tokenable_type' => $event->getAuthorization()->getAttribute('tokenable_type'),
                    'tokenable_id'   => $event->getAuthorization()->getAttribute('tokenable_id'),
                ]);
            }, function (Builder $builder) use ($event) {
                $builder->where([
                    'tokenable_type' => $event->getAuthorization()->getAttribute('tokenable_type'),
                    'tokenable_id'   => $event->getAuthorization()->getAttribute('tokenable_id'),
                    'platform'       => $event->getAuthorization()->getAttribute('platform'),
                ]);
            })
            ->chunkById(10, static fn(Collection $collection) => $collection->each(function (Authenticable $authorization) use ($event) {
                if ($authorization->delete()) {
                    event(new AccessTokenRevoked($authorization, $authorization->getRelation('tokenable'), app(Token::class)));
                }
            }));

        $keys = [get_class($event->getTokenable()), $event->getTokenable()->getKey()];

        if (!$event->isGlobal()) {
            $keys[] = $event->getAuthorization()->getAttribute('platform');
        }

        $this->blacklist->forever(implode(':', $keys), 1);
    }

    /**
     * Determine whether the listener should be queued.
     *
     * @param SuspendToken $event
     *
     * @return bool
     */
    public function shouldQueue($event): bool
    {
        return $this->blacklist->isBlacklistEnabled();
    }
}
