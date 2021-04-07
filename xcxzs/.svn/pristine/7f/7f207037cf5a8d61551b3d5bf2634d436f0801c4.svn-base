<?php

namespace app\common\websocket;



use app\common\traits\TraitInstance;
use Smf\ConnectionPool\ConnectionPool;
use Smf\ConnectionPool\Connectors\PhpRedisConnector;
use Swoole\Timer;
use think\Container;
use think\facade\Config;
use think\swoole\Manager;
use think\swoole\Pool;
use think\swoole\websocket\Room;


class BroadcastProcess
{
    use TraitInstance;
    /**
     * @var Manager
     */
    private $manager;

    protected $config=[ ];

    /**
     * Worker 进程数组
     * @var array
     */
    private $process_list = [];

    private $queueKey = 'swoole:_broadcast';

    private $tableKey = '_broadcast_process';

    /**
     * @var \swoole_table
     */
    private $table = null;
    /**
     * @var RedisRoom
     */
    private $tableRoom = null;

    /**
     * 初始化操作
     * @param array $config
     */
    public function init($config=[]){
        $this->config = [
            'process_num' => '8',
            'redis' => $config['pool']['redis'],
            'local' => $config['distributed']['local'],
        ];

        $this->manager = Container::getInstance()->make(Manager::class);
        $table = new \swoole_table(intval($this->config['process_num'])+1);
        $table->column('timer_id',7,32);
        $table->column('isRun',1,1);
        $table->create();
        $this->table = $table;
        $this->queueKey = $this->queueKey.':'.$this->config['local']['ip'].'-'.$this->config['local']['port'];

        $this->createPool();
    }

    protected function createPool(){
      $processPool = [];
      for ($i=0;$i<$this->config['process_num'];$i++){
          $process = $this->createProcess($i);
          $this->manager->addProcess($process);
          $processPool[$i] = $process;
      }
      return $processPool;
    }

    protected function createProcess($i){
        /**
         * 用户进程进行广播功能，循环接收unixSocket的消息，并发给服务器的所有连接
         */
        //创建子进程
        return new \Swoole\Process(function ($process)use($i) {
            $this->setProcessName($process,'user_'.$i);

            $this->prepareRedis();
            $currentKey = $this->config['local']['ip'].'_'.$this->config['local']['port'].$this->tableKey.$i;
            $table = $this->table->get($currentKey);

            $timer_id = Timer::tick(250,function ()use($table, $currentKey){
                /*if(!empty($table['isRun'])){
                    return;
                }*/

                $this->runWithRedis(function ($redis)use($currentKey){
                    $this->taskRun($redis, $currentKey);
                });
            });

            if(!empty($table['timer_id'])&&$table['timer_id']!=$timer_id){//删除旧的timer_id
                $this->table->set($currentKey,[
                    'isRun' => 0
                ]);
                \Swoole\Timer::clear($table['timer_id']);
            }
            $this->table->set($currentKey,[
                'timer_id' => $timer_id
            ]);

        }, false, 2, true);//使用SOCK_DGRAM避免处理粘包问题
    }
    protected function setProcessName($process, $name){
        $serverName = 'swoole_http_server';
        $appName    = Config::get('app.name', 'ThinkPHP');
        $name = sprintf('%s: %s for %s', $serverName, $name, $appName);
        $process->name($name);
    }

    /**
     * @param \Redis $redis
     * @param $currentKey
     */
    protected function taskRun($redis, $currentKey){
        $table = $this->table->get($currentKey);
        $redis->sAdd('swoole:tablekey',$currentKey.'-'.json_encode($table['isRun']));
        if(!empty($table['isRun'])){
            return;
        }

        $msg = $redis->rPop($this->queueKey);
        if (empty($msg)){
            return;
        }
        $this->table->set($currentKey,[
            'isRun' => 1
        ]);

        $msg = $this->unCompressData($msg);
        if(!empty($msg['_$method'])){
            $unserializeObj = \Opis\Closure\unserialize($msg['_$method']);

            if(is_callable($unserializeObj)===true){
                call_user_func($unserializeObj, $msg['_$data']);
            }elseif(method_exists($unserializeObj,'run')===true){
                $unserializeObj = new $unserializeObj();
                call_user_func([$unserializeObj, 'run'], $msg['_$data']);
            }
        }else{
            $this->doSend($msg['_$data']);
        }

        $this->table->set($currentKey,[
            'isRun' => 0
        ]);
    }


