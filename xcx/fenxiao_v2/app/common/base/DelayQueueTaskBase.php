<?php
declare (strict_types = 1);

namespace app\common\base;


use app\common\pool\RedisPool;
use app\common\traits\TraitCompressData;
use think\Exception;

/**
 * 延迟队列的任务操作父类
 * Class DelayQueueTaskBase
 * @package app\common\base
 */
class DelayQueueTaskBase
{
    use TraitCompressData;


    protected $db = null;

    public function __construct()
    {
        $this->db = (new HhDb())->init();//数据库调用
    }

    protected function getRedis(){
        $redisPool= RedisPool::getInstance();
        return $redisPool->init($redisPool->setConfig('swoole.pool.redis',[
            'database' => 5,
            'max_active' => 35,
        ]));
    }

    /**
     * 获取任务锁的key和此次可解锁操作的版本号
     * @param $taskItem
     * @return string[]
     */
    protected function getLockKeyVersion($taskItem){
        //任务锁的版本号对上才可手动删除，避免此次任务执行到锁自动失效时，删除了新创建的锁
        if(empty($taskItem['try_times'])){$taskItem['try_times'] = 0;}
        $lock_version= time().'_'.$taskItem['try_times'].'_'.mt_rand(1,1000);

        unset($taskItem['key'], $taskItem['try_times']);//去除该任务中会自动变化的无redis的值
        $key = 'fenxiao:lock:'.md5(json_encode($taskItem,JSON_UNESCAPED_UNICODE));

        return [$key, $lock_version];
    }

    /**
     * 锁中运行任务
     * @param $taskItem //任务值
     * @param int $limit_time //锁的有效时间
     * @param callable $callback
     * @return bool|string
     */
    public function runInLock($taskItem, $limit_time = 5, $callback = null){
            $ok = false;
            try {
                if(!is_callable($callback)){
                    throw new Exception('缺失设置的回调函数');
                }
                $redis = $this->getRedis();
                [$key, $lock_version] = $this->getLockKeyVersion($taskItem);
                unset($taskItem);

                $ok = $redis->set($key, $lock_version, ['nx', 'ex' => $limit_time]);

                echo PHP_EOL.'dorun'.PHP_EOL;
                $ok==true&&$callback();
                echo PHP_EOL.'dorun end'.PHP_EOL;

            }catch (DelayQueueToLogException $e){//失败错误直接存储日志，不在进行重试
                echo PHP_EOL.$e->getMessage().PHP_EOL;
                throw new DelayQueueToLogException($e->getMessage());
            }catch (\Throwable $e){
                echo PHP_EOL.$e->getMessage().PHP_EOL;
                throw new Exception($e->getMessage());
            } finally {
                if ($ok==true && ($redis->get($key) == $lock_version)) {//需要版本号对上才可手动删除
                    $redis->del($key);
                }
            }
    }

    public function runBox(){
        $rs = null;
        $args = func_get_args();
        $taskItem = $args[1];
        $is_runed = false;
        try {
            if(!empty($taskItem['try_times'])&&$taskItem['try_times']>6){//超过尝试次数
                throw new DelayQueueLogException('已到达该任务重试次数上限，请排查问题');
            }

            //$rs = $this->run(...$args);
            $this->runInLock($taskItem,5,function () use($args, $taskItem, &$rs){
                $rs = $this->run(...$args);
            });
            $is_runed = true;

            //任务整体执行完毕后删除任务缓存
            $this->removeTaskInRedis($taskItem);
        }  catch (DelayQueueToLogException $e){//直接进行失败日志存储，不在重试
            $errMsg = $e->getMessage();
            go(function ()use($taskItem, $errMsg){
                $r = $this->saveFailLog($taskItem, $errMsg);

                //进行失败日志迁移
                if(!empty($r)){
                    $this->removeTaskInRedis($taskItem);
                }
            });
            if($is_runed!==true){//执行完毕后出现的异常不抛出
                throw new Exception($errMsg);
            }
        } catch (\Throwable $e){//执行过程中的一些错误
            $errMsg = $e->getMessage();
            if((!empty($taskItem['try_times'])&&$taskItem['try_times']>=6)){//出现错误时且超过尝试次数
                go(function ()use($taskItem, $errMsg){
                    $r = $this->saveFailLog($taskItem, $errMsg);

                    //进行失败日志迁移
                    if(!empty($r)){
                        $this->removeTaskInRedis($taskItem);
                    }
                });
            }

            if($is_runed!==true){//执行完毕后出现的异常不抛出
                throw new Exception($errMsg);
            }
        } finally {
            unset($this->db);
            return $rs;
        }
    }

