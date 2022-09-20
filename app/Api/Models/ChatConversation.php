<?php

namespace App\Api\Models;

use App\Api\Jobs\PusherJob;
use App\Api\Services\ChannelMaker;
use App\Api\Services\Pusher;
use App\Api\Services\WebsocketEvents;
use App\Api\Utils\Tool;
use App\Concerns\TimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ChatConversation extends Model
{
    use HasFactory;
    use TimeFormat;

    const CONVERSATION_NORMAL = 1;// 私聊
    const CONVERSATION_GROUP  = 2;// 群聊

    protected        $table     = 'icy8_chat_conversation';
    protected        $appends   = ['record', 'unread', 'object_info', 'datetime'];
    protected static $unguarded = true;
    protected        $error     = '';

    /**
     * 群聊聊天记录关联
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function groupRecord()
    {
        return $this->hasOne(ChatRecord::class, 'id', 'record_id')->where('scene', self::CONVERSATION_GROUP);
    }

    /**
     * 私聊聊天记录关联
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function normalRecord()
    {
        return $this->hasOne(ChatRecord::class, 'id', 'record_id')->where('scene', self::CONVERSATION_NORMAL);
    }


    /**
     * 会话信息获取器
     * @return string[]
     */
    public function getObjectInfoAttribute()
    {
        $info = ['avatar' => '', 'title' => '', 'is_online' => 0,];
        if ($this->object_type == self::CONVERSATION_NORMAL) {
            // 私聊
            $friend            = Friend::query()->where([
                'friend_id' => $this->object_id,
                'user_id'   => $this->user_id,
            ])->with('friend')->first();
            $user              = $friend ? $friend->friend : null;
            $info['title']     = $friend ? $friend->realname : '';
            $info['avatar']    = $user ? $user->avatar : '';
            $info['is_online'] = $friend ? $friend->friend->is_online : 0;
        } else if ($this->object_type == self::CONVERSATION_GROUP) {
            // 群聊
            $group          = ChatGroupUser::query()
                    ->where('group_id', $this->object_id)
                    ->where('user_id', $this->user_id)->with('group')->first()['group'] ?? null;
            $info['title']  = $group ? $group['name'] : '';
            $info['avatar'] = $group ? $group['avatar'] : '';
        }
        return $info;
    }

    /**
     * 最新的一条聊天记录
     * @return \Illuminate\Database\Eloquent\Builder|Model|object|null
     */
    public function getRecordAttribute()
    {
        $record = ChatRecord::query()
            ->where('id', $this->record_id)
            ->where('scene', $this->object_type)
            ->select(['id', 'content', /*'sender', 'receiver', 'type', 'scene', 'created_at'*/])->first();
        return $record ? $record->makeHidden(['content', 'sender_realname']) : null;
    }

    public function getDatetimeAttribute()
    {
        return Tool::smartDate(strtotime($this->updated_at), true);
    }

    /**
     * 未读数量
     * @return int
     */
    public function getUnreadAttribute()
    {
        return ChatRecordRelation::query()
            ->where('receiver', $this->user_id)
            ->where('sender', $this->object_id)
            ->where('scene', $this->object_type)
            ->where('is_read', 0)->count();
    }

    /**
     * 发送消息
     * @param $content
     * @param int $receiver
     * @param int $sender
     * @param bool $isGroup
     * @param int $type
     * @return bool
     */
    public function send($content, int $receiver, int $sender, $isGroup = false, $type = 0)
    {
        try {
            // 保存聊天记录
            $chatRecord = new ChatRecord([
                'content'  => $content,
                'sender'   => $sender,
                'receiver' => $receiver,
                'scene'    => $isGroup ? self::CONVERSATION_GROUP : self::CONVERSATION_NORMAL,
                'type'     => $type,
            ]);
            $res        = DB::transaction(function () use ($chatRecord) {
                $chatRecord->save();
                return true;
            });
            if (!$res) return false;
            // websocket
            Pusher::instance()
                ->channel(ChannelMaker::chat($receiver, $isGroup))
                ->emit(WebsocketEvents::CHAT_NEW_MESSAGE, $chatRecord->newQuery()->with('senderInfo')->find($chatRecord->id)->toArray());
            // @todo 替换为消息队列
            //            PusherJob::push([
            //                'channel' => ChannelMaker::chat($receiver, $isGroup),
            //                'event'   => WebsocketEvents::CHAT_NEW_MESSAGE,
            //                'data'    => $chatRecord->newQuery()->with('senderInfo')->find($chatRecord->id)->toArray(),
            //            ]);
        } catch (\Throwable $e) {
            return false;
        }
        return true;
    }

    /**
     * 创建私聊会话 双向会话
     * @param $sender
     * @param $receiver
     * @param $chatRecordId
     */
    public function createNormalConversation($sender, $receiver, $chatRecordId)
    {
        try {
            return Db::transaction(function () use ($chatRecordId, $receiver, $sender) {
                // 发送者会话
                if (!$this->createConversation($sender, $receiver, $chatRecordId, self::CONVERSATION_NORMAL)) {
                    throw new \Exception('fail');
                }
                // 接收者会话
                if (!$this->createConversation($receiver, $sender, $chatRecordId, self::CONVERSATION_NORMAL)) {
                    throw new \Exception('fail');
                }
            });
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * 创建群组会话
     * @param $sender
     * @param $groupId
     * @param $chatRecordId
     */
    public function createGroupConversation($sender, $groupId, $chatRecordId)
    {
        return $this->createConversation($sender, $groupId, $chatRecordId, self::CONVERSATION_GROUP);
    }

    /**
     * 创建指定类型的会话
     * @param $sender
     * @param $receiver
     * @param $chatRecordId
     * @param $scene
     */
    public function createConversation($sender, $receiver, $chatRecordId, $scene)
    {
        $senderConversation = ChatConversation::query()->firstOrCreate([
            'user_id'     => $sender,
            'object_id'   => $receiver,
            'object_type' => $scene,
        ], $chatRecordId ? ['record_id' => $chatRecordId,] : []);
        if (!$senderConversation->wasRecentlyCreated) {
            if ($chatRecordId) $senderConversation->record_id = $chatRecordId;
            if (!$senderConversation->save()) {
                return false;
            }
        }
        // 通知
        Pusher::instance()->channel(ChannelMaker::chat($sender))->emit(WebsocketEvents::REFRESH_CONVERSATION, $senderConversation->toArray());
        //        PusherJob::push([
        //            'channel' => ChannelMaker::chat($sender),
        //            'event'   => WebsocketEvents::REFRESH_CONVERSATION,
        //            'data'    => $senderConversation->toArray(),
        //        ]);
        return $senderConversation->id;
    }

    /**
     * @param $conversation
     * @return bool
     */
    public function validateConversation($conversation)
    {
        $groupModel  = new ChatGroup();
        $friendModel = new Friend();
        if (!$conversation) {
            return false;
        }
        $isGroup  = $conversation['object_type'] == ChatConversation::CONVERSATION_GROUP;
        $receiver = $conversation['object_id'];
        $userId   = $conversation['user_id'];
        if ($isGroup) {
            if (!$groupModel->isMember($receiver, $userId)) return false;
        } else if (!$friendModel->isFriend($receiver, $userId)) {
            return false;
        }
        return true;
    }

    /**
     *
     * @param $sender
     * @param $receiver
     * @param bool $isGroup
     * @return bool
     */
    public function validateReceiver($sender, $receiver, $isGroup = false)
    {
        $groupModel  = new ChatGroup();
        $friendModel = new Friend();
        if (!$receiver) {
            $this->error = 'error';
            return false;
        } else if ($isGroup) {
            if (!$groupModel->isMember($receiver, $sender)) {
                $this->error = '你不是群员';
                return false;
            }
        } else if (!$friendModel->isFriend($receiver, $sender)) {
            $this->error = '双方不是好友关系';
            return false;
        }
        return true;
    }

    public function getError()
    {
        return $this->error;
    }
}
