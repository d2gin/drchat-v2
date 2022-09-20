<?php

namespace App\Api\Services;
class ChannelMaker
{
    /**
     * 聊天消息事件
     * @param $receiver
     * @param bool $is_group
     * @return string
     */
    static function chat($receiver, $is_group = false)
    {
        if ($is_group) {
            return "group_chat#{$receiver}";
        }
        return "normal_chat#{$receiver}";
    }
}
