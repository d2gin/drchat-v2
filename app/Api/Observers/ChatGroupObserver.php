<?php

namespace App\Api\Observers;

use App\Api\Models\ChatConversation;
use App\Api\Models\ChatGroup;
use App\Api\Models\ChatGroupUser;

class ChatGroupObserver
{
    public function created(ChatGroup $chatGroup)
    {
        // 把群主拉到群里
        if (!ChatGroupUser::query()->firstOrCreate([
            'group_id' => $chatGroup->id,
            'user_id'  => $chatGroup->founder,
        ], ['join_at' => $chatGroup->created_at,])) {
            throw new \Exception('丢失群主');
        }
        $conversationModel = new ChatConversation();
        // 创建一个会话
        $conversationModel->createGroupConversation($chatGroup->founder, $chatGroup->id, 0);
    }

    public function deleted()
    {
    }
}