    /**
     * 任务投递
     * @param $data
     */
    public function task($data){
        $server = $this->manager->getServer();
        $server->push($data['sender'], '42["dd", {cc: "进行广播投递"}]');

        $this->runWithRedis(function ($redis)use($data){
            $redis->lPush($this->queueKey,$this->compressData([
                '_$data' => $data,
            ]));
        });
    }
    /**
     * 默认的任务处理方法
     * @param $data
     */
    protected function doSend($data){
        $server = $this->manager->getServer();
        $server->push($data['sender'], '42["dd", {cc: "正在投递群发"}]');

        $descriptors = [];
        if(!empty($data['toRoom'])){//聊天室内群发
            foreach ($data['toRoom'] as $value){
                $descriptors = array_merge($descriptors, $this->getTableRoom()->getClients($value));//获取本机中room的Clients的fd集
            }

            unset($data['toRoom']);
            $descriptors = $this->addDescriptors($descriptors);
        }

        if(!empty($data['isBroadcast'])){//进行广播时
            unset($data);
            $descriptors = $this->addDescriptors($this->getWebsocketConnections());
        }

        foreach ($descriptors as $fd){
            $fd = intval($fd);
            if (!$server->isEstablished($fd)) {//判断在线
                continue;
            }
            //$fd==$data['sender']

            $server->push($fd, $data['payload']);
        }
    }
    /**
     * 进行匿名函数/类投递时
     * @param $data
     * @param $callFun
     * @throws \Exception
     */
    protected function taskClosure($data,$callFun){
        if(!is_callable($callFun)&&!method_exists($callFun,'run')){
            throw new \Exception('请在对应类中缺失run方法或者使用匿名函数');
        }
        $closure = \Opis\Closure\serialize($callFun);

        $this->runWithRedis(function ($redis)use($data, $closure){
            $redis->lPush($this->queueKey,$this->compressData([
                '_$data' => $data,
                '_$method' => $closure
            ]));
        });
    }

    /**
     * 获取要推送的fd去重集合
     * @param array $descriptors
     * @return array
     */
    protected function addDescriptors($descriptors)
    {
        return array_values(array_unique($descriptors));
    }

    /**
     * 获取当前所有连接用于广播
     * @param $server
     * @return array
     */
    protected function getWebsocketConnections($server)
    {
        return array_filter(iterator_to_array($server->connections), function ($fd)use($server) {
            return (bool) $server->getClientInfo($fd)['websocket_status'] ?? false;
        });
    }
    protected function getTableRoom(){
        if(empty($this->tableRoom)){
            $this->tableRoom = Container::getInstance()->make(Room::class);
        }
        return $this->tableRoom;
    }




    protected function prepareRedis()
    {
        $pools = Container::getInstance()->make(Pool::class);
        $pool = $pools->get("websocket.uid_fd_room");
        if(empty($pool)){
            $config     = $this->config['redis'];
            $pool = new ConnectionPool(
                Pool::pullPoolConfig($config),
                new PhpRedisConnector(),
                $config
            );
            $pools->add("websocket.uid_fd_room", $pool);
        }
    }
    protected function getRedisPool(){
        $pools = Container::getInstance()->make(Pool::class);
        return $pools->get("websocket.uid_fd_room");
    }
    protected function runWithRedis(\Closure $callable)
    {
        $redis = $this->getRedisPool()->borrow();

        try {
            return $callable($redis);
        } finally {
            $this->getRedisPool()->return($redis);
        }
    }

    /**
     * 压缩字符串内容
     * @param string|array $data
     * @return false|string
     */
    protected function compressData($data){
        return gzcompress(json_encode($data,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 解压字符串内容
     * @param string $data
     * @return false|string
     */
    protected function unCompressData($data = ''){
        return json_decode(gzuncompress($data),true);
    }

}


