<?php

namespace Database\Factories;

use App\Api\Models\ChatGroup;
use App\Api\Models\ChatGroupUser;
use App\Api\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatGroupUserFactory extends Factory
{
    protected $model = ChatGroupUser::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $group = ChatGroup::query()->inRandomOrder()->first();
        $user  = User::query()->inRandomOrder()->first();
        return [
            'group_id' => $group->id,
            'user_id'  => $user->id,
            'join_at'  => $this->faker->dateTime,
        ];
    }
}
