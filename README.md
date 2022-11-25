# 仿微信聊天

#### 👀 介绍

仿微信模式的即时聊天系统，实现私聊、群聊、一对一视频功能。支持pc、移动端自适应。开源代码仅供学习交流。

#### ✨ 软件架构

1. `Workerman 4.0`、`Laravel`、`php >= 7.3`、`Vue3`+`typescript`、`Socket-io 4.0`、`WebRTC`+`Coturn`。
2. 使用`Vue3`渐进式框架进行前端渲染，通过`typescript`实现面向对象编程，使代码变得易读易维护。
3. 使用`Workerman`作为即时通讯框架，为`Socketio 4.0`实现了一套简单的php服务端组件。
4. 使用`Laravel`进行接口数据交互，通过`migrate`命令一键初始化数据库和`db:seed`命令填充测试数据。
5. 使用`Coturn`搭建视频通信的信令服务器，使得公网环境也可以进行视频通信。
6. 使用`WebRTC`协议进行建立视频通信的对等连接，实现媒体流的远程播放。

#### 🔨 安装教程

- 初始化数据库

```shell
composer install
php artisan migrate --seed
php artisan websocket start -d
```

- 前端部署前修改`.env.development`、`.env.production`文件，分别是开发、生产环境的接口配置。

- 后端部署前修改`config/chat.php`文件，按照注释修改即可。

- 数据库初始化后会生成两个账号`icy8`、`test`，密码都是`111111`。

