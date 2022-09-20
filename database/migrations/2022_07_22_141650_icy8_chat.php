<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Icy8Chat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->userTable();
        $this->userExpressionTable();
        $this->friendTable();
        $this->friendRequestTable();
        $this->chatRecordTable();
        $this->chatRecordRelationTable();
        $this->chatConversationTable();
        $this->chatExprssionTable();
        $this->chatGroupTable();
        $this->chatGroupUserTable();
    }

    public function userTable()
    {
        Schema::create('icy8_user', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('username', 30)->nullable(false)->comment('用户名')->index();
            $table->string('nickname')->nullable(false)->default('')->comment('昵称');
            $table->string('password', 100)->nullable(false)->comment('密码');
            $table->string('avatar')->nullable(false)->default('')->comment('头像');
            $table->tinyInteger('sex')->default(1)->comment('性别 1男 2女');
            $table->string('signature')->nullable(false)->default('')->comment('个性签名');
            $table->string('city')->nullable(false)->default('')->comment('城市');
            $table->timestamp('last_login_at')->comment('最后一次登录的时间')->nullable();
            $table->tinyInteger('is_online')->default(0)->comment('是否在线 1是 0否')->nullable(false);
            $table->timestamps();
            $table->index('created_at');
            $table->index('updated_at');
            $table->softDeletes();
        });
    }

    public function userExpressionTable()
    {
        Schema::create('icy8_user_expression', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->integer('user_id')->nullable(false)->comment('用户id');
            $table->string('path')->nullable(false)->comment('表情资源路径');
            $table->integer('sort')->default(1)->comment('排序 按顺序');
            $table->string('remark')->nullable(false)->default('')->comment('表情备注');
            $table->timestamps();
            $table->index('created_at');
            $table->index('updated_at');
            $table->softDeletes();
        });
    }

    public function friendTable()
    {
        Schema::create('icy8_friend', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->integer('user_id')->nullable(false)->comment('用户id')->index();
            $table->integer('friend_id')->nullable(false)->comment('好友id');
            $table->string('remark_name')->nullable(false)->default('')->comment('备注名');
            $table->string('source')->nullable(false)->default('')->comment('来源');
            $table->timestamps();
            $table->index('created_at');
            $table->index('updated_at');
            $table->softDeletes()->index();

            $table->index(['friend_id', 'user_id',]);
        });
    }

    public function friendRequestTable()
    {
        Schema::create('icy8_friend_request', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->integer('sender')->nullable(false)->default(0)->comment('发送人')->index();
            $table->integer('receiver')->nullable(false)->default(0)->comment('接收人')->index();
            $table->string('remark_name')->nullable(false)->default('')->comment('备注名称');
            $table->string('message')->nullable(false)->default('')->comment('验证消息');
            $table->tinyInteger('status')->nullable(false)->default(1)->comment('状态 1新请求 2新请求不提示 3通过 4不通过');
            $table->string('source')->nullable(false)->default('')->comment('请求来源');
            $table->timestamps();
            $table->index('created_at');
            $table->index('updated_at');
            $table->softDeletes();
        });
    }

    public function chatRecordTable()
    {
        Schema::create('icy8_chat_record', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->integer('sender')->nullable(false)->default(0)->comment('发送者')->index();
            $table->integer('receiver')->nullable(false)->default(0)->comment('接收者')->index();
            $table->text('content')->comment('聊天内容');
            $table->tinyInteger('type')->default(0)->comment('消息类型 图片、链接等');
            $table->tinyInteger('scene')->nullable(false)->default(1)->comment('聊天场景 1 私聊 2群聊');
            // $table->tinyInteger('is_read')->nullable(false)->default(0)->comment('是否已读');
            $table->timestamps();
            $table->index('created_at');
            $table->index('updated_at');
            $table->softDeletes();
        });
    }

    public function chatRecordRelationTable()
    {
        Schema::create('icy8_chat_record_relation', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->integer('sender')->nullable(false)->default(0)->comment('发送者');
            $table->integer('receiver')->nullable(false)->default(0)->comment('接收者')->index();
            $table->integer('record_id')->nullable(false)->default(0)->comment('聊天记录id')->index();
            $table->integer('mix')->nullable(false)->default(0)->comment('发送者和接受者id的融合值')->index();
            $table->tinyInteger('scene')->nullable(false)->default(1)->comment('聊天场景 1 私聊 2群聊');
            $table->enum('direction', ['forward', 'reverse'])->nullable(false)->default('forward')->comment('记录指向，正向，反向');
            $table->tinyInteger('is_read')->nullable(false)->default(0)->comment('是否已读')->index();
            $table->timestamps();
            $table->index('created_at');
            $table->index('updated_at');

            // 复合索引
            // 对象聊天记录索引
            $table->index(['receiver', 'sender', 'scene',]);
            // 融合用户聊天记录索引
            $table->index(['receiver', 'mix', 'scene',]);
        });
    }

    public function chatConversationTable()
    {
        Schema::create('icy8_chat_conversation', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->integer('user_id')->nullable(false)->default(0)->comment('用户id')->index();
            $table->integer('record_id')->nullable(false)->default(0)->comment('最后一条聊天记录id')->index();
            $table->integer('object_id')->comment('会话对象id')->index();
            $table->tinyInteger('object_type')->nullable(false)->default(1)->comment('会话对象 1普通 2群聊');
            $table->timestamps();
            $table->index('created_at');
            $table->index('updated_at');
            $table->softDeletes();
            // 复合索引
            // 会话详情
            $table->index(['user_id', 'object_id', 'object_type',]);
        });
    }

    public function chatExprssionTable()
    {
        Schema::create('icy8_chat_expression', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('name')->nullable(false)->default('')->comment('表情名称');
            $table->string('code')->nullable(false)->default('')->index();
            $table->string('path')->nullable(false)->default('')->comment('表情资源地址');
            $table->tinyInteger('status')->nullable(false)->default(1)->comment('是否显示');
            $table->timestamps();
            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    public function chatGroupTable()
    {
        Schema::create('icy8_chat_group', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('name')->nullable(false)->default('')->comment('群聊名称');
            $table->string('avatar')->nullable(false)->default('')->comment('群头像');
            $table->integer('founder')->nullable(false)->comment('创建人');
            $table->timestamps();
            $table->index('created_at');
            $table->index('updated_at');
            $table->softDeletes();
        });
    }

    public function chatGroupUserTable()
    {
        Schema::create('icy8_chat_group_user', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->integer('group_id')->nullable(false)->default(0)->comment('群id')->index();
            $table->integer('user_id')->nullable(false)->default(0)->comment('用户id')->index();
            $table->timestamp('join_at')->nullable()->comment('加群时间')->index();
            $table->timestamps();
            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('icy8_user');
        Schema::dropIfExists('icy8_user_expression');
        Schema::dropIfExists('icy8_friend');
        Schema::dropIfExists('icy8_friend_request');
        Schema::dropIfExists('icy8_chat_record');
        Schema::dropIfExists('icy8_chat_record_relation');
        Schema::dropIfExists('icy8_chat_group');
        Schema::dropIfExists('icy8_chat_group_user');
        Schema::dropIfExists('icy8_chat_expression');
        Schema::dropIfExists('icy8_chat_conversation');
    }
}
