<?php

namespace App\Api\Models;

use App\Concerns\TimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ChatGroupUser extends Model
{
    use HasFactory;
    use TimeFormat;

    protected        $table     = 'icy8_chat_group_user';
    protected static $unguarded = true;

    /**
     * 群组关联
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function group()
    {
        return $this->hasOne(ChatGroup::class, 'id', 'group_id');
    }

    /**
     * 群员资料关联
     */
    public function userInfo()
    {
        return $this->hasOne(User::class, 'id', 'user_id')->select(['id', 'nickname', 'sex', 'avatar', 'signature']);
    }

    /**
     * 加入群聊
     * @param $group_id
     * @param $friend_id
     * @return mixed
     */
    public function joinGroup($group_id, $user_id)
    {
        return DB::transaction(function () use ($group_id, $user_id) {
            return ChatGroupUser::query()->firstOrCreate([
                'user_id'  => $user_id,
                'group_id' => $group_id,
            ], ['join_at' => date('Y-m-d H:i:s')]);
        });
    }

    /**
     * 退出群聊
     * @param $group_id
     * @param $user_id
     * @return mixed
     */
    public function exitGroup($group_id, $user_id)
    {
        return DB::transaction(function () use ($group_id, $user_id) {
            $record = ChatGroupUser::query()
                ->where('group_id', $group_id)
                ->where('user_id', $user_id)
                ->first();
            if ($record) {
                return $record->delete();
            }
            return true;
        });
    }

    /**
     * 群成员
     * @param $group_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function members($group_id)
    {
        return $this->newQuery()->where('group_id', $group_id)->orderBy('join_at');
    }
}
