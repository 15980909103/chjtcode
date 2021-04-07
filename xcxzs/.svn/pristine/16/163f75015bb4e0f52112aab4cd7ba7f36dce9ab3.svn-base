<?php
declare (strict_types = 1);

namespace app\common\lib\delayQueue;
use app\common\base\CoTaskPoolBase;
use think\App;




//使用示例，投递一组同时请求
//$obj = Container::getInstance()->make(CoWxPool::class);
//$result = $obj->addTask([
//    [
//        'key'=> 'result', //获取用户信息
//        'data'=>'',
//        'doTask' => function()use($access_token,$openid) {
//            $cli = new \Swoole\Coroutine\Http\Client('api.weixin.qq.com', 443,true);
//            $cli->setMethod("GET");
//            $status = $cli->execute('/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN');
//            $rs = $cli->getBody();
//            $rs = json_decode($rs,true);
//            $cli->close();
//
//            return $rs;
//            //return self::curlGet('https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN');
//        },
//        callback=> function(){}//有回调时候可用于异步返回值
//    ],
//    [
//        'key'=> 'result1', //获取是否关注
//        'data'=>'',
//        'doTask' => function()use ($openid,$access_token1){
//            $cli = new \Swoole\Coroutine\Http\Client('api.weixin.qq.com', 443,true);
//            $cli->setMethod("GET");
//            $status = $cli->execute('/cgi-bin/user/info?access_token='.$access_token1.'&openid='.$openid.'&lang=zh_CN');
//            $rs = $cli->getBody();
//            $rs = json_decode($rs,true);
//            $cli->close();
//
//            return $rs;
//            //return self::curlGet('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token1.'&openid='.$openid.'&lang=zh_CN');
//        },
//        callback=> function(){}//有回调时候可用于异步返回值
//    ]
//]);

/**
 * 协程工作池 减少一些频繁使用的开销
 * Class CoPool
 * @package app\common\pool
 */
class DelayQueueCoTaskPool extends CoTaskPoolBase
{
    /**
     * 初始化启动操作
     */
    public function init(){
        $this->coCount = 10; //工作协程数量
        $this->childNum = 10;
        $this->queueLength = 10; //队列最大长度

        $this->run();
    }

    protected function getResult($taskItem= null){
        //var_dump($taskItem);
        $rs = '';
        if(is_callable($taskItem['doTask'])){
            $rs = $taskItem['doTask']($taskItem['data'], $taskItem)??'';
        }elseif (method_exists($taskItem['doTask'], 'runBox') ){
            $taskObject = new $taskItem['doTask'];
            $rs = $taskObject->runBox($taskItem['data'], $taskItem)??'';
        }
        return $rs;
    }
}
