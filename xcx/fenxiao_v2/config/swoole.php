<?php

//use think\swoole\websocket\socketio\Handler;
//use think\swoole\websocket\socketio\Parser;
use app\common\websocket\MyParser;

$host = env('SWOOLE_HOST', '0.0.0.0'); // 监听地址
$port = env('SWOOLE_PORT', 9501);

$redis = [
    'host' => env('REDIS.host'), //47.107.72.79
    'password' => env('REDIS.password'),//'999houseredis!@#'
    'port' => env('REDIS.port'), //59091
];

return [
    'server'     => [
        'host'      => $host, // 监听地址
        //'port'      => env('SWOOLE_PORT', 80), // 监听端口
        'port'      => $port, // 监听端口
        'mode'      => SWOOLE_PROCESS,//SWOOLE_PROCESS, // 运行模式 默认为SWOOLE_PROCESS
        'sock_type' => SWOOLE_SOCK_TCP, // sock type 默认为SWOOLE_SOCK_TCP
        'options'   => [
            'pid_file'              => runtime_path() . 'swoole.pid',
            'log_file'              => runtime_path() . 'swoole.log',
            'daemonize'             => false,
            // Normally this value should be 1~4 times larger according to your cpu cores.
            'reactor_num'           => swoole_cpu_num(),
            'worker_num'            => swoole_cpu_num(),
            'task_worker_num'       => swoole_cpu_num()*2,
            'enable_static_handler' => true,
            'document_root'         => root_path('public'),
            'package_max_length'    => 20 * 1024 * 1024,
            'buffer_output_size'    => 10 * 1024 * 1024,
            'socket_buffer_size'    => 128 * 1024 * 1024,
        ],
    ],
    'websocket'  => [
        'enable'        => false,
        'handler'       => \app\common\websocket\MyHandler::class, //Handler:class
        'parser'        => \app\common\websocket\MyParser::class,  //Parser:class
        'ping_interval' => 25000,
        'ping_timeout'  => 60000,
        'room'          => [
            //'type'  => \app\websocket\TableRoom::class,
            'type'  => \app\common\websocket\RedisRoom::class,
        ],
        'listen'        => [],
        'subscribe'     => [
            //\app\websocket\MyWebsocket::class //websocket事件监听
        ],
    ],
    'rpc'        => [
        'server' => [
            'enable'   => false,
            'port'     => 9000,
            'services' => [
            ],
        ],
        'client' => [
            'houseV2'=> [
                'host'=>'127.0.0.1',
                'port'=> 9000
            ]
        ],
    ],
    'hot_update' => [
        'enable'  => true,//env('APP_DEBUG', true),//是否开启热更新 false/true
        'name'    => ['*.php'],
        'include' => [app_path()],
        'exclude' => [],
    ],
    //连接池
    'pool'       => [
        'db'    => [
            'enable'        => true,
            'max_active'    => 3,
            'max_wait_time' => 5,
        ],
        'cache' => [
            'enable'        => true,
            'max_active'    => 3,
            'max_wait_time' => 5,
        ],
        //自定义连接池
        'redis' => [
            'enable'        => true,
            'host'          => $redis['host'],
            'max_active'    => 3,
            'max_wait_time' => 5,
            'password' => $redis['password'],
            'port'      => $redis['port'],
        ],
    ],
    'coroutine'  => [
        'enable' => true,
        'flags'  => SWOOLE_HOOK_ALL,
    ],
    'tables'     => [
        'fd2uid' => [
            'size' => 10240,
            'columns' => [
                ['name' => 'uid', 'type' => 7, 'size' => 32],
                ['name' => 'user_id', 'type' => 7, 'size' => 11],//string
                ['name' => 'merch_id', 'type' => 7, 'size' => 11],//string
            ]
        ],
    ],
    //每个worker里需要预加载以共用的实例
    'concretes'  => [],
    //重置器
    'resetters'  => [],
    //每次请求前需要清空的实例
    'instances'  => [],
    //每次请求前需要重新执行的服务
    'services'   => [],

    // 分布式服务器通道 //允许哪些ip +秘钥连接到与本机接收推送消息
    "distributed" => [
        //将会把用户连接的ip+端口存入到redis缓存。
        "local"=> [//标识是本机网络配置
            "ip" => '127.0.0.1',
            "port" => 9601,//udp端口
            //"pwd" => "ksU@M(8cHJzTqnV6uKmigrx0dyA5Qj)YwfvpCOXFNPoBDe3LGbZW42h#l%IR9ES1",//组网，分布式连接秘钥
            'pwd' => '123456'
        ],
        // websocket服务器集群，UDP端口监听配置,udpsocket，每台websocket都配备了udp监听。如果接收用户不在本服务器，会根据用户所在服务器推送至远程udp口,
        "udpsocket_group" => [
            // 如果来源ip不是127.0.0.1 需要验证secret_key秘钥值
            [
                "ip" => "127.0.0.1",
                "port" => 9602, //udp端口 ，webSocket集群监听跨服务器接收消息
                //"pwd" => "ksU@M(8cHJzTqnV6uKmigrx0dyA5Qj)YwfvpCOXFNPoBDe3LGbZW42h#l%IR9ES1",
                'pwd' => '123456'
            ],
            [
                "ip" => "127.0.0.1",
                "port" => 9603, //udp端口
                //"pwd" => "ksU@M(8cHJzTqnV6uKmigrx0dyA5Qj)YwfvpCOXFNPoBDe3LGbZW42h#l%IR9ES1",
                'pwd' => '123456'
            ],
        ],
    ],
];
