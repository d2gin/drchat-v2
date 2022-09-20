<?php


namespace App\Api\Controllers;


use App\Api\Http\ApiResponse;
use App\Api\Models\Friend;
use App\Api\Models\FriendRequest;
use App\Api\Models\User;
use App\Api\Services\FriendService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FriendController extends Controller
{
    /**
     * 好友列表
     * @param Request $request
     */
    public function list(Request $request)
    {
        $page  = $request->get('page', 1);
        $limit = $request->get('limit', 500);
        $list  = Friend::query()
            ->where('user_id', $request->userId())
            ->with('friend')
            ->forPage($page, $limit)
            ->get();
        return ApiResponse::success($list);
    }

    /**
     * 好友资料
     * @param Request $request
     * @return ApiResponse
     */
    public function info(Request $request)
    {
        $friend_id = $request->get('friend_id');
        $friend    = Friend::query()
            ->where('user_id', $request->userId())
            ->where('friend_id', $friend_id)
            ->with('friend')
            ->first();
        if (!$friend) {
            return ApiResponse::error('不存在该好友');
        }
        return ApiResponse::success($friend);
    }

    /**
     * 删除好友
     * @param Request $request
     */
    public function delete(Request $request)
    {
        $friend_id = $request->post('friend_id');
        $friend    = Friend::query()
            ->where('user_id', $request->userId())
            ->where('friend_id', $friend_id)->first();
        if (!$friend) {
            return ApiResponse::error('没有该好友');
        }
        $res = DB::transaction(function () use ($friend, $friend_id, $request) {
            return $friend->delete();
        });
        if ($res) {
            return ApiResponse::success('已删除');
        }
        return ApiResponse::error('删除失败');
    }

    /**
     * 精确搜索
     * @param Request $request
     * @return ApiResponse
     */
    public function preciseSearch(Request $request)
    {
        $keyword = $request->post('keyword');
        $row     = User::query()->orWhere('username', $keyword)->select(['id', 'username', 'nickname', 'avatar', 'signature', 'sex', 'city',])->first();
        return ApiResponse::success($row);
    }

    /**
     * 提交好友请求
     * @param Request $request
     * @return ApiResponse
     */
    public function request(Request $request)
    {
        $friend_id   = $request->post('friend_id');
        $message     = $request->post('message');
        $source      = $request->post('source');
        $remark_name = $request->post('remark_name');
        $model       = new FriendRequest();
        $friendModel = new Friend();
        if (!$friend_id) {
            return ApiResponse::error('参数错误');
        } else if ($friendModel->isFriend($friend_id, $request->userId())) {
            return ApiResponse::error('双方已经是好友关系');
        }
        $result = $model->createRequest($friend_id, $request->userId(), ['remark_name' => $remark_name, 'source' => $source, 'message' => $message]);
        if (!$result) {
            return ApiResponse::error('添加失败');
        }
        return ApiResponse::success('已发送请求');
    }

    /**
     * 请求列表
     * @param Request $request
     * @return ApiResponse
     */
    public function requestList(Request $request)
    {
        $page  = $request->get('page', 1);
        $limit = $request->get('limit', 20);
        $query = FriendRequest::query()
            ->where('receiver', $request->userId())
            ->with('senderInfo')
            ->forPage($page, $limit);
        $list  = $query->orderByDesc('created_at')->get();
        //
        $query->scopes('newRequest')->update(['status' => FriendRequest::STATUS_UNTIPS_REQUEST]);
        return ApiResponse::success($list);
    }

    /**
     * 处理好友请求
     * @param Request $request
     * @param $todo
     * @return ApiResponse
     */
    public function toRequest(Request $request, $todo)
    {
        $id = $request->post('request_id');
        /* @var FriendRequest $record */
        $record = FriendRequest::query()->where([
            ['id', '=', $id],
            ['receiver', '=', $request->userId()],
        ])->first();
        if (!$record) {
            return ApiResponse::error('该请求不存在');
        } else if ($record->status > 2) {
            return ApiResponse::error('该请求已被处理');
        }
        if ($todo == 'pass') {
            // 通过
            $record->passRequest();
        } else if ($todo == 'refuse') {
            // 拒绝
            $record->refuseRequest();
        }
        return ApiResponse::success('已通过');
    }

    /**
     * 好友模块的代办计数
     * @param Request $request
     * @return ApiResponse
     */
    public function hintCount(Request $request)
    {
        $count = FriendRequest::query()->where('receiver', $request->userId())->scopes('newRequest')->count();
        return ApiResponse::data($count);
    }

    /**
     * 修改备注
     * @param Request $request
     * @return ApiResponse
     */
    public function remarkName(Request $request)
    {
        $remarkName = $request->post('remark_name');
        $friendId   = $request->post('friend_id');
        $friend     = Friend::query()->where('friend_id', $friendId)->where('user_id', $request->userId())->first();
        if (!$friend) {
            return ApiResponse::error('双方不是好友关系');
        }
        $friend->remark_name = $remarkName;
        if ($friend->save()) {
            return ApiResponse::success('保存成功');
        }
        return ApiResponse::error('保存失败');
    }
}
