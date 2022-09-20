<?php
return [
    'websocket_port'   => 9502,// websocket服务器
    'http_port'        => 9580,// 用于nginx和websocket通信的服务
    'expression_url'   => 'https://chat-api.icy8.net',// 系统表情资源url
    'event_pusher_url' => '',// 推送地址 留空会自动调 127.0.0.1:http_port
    'call_timeout'     => 60,// 呼叫超时时间 单位秒
];