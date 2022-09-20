<?php

use App\Api\Controllers\ChatController;
use App\Api\Controllers\ChatConversationController;
use App\Api\Controllers\ChatGroupController;
use App\Api\Controllers\ChatNormalController;
use App\Api\Controllers\FriendController;
use App\Api\Controllers\PassportController;
use App\Api\Controllers\UserController;
use App\Api\Middleware\TokenAccess;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// 无需登录放行
Route::group(['middleware' => [TokenAccess::class . ':unauth'],], function (Router $router) {
    // 登录相关
    $router->group([
        'prefix'     => 'passport',
        'controller' => PassportController::class,
    ], function (Router $router) {
        // 登录
        $router->post('login', 'login');
        // 注册
        $router->post('register', 'register');
    });
});
// 需要登录放行
Route::group(['middleware' => [TokenAccess::class],], function (Router $router) {
    // 用户相关
    $router->group([
        'prefix'     => 'user',
        'controller' => UserController::class,
    ], function (Router $router) {
        // 用户中心
        $router->get('info', 'info');
        // 修改资料
        $router->post('edit', 'edit');
    });
    $router->group([
        'prefix'     => 'friend',
        'controller' => FriendController::class
    ], function (Router $router) {
        // 发起添加好友请求
        $router->post('request', 'request');
        // 处理好友请求
        $router->post('request/{todo}', 'toRequest')->where('todo', '(?:pass|unpass)');
        // 请求列表
        $router->get('request/list', 'requestList');
        // 好友列表
        $router->get('list', 'list');
        // 好友资料
        $router->get('info', 'info');
        // 删除好友
        $router->post('delete', 'delete');
        // 搜索
        $router->get('preciseSearch', 'preciseSearch');
        // 新的请求数
        $router->get('hintCount', 'hintCount');
        // 修改备注
        $router->post('remarkName', 'remarkName');
    });
    // 聊天相关
    $router->group([
        'prefix'     => 'chat',
        'controller' => ChatController::class,
    ], function (Router $router) {
        // 发送消息
        $router->post('send/{scene?}', 'send')->where('scene', '(?:normal|group)');
        // 聊天记录
        $router->get('records', 'records');
        // 系统表情
        $router->get('expressions', 'expressions');
    });
    // 群聊相关
    $router->group([
        'prefix'     => 'chat/group',
        'controller' => ChatGroupController::class,
    ], function (Router $router) {
        // 聊天记录
        $router->get('list', 'list');
        // 群成员
        $router->get('members', 'members');
        // 创建群聊
        $router->post('create', 'create');
        // 退出群聊
        $router->post('exit', 'exit');
        // 加入群聊
        $router->post('join', 'join');
        // 改名
        $router->post('rename', 'rename');
    });
    // 会话相关
    $router->group([
        'prefix'     => 'chat/conversation',
        'controller' => ChatConversationController::class,
    ], function (Router $router) {
        // 会话列表
        $router->get('list', 'list');
        // 获取未读的消息数
        $router->get('hintCount', 'hintCount');
        // 创建会话
        $router->post('createConversation/{scene?}', 'createConversation')->where('scene', '(?:normal|group)');
        // 清空未读
        $router->post('clearUnread/{scene?}', 'clearUnread')->where('scene', '(?:normal|group)');
    });
});
