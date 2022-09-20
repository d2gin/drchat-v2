<?php


namespace App\Api\Controllers;


use App\Api\Http\ApiResponse;
use App\Api\Models\ChatConversation;
use App\Api\Models\ChatExpression;
use App\Api\Models\ChatGroup;
use App\Api\Models\ChatRecordRelation;
use App\Api\Models\Friend;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ChatController extends Controller
{

    /**
     * 发送消息
     * @param Request $request
     * @return ApiResponse
     */
    public function send(Request $request, $scene = 'normal')
    {
        $content = htmlspecialchars($request->post('content'));
        $sendTo  = $request->post('send_to');
        $userId  = $request->userId();
        $model   = new ChatConversation();
        $isGroup = $scene == 'group';
        if (!$model->validateReceiver($userId, $sendTo, $isGroup)) {
            return ApiResponse::error($model->getError());
        } else if (trim($content) === '') {
            return ApiResponse::error('消息内容不能为空');
        }
        // 发送消息
        if (!$model->send($content, $sendTo, $userId, $isGroup)) {
            return ApiResponse::error('发送失败');
        }
        return ApiResponse::success('发送成功');
    }

    /**
     * 聊天记录
     * @param Request $request
     * @return ApiResponse
     */
    public function records(Request $request)
    {
        $page           = $request->get('page', 1);
        $limit          = $request->get('limit', 20);
        $conversationId = $request->post('conversation_id');
        $userId         = $request->userId();
        $model          = new ChatConversation();
        $relationModel  = new ChatRecordRelation();
        $conversation   = $model->newQuery()->where('user_id', $userId)->where('id', $conversationId)->first();
        if (!$model->validateConversation($conversation)) {
            return ApiResponse::error('会话异常');
        }
        $sender  = $conversation['object_id'];
        $isGroup = $conversation['object_type'] === ChatConversation::CONVERSATION_GROUP;
        // 标记为已读
        $relationModel->recordsForUser($userId, $sender, $isGroup)->update(['is_read' => 1]);
        // 消息列表
        $list = $relationModel->recordsForUser($userId, $sender, $isGroup)->orderByDesc('created_at')->forPage($page, $limit)->get()->reverse()->values();
        return ApiResponse::success([
            'conversation' => $conversation,
            'list'         => $list,
        ]);
    }

    public function expressions(Request $request)
    {
        $list = ChatExpression::query()->where('status', 1)->orderByDesc('id')->get();
        return ApiResponse::success([
            'list' => $list,
        ]);
    }
}
