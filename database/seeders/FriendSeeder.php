<?php

namespace Database\Seeders;

use App\Api\Models\ChatGroup;
use App\Api\Models\ChatGroupUser;
use App\Api\Models\Friend;
use App\Api\Models\User;
use Illuminate\Database\Seeder;

class FriendSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createFriends(1000);
    }

    public function createFriends($nums = 1000)
    {
        $friendModel = new Friend();
        $total       = 1;
        foreach (User::query()->inRandomOrder()->cursor() as $user) {
            foreach (User::query()->where('id', '<>', $user->id)->inRandomOrder()->cursor() as $friend) {
                $total++;
                $friendModel->createFriend($user->id, $friend->id);
                if ($total > $nums) {
                    break(2);
                }
            }
        }
    }
}
