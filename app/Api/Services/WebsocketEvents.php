<?php

namespace App\Api\Services;

class WebsocketEvents
{
    const CHAT_NEW_MESSAGE      = 'chat_new_message';
    const FRIEND_NEW_REQUEST    = 'friend_new_request';
    const FRIEND_REQUEST_PASS   = 'friend_request_pass';
    const CHAT_NEW_CONVERSATION = 'chat_new_conversation';
    const REFRESH_CONVERSATION  = 'refresh_conversation';
}
