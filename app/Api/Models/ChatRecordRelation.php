<?php

namespace App\Api\Models;

use App\Concerns\TimeFormat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRecordRelation extends Model
{
    use HasFactory;
    use TimeFormat;

    protected        $table     = 'icy8_chat_record_relation';
    protected static $unguarded = true;
    protected        $appends   = ['sender_info'];


    public function scopeUnread(Builder $query)
    {
        return $query->where('is_read', 0);
    }

    /**
     * 聊天记录 一对一关联
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function record()
    {
        return $this->hasOne(ChatRecord::class, 'id', 'record_id')->select(['id', 'content', 'type', 'sender', 'created_at']);
    }

    public function getSenderInfoAttribute()
    {
        $user = User::query()->where('id', $this->sender)->select(['id', 'username', 'nickname', 'avatar', 'sex', 'signature', 'city'])->first();
        if (!$user) return '';
        $friendModel      = new Friend();
        $friend           = $friendModel->newQuery()->where('user_id', $this->receiver)->first();
        $user['realname'] = $friend ? $friend->realname : $user->nickname;
        return $user;
    }

    /**
     * 用户的聊天记录
     * @param $userId
     * @param $sender
     * @param bool $isGroup
     * @return Builder|\Illuminate\Support\Traits\Conditionable|mixed
     */
    public function recordsForUser($userId, $sender, $isGroup = false)
    {
        return $this->newQuery()
            ->where('receiver', $userId)
            ->when($sender, function (Builder $query) use ($isGroup, $userId, $sender) {
                $mix = $this->calMix($sender, $userId);
                $query->where('mix', $isGroup ? $sender : $mix);
            })
            ->where('scene', $isGroup ? ChatConversation::CONVERSATION_GROUP : ChatConversation::CONVERSATION_NORMAL)
            ->with(['record']);
    }

    public function clearUnread($ids)
    {
        if (!is_array($ids)) $ids = explode(',', $ids);
        return $this->newQuery()->whereIn('id', $ids)->update(['is_read' => 1]);
    }

    /**
     * 计算会话的融合值
     * @param $sender
     * @param $receiver
     * @return mixed
     */
    public function calMix($sender, $receiver)
    {
        return $sender + $receiver;
    }
}
