<?php


namespace App\Api\Jobs;


use Illuminate\Support\Facades\Queue;

class JobDispatcher
{
    static public function push(...$args)
    {
        $job = static::class;
        return Queue::push($job, ...$args);
    }

    static public function pushAction($method, ...$args)
    {
        $job = static::class . '@' . $method;
        return Queue::push($job, ...$args);
    }
}