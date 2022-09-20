<?php

namespace App\Api\Models;

use App\Concerns\TimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Friend extends Model
{
    use HasFactory;
    use TimeFormat;
    use SoftDeletes;

    protected        $table     = 'icy8_friend';
    protected        $appends   = ['realname'];
    protected static $unguarded = true;

    /**
     * 好友显示的名称
     * @return mixed
     */
    public function getRealnameAttribute()
    {
        if (!empty($this->remark_name)) {
            return $this->remark_name;
        }
        return $this->friend->nickname;
    }

    /**
     * 好友信息
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function friend()
    {
        return $this->hasOne(User::class, 'id', 'friend_id')->select(['id', 'username', 'nickname', 'avatar', 'sex', 'signature', 'city', 'is_online']);
    }

    /**
     * 是不是好友关系
     * @param int $friend_id
     * @param $me
     * @return bool
     */
    public function isFriend(int $friend_id, $me)
    {
        $isMyFriend  = Friend::query()->where('user_id', $me)->where('friend_id', $friend_id)->count();
        $isHisFriend = Friend::query()->where('user_id', $friend_id)->where('friend_id', $me)->count();
        return $isMyFriend && $isHisFriend;
    }

    public function createFriend($userId, $friendId, $remarkName = '', $source = FriendRequest::REQUEST_DEFAULT_SOURCE)
    {
        return DB::transaction(function () use ($source, $remarkName, $userId, $friendId) {
            // 生成好友关系
            $relations = [
                // 请求者
                Friend::withTrashed()->firstOrCreate(
                    ['user_id' => $userId, 'friend_id' => $friendId,],
                    ['remark_name' => $remarkName, 'source' => '通过' . $source . '添加',]
                ),
                // 接受者
                Friend::withTrashed()->firstOrCreate(
                    ['user_id' => $friendId, 'friend_id' => $userId,],
                    ['source' => "对方通过{$source}添加",]
                ),
            ];
            // 复用旧记录
            foreach ($relations as $item) {
                if ($item->trashed()) {
                    // 使用新的备注名
                    if ($item->user_id == $userId) {
                        $item->remark_name = $remarkName;
                    }
                    $item->restore();
                }
            }
            return true;
        });
    }
}
