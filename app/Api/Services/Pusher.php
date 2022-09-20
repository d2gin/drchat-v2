<?php


namespace App\Api\Services;


use icy8\EventPusher\Pusher as PusherKernal;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Config;

class Pusher
{

    protected $channel;
    protected $pusher;
    static    $instance;

    public function __construct()
    {
        $this->pusher = new PusherKernal(Config::get('chat.event_pusher_url', 'http://127.0.0.1:' . Config::get('chat.http_port', '9580')));
    }

    static public function instance()
    {
        return new static();
    }

    public function channel($name)
    {
        $this->channel = $name;
        return $this;
    }

    public function emit($event, $data, $channel = '')
    {
        if ($channel) {
            $this->channel($channel);
        }
        return $this->pusher->trigger($event, $this->channel, $data);
    }

    public function broacast($event, $data)
    {
        return $this->pusher->broadcast($event, $data);
    }
}