    public function removeTaskInRedis($taskItem, $is_multi =0){
        //先删除延迟队列中的下次等待的操作
        $redis = $this->getRedis();

        if($is_multi==1){
            $taskItem2 = [];$taskItem2_keys =[]; $taskItem3 = []; $taskItem3_keys = [];
            foreach ($taskItem as $item){
                unset($item['key']);//去除无redis的值
                $item['try_times'] = empty($item['try_times'])?1: intval($item['try_times'])+1;//删除新的重试
                if($item['try_times']>6){//超过最大错误次数
                    if(!isset($taskItem2[$item['queue_fail_key']])){
                        $taskItem2[$item['queue_fail_key']] = [];
                    }
                    $taskItem2_keys[$item['queue_fail_key']] = $item['queue_fail_key'];
                    $taskItem2[$item['queue_fail_key']][] = $this->compressData($item);
                }else{
                    if(!isset($taskItem3[$item['queue_again_key']])){
                        $taskItem3[$item['queue_again_key']] = [];
                    }
                    $taskItem3_keys[$item['queue_fail_key']] = $item['queue_fail_key'];
                    $taskItem3[$item['queue_again_key']][] = $this->compressData($item);
                }
            }
            unset($taskItem);

            if(!empty($taskItem2_keys)) {
                foreach ($taskItem2_keys as $v2){
                    if(!empty($taskItem2[$v2])){
                        $redis->sRem($v2, ...$taskItem2[$v2]);//删除失败的
                    }
                }
            }
            if(!empty($taskItem3_keys)) {
                foreach ($taskItem3_keys as $v3){
                    if(!empty($taskItem3[$v3])){
                        $redis->zRem($v3, ...$taskItem3[$v3]);//删除重试的
                    }
                }
            }
        }else{
            unset($taskItem['key']);//去除无redis的值
            $taskItem['try_times'] = empty($taskItem['try_times'])?1: intval($taskItem['try_times'])+1;//删除新的重试
            if($taskItem['try_times']>6){//超过最大错误次数
                $redis->lRem($taskItem['queue_fail_key'], $this->compressData($taskItem));//删除失败的
            }else{
                $redis->zRem($taskItem['queue_again_key'], $this->compressData($taskItem));//删除重试的
            }
        }
    }

    /**
     * @return bool
     */
    public function run() {}

    /**
     * 存入日志操作数据库
     * @param $taskItem
     * @param $errMsg
     * @param $is_multi //是否执行批量写入操作
     * @return bool
     */
    public function saveFailLog($taskItem = [], $errMsg='', $is_multi =0 ){
        $inData = [];
        //print_r($taskItem);
        if($is_multi==1){
            foreach ($taskItem as $item){
                $inData[] = [
                    'name' => empty($item['doTask'])?'名称丢失': (is_callable($item['doTask'])?'匿名函数': $item['doTask']),
                    'parmas' => json_encode($item,JSON_UNESCAPED_UNICODE),
                    'type' => 'fail',
                    'errmsg' => $errMsg,
                    'create_time' => time(),
                    'queuename_key' => $item['queuename_key']??''
                ];
            }

            $rs = $this->db->name('delay_task_log')->insertAll($inData);
        }else{
            $inData = [
                'name' => empty($taskItem['doTask'])?'名称丢失': (is_callable($taskItem['doTask'])?'匿名函数': $taskItem['doTask']),
                'parmas' => json_encode($taskItem,JSON_UNESCAPED_UNICODE),
                'type' => 'fail',
                'errmsg' => $errMsg,
                'create_time' => time(),
                'queuename_key' => $taskItem['queuename_key']??''
            ];

            $rs = $this->db->name('delay_task_log')->insert($inData);
        }

        return $rs;
    }
}

/**
 * 抛出异常给日志存储使用
 * Class DelayQueueToLogException
 * @package app\common\base
 */
class DelayQueueToLogException extends \Exception{

}
