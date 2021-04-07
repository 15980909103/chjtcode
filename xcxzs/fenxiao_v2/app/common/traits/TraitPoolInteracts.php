<?php
namespace app\common\traits;

use Swoole\Coroutine;
use Swoole\Coroutine\Channel;
use think\Config;
use think\Container;

/**
 * 连接池基础接口
 * Trait TraitInstance
 * @package app\common\traits
 */
trait TraitPoolInteracts{
    /** @var Channel[] */
    protected $pools = []; //存储多个池中的不同对象
    protected $connectionCount = []; //存储多个池中不同对象的连接数量
    protected static $context=[]; //存储此次操作的上下文切换


    public function clearPoos(){
        foreach ($this->pools as $pool){
            while (!$pool->isEmpty()){
                $connection = $pool->pop(1);
                $connection->close();
            }
        }
    }
    /**
     * 获取连接池
     * @param array $config
     * @return Channel
     */
    protected function getPool(array $config)
    {
        $name = $config['_flag_name'];
        if (empty($this->pools[$name])) {
            $this->pools[$name] = new Channel($config['max_active']);
        }
        return $this->pools[$name];
    }
    /**
     * 创建连接池链接
     * @param array $config
     * @return mixed
     */
    protected function createPoolConnection(array $config)
    {
        return $this->createConnection($config);
    }

    /**
     * 在协程上下文中安全调用（子类中的调用）获取连接池中的连接
     * @param array $config
     * @return callable|mixed|string
     */
    protected function getPoolConnectionInContext(array $config){
       $flag_name = $config['_flag_name'];
       return self::contextRememberData($flag_name, function () use ($config) {//上下文中存储调用
            return $this->getPoolConnection($config); //获取连接池中的连接
       });
    }

    /**
     * 在协程中时使用 getPoolConnectionInContext()
     * 获取连接池中的连接
     * @param array $config
     * @return mixed
     */
    protected function getPoolConnection(array $config)
    {
        $flag_name = $config['_flag_name'];
        $pool = $this->getPool($config);

        if (!isset($this->connectionCount[$flag_name])) {
            $this->connectionCount[$flag_name] = 0;
        }

        $now_num = $this->connectionCount[$flag_name];
        $now_num++;

        //echo PHP_EOL.PHP_EOL.PHP_EOL;print_r($pool->stats());PHP_EOL.PHP_EOL.PHP_EOL;

        if ( $now_num <= $config['max_active'] && $pool->isEmpty() ) {
            $this->connectionCount[$flag_name] = $now_num;
            //新建
            $connection = $this->createPoolConnection($config);

            //echo PHP_EOL.PHP_EOL.PHP_EOL.$this->connectionCount[$flag_name].PHP_EOL.PHP_EOL.PHP_EOL;
        } else {
            $connection = $pool->pop($config['max_wait_time']);

            //echo PHP_EOL.PHP_EOL.'pool:pop:'.getmypid().'|'.Coroutine::getCid().'----pp'.var_dump($connection).PHP_EOL.PHP_EOL;

            //echo PHP_EOL.PHP_EOL.PHP_EOL.$this->connectionCount[$flag_name].PHP_EOL.PHP_EOL.PHP_EOL;

            if ($connection === false) {//通道连接数已全部获取
                throw new \RuntimeException(sprintf(
                    'Borrow the connection timeout in %.2f(s), connections in pool: %d, all connections: %d',
                    $config['max_wait_time'],
                    $pool->length(),
                    $this->connectionCount[$flag_name] ?? 0
                ));
            }
            //判断连接是否还处于连接成功
            if($this->isConnected($connection)==false){
                //关闭错误连接
                $this->disconnect($connection, $flag_name);
                //重新创建连接
                $connection = $this->createConnection($config);
            }
        }

        return $this->wrapProxy($pool, $connection, $flag_name);
    }

    protected function wrapProxy(Channel $pool, $connection, $flag_name)
    {
        if(empty($flag_name)){
            throw new \InvalidArgumentException(sprintf('Failed to connect Redis server: %s', '参数缺失'));
        }
        Coroutine::defer(function () use ($pool, $connection, $flag_name) {
            //自动归还连接
            if (!$pool->isFull()) {
                self::contextRemoveData($flag_name);//清除连接的上下文数据
                $ret = $pool->push($connection, 0.001);
                if ($ret === false) {//通道占满写入失败时
                    $this->disconnect($connection, $flag_name);//关闭连接
                }
            }else{
                $this->disconnect($connection, $flag_name, true);//关闭连接,去除上下文操做
            }
            unset($connection);
        });

        return $connection;
    }


