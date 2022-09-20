<?php

namespace Database\Factories;

use App\Api\Models\ChatConversation;
use App\Api\Models\ChatGroupUser;
use App\Api\Models\ChatRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatRecordGroupFactory extends Factory
{
    protected $model = ChatRecord::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $groupUserRow = ChatGroupUser::query()->inRandomOrder()->first();
        return [
            'sender'   => $groupUserRow->user_id,
            'receiver' => $groupUserRow->id,
            'content'  => $this->faker->realText,
            'scene'    => ChatConversation::CONVERSATION_GROUP,
        ];
    }
}
