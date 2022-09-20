<?php

namespace App\Api\Models;

use App\Concerns\TimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ChatGroup extends Model
{
    use HasFactory;
    use TimeFormat;
    use SoftDeletes;

    protected        $table     = 'icy8_chat_group';
    protected static $unguarded = true;

    /**
     * 创建群组
     * @param $name
     * @param $founder
     * @param string $avatar
     * @return mixed
     */
    public function createGroup($name, $founder, $avatar = '')
    {
        return DB::transaction(function () use ($avatar, $founder, $name) {
            return ChatGroup::query()->firstOrCreate([
                'name'    => $name,
                'founder' => $founder,
                'avatar'  => $avatar ?: 'https://s2.loli.net/2022/09/02/J1stYe24f6cWHuQ.png',
            ]);
        });
    }

    /**
     * 是否为群成员
     * @param $group_id
     * @param $user_id
     * @return bool
     */
    public function isMember($group_id, $user_id)
    {
        return ChatGroupUser::query()->where([
            ['group_id', '=', $group_id],
            ['user_id', '=', $user_id],
        ])->count() ? true : false;
    }
}
