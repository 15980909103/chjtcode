<?php
namespace app\common\lib\delayQueue;


use app\common\manage\TaskManage;
use app\common\pool\RedisPool;
use app\common\traits\TraitCompressData;
use app\common\traits\TraitInstance;
use Swoole\Coroutine\Channel;
use think\facade\Config;
use think\facade\Log;

class DelayQueue {
    use TraitCompressData;
    private $prefix = 'fenxiao:delay_queue:';//前缀

    /**
     * redis存储队列名称集的名称
     * @var string
     */
    private $redis_delayQueueNames = 'fenxiao:delayQueueNames';

    private static $_instance = null;

    /**
     * @var \Swoole\Coroutine\Channel
     */
    private $getTaskChannel = null;
    /**
     * @var \Swoole\Coroutine\Channel
     */
    private $getFailTaskChannel = null;


    private function __construct() { }

    final private function __clone() {}

    use TraitInstance;

    protected function getRedis(){
       $redisPool= RedisPool::getInstance();
       return $redisPool->init($redisPool->setConfig('swoole.pool.redis',[
           'database' => 5,
           'max_active' => 10,
       ]));
    }

    /**
     * 添加任务信息到队列
     * @param string $queue_name //队列名称
     * @param int $delay //延迟几秒
     * @param $jobClass //要处理的类或者方法
     * @param null $args //执行的传入参数
     *
     * demo DelayQueue::getInstance('test')->addTask(
     *    'app\common\lib\delayqueue\job\Test',
     *    strtotime('2018-05-02 20:55:20'),
     *    ['abc'=>111]
     * );
     */
    public function addTask($queue_name = '', $delay = 0, $jobClass, $args = null)
    {
        $key = $this->prefix.'delay:'.$queue_name;
        $delay = intval($delay);

        $params = [
            'doTask' => $jobClass,
            'data'  => $args,
            'addtime' => empty($args['addtime'])? time() : (intval($args['addtime'])??time()),
            'delay'=> $delay,
            'queuename_key'=> $key,
        ];
        $sorce = $params['addtime'] + $delay;

        $redis = $this->getRedis();
        //开启管道模式，代表将操作命令暂时放在管道里
        $pipe = $redis->multi($redis::PIPELINE);
        $pipe->sAdd($this->redis_delayQueueNames, $queue_name);
        $pipe->zAdd(
            $key,
            $sorce,
            //serialize($params)
            $this->compressData($params)
        );
        //提交管道里操作命令
        $pipe->exec();
        $pipe->discard();
    }

    /**
     * 取出到期队列的数据
     * @param string $queue_name
     * @param string $type
     * @return array
     */
    public function getTask($queue_name = '',  $type = 'delay'){
        $prefix = $this->prefix.$type.':';
        $key = $prefix.$queue_name;
        $redis = $this->getRedis();

        $result = $redis->zRangeByScore($key, 0 ,time(), ['limit' => [0,1] ]); //获取待处理的数据
        $res = [];
        if(!empty($result)){
            //取出后删除队列
            $del_num = $redis->zRem($key, ...$result);
            if(!$del_num==1){//删除成功后才可操作，因只操作一条数据具备并发的原子性
                return [];
            }

            foreach ($result as $item){
                $item = $this->unCompressData($item);
                $jobClass = $item['doTask'];
                if(!is_callable($jobClass)&&!@class_exists($jobClass)) {//去除未设置任务执行方法
                    print_r($key . ' undefined doTask' . PHP_EOL);
                }else{
                    $res[] = $item;
                }
            }

            if(!empty($res)){
                //加入重试等待队列，等执行了方法得去删除重试队列的数据
                $res = $this->addAgainTask($queue_name, $res)[0];
            }
            //print_r($res);
        }
        unset($result);

        return $res;
    }

    public function getQueueNames($redis=null){
        if(empty($redis)){
            $redis = $this->getRedis();
        }
        return $redis->sMembers($this->redis_delayQueueNames)??'';
    }

    private function run(){
        $delayQueueCoTaskPool = \app\common\lib\delayQueue\DelayQueueCoTaskPool::getInstance();
        $delayQueueCoTaskPool->init();
        $this->getTaskChannel = new Channel(2);
        $this->getFailTaskChannel = new Channel(10);

        $this->startDelayTaskQueue($delayQueueCoTaskPool);
        //return;
        $this->startDelayFailTaskQueue();
    }

