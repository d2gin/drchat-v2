<?php

namespace App\Providers;

use App\Api\Models\ChatGroup;
use App\Api\Models\ChatGroupUser;
use App\Api\Models\ChatRecord;
use App\Api\Models\Friend;
use App\Api\Models\FriendRequest;
use App\Api\Models\User;
use App\Api\Observers\ChatGroupObserver;
use App\Api\Observers\ChatGroupUserObserver;
use App\Api\Observers\ChatRecordObserver;
use App\Api\Observers\FriendObserver;
use App\Api\Observers\FriendRequestObserver;
use App\Api\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class ModelEventProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(UserObserver::class);
        ChatRecord::observe(ChatRecordObserver::class);
        FriendRequest::observe(FriendRequestObserver::class);
        Friend::observe(FriendObserver::class);
        ChatGroup::observe(ChatGroupObserver::class);
        ChatGroupUser::observe(ChatGroupUserObserver::class);
    }
}
