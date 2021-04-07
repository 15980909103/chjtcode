<?php
declare (strict_types = 1);

namespace app\common\base;
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
class CoTaskPoolBase
{
    use TraitInstance;
    /**
     * 任务队列
     *
     * @var \Swoole\Coroutine\Channel
     */
    protected $taskQueue;

    /**
     * 是否正在运行
     *
     * @var boolean
     */
    protected $running = false;

    /**
     * 等待的通道
     *
     * @var \Swoole\Coroutine\Channel
     */
    protected $waitChannel;


    /**
     * 工作协程数量
     *
     * @var int
     */
    protected $coCount = 2;

    /**
     * 队列最大长度,队列长度一般>=工作主协程数量
     *
     * @var int
     */
    protected $queueLength = 2;

    /**
     * 在执行一组任务时其子协程的并发执行数
     * @var int
     */
    protected $childNum = 1;

    /**
     * 初始化启动操作
     */
    public function init(){
        $this->coCount = 1; //工作协程数量
        $this->queueLength = 10; //队列最大长度
        $this->childNum = 1 ;

        $this->run();
    }

    public function createTaskQueue(){
        $this->taskQueue = new Channel($this->queueLength);
    }

    /**
     * 运行协程池
     *
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

        $this->createTaskQueue();
        //$this->waitChannel = new Channel(1);

        $this->onWorkerExit();//监听进程异常退出

        $this->running = true;


        //使用并行协程运行
        for($i = 0; $i < $this->coCount; ++$i)
        {
            Coroutine::create(function() use($i){
                $this->doTask($i);
            });
        }
    }

    protected function getResult($taskItem = null){
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
    /**
     * 做任务
     * @param $index
     */
    public function doTask($index){
        $childNum= $this->childNum;
        if($childNum>1){//开启多个子协程时
            $this->doTaskChildGroup($index, $childNum);
        }else{
            //无子协程跑时主要靠队列长度提升投递速度
            do {
                $task = $this->taskQueue->pop();//提取任务
                echo 'doworker_'.$index;
                //print_r($this->taskQueue->stats());
                if(false !== $task){
                    //print_r($task);
                    $checkDoneChannel = new Channel(1);

                    foreach ($task['tasks'] as $item){
                        //无设置时默认任务超时时间
                        $time_out = !empty($item['time_out'])? $item['time_out'] : 60;

                        go(function ()use($item, $checkDoneChannel){
                            try{
                                $rs['msg'] = 'success';

                                $rs['result'] = $this->getResult($item);
                            }catch (\Throwable $e){
                                $rs['msg']= $e->getMessage();
                                echo $rs['msg'];
                            } finally {
                                $checkDoneChannel->push('done');

                                if(!empty($task['channel'])){//返回结果到预设的结果通道
                                    $task['channel']->push([$item['key'] => $rs]);
                                }elseif (!empty($item['callback'])){
                                    $item['callback']($rs); //回调方法
                                }
                            }
                        });


                        //避免任务僵死，设置超时时间
                        $done = $checkDoneChannel->pop($time_out);
                        if($done!='done'){
                            continue;//跳出此次循环
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
                    $data = $chan->pop();
                    $task2 = $data['taskItem'];
                    $result_channel = $data['channel'];
                    unset($data);

                    $checkDoneChannel = new Channel(1);
                    $time_out = !empty($task2['time_out'])? $task2['time_out'] : 60;//无设置时默认任务超时时间


                    go(function ()use($task2, $checkDoneChannel, $result_channel){
                        try{
                            $rs['msg'] = 'success';

                            $rs['result'] = $this->getResult($task2);
                        }catch (\Throwable $e){
                            $rs['msg'] = $e->getMessage();
                            echo $rs['msg'];
                        } finally {
                            $checkDoneChannel->push('done');

                            if(!empty($task2['channel'])){//相应的结果通道中返回结果
                                $result_channel->push([$task2['key'] => $rs]);
                            }elseif (!empty($task2['callback'])&&is_callable($task2['callback'])){
                                //用于异步回调方法
                                $task2['callback']($rs);
                            }
                        }
                    });

                    //避免任务僵死，设置超时时间
                    $done = $checkDoneChannel->pop($time_out);
                    if($done!='done'){
                        continue;//跳出此次循环
                    }
                }while($this->running);
            });
        }

        do {
            $task = $this->taskQueue->pop();//提取此次任务组
            if(false !== $task){
                //投递一次任务组到子协程
                foreach ($task['tasks'] as $item){
                    $chan->push([
                        'channel' => $task['channel']??null,//转存任务组相应的结果通道
                        'taskItem' => $item
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

            if(empty($tasks)){
                return false;
            }

            $i = 0;
            foreach ($tasks as &$item){
                if(!isset($item['key'])){
                    $item['key'] = $i;
                }
                $i++;
            }
            unset($item);

            $count_task = count($tasks);
            $channel = new Channel($count_task);//创建返回结果通道

            if($this->taskQueue->push([
                'tasks'      =>  $tasks,
                'channel'   =>  $channel, //转存结果通道（为同步等待）
            ]))
            {
                $result =[];
                for ($i = 0; $i < $count_task; $i++)//从结果通道中获取数据
                {
                    //$result += $channel->pop();
                    $result[]= $channel->pop();
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
     * 增加任务，异步执行，回调获取
     *
     * $tasks['callback']执行完成后回调，为 null 不执行回调
     *
     * @param array $tasks
     * [
     *  [
     *  'key' => $key,
     *  'doTask'=> $doTask,//类或匿名函数
     *  'data'  => $data,
     *  'callback'=>function
     *  ]
     * ]
     * @return boolean
     */
    public function addTaskAsync($tasks)
    {
        if(empty($tasks)){
            return false;
        }

        $i = 0;
        foreach ($tasks as &$item){
            if(!isset($item['key'])){
                $item['key'] = $i;
            }
            $i++;
        }

        unset($i);unset($item);
        //print_r($tasks);
        return $this->taskQueue->push([
            'tasks'     =>  $tasks,
        ]);
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
     * 获取队列任务通道，用于一些状态判断//如是否通道任务已满getQueueChannel()->isFull()来进行堵塞等待
     * @return Channel
     */
    public function getQueueChannel(){
        return $this->taskQueue;
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
