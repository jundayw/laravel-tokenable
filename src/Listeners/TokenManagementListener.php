<?php

namespace Jundayw\Tokenable\Listeners;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Jundayw\Tokenable\Contracts\Auth\Authenticable;
use Jundayw\Tokenable\Events\AccessTokenCreated;
use Jundayw\Tokenable\Events\AccessTokenRevoked;

class TokenManagementListener extends ShouldQueueable
{
    /**
     * @inheritdoc
     *
     * @param AccessTokenCreated $event
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
            ->when(static function (Builder $builder) use ($event) {
                return $event->getConfig('allow_multi_platforms') ? $builder->getModel()
                    ->newQuery()
                    ->selectRaw("MAX({$builder->getModel()->getKeyName()}) as latest")
                    ->groupBy('platform') : false;
            }, static function (Builder $builder, Builder $latest) use ($event) {
                return $builder->whereNotIn($builder->getModel()->getKeyName(), $latest)
                    ->when($event->getConfig('multi_platform_tokens'), function (Builder $builder, array $values) {
                        $builder->whereNotIn('platform', $values);
                    });
            }, static function (Builder $builder) {
                return $builder->whereKeyNot($builder->getModel());
            })
            ->chunkById(10, static fn(Collection $collection) => $collection->each(function (Authenticable $authorization) use ($event) {
                if ($authorization->delete()) {
                    event(new AccessTokenRevoked($authorization->getAttributes()));
                }
            }));
    }

    /**
     * Determine whether the listener should be queued.
     *
     * @param AccessTokenCreated $event
     *
     * @return bool
     */
    public function shouldQueue($event): bool
    {
        return $event->getConfig('enabled', false);
    }
}
