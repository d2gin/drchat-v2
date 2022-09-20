<?php

namespace App\Api\Models;

use App\Concerns\TimeFormat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class ChatRecord extends Model
{
    use HasFactory;
    use SoftDeletes;
    use TimeFormat;

    protected        $table     = 'icy8_chat_record';
    protected        $appends   = ['sender_realname', 'brief'];
    protected static $unguarded = true;

    public function scopeNormalRecord(Builder $query)
    {
        return $query->where('type', ChatConversation::CONVERSATION_NORMAL);
    }

    public function scopeGroupRecord(Builder $query)
    {
        return $query->where('type', ChatConversation::CONVERSATION_GROUP);
    }

    public function getSenderRealnameAttribute()
    {
        if (App::runningInConsole()) return '';
        $me = Request::instance()->userId();
        // 自己
        if ($me == $this->sender) return '';
        // 好友
        $friend = Friend::query()
            ->where('friend_id', $this->sender)
            ->where('user_id', $me)
            ->first();
        if ($friend) return $friend->realname;
        // 非好友
        return User::query()->where('id', $this->sender)->value('nickname');
    }

    public function getBriefAttribute()
    {
        if (!$this->content) return '';
        return Str::limit($this->content);
    }

    public function senderInfo()
    {
        return $this->hasOne(User::class, 'id', 'sender')->select(['id', 'nickname', 'signature', 'avatar']);
    }
}
