<?php

use app\ExceptionHandle;
use app\Request;

// 容器Provider定义文件
return [
    'think\Request'          => Request::class,
    'think\exception\Handle' => ExceptionHandle::class,

    //'think\swoole\websocket\Pusher' => \app\websocket\MyPusher::class,框架的类替换为我们的
    //'Hasids'                => \Hashids\Hashids::class
];