    /**
     * 启动监听延时队列的任务
     * @param \app\common\lib\delayQueue\DelayQueueCoTaskPool $delayQueueCoTaskPool//需要单例
     */
    public function startDelayTaskQueue($delayQueueCoTaskPool = null){
        //延时队列的获取各队列中的任务处理
        go(function ()use( $delayQueueCoTaskPool){
            $redis = $this->getRedis();
            $queueNames_time = 0;$queueNames = [];
            do{
                try{
                    if(time()-$queueNames_time>1){
                        $queueNames = $this->getQueueNames($redis);
                        if(empty($queueNames)){
                            \Co::sleep(0.2);
                            continue;
                        }
                        $queueNames_time = time();
                    }
                    //正常的延时队列
                    $this->getTaskChannel->push([
                        $queueNames,
                        $delayQueueCoTaskPool,
                        'delay'
                    ]);
                    //有重试的延时队列
                    $this->getTaskChannel->push([
                        $queueNames,
                        $delayQueueCoTaskPool,
                        'again'
                    ]);
                }catch (\Throwable $e){
                    Log::write('延迟队列进行投递任务错误'.$e->getMessage(),'notice');
                }
            }while (true);
        });

        //延时队列与重试的延时队列的进行投递执行的任务
        for($i=0;$i<2;$i++){
            go(function (){
                do{
                    try{
                        $data = $this->getTaskChannel->pop();
                        [$queueNames, $delayQueueCoTaskPool, $type] = $data;
                        unset($data);
                        $delayQueue = DelayQueue::getInstance();

                        foreach ($queueNames as $v){
                            $rs = $delayQueue->getTask($v, $type);//获取要处理的任务
                            if($type=='delay'&&empty($rs)){//暂时无任务的时候
                                \Co::sleep(0.2);
                                continue;
                            }

                            //进行任务池投递//任务的通道满了时候，写入自动进行堵塞等待
                            $delayQueueCoTaskPool->addTaskAsync($rs);//使用不获取返回通道值的异步投递
                        }
                        unset($rs);

                        if($type=='fail'){
                            \Co::sleep(0.2);
                        }
                    }catch (\Throwable $e){
                        Log::write('获取到时间的延迟队列任务错误'.$e->getMessage(),'notice');
                    }
                }while (true);
            });
        }
    }

    /**
     * 启动监听失败时候的定时数据迁移
     */
    public function startDelayFailTaskQueue(){
        echo 'startDelayFailTaskQueue';
        $nextChannel = new Channel(1);
        //失败的协程任务,从redis存入数据库进行投递操作
        go(function ()use($nextChannel){
            $redis = $this->getRedis();$queueNames_time = 0;
            do{
                try {
                    if(time()-$queueNames_time>1){
                        $queueNames = $this->getQueueNames($redis);
                        if(empty($queueNames)){
                            return;
                        }
                        $queueNames_time = time();
                    }

                    $i = 0; $queueNames_total = count($queueNames);
                    foreach ($queueNames as $v){
                        $this->getFailTaskChannel->push([
                            $v, $i, $queueNames_total
                        ]);
                        $i++;
                    }

                    $nextChannel->pop();//堵塞等待本轮完成，避免并发抢出现重复处理同一个
                    echo '失败的延迟队列任务存储数据库 本轮处理完成';
                    \Co::sleep(60*1);
                }catch (\Throwable $e){
                    Log::write('失败的延迟队列任务存储数据库错误'.$e->getMessage(),'notice');
                }
            }while(true);
        });
        //失败的协程任务,从redis存入数据库进行入库操作
        for($i=0;$i<5;$i++){
            go(function ()use($nextChannel){
                $redis = $this->getRedis();
                do{
                    try{
                        $data = $this->getFailTaskChannel->pop();
                        [$queueName, $idx, $queueNames_total] = $data;
                        unset($data);
                        $fail_key = $this->prefix.'fail:'.$queueName;

                        $iterator = null;
                        $res = $redis->lRange($fail_key, 0,9);//取出10个

                        /*echo PHP_EOL.'进行定时迁移失败搜索 获取数据'.$idx.$fail_key.PHP_EOL;
                        var_dump($res);
                        echo PHP_EOL.'进行定时迁移失败搜索 获取数据 end'.$idx.$fail_key.PHP_EOL;*/

                        if(!empty($res)){
                            $res2 = [];
                            foreach ($res as $item){
                                $res2[] = $this->unCompressData($item);
                            }

                            /*echo PHP_EOL.'进行定时迁移失败搜索 入库数据'.$idx.$fail_key.PHP_EOL;
                            print_r($res2);
                            echo PHP_EOL.'进行定时迁移失败搜索 入库数据 end'.$idx.$fail_key.PHP_EOL;*/

                            if(!empty($res2[0])){
                                $taskItem = $res2[0];
                                if (method_exists($taskItem['doTask'], 'saveFailLog') ){
                                    $taskObject = new $taskItem['doTask'];
                                    $rs = $taskObject->saveFailLog($res2, 'redis超出重试次数（定时器入库）', 1);

                                    /*echo PHP_EOL.'进行定时迁移失败搜索 入库操作'.$idx.$fail_key.PHP_EOL;
                                    //var_dump($rs);
                                    echo PHP_EOL.'进行定时迁移失败搜索 入库操作 end'.$idx.$fail_key.PHP_EOL;*/

                                    if(!empty($rs)){
                                        $redis->lTrim($fail_key, count($res),-1);
                                    }
                                }
                            }
                        }
                        unset($res);

                        if(intval($idx) == intval($queueNames_total)-1){
                            //echo $idx; echo '---'.intval($queueNames_total);
                            $nextChannel->push('ok');
                        }
                    }catch (\Throwable $e){
                        echo '进行定时迁移失败搜索 失败数据入库时异常: '.$e->getMessage();
                        Log::write('进行定时迁移失败搜索 失败数据入库时异常'.$e->getMessage(),'notice');
                    }
                }while (true);
            });
        }
    }

