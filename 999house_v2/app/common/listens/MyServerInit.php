<?php
declare (strict_types = 1);

namespace app\common\listens;

use app\common\lib\wxapi\co\CoWxPool;
use app\common\pool\CoPool\CoPoolsTaskManager;
use app\common\pool\RedisPool;
use app\common\websocket\BroadcastProcess;
use Swoole\Timer;
use think\Container;
use think\Event;
use think\facade\Config;
use think\swoole\Manager;


class MyServerInit
{

    public function handle(Manager $manager)
    {
        $config = Config::get('swoole');
        BroadcastProcess::getInstance()->init($config);//添加广播进程

        $manager->onEvent('workerStart', [$this,'workerStart']);

        //进程退出时的一些清理
        $manager->onEvent('workerStop', [$this,'clearInWorker']);
        $manager->onEvent('WorkerError', [$this,'clearInWorker']);
        $manager->onEvent('WorkerExit', [$this,'clearInWorker']);
    }

    public function clearInWorker(){
        go(function (){
            RedisPool::getInstance()->clearPoos();
        });
        Timer::clearAll();
    }

    public function workerStart(){
        $obj = Container::getInstance()->make(CoWxPool::class);//用于微信公众号请求的协程工作池
        $obj->run();

        //协程任务池管理
        CoPoolsTaskManager::getInstance()->startPools();
    }

    public function onEvent(string $event, $listener, bool $first = false): void
    {
        $eventObj = Container::getInstance()->make(Event::class);
        $eventObj->listen("swoole.{$event}", $listener, $first);
    }

}
