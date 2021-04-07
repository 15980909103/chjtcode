<?php
declare (strict_types = 1);

namespace app\common\lib\wxapi\co;
use app\common\traits\TraitInstance;
use app\common\traits\TraitPoolInteracts;
use think\Config;
use think\App;

use Swoole\Coroutine;
use Swoole\Coroutine\Channel;
use think\Container;
use think\Event;


//使用示例，投递一组同时请求
//$obj = Container::getInstance()->make(CoWxPool::class);
//$result = $obj->addTask([
//    [
//        'key'=> 'result', //获取用户信息
//        'data'=>'',
//        'callFun' => function()use($access_token,$openid) {
//            $cli = new \Swoole\Coroutine\Http\Client('api.weixin.qq.com', 443,true);
//            $cli->setMethod("GET");
//            $status = $cli->execute('/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN');
//            $rs = $cli->getBody();
//            $rs = json_decode($rs,true);
//            $cli->close();
//
//            return $rs;
//            //return self::curlGet('https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN');
//        }
//    ],
//    [
//        'key'=> 'result1', //获取是否关注
//        'data'=>'',
//        'callFun' => function()use ($openid,$access_token1){
//            $cli = new \Swoole\Coroutine\Http\Client('api.weixin.qq.com', 443,true);
//            $cli->setMethod("GET");
//            $status = $cli->execute('/cgi-bin/user/info?access_token='.$access_token1.'&openid='.$openid.'&lang=zh_CN');
//            $rs = $cli->getBody();
//            $rs = json_decode($rs,true);
//            $cli->close();
//
//            return $rs;
//            //return self::curlGet('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token1.'&openid='.$openid.'&lang=zh_CN');
//        }
//    ]
//]);

/**
 * 协程工作池 减少一些频繁使用的开销
 * Class CoPool
 * @package app\common\pool
 */
class CoWxPool
{
    /**
     * 工作协程数量
     *
     * @var int
     */
    private $coCount;

    /**
     * 队列最大长度
     *
     * @var int
     */
    private $queueLength;

    /**
     * 任务队列
     *
     * @var \Swoole\Coroutine\Channel
     */
    private $taskQueue;

    /**
     * 是否正在运行
     *
     * @var boolean
     */
    private $running = false;

    private $groupNum =2;//在执行一组任务时其子协程的并发执行数


    /**
     * 等待的通道
     *
     * @var \Swoole\Coroutine\Channel
     */
    private $waitChannel;


    public function __construct( )
    {
        $this->coCount = 10; //工作协程数量
        $this->queueLength = 100; //队列最大长度
    }

    /**
     * 运行协程池
     *
     * @return void
     */
    public function run()
    {
        if($this->taskQueue)
        {
            $this->taskQueue->close();
        }
        $this->taskQueue = new Channel($this->queueLength);
        //$this->waitChannel = new Channel(1);
        $this->workerExit();

        $this->running = true;
        for($i = 0; $i < $this->coCount; ++$i)
        {
            Coroutine::create(function() use($i){
                $this->doTask($i);
            });
        }
    }

    public function doTask($index){
        $groupNum= $this->groupNum;
        $chan = new Channel($groupNum);//任务组投递组内任务集通道

        //协程关闭时关闭通道
        defer(function () use ($chan) {
            $chan->close();
        });

        for($i=0;$i<$groupNum;$i++){
            go(function () use ($chan) {//执行任务组组内各个任务
                do{
                    $task2 = $chan->pop();
                    $rs = '';
                    if(!empty($task2['callFun'])){
                        $rs = $task2['callFun']($task2['data'])??'';
                    }
                    $task2['channel']->push([$task2['key'] => $rs]);//返回结果
                }while($this->running);
            });
        }

        do {
            $task = $this->taskQueue->pop();
            if(false !== $task){
                //投递一次任务组
                foreach ($task['tasks'] as $item){
                    $chan->push([
                        'key' => $item['key'],
                        'data' => $item['data'],
                        'callFun' => $item['callFun'],
                        'channel' => $task['channel'],
                    ]);
                }
            }
        }while($this->running);
    }


    /**
     * 增加任务，并挂起协程等待返回任务执行结果
     * @param $tasks
     * @return bool|mixed
     * @throws \Throwable
     */
    public function addTask($tasks)
    {
        $channel = new Channel(2);//创建放回结果通道
        try {
            if($this->taskQueue->push([
                'tasks'      =>  $tasks,
                'channel'   =>  $channel,
            ]))
            {
                $result =[];
                for ($i = 0; $i < $this->groupNum; $i++)
                {
                    $result += $channel->pop();
                }

                return $result;
            }
            else
            {
                throw new \RuntimeException(sprintf('AddTask failed! Channel errCode = %s', $this->taskQueue->errCode));
            }
        } catch(\Throwable $th) {
            throw $th;
        } finally {
            $channel->close();
        }
    }


    /**
     * 停止协程池
     *
     * 不会中断正在执行的任务
     *
     * 等待当前任务全部执行完后，才算全部停止
     *
     * @return void
     */
    public function stop()
    {
        $this->running = false;
        if($this->taskQueue){
            $this->taskQueue->close();
        }
        $this->taskQueue = null;
        //$this->waitChannel->push(1);
    }

    /**
     * 等待协程池停止
     *
     * @param float $timeout
     * @return boolean
     */
    public function wait(float $timeout = -1): bool
    {
        //return !!$this->waitChannel->pop($timeout);
    }


    /**
     * 检测是否正在运行
     *
     * @return boolean
     */
    public function isRunning()
    {
        return $this->running;
    }

    /**
     * 获取队列中待执行任务长度
     *
     * @return int
     */
    public function getQueueLength()
    {
        return $this->taskQueue->length();
    }


    /**
     * 用于清理数据
     */
    public function workerExit(){
        //进程退出时的一些清理
        $this->onEvent('workerStop', [$this,'stop']);
        $this->onEvent('WorkerError', [$this,'stop']);
        $this->onEvent('WorkerExit', [$this,'stop']);
    }

    public function onEvent(string $event, $listener, bool $first = false): void
    {
        $eventObj = Container::getInstance()->make(Event::class);
        $eventObj->listen("swoole.{$event}", $listener, $first);
    }
}
