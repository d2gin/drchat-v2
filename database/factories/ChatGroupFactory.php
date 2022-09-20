<?php

namespace Database\Factories;

use App\Api\Models\ChatGroup;
use App\Api\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatGroupFactory extends Factory
{
    protected $model = ChatGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'    => $this->faker->word,
            'avatar'  => 'https://s2.loli.net/2022/09/02/J1stYe24f6cWHuQ.png',
            'founder' => User::query()->inRandomOrder()->value('id'),
        ];
    }
}