    /**
     * //添加重试操作，先默认添加等任务成功时再删除
     * @param string $queue_name
     * @param $data
     */
    public function addAgainTask($queue_name = '', &$data){
        $again_key = $this->prefix.'again:'.$queue_name;
        $fail_key = $this->prefix.'fail:'.$queue_name;

        $now_time = time();
        $againList = [$again_key];
        $failList = [$fail_key];
        $list = [];
        foreach($data as $item){
            if(empty($item['doTask'])){
               continue;
            }

            $item['queue_again_key'] = $again_key;//用于任务处理后查找移除
            $item['queue_fail_key'] = $fail_key;//用于任务处理后查找移除
            $list[]= $item;

            $item['try_times'] = empty($item['try_times'])?1: intval($item['try_times'])+1;//重新尝试次数+1
            $delay = $this->computedAgainDelay([
                'nowtime' => $now_time,
                'try_times' => $item['try_times'],//尝试次数
            ]);

            $item = $this->compressData($item);
            if($delay==-1){//已经为失败的队列
                array_push($failList, $item);
            }else{
                //再次重试的队列
                array_push($againList, $delay, $item);
            }
        }
        unset($data);

        $redis = $this->getRedis();
        count($againList)>1&&call_user_func_array(array($redis, 'zAdd'), $againList); //添加重试的数据
        count($failList)>1&&call_user_func_array(array($redis, 'rPush'), $failList); //添加失败的数据

        return [
            $list,
        ];
    }

    /**
     * 计算重试的延时时间
     * @param array $arr
     * @return int
     */
    private function computedAgainDelay($arr = []){
        $arr['nowtime'] = intval($arr['nowtime']);
        if(empty($arr['nowtime'])){
            $arr['nowtime'] = time();
        }
        $arr['try_times'] = intval($arr['try_times'])??1;
        if($arr['try_times']>6){
            //超过多次直接失败
            $val = -1;
            $arr['try_times'] = 7;
        }else if(6>=$arr['try_times']&&$arr['try_times']>3){
            $delay = 60*60*2;
            $delay = 5;
            $val = $arr['nowtime'] + $delay;
        }else if(3 == $arr['try_times']){
            $delay = 60*60;
            $delay = 5;
            $val = $arr['nowtime'] + $delay;
        }else if(2==$arr['try_times']){
            $delay = 60*10;
            $delay = 3;
            $val = $arr['nowtime'] + $delay;
        }else{
            $delay = 10;
            $delay = 1;
            $val = $arr['nowtime'] + $delay;
        }

        return $val;
    }

    //通过自定义进程启动进行监听处理
    public function startInMyProcess($Server){
        //添加进程进行任务获取监听
        $Server->addProcess($this->createProcess());
    }

    protected function createProcess(){
        return new \Swoole\Process(function ($process) {
            $this->setProcessName($process,'user_delayQueue');

            $this->run();
        });
    }

    protected function setProcessName($process, $name){
        $serverName = 'swoole_http_server';
        $appName    = Config::get('app.name', 'ThinkPHP');
        $name = sprintf('%s: %s for %s', $serverName, $name, $appName);
        $process->name($name);
    }


}