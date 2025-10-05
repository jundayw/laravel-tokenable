---
title: Token Queue Configuration
lang: en-US
---

# Token Queue Configuration

```php
/*
|--------------------------------------------------------------------------
| Token Queue Configuration
|--------------------------------------------------------------------------
|
| This section defines the queue connection and queue name used for
| processing token-related jobs, such as issuing, refreshing, or
| revoking tokens asynchronously.
|
| You can configure the connection to use any queue driver supported
| by Laravel, e.g., "sync", "database", "redis", "sqs", etc.
|
*/

'queue' => [

    /*
    |--------------------------------------------------------------------------
    | Token Queue Connection
    |--------------------------------------------------------------------------
    |
    | The queue connection to use for token jobs.
    |
    | If set to 'sync', jobs will be executed immediately in the current process.
    |
    */

    'connection' => env('TOKEN_QUEUE_CONNECTION', env('QUEUE_CONNECTION', 'sync')),

    /*
    |--------------------------------------------------------------------------
    | Token Queue's Work Queue
    |--------------------------------------------------------------------------
    |
    | The name of the queue where token jobs will be pushed.
    |
    | This is useful for separating token jobs from other application jobs and
    | allows prioritization or dedicated workers.
    |
    */

    'queue' => env('TOKEN_QUEUE', 'default'),
],
```

```shell
php artisan queue:work
```
