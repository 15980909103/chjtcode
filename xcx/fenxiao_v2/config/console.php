<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    // 指令定义
    'commands' => [
        'test'=> 'app\command\crontab\Test',
        'dest'=> 'app\command\crontab\Dest',
        'CardCake'=> 'app\command\crontab\CardCake',
        'offline'=> 'app\command\crontab\Offline',
        'oldnewstonew'=> 'app\command\crontab\OldNewsToNew',
        'crontabmanage' => 'app\command\crontab\CrontabManage',
    ],
];
