<?php


namespace App\Api\Observers;


use App\Api\Models\ChatConversation;
use App\Api\Models\ChatGroupUser;
use App\Api\Models\ChatRecord;
use App\Api\Models\ChatRecordRelation;

class ChatRecordObserver
{

    public function created(ChatRecord $chatRecord)
    {
        $conversationModel = new ChatConversation();
        $relationModel     = new ChatRecordRelation();
        if ($chatRecord->scene == ChatConversation::CONVERSATION_NORMAL) {
            // 私聊
            // 我方消息关联 发给自己
            $relationModel->newQuery()->firstOrCreate([
                'sender'    => $chatRecord->sender,
                'receiver'  => $chatRecord->sender,// 发送者和接收者都是自己
                'mix'       => $relationModel->calMix($chatRecord->sender, $chatRecord->receiver),// 双方的融合值
                'record_id' => $chatRecord->id,
                'scene'     => $chatRecord->scene,
                'is_read'   => 1,
                'direction' => 'reverse',
            ]);
            // 对方消息关联
            $relationModel->newQuery()->firstOrCreate([
                'sender'    => $chatRecord->sender,
                'receiver'  => $chatRecord->receiver,
                'mix'       => $relationModel->calMix($chatRecord->sender, $chatRecord->receiver),
                'record_id' => $chatRecord->id,
                'scene'     => $chatRecord->scene,
                'direction' => 'forward',
            ]);
            // 创建会话记录
            $conversationModel->createNormalConversation($chatRecord->sender, $chatRecord->receiver, $chatRecord->id);
        } else if ($chatRecord->scene == ChatConversation::CONVERSATION_GROUP) {
            // 群聊
            $relations  = [];
            $groupId    = $chatRecord->receiver;
            $groupUsers = ChatGroupUser::query()->where('group_id', $groupId)->orderByDesc('created_at')->get();
            foreach ($groupUsers as $groupUser) {
                $isRead    = 0;
                $direction = 'forward';
                if ($groupUser->user_id == $chatRecord->sender) {
                    // 发给自己的消息标记为已读
                    $isRead    = 1;
                    $direction = 'reverse';
                }
                $relations[] = ChatRecordRelation::query()->firstOrCreate([
                    'sender'    => $chatRecord->sender,// 我发消息
                    'receiver'  => $groupUser->user_id,// 发给群员
                    'mix'       => $groupId,
                    'record_id' => $chatRecord->id,
                    'is_read'   => $isRead,
                    'scene'     => $chatRecord->scene,
                    'direction' => $direction,
                ]);
                // 创建群员的会话
                $conversationModel->createGroupConversation($chatRecord->sender, $groupId, $chatRecord->id);
                $conversationModel->createGroupConversation($groupUser->user_id, $groupId, $chatRecord->id);
            }
        }
    }

    public function deleted(ChatRecord $chatRecord)
    {
        ChatRecordRelation::query()->where('record_id', $chatRecord->id)->delete();
    }
}