- 公网中视频通讯需要stun服务器，[如何搭建stun服务器？](https://blog.icy8.net/2022/01/13/turnserver%E6%90%AD%E5%BB%BAwebrtc%E7%A9%BF%E9%80%8F%E6%9C%8D%E5%8A%A1%E5%99%A8/)

#### 🎨 测试数据

```shell
# 填充测试数据
php artisan db:seed --class=FakeSeeder
```
数据量：

- 用户：10000
- 群组：10000
- 好友关系：1000
- 群组关系：1000
- 私聊记录：10000
- 群聊记录：10000

#### 💎 功能清单

1. 聊天

   - 会话列表
   - 创建会话
   - 发送消息
   - 聊天记录
   - 清空会话
   - 视频私聊
     - 自由切换摄像头：pc端手动选择，移动端自动切换前后摄像头。
     - 自由开关摄像头
     - 自由开关麦克风

2. 用户
   - 用户登录
   - 用户中心
   - 用户注册
   - 修改资料

3. 好友
   - 请求
     - 发起请求
     - 通过请求
     - 拒绝请求
     - 请求列表

   - 好友列表
   - 添加好友
   - 好友资料
   - 好友备注

4. 群组
   - 创建群聊
   - 加入群聊
   - 群员列表
   - 退出群聊
   - 修改群名
   - 添加群员

#### 📢 使用说明

- 视频功能需要浏览器支持`Webrtc`接口，目前测试移动端的浏览器只有`Edge`、`Chrome`、`Firefox`能友好运行，PC端基本都支持`Webrtc`。

- 如果摄像头被占用会导致视频连线失败。

- 视频功能务必将程序部署在`https`站点。

- 用户注册没有做可视化页面，需要自行调用`{{host}}/api/passport/register`创建用户。

- 群聊成员全部退出时，群聊依然会存在，那么这个群聊就成为了僵尸群聊，需要优化。

- 暂时没有做邀请群成员的功能，只能在创建群聊时勾选群成员。

#### 🙋‍♂️添加好友

- 如果A、B是好友关系，当A删了B：

   - 此时B好友列表中依然有A，但无法发送消息给A。

   - 此时A可以直接添加B为好友，且B不需要再验证。

   - 此时B可以请求添加A为好友，且A需要验证这个请求。

- 如果同一个人添加了`多条`好友请求，此时只要其中一条请求`通过`或`拒绝`则所有的请求状态都变为`通过`或`拒绝`。

#### 👟 路由列表

```
+--------+----------+---------------------------------------------------+------+-------------------------------------------------------------------+---------------------------------------+
| Domain | Method   | URI                                               | Name | Action                                                            | Middleware                            |
+--------+----------+---------------------------------------------------+------+-------------------------------------------------------------------+---------------------------------------+
|        | POST     | api/chat/conversation/clearUnread/{scene?}        |      | App\Api\Controllers\ChatConversationController@clearUnread        | api                                   |
|        |          |                                                   |      |                                                                   | App\Api\Middleware\TokenAccess        |
|        | POST     | api/chat/conversation/createConversation/{scene?} |      | App\Api\Controllers\ChatConversationController@createConversation | api                                   |
|        |          |                                                   |      |                                                                   | App\Api\Middleware\TokenAccess        |
|        | GET|HEAD | api/chat/conversation/hintCount                   |      | App\Api\Controllers\ChatConversationController@hintCount          | api                                   |
|        |          |                                                   |      |                                                                   | App\Api\Middleware\TokenAccess        |
|        | GET|HEAD | api/chat/conversation/list                        |      | App\Api\Controllers\ChatConversationController@list               | api                                   |
|        |          |                                                   |      |                                                                   | App\Api\Middleware\TokenAccess        |
|        | GET|HEAD | api/chat/expressions                              |      | App\Api\Controllers\ChatController@expressions                    | api                                   |
|        |          |                                                   |      |                                                                   | App\Api\Middleware\TokenAccess        |
|        | POST     | api/chat/group/create                             |      | App\Api\Controllers\ChatGroupController@create                    | api                                   |
|        |          |                                                   |      |                                                                   | App\Api\Middleware\TokenAccess        |
|        | POST     | api/chat/group/exit                               |      | App\Api\Controllers\ChatGroupController@exit                      | api                                   |
|        |          |                                                   |      |                                                                   | App\Api\Middleware\TokenAccess        |
|        | POST     | api/chat/group/join                               |      | App\Api\Controllers\ChatGroupController@join                      | api                                   |
|        |          |                                                   |      |                                                                   | App\Api\Middleware\TokenAccess        |
|        | GET|HEAD | api/chat/group/list                               |      | App\Api\Controllers\ChatGroupController@list                      | api                                   |
|        |          |                                                   |      |                                                                   | App\Api\Middleware\TokenAccess        |
|        | GET|HEAD | api/chat/group/members                            |      | App\Api\Controllers\ChatGroupController@members                   | api                                   |
|        |          |                                                   |      |                                                                   | App\Api\Middleware\TokenAccess        |
|        | POST     | api/chat/group/rename                             |      | App\Api\Controllers\ChatGroupController@rename                    | api                                   |
|        |          |                                                   |      |                                                                   | App\Api\Middleware\TokenAccess        |
|        | GET|HEAD | api/chat/records                                  |      | App\Api\Controllers\ChatController@records                        | api                                   |
|        |          |                                                   |      |                                                                   | App\Api\Middleware\TokenAccess        |
|        | POST     | api/chat/send/{scene?}                            |      | App\Api\Controllers\ChatController@send                           | api                                   |
|        |          |                                                   |      |                                                                   | App\Api\Middleware\TokenAccess        |
|        | POST     | api/friend/delete                                 |      | App\Api\Controllers\FriendController@delete                       | api                                   |
|        |          |                                                   |      |                                                                   | App\Api\Middleware\TokenAccess        |
|        | GET|HEAD | api/friend/hintCount                              |      | App\Api\Controllers\FriendController@hintCount                    | api                                   |
|        |          |                                                   |      |                                                                   | App\Api\Middleware\TokenAccess        |
|        | GET|HEAD | api/friend/info                                   |      | App\Api\Controllers\FriendController@info                         | api                                   |
|        |          |                                                   |      |                                                                   | App\Api\Middleware\TokenAccess        |
|        | GET|HEAD | api/friend/list                                   |      | App\Api\Controllers\FriendController@list                         | api                                   |
|        |          |                                                   |      |                                                                   | App\Api\Middleware\TokenAccess        |
|        | GET|HEAD | api/friend/preciseSearch                          |      | App\Api\Controllers\FriendController@preciseSearch                | api                                   |
|        |          |                                                   |      |                                                                   | App\Api\Middleware\TokenAccess        |
|        | POST     | api/friend/remarkName                             |      | App\Api\Controllers\FriendController@remarkName                   | api                                   |
|        |          |                                                   |      |                                                                   | App\Api\Middleware\TokenAccess        |
|        | POST     | api/friend/request                                |      | App\Api\Controllers\FriendController@request                      | api                                   |
|        |          |                                                   |      |                                                                   | App\Api\Middleware\TokenAccess        |
|        | GET|HEAD | api/friend/request/list                           |      | App\Api\Controllers\FriendController@requestList                  | api                                   |
|        |          |                                                   |      |                                                                   | App\Api\Middleware\TokenAccess        |
|        | POST     | api/friend/request/{todo}                         |      | App\Api\Controllers\FriendController@toRequest                    | api                                   |
|        |          |                                                   |      |                                                                   | App\Api\Middleware\TokenAccess        |
|        | POST     | api/passport/login                                |      | App\Api\Controllers\PassportController@login                      | api                                   |
|        |          |                                                   |      |                                                                   | App\Api\Middleware\TokenAccess:unauth |
|        | POST     | api/passport/register                             |      | App\Api\Controllers\PassportController@register                   | api                                   |
|        |          |                                                   |      |                                                                   | App\Api\Middleware\TokenAccess:unauth |
|        | POST     | api/user/edit                                     |      | App\Api\Controllers\UserController@edit                           | api                                   |
|        |          |                                                   |      |                                                                   | App\Api\Middleware\TokenAccess        |
|        | GET|HEAD | api/user/info                                     |      | App\Api\Controllers\UserController@info                           | api                                   |
|        |          |                                                   |      |                                                                   | App\Api\Middleware\TokenAccess        |
+--------+----------+---------------------------------------------------+------+-------------------------------------------------------------------+---------------------------------------+
```

#### 🚗 TODO

- [ ] 优化视频通话呼叫逻辑

- [ ] 头像修改/上传

- [ ] 移除会话

- [ ] 登录验证码

- [ ] 前端邀请群成员

- [ ] 处理僵尸群聊

- [ ] 设计缓存优化性能

#### 📸 截图

1. 登录页

![登录页](/截图/login.png)

2. 会话页

![会话页](/截图/conversation.png)

3. 联系人

![联系人](/截图/contact.png)

4. 创建群聊

![创建群聊](/截图/create_group.png)

5. 添加好友

![添加好友](/截图/friend.png)

![添加好友](/截图/friend2.png)

6. 视频聊天

![视频聊天](/截图/video_chat.png)

7. 群成员

![群成员](/截图/members.png)
