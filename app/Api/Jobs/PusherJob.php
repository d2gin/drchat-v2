<?php


namespace App\Api\Jobs;


use App\Api\Services\Pusher;
use App\Api\Services\WebsocketEvents;
use Illuminate\Queue\Jobs\Job;

class PusherJob extends JobDispatcher
{
    public function fire(Job $job, $data)
    {
        $channel = $data['channel'] ?? '';
        $event   = $data['event'] ?? '';
        if (!$channel || !$event) return true;
        Pusher::instance()->channel($channel)->emit($event, $data['data']);
        return true;
    }
}
