<?php

namespace Database\Seeders;

use Database\Factories\ChatGroupFactory;
use Database\Factories\ChatUserFactory;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ChatUserFactory::new()->count(1000)->create();
    }
}
