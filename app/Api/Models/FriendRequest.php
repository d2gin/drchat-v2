<?php

namespace App\Api\Models;

use App\Api\Jobs\PusherJob;
use App\Api\Services\ChannelMaker;
use App\Api\Services\Pusher;
use App\Api\Services\WebsocketEvents;
use App\Concerns\TimeFormat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class FriendRequest extends Model
{
    use HasFactory;
    use TimeFormat;
    use SoftDeletes;

    protected        $table     = 'icy8_friend_request';
    protected static $unguarded = true;
    const REQUEST_DEFAULT_MESSAGE = '请求添加好友';
    const REQUEST_DEFAULT_SOURCE  = '帐号查找';
    const STATUS_NEW_REQUEST      = 1;// 新验证
    const STATUS_UNTIPS_REQUEST   = 2;// 不提示
    const STATUS_PASS_REQUEST     = 3;// 通过
    const STATUS_REFUSE_REQUEST   = 4;// 不通过

    public function senderInfo()
    {
        return $this->hasOne(User::class, 'id', 'sender')->select(['id', 'nickname', 'username', 'sex', 'avatar', 'signature']);
    }

    /**
     * 新请求 作用域
     * @param Builder $query
     * @return Builder
     */
    public function scopeNewRequest(Builder $query)
    {
        return $query->where('status', self::STATUS_NEW_REQUEST);
    }

    /**
     * 添加好友请求
     * @param $friend_id
     * @param $me
     * @param array $data
     * @return bool
     */
    public function createRequest($friend_id, $me, $data = [])
    {
        $frq = new FriendRequest([
            'sender'      => $me,
            'receiver'    => $friend_id,
            'message'     => $data['message'] ?? '',
            'source'      => $data['source'] ?? '',
            'remark_name' => $data['remark_name'] ?? '',
        ]);
        // 不关注true/false 因为观察器会通过false来阻止新增记录
        $frq->save();
        PusherJob::push([
            'channel' => ChannelMaker::chat($friend_id),
            'event'   => WebsocketEvents::FRIEND_NEW_REQUEST,
            'data'    => $frq->toArray(),
        ]);
        return true;
    }

    /**
     * 通过请求 连贯方法
     * @return bool|mixed
     */
    public function passRequest()
    {
        if (!$this->exists) return true;
        return DB::transaction(function () {
            $this->status = self::STATUS_PASS_REQUEST;
            $this->save();
            // websocket通知
            Pusher::instance()->channel(ChannelMaker::chat($this->receiver))->emit(WebsocketEvents::FRIEND_REQUEST_PASS, $this->toArray());
            // @todo 替换为消息队列
//            PusherJob::push([
//                'channel' => ChannelMaker::chat($this->receiver),
//                'event'   => WebsocketEvents::FRIEND_REQUEST_PASS,
//                'data'    => $this->toArray(),
//            ]);
        });
    }

    /**
     * 拒绝请求 连贯方法
     * @return bool|mixed
     */
    public function refuseRequest()
    {
        if (!$this->exists) return true;
        return DB::transaction(function () {
            $this->status = self::STATUS_REFUSE_REQUEST;
            return $this->save();
        });
    }
}
