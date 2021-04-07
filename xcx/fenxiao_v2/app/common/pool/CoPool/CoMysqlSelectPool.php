<?php
declare (strict_types = 1);

namespace app\common\pool\CoPool;
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
class CoMysqlSelectPool
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


    /**
     * 等待的通道
     *
     * @var \Swoole\Coroutine\Channel
     */
    private $waitChannel;

    private $childNum =6;//在执行一组任务时其子协程的并发执行数


    public function init(){
        $this->coCount = 10; //工作协程数量
        $this->queueLength = 100; //队列最大长度

        $this->run();
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
        if($this->running == true){
            return;
        }

        $this->taskQueue = new Channel($this->queueLength);
        //$this->waitChannel = new Channel(1);

        $this->onWorkerExit();//监听进程异常退出

        $this->running = true;
        for($i = 0; $i < $this->coCount; ++$i)
        {
            Coroutine::create(function() use($i){
                $this->doTask($i);
            });
        }
    }

    /**
     * 做任务
     * @param $index
     */
    public function doTask($index){
        $childNum= $this->childNum;
        if($childNum>1){//开启多个子协程时
            $this->doTaskChildGroup($index, $childNum);
        }else{
            do {
                $task = $this->taskQueue->pop();//提取任务
                if(false !== $task){
                    foreach ($task['tasks'] as $item){
                        try{
                            $rs = '';
                            if(!empty($item['callFun'])){
                                $rs = $item['callFun']($item['data'])??'';
                            }
                        }catch (\Throwable $e){
                            $rs = $e->getMessage();
                        } finally {
                            !empty($task['channel'])&&$task['channel']->push([$item['key'] => $rs]);//返回结果到预设的结果通道
                        }
                    }
                }
            }while($this->running);
        }
    }


    public function doTaskChildGroup($index, $childNum){
        //$childNum= $this->childNum;
        $chan = new Channel($childNum);//一个父级任务组投递组内子协程任务集通道

        //协程关闭时关闭通道
        defer(function () use ($chan) {
            $chan->close();
        });

        for($i=0;$i<$childNum;$i++){//创建多个子协程
            go(function () use ($chan) {
                //执行任务组组内各个任务
                do{
                    $task2 = $chan->pop();
                    $rs = '';
                    try{
                        if(!empty($task2['callFun'])){
                            $rs = $task2['callFun']($task2['data'])??'';
                        }
                    }catch (\Throwable $e){
                        $rs = $e->getMessage();
                    } finally {
                        !empty($task2['channel'])&&$task2['channel']->push([$task2['key'] => $rs]);//相应的结果通道中返回结果
                    }
                }while($this->running);
            });
        }

        do {
            $task = $this->taskQueue->pop();//获取此次任务组
            if(false !== $task){
                //投递一次任务组到子协程
                foreach ($task['tasks'] as $item){
                    $chan->push([
                        'key' => $item['key'], //为item结果集标识
                        'data' => $item['data'],
                        'callFun' => $item['callFun'],
                        'channel' => $task['channel'],//转存任务组相应的结果通道
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
        try {
            if(empty($tasks[0]['key'])){
                throw new \RuntimeException(sprintf('AddTask failed! Channel errCode = %s', '数据格式错误'));
            }
            $count_task = count($tasks);
            $channel = new Channel($count_task);//创建返回结果通道

            if($this->taskQueue->push([
                'tasks'      =>  $tasks,
                'channel'   =>  $channel, //转存结果通道
            ]))
            {
                $result =[];
                for ($i = 0; $i < $count_task; $i++)//从结果通道中获取数据
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
            !empty($channel)&&$channel->close();
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
    public function onWorkerExit(){
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
