<?php

namespace App\Console\Commands;

use App\Api\Models\ChatGroup;
use App\Api\Models\Friend;
use App\Api\Models\User;
use App\Api\Services\ChannelMaker;
use App\Api\Services\Jwt;
use App\Api\Services\WebsocketEvents;
use App\Api\Video\Listener as VideoListen;
use icy8\SocketIO\Server;
use icy8\SocketIO\Socket;
use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Console\Input\InputOption;
use Workerman\Worker;

class Websocket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'websocket';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'socket.io for websocket';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Application $app)
    {
        $websocket_port      = Config::get('chat.websocket_port');
        $http_port           = Config::get('chat.http_port');
        $server              = new Server('websocket://0.0.0.0:' . $websocket_port, [
            'ssl' => [
                'local_pk'    => $app->configPath() . '/server.key',
                'local_cert'  => $app->configPath() . '/server.pem',
                'verify_peer' => false,
            ],
        ]);
        $server->httpHost    = '0.0.0.0:' . $http_port;
        $server->httpLimitIp = '127.0.0.1';
        if ($this->option('daemon')) {
            Worker::$daemonize = true;
        }
        $server->on('connection', function (Socket $socket) use ($server) {
            $token = $socket->request->query['token'];
            if (!$user = $this->validateUserToken($token)) {
                $socket->close('Illegal token.');// 踢掉客户端
                return;
            }
            $socket->user = $user;
            $video        = new VideoListen();
            $video->handle($server, $socket);
        });
        // 订阅
        $server->on('subcribe', function (Socket $socket, $channel, $data) use ($server) {
            $normalChannelPrefix = ChannelMaker::chat('', false);
            $groupChannelPrefix  = ChannelMaker::chat('', true);
            if (strpos($channel, $normalChannelPrefix) === 0) {//订阅普通频道消息
                $user_id = (int)str_replace($normalChannelPrefix, '', $channel);
                if ($user_id !== $socket->user->id) { // 恶意订阅
                    $socket->close();// 踢掉
                    return;
                }
                // 用户上线
                User::query()->where('id', $user_id)->update(['is_online' => 1]);
                $this->broadcastFriendOnline($user_id, $server);
            } else if (strpos($channel, $groupChannelPrefix) === 0) {// 订阅群组消息
                $user_id    = $socket->user->id;
                $group_id   = (int)str_replace($groupChannelPrefix, '', $channel);
                $groupModel = new ChatGroup();
                if ($socket->user->id !== $user_id || !$groupModel->isMember($group_id, $user_id)) {// 恶意订阅
                    $server->leave($channel, $socket->id);
                    return;
                }
            }
        });

        // 退订
        $server->on('unsubcribe', function (Socket $socket, $channel, $data) use ($server) {
            $normalChannelPrefix = ChannelMaker::chat('', false);
            $groupChannelPrefix  = ChannelMaker::chat('', true);
            $rooms               = $server->fetchRooms($channel);
            if (strpos($channel, $normalChannelPrefix) === 0 && empty($rooms)) {
                // 用户下线
                User::query()->where('id', $socket->user->id)->update(['is_online' => 0]);
                $this->broadcastFriendOnline($socket->user->id, $server);
            } else if (strpos($channel, $groupChannelPrefix) === 0) {
                $group_id   = (int)str_replace($groupChannelPrefix, '', $channel);
                $groupModel = new ChatGroup();
                if (!$groupModel->isMember($group_id, $socket->user->id)) { // 恶意下线
                    // 恢复
                    $server->join($channel, $socket->id, $data);
                }
            }
        });
        Worker::runAll();
        return 0;
    }

    protected function validateUserToken($token, $userId = null)
    {
        try {
            $jwtService = new Jwt();
            $tokenInfo  = $jwtService->decode($token);
            $uid        = $tokenInfo['uid'];//$passwordHash   = $tokenInfo['hash'];
            if ($userId !== null && $userId != $uid) {
                return false;
            }
            return User::query()->where('id', $uid)->first();
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function broadcastFriendOnline($user_id, Server $server)
    {
        $friends = Friend::query()->where('friend_id', $user_id)->select(['user_id', 'friend_id'])->get();
        foreach ($friends as $friend) {
            $server->to(ChannelMaker::chat($friend->user_id))->emit(WebsocketEvents::REFRESH_CONVERSATION, ['user_id' => $user_id]);
        }
    }

    protected function getArguments()
    {
        return [
            ['cmd'],
        ];
    }

    protected function getOptions()
    {
        return [
            ['daemon', 'd', InputOption::VALUE_NONE, 'daemon process',],
        ];
    }
}
