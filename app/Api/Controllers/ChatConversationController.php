<?php

namespace App\Api\Controllers;

use App\Api\Http\ApiResponse;
use App\Api\Models\ChatConversation;
use App\Api\Models\ChatGroup;
use App\Api\Models\ChatRecordRelation;
use App\Api\Models\Friend;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ChatConversationController extends Controller
{
    public function list(Request $request)
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 0);
        $user_id = $request->userId();
        $list    = ChatConversation::query()
            ->where('user_id', $user_id)
            ->orderByDesc('updated_at')
            ->when($page&& $limit, function (Builder $query) use ($page, $limit) {
                $query->forPage($page, $limit);
            })
            ->get()
            ->makeHidden(['created_at','deleted_at', 'user_id', 'record_id','object_id','object_type', 'updated_at']);
        return ApiResponse::success($list ?: null);
    }

    public function hintCount(Request $request)
    {
        $count = ChatRecordRelation::query()
            ->where('receiver', $request->userId())
            ->scopes('unread')
            ->count();
        //sleep(1);
        return ApiResponse::data($count);
    }

    public function createConversation(Request $request, $scene = 'normal')
    {
        $receiver = $request->post('receiver');
        $model    = new ChatConversation();
        $isGroup  = $scene == 'group';
        $userId   = $request->userId();
        if (!$model->validateReceiver($userId, $receiver, $isGroup)) {
            return ApiResponse::error($model->getError());
        } else if (!$conversation_id = $model->createConversation($userId, $receiver, 0, $isGroup ? ChatConversation::CONVERSATION_GROUP : ChatConversation::CONVERSATION_NORMAL)) {
            return ApiResponse::error('创建失败');
        }
        return ApiResponse::success(['conversation_id' => $conversation_id,]);
    }

    public function clearUnread(Request $request, $scene = 'normal')
    {
        $userId   = $request->userId();
        $receiver = $request->post('receiver');
        $model    = new ChatRecordRelation();
        $model->recordsForUser($userId, $receiver, $scene == 'group')->update(['is_read' => 1]);
        return ApiResponse::success('已清空');
    }
}
