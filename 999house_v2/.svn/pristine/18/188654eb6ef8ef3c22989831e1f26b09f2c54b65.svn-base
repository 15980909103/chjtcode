<?php
// 事件定义文件
return [
    'bind'      => [
    ],

    'listen'    => [
        'AppInit'  => [],
        'HttpRun'  => [],
        'HttpEnd'  => [],
        'LogLevel' => [],
        'LogWrite' => [],
        'swoole.task' => [\app\common\listens\MyTask::class], //监听swoole的任务投递
        'swoole.init' => [
            //\app\common\listens\UdpServer::class,
            \app\common\listens\MyServerInit::class
        ],//监听swoole的服务初始化，用于自定义服务端的拓展
    ],

    'subscribe' => [
    ],
];
