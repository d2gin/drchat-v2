<?php

namespace Database\Seeders;

use App\Api\Models\Friend;
use App\Api\Models\User;
use Database\Factories\ChatGroupFactory;
use Database\Factories\ChatGroupUserFactory;
use Database\Factories\ChatRecordFactory;
use Database\Factories\ChatRecordGroupFactory;
use Database\Factories\ChatUserFactory;
use Illuminate\Database\Seeder;

/**
 * 填充测试数据
 * Class FakeSeeder
 * @package Database\Seeders
 */
class FakeSeeder extends Seeder
{
    /**
     *
     * @return void
     */
    public function run()
    {
        // 填充表情数据
        $this->call([ExpressionSeeder::class,]);
        ChatUserFactory::new()->count(10000)->create();// 用户数据
        ChatGroupFactory::new()->count(10000)->create();// 群组数据
        $this->createFriends(1000);// 生成好友关系
        ChatGroupUserFactory::new()->count(1000)->create();// 群成员数据
        ChatRecordFactory::new()->count(10000)->create();// 私聊记录
        ChatRecordGroupFactory::new()->count(10000)->create();// 群聊记录
    }

    public function createFriends($nums = 1000)
    {
        $friendModel = new Friend();
        $total       = 1;
        foreach (User::query()->inRandomOrder()->cursor() as $user) {
            foreach (User::query()->where('id', '<>', $user->id)->inRandomOrder()->cursor() as $friend) {
                $total++;
                $friendModel->createFriend($user->id, $friend->id);
                if ($total > $nums) return;
            }
        }
    }
}
