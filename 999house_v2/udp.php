<?php
//测试test

$j = 1;
global $j;
function udpPost(){
    go(function (){
        global $j;
        $remoteConfig=[
            //"ip" => '127.0.0.1',
            "ip" => '192.168.1.10',
            "port" => 9601,//udp端口
        ];
        $postData['pwd'] = '123456';
        $postData['event'] = 'isOnlineByFd';
        $postData['local_ip'] = '192.168.1.10';
        $postData['local_port'] = '9601';
        $postData['fd'] = '2';

        $client = new \Swoole\Coroutine\Client(SWOOLE_SOCK_UDP);
        if (!$client->connect($remoteConfig['ip'], $remoteConfig['port'], 0.5))
        {
            throw new InvalidArgumentException("connect failed. Error: {$client->errCode}\n");
        }
        $client->send(json_encode($postData,JSON_UNESCAPED_UNICODE));
        $rs = $client->recv();
        $client->close();

        var_dump($rs);
        if(!empty($rs)){
            $rs = json_decode($rs,true);
            if(!empty($rs)){
               echo $j++.PHP_EOL;
            }
        }

        return $rs;
    });
}


//for($i=0;$i<1;$i++){
//    $rs = udpPost();
//}

$scheduler = new Swoole\Coroutine\Scheduler;
$time = time();
echo $time;
$scheduler->parallel(2, function ($data){
    while (true){
        if(time()-$data>=2){
            echo 'ok';
            echo time();
            break;
        }
    }
},$time);
$scheduler->start();
return;
include 'app/common/pool/Copool/CoPool.php';
include 'app/common/pool/Copool/TaskParam.php';
go(function (){
    $coCount = 2; // 同时工作协程数
    $queueLength = 5; // 队列长度
    $pool = new \app\common\pool\Copool\CoPool($coCount, $queueLength,
        // 定义任务匿名类，当然你也可以定义成普通类，传入完整类名
        new class
        {
            /**
             * 执行任务
             *
             * @param ITaskParam $param
             * @return mixed
             */
            public function run(\app\common\pool\Copool\TaskParam $param)
            {
                $data = $param->getData();
                var_dump($data);
                while (true){
                    if(time()-$data>1){
                        break;
                    }
                }

                // 执行任务
                return true; // 返回任务执行结果，非必须
            }

        }
    );
    $pool->run();

    for($i=0;$i<3;$i++){
        $pool->addTaskAsync(time(),function (\app\common\pool\Copool\TaskParam $param,$data){
            var_dump($data);
        });
    }

//$pool->wait(); // 等待协程池停止，不限时，true/false
    $pool->wait(5); // 等待协程池停止，限时5秒，如果为-1则不限时，true/false
});

