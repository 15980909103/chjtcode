<?php
declare (strict_types = 1);

namespace app\task;


use app\common\pool\RedisPool;
use app\server\index\BoMessageLog;
use Swoole\IDEHelper\StubGenerators\Swoole;
use Swoole\Runtime;
use think\cache\driver\Redis;
use function Co\run;

class Message
{
    public function run($data= null){

        $redisObj      =  RedisPool::getInstance();
        $config        = $redisObj->setConfig('swoole.pool.redis');
        $redis      =  $redisObj->init($config);
        $key        =  'msg_redis_key_log:'.$data['talker'];
        $reis_res   =  $redis->zAdd($key,$data['order'],json_encode($data));
        $redis->expire($key,7*60*24*60);
////        if($reis_res){
//            $msgServer  = new BoMessageLog();
//            $result     = $msgServer->addMessageLog($data);
////        }
    }
}
