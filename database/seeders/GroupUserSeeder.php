<?php

namespace Database\Seeders;

use Database\Factories\ChatGroupUserFactory;
use Illuminate\Database\Seeder;

class GroupUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ChatGroupUserFactory::new()->count(1000)->create();
    }
}
