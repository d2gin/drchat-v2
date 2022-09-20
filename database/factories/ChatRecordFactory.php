<?php

namespace Database\Factories;

use App\Api\Models\ChatConversation;
use App\Api\Models\ChatRecord;
use App\Api\Models\Friend;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatRecordFactory extends Factory
{
    protected $model = ChatRecord::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $friendRow = Friend::query()->inRandomOrder()->first();
        return [
            'sender'   => $friendRow->user_id,
            'receiver' => $friendRow->friend_id,
            'content'  => $this->faker->realText,
            'scene'    => ChatConversation::CONVERSATION_NORMAL,
        ];
    }
}
