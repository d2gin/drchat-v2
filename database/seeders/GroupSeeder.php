<?php

namespace Database\Seeders;

use Database\Factories\ChatGroupFactory;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ChatGroupFactory::new()->count(10000)->create();
    }
}
