<?php

namespace app\common\base;



use app\common\traits\TraitInstance;
use Smf\ConnectionPool\ConnectionPool;
use Smf\ConnectionPool\Connectors\PhpRedisConnector;
use Swoole\Timer;
use think\Container;
use think\facade\Config;
use think\swoole\Manager;
use think\swoole\Pool;
use think\swoole\websocket\Room;

//自定义进程池
class CustomProcessBase
{
    use TraitInstance;
    /**
     * @var Manager
     */
    private $manager;

    protected $config=[
        'name_prefix' => '',
        'process_num' => 1
    ];

    /**
     * Worker 进程数组
     * @var array
     */
    private $processPool = [];


    public function __construct()
    {
        $this->manager = Container::getInstance()->make(Manager::class);
    }

    /**
     * 初始化操作
     * @param array $config
     */
    protected function init($config=[]){
        $this->config = array_merge($this->config,$config);
        if(empty($this->config['name_prefix'])){
            throw new \Exception('缺失进程标识');
        }

        $this->processPool = $this->createPool();
    }

    protected function createPool(){
      $processPool = [];
      for ($i=0;$i<$this->config['process_num'];$i++){
          $process = $this->createProcessBase($i);
          $this->manager->addProcess($process);
          $processPool[$i] = $process;
      }
      return $processPool;
    }

    private function createProcessBase($i){
        /**
         * 用户进程进行广播功能，循环接收unixSocket的消息，并发给服务器的所有连接
         */
        //创建子进程
        return new \Swoole\Process(function ($process)use($i) {
            $this->setProcessName($process,$this->config['name_prefix'].$i);

            $this->createProcess($process,$i);

        }, false, 2, true);//使用SOCK_DGRAM避免处理粘包问题
    }

    protected function createProcess($process,$index){ }

    protected function setProcessName($process, $name){
        $serverName = 'swoole_http_server';
        $appName    = Config::get('app.name', 'ThinkPHP');
        $name = sprintf('%s: %s for %s', $serverName, $name, $appName);
        $process->name($name);
    }

    protected function onRecvToFather(){
        foreach ($this->processPool as $process){
            go(function() use ($process) {
                $socket = $process->exportSocket();
                $socket->recv();//一次recv只会收到一个"hello master\n"字符串 不会出现多个"hello master\n"字符串
            });
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


