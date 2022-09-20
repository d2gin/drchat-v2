<?php

namespace App\Api\Controllers;

use App\Api\Http\ApiResponse;
use App\Api\Models\ChatConversation;
use App\Api\Models\ChatGroup;
use App\Api\Models\ChatGroupUser;
use App\Api\Models\Friend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatGroupController extends Controller
{
    /**
     * 群聊列表
     * @param Request $request
     * @return ApiResponse
     */
    public function list(Request $request)
    {
        $page  = $request->get('page', 1);
        $limit = $request->get('limit', 200);
        $list  = ChatGroupUser::query()
            ->where('user_id', $request->userId())
            ->forPage($page, $limit)->with('group')
            ->get();
        return ApiResponse::success($list);
    }

    /**
     * 创建群聊
     * @param Request $request
     * @return ApiResponse
     */
    public function create(Request $request)
    {
        $name    = $request->post('group_name');
        $avatar  = $request->post('avatar', '');
        $userIds = $request->post('members', []);// 创建时拉入群成员
        if (!is_array($userIds)) {
            $userIds = explode(',', $userIds);
        }
        if (count($userIds) > 50) {
            return ApiResponse::error('不能群成员不能一次添加超过50个');
        } else if (!trim($name)) {
            return ApiResponse::error('群名称不能为空');
        } else if ($group = DB::transaction(function () use ($userIds, $avatar, $request, $name) {
            $model = new ChatGroup();
            $group = $model->createGroup($name, $request->userId(), $avatar);
            if (!$group) return false;
            $group_id       = $group->id;
            $groupUserModel = new ChatGroupUser();
            $friendModel    = new Friend();
            foreach ($userIds as $userId) {
                if ($friendModel->isFriend($userId, $request->userId())) {
                    $groupUserModel->joinGroup($group_id, $userId);
                }
            }
            return $group;
        })) {
            return ApiResponse::success($group, '创建成功');
        }
        return ApiResponse::error('创建失败');
    }

    /**
     * 加入群聊
     * @param Request $request
     */
    public function join(Request $request)
    {
        $model    = new ChatGroupUser();
        $group_id = $request->post('group_id');
        if (!$group_id) {
            return ApiResponse::error('参数错误');
        } else if (!ChatGroup::query()->find($group_id)) {
            return ApiResponse::error('群聊不存在');
        }
        $model->joinGroup($group_id, $request->userId());
        return ApiResponse::success('已加入群聊');
    }

    /**
     * 退出群聊
     * @param Request $request
     * @return ApiResponse
     */
    public function exit(Request $request)
    {
        $model    = new ChatGroupUser();
        $group_id = $request->post('group_id');
        if (!(new ChatGroup())->isMember($group_id, $request->userId())) {
            return ApiResponse::error('不是群成员');
        }
        $model->exitGroup($group_id, $request->userId());
        return ApiResponse::success('已退出');
    }

    /**
     * 群成员
     * @param Request $request
     * @return ApiResponse
     */
    public function members(Request $request)
    {
        $group_id   = $request->get('group_id');
        $page       = $request->get('page', 1);
        $limit      = $request->get('limit', 100);
        $groupModel = new ChatGroup();
        if (!$groupModel->isMember($group_id, $request->userId())) {
            return ApiResponse::error('不是群成员');
        }
        $groupUserModel = new ChatGroupUser();
        $list           = $groupUserModel->members($group_id)->forPage($page, $limit)->with('userInfo')->get();
        return ApiResponse::success($list);
    }

    /**
     * 添加群成员
     * @param Request $request
     * @return ApiResponse
     */
    public function addMembers(Request $request)
    {
        $userIds        = $request->post('members', []);
        $groupId        = $request->post('group_id');
        $groupUserModel = new ChatGroupUser();
        $friendModel    = new Friend();
        if (!is_array($userIds)) {
            $userIds = explode(',', $userIds);
        }
        DB::transaction(function () use ($groupUserModel, $groupId, $request, $friendModel, $userIds) {
            foreach ($userIds as $userId) {
                if ($friendModel->isFriend($userId, $request->userId())) {
                    $groupUserModel->joinGroup($groupId, $userId);
                } else {
                    throw new \Exception('不能添加非好友成员');
                }
            }
        });
        return ApiResponse::success('添加成功');
    }

    /**
     * 修改群名
     * @param Request $request
     */
    public function rename(Request $request)
    {
        $name    = $request->post('name');
        $groupId = $request->post('group_id');
        $group   = ChatGroup::query()
            ->where('id', $groupId)
            ->where('founder', $request->userId())
            ->first();
        if (!$group) {
            return ApiResponse::error('只允许群主修改群名称');
        }
        $group->name = $name;
        $group->save();
        return ApiResponse::success('修改成功');
    }
}
