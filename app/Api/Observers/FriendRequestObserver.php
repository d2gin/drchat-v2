<?php


namespace App\Api\Observers;


use App\Api\Models\ChatConversation;
use App\Api\Models\ChatRecord;
use App\Api\Models\Friend;
use App\Api\Models\FriendRequest;
use App\Api\Services\ChannelMaker;
use App\Api\Services\ChatService;
use App\Api\Services\Pusher;
use App\Api\Services\WebsocketEvents;
use Illuminate\Support\Facades\DB;

class FriendRequestObserver
{
    public function creating(FriendRequest $friendRequest)
    {
        if (empty($friendRequest->message)) {
            $friendRequest->message = FriendRequest::REQUEST_DEFAULT_MESSAGE;
        }
        if (empty($friendRequest->source)) {
            $friendRequest->source = FriendRequest::REQUEST_DEFAULT_SOURCE;
        }
        // 如果对方没有删除自己 那就直接添加好友 不产生请求数据
        // 对方的好友关系
        $relationLog = Friend::withTrashed()->where('user_id', $friendRequest->receiver)->where('friend_id', $friendRequest->sender)->first();
        if ($relationLog && $relationLog->trashed()) {
            return true;
        }
        // 我的好友关系
        $friendLog = Friend::withTrashed()->where('user_id', $friendRequest->sender)->where('friend_id', $friendRequest->receiver)->first();
        if ($friendLog && $friendLog->trashed()) {
            $friendLog->remark_name = $friendRequest->remark_name;
            $friendLog->restore();
            return false;
        }
        return true;
    }

    public function created(FriendRequest $friendRequest)
    {
    }

    public function updating(FriendRequest $friendRequest)
    {
    }

    public function updated(FriendRequest $friendRequest)
    {
        DB::transaction(function () use ($friendRequest) {
            if ($friendRequest->isDirty('status') && $friendRequest->status == FriendRequest::STATUS_PASS_REQUEST) {
                $userId     = $friendRequest->sender;
                $friendId   = $friendRequest->receiver;
                $remarkName = $friendRequest->remark_name;
                $source     = $friendRequest->source;
                // 创建好友关系
                (new Friend())->createFriend($userId, $friendId, $remarkName, $source);
                // 发送一条聊天通知
                (new ChatConversation())->send($friendRequest->message, $friendRequest->receiver, $friendRequest->sender);
            }
            // 如果提交了多条申请
            if ($friendRequest->isDirty('status') && in_array($friendRequest->status, [
                    FriendRequest::STATUS_PASS_REQUEST,
                    FriendRequest::STATUS_REFUSE_REQUEST,
                ])) {
                $friendRequest->newQuery()->where([
                    'sender'   => $friendRequest->sender,
                    'receiver' => $friendRequest->receiver,
                ])->update(['status' => $friendRequest->status,]);
            }
        });
    }
}
