<?php

namespace Database\Seeders;

use App\Api\Models\User;
use App\Api\Services\UserService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * 填充正式数据
     * @return void
     */
    public function run()
    {
        $userService = new UserService();
        $userService->register([
            'username'  => 'icy8',
            'nickname'  => 'icy8',
            'signature' => 'https://github.com/d2gin',
            'city'      => '广东',
            'avatar'    => 'https://s2.loli.net/2022/09/19/WbC82gHqt3S465w.png',
            'password'  => '111111',
        ]);
        $userService->register([
            'username'  => 'test',
            'nickname'  => 'test',
            'signature' => 'https://github.com/d2gin',
            'city'      => '广东',
            'avatar'    => 'https://s2.loli.net/2022/09/19/STVb4is62GJfMvp.png',
            'password'  => '111111',
        ]);
        // 填充表情数据
        $this->call([
            ExpressionSeeder::class,
        ]);
    }
}
