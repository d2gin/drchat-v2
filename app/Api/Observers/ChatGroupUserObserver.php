<?php


namespace App\Api\Observers;


use App\Api\Models\ChatConversation;
use App\Api\Models\ChatGroup;
use App\Api\Models\ChatGroupUser;

class ChatGroupUserObserver
{
    public function created(ChatGroupUser $chatGroupUser)
    {
        // 处理新群主
        $group = ChatGroup::query()->find($chatGroupUser->group_id);
        if ($group && $group->founder === 0) {
            $group->founder = $chatGroupUser->user_id;
        }
        $conversationModel = new ChatConversation();
        // 创建一个会话
        $conversationModel->createGroupConversation($chatGroupUser->user_id, $chatGroupUser->group_id, 0);
    }

    public function deleted(ChatGroupUser $chatGroupUser)
    {
        // 删掉会话记录
        ChatConversation::query()
            ->where('user_id', $chatGroupUser->user_id)
            ->where('object_id', $chatGroupUser->group_id)
            ->where('object_type', ChatConversation::CONVERSATION_GROUP)
            ->delete();
        $group = ChatGroup::query()->find($chatGroupUser->group_id);
        // 如果群主退群
        if ($group && $chatGroupUser->user_id == $group->founder) {
            $group->founder = 0;
            $new_founder    = ChatGroupUser::query()->where('group_id', $chatGroupUser->group_id)->orderBy('join_at')->value('user_id');
            if ($new_founder) {
                $group->founder = $new_founder;
            }
            $group->save();
        }
    }
}