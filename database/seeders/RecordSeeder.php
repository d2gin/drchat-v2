<?php

namespace Database\Seeders;

use Database\Factories\ChatRecordFactory;
use Database\Factories\ChatRecordGroupFactory;
use Illuminate\Database\Seeder;

class RecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ChatRecordFactory::new()->count(10000)->create();
        ChatRecordGroupFactory::new()->count(10000)->create();
    }
}