    /**
     * 获取某个对象连接池的状态
     * @param array $config
     * @return mixed
     */
    public function getPoolStatus(array $config = []){
        if(empty($config)){
            throw new \InvalidArgumentException(sprintf('Failed to connect Redis server: %s', '参数缺失'));
        }

        $config = $this->getConnectionConfig($config);
        $flag_name = $config['_flag_name']; //带上标识

        return $this->getPool($flag_name)->stats();
    }


    //============配置操作start============//
    /**
     * 获取连接配置
     * @param string $config_name
     * @param array $newconfig
     * @return array|mixed
     */
    public function getConnectionConfig(string $config_name = '', $newconfig=[]){
        if(empty($config_name)){
            throw new \InvalidArgumentException(sprintf('Failed to connect Redis server: %s', '缺少swoole.pool.redis配置'));
        }
        $configObj = Container::getInstance()->make(Config::class);
        $config = $configObj->get($config_name);
        if(!empty($newconfig)){
            $config = array_merge($config,$newconfig);
        }
        if(empty($config)||empty($config['host'])){
            throw new \InvalidArgumentException(sprintf('Failed to connect Redis server: %s', '缺少swoole.pool.redis配置'));
        }
        if(empty($config['max_active'])){
            $config['max_active'] = 3;
        }
        if(empty($config['max_wait_time'])){
            $config['max_wait_time'] = 3;
        }

        $flag_name = md5($config_name.json_encode($config)); //带上标识
        $config['_flag_name'] = $flag_name;

        return $config;
    }


    //============配置操作end============//

    //============上下文操作start=============//
    use TraitContext;
//    private static function contextSetData(string $key, $value, $cid= null)
//    {
//        $cid = $cid??getmypid().'_'.Coroutine::getCid();
//        static::$context[$cid][$key] = $value;
//    }
//    private static function contextGetData(string $key, $default = null, $cid= null)
//    {
//        $cid = $cid??getmypid().'_'.Coroutine::getCid();
//        return static::$context[$cid][$key] ?? $default;
//    }
//
//    private static function contextHasData(string $key, $cid= null)
//    {
//        $cid = $cid??getmypid().'_'.Coroutine::getCid();
//        return isset(static::$context[$cid]) && !empty(static::$context[$cid][$key]);
//    }
//    /**
//     * @param string $key
//     * @param $value
//     * @param null $cid
//     * @return mixed|null
//     */
//    private static function contextRememberData(string $key, $value, $cid= null)
//    {
//        $cid = $cid??getmypid().'_'.Coroutine::getCid();
//        if (self::contextHasData($key, $cid)) {
//            return self::contextGetData($key, $cid);
//        }
//        if ($value instanceof \Closure) {
//            // 获取缓存数据
//            $value = $value();
//        }
//
//        self::contextSetData($key, $value, $cid);
//        return $value;
//    }
//    private static function contextRemoveData(string $key, $cid=null)
//    {
//        $cid = $cid??getmypid().'_'.Coroutine::getCid();
//        unset(static::$context[$cid][$key]);
//        if(empty(static::$context[$cid])){
//            unset(static::$context[$cid]);
//        }
//    }
    //============上下文操作end=============//

    /**
     * @param string $name
     * @return mixed
     * protected function setConfig(string $name, $newconfig){
     *   return $this->getConnectionConfig($name);
     * }
     */
    abstract public function setConfig(string $name);
    //创建连接对象实例
    abstract public function init(array $config,bool $force);
    //创建连接
    abstract protected function createConnection( $config);
    //检测连接是否连接成功
    abstract protected function isConnected($connection,string $flag_name);

    //关闭连接 //关闭时注意清除连接的上下文数据 self::contextRemoveData($flag_name);
    /**
     * 关闭连接
     * @param $connection
     * @param $flag_name
     * @param $contextRemove
     */
    public function disconnect($connection,$flag_name,$contextRemove = false)
    {
        if($contextRemove==true){
            if(empty($flag_name)){
                throw new \InvalidArgumentException(sprintf('Failed to connect Redis server: %s', '参数缺失'));
            }
            self::contextRemoveData($flag_name);//清除连接的上下文数据
        }

        $connection->close();
    }
}

