<?php


namespace App\Api\Observers;

use App\Api\Models\ChatConversation;
use App\Api\Models\ChatRecord;
use App\Api\Models\ChatRecordRelation;
use App\Api\Models\Friend;

class FriendObserver
{
    public function created(Friend $friend)
    {
    }

    public function deleting(Friend $friend)
    {
    }

    public function deleted(Friend $friend)
    {
        // 删除对应的会话信息
        ChatConversation::query()
            ->where('user_id', $friend->user_id)
            ->where('object_id', $friend->friend_id)
            ->where('object_type', ChatConversation::CONVERSATION_NORMAL)
            ->delete();
        $relationModel = new ChatRecordRelation();
        // 删除对应的聊天记录
        ChatRecordRelation::query()->where('mix', $relationModel->calMix($friend->user_id, $friend->friend_id))->delete();
    }
}
