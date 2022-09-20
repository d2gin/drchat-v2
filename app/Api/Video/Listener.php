<?php

namespace App\Api\Video;

use App\Api\Services\ChannelMaker;
use icy8\SocketIO\Server;
use icy8\SocketIO\Socket;

//call_invite  call_invite_ask邀请应答,服务端   call_invite_feedback邀请反馈,前端
//call_ring    call_ring_ask响铃应答,服务端
//call_ping    call_pong响铃心跳
class Listener
{
    /* @var Server $server */
    protected $server;
    /* @var Socket $server */
    protected $socket;

    public function handle(Server $server, Socket $socket)
    {
        $this->server = $server;
        $this->socket = $socket;
        $this->socket->on('video-event-publish', function ($data) {
            if ($data['event'] == 'call-up') {
                $sender = $data['data']['sender'];
                if (!$this->isOnline($sender)) {
                    $data = [
                        'friend_id' => $sender,
                        'event'     => 'call-invite-feedback',
                        'data'      => ['result' => 'outline'],
                    ];
                }
            }
            $this->publish($data);
        });
    }

    /**
     *
     * @param $friendId
     * @return bool
     */
    public function isOnline($friendId)
    {
        $channel = ChannelMaker::chat($friendId);
        $clients = $this->server->fetchRooms($channel);
        return !empty($clients);
    }

    /**
     * 向指定客户端发布事件
     * @param $event
     * @param $data
     */
    public function publish($data)
    {
        $event      = $data['event'];
        $friendId   = $data['friend_id'];
        $clientData = $data['data'] ?? null;
        $channel    = ChannelMaker::chat($friendId);
        $this->server->to($channel)->emit('video-event', [
            'event' => $event,
            'data'  => $clientData,
        ]);
    }
}