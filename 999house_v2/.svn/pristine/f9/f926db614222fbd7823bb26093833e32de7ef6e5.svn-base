<?php
declare (strict_types = 1);

namespace app\common\manage;
use app\common\traits\TraitInstance;
use Opis\Closure\SerializableClosure;
use Swoole\Server\Task;
//use think\queue\Job;
//https://github.com/top-think/think-queue
use think\App;
use Swoole\Server;
use think\Container;

/**
 * 任务投递
 * TaskManage::getInstance()->asyncPost('aaa',\app\task\Test::class,function ($result){
 *  //echo 666;
 *  //print_r($result);
 * });
 */
class TaskManage
{
    use TraitInstance;
    /**
     * @var Server
     */
    public $server = null;
    
    public function __construct($server = null)
    {
        if(empty($server)){
            $this->server = Container::getInstance()->make(Server::class);
        }else{
            $this->server = $server;
        }
    }


    

    /**
     *
     * 投递任务
     * @param null $postData 投递的数据
     * @param null $doTask 匿名函数或者调用类 //funciton(){} // \app\task\Test::class, 类中需要实现run方法
     * @param null $callFinish 完成时回调
     * @return bool|string
     */
    final function asyncPost($postData = null, $doTask = null, $callFinish = null){
        try {
            $postData = $this->formatData($postData,$doTask);

            return $this->asyncPostTask($postData, $callFinish);
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * 异步swoole任务投递
     * @param null $arr
     * @param null $callFinish
     * @return bool
     */
    private function asyncPostTask($arr = null, $callFinish = null){
        if(empty($callFinish)){
            $callFinish = function ($server, $task_id, $data){ };
        }

        $result = $this->server->task($arr, -1, $callFinish);
        return $result !== false;
    }

    /**
     * 处理并发执行
     * @param array $tasks
     * @param float $timeout
     * @return mixed
     */
    public function asyncPostMultiTask($tasks = [], $dotask,$timeout = 0.5){
        $arr =[];
        foreach ($tasks as $v){
            $postData = $this->formatData($v,$dotask);
            array_push($arr,$postData);
        }
        unset($tasks);
        $result = $this->server->taskCo($arr,(float)$timeout);
        return $result;

    }

    /**
     * 格式话支持匿名函数与类的投递
     * @param null $postData
     * @param null $doTask
     * @return false[]|string[]
     * @throws \Exception
     */
    private function formatData($postData = null, $doTask = null){
        try {
            if(!is_callable($doTask)&&!method_exists($doTask,'run')){
                throw new \Exception('请在对应类中缺失run方法或者使用匿名函数');
            }

            //$closure = serialize(new SerializableClosure($doTask));
            $closure = \Opis\Closure\serialize($doTask);
            //var_dump(method_exists(\app\admin\controller\test::class,'run'));

            return [
                '_$action' => $this->compressData([
                    '_$post_data' => $postData,
                    '_$action' => $closure,
                ]) //数据压缩
            ];
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Sleep the script for a given number of seconds.
     * @param int $seconds
     * @return void
     */
    public function sleep($seconds)
    {
        if(empty($seconds)){
            return;
        }
        if ($seconds < 1) {
            usleep($seconds * 1000000);
        } else {
            sleep($seconds);
        }
    }

    /**
     * 压缩字符串内容
     * @param string|array $data
     * @return false|string
     */
    public function compressData($data){
        return gzcompress(json_encode($data,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 解压字符串内容
     * @param string $data
     * @return false|string
     */
    public function unCompressData($data = ''){
        return json_decode(gzuncompress($data),true);
    }
}
