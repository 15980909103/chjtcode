<?php

namespace app\common\websocket;


use InvalidArgumentException;
use Redis as PHPRedis;
use Smf\ConnectionPool\ConnectionPool;
use Smf\ConnectionPool\Connectors\PhpRedisConnector;
use think\Container;
use think\facade\Config;
use think\swoole\Manager;
use think\swoole\Pool;



/**
 * 聊天室的操作 操作用户uid和room之间的关系
 */
class RedisRoom
{
    /**
     * Rooms key
     * 房间中成员的fd集合
     * @const string
     */
    public const ROOM_CLIENTS = 'roomToFds'; //房间中成员的fd集合
    /**
     * Clients key
     * 用户有加入的房间集合
     * @const string
     */
    public const CLIENT_ROOMS = 'uidToRooms'; //用户有加入的房间集合

    /** @var ConnectionPool */
    protected $pool;

    /** @var Manager */
    protected $manager;
    
    /**
     * @var array
     */
    protected $config = [ ];

    protected $room_expireTime = 3600*24;

    protected $pools = null;


    /**
     * TableRoom constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config['redis'] = Config::get('swoole.pool')['redis'];
        $this->config['redis']['min_active'] = 2;
        $this->config['redis']['max_active'] = 5;
        $this->config['local'] = Config::get('swoole.distributed')['local'];
        $this->config['udpsocket_group'] = Config::get('swoole.distributed')['udpsocket_group'];
        $this->config = array_merge($this->config, $config);

        if(empty($this->config['local'])){
            throw new InvalidArgumentException("Invalid config local_ip|local_port");
        }

        $this->manager = Container::getInstance()->make(Manager::class);
        $this->pools = Container::getInstance()->make(Pool::class);
    }

    /**
     * Get key.
     *
     * @param string $key
     * @param string $table
     *
     * @return string
     */
    protected function getRedisKey(string $key, string $table)
    {
        return "swoole:{$table}:{$key}";
    }

    /**
     * Do some init stuffs before workers started.
     *
     * @return $this
     */
    public function prepare()
    {
        $this->manager->onEvent('workerStart', function () {
            $this->prepareRedis();
        });
        return $this;
    }

    public function prepareRedis()
    {
        $this->pool = $this->pools->get("websocket.uid_fd_room");
        if(empty($this->pool)){
            $config     = $this->config['redis'];
            $this->pool = new ConnectionPool(
                Pool::pullPoolConfig($config),
                new PhpRedisConnector(),
                $config
            );
            $this->pools->add("websocket.uid_fd_room", $this->pool);
        }
    }

    protected function getPool(){
        if(empty($this->pool)){
            $this->pool = $this->pools->get("websocket.uid_fd_room");
        }
        return $this->pool;
    }

    /**
     * Add multiple socket uids to a room.
     *
     * @param string|int $uid
     * @param array|string $roomNames
     * @param int $type 1的时候都绑定，0的时候只绑定room映射uid
     */
    public function add($uid, $roomNames, $type=1)
    {
        if(empty($uid)||empty($roomNames)){
            throw new InvalidArgumentException("Invalid uid|rooms");
        }
        $roomNames = is_array($roomNames) ? $roomNames : [$roomNames];

        foreach ($roomNames as $room) {
            $this->setClients($room, [$uid]);
        }

        if($type==1){
            $this->setRooms($uid, $roomNames);
        }
    }

    /**
     * 检测某个用户是否在某个房间内
     * @param $uid
     * @param $roomName
     * @return bool
     */
    public function isUidInRoom($uid, $roomName){
        $rooms = $this->getRooms($uid);
        if(empty($rooms)||!in_array($roomName,$rooms)){
            return false;
        }
        return true;
    }

    /**
     * 重置用户映射的fd对应用户的room中的关系（在此台机子），在用户重新登录时产生新的fd
     * @param $arr 
     * @param string|array $rooms
     */
    public function resetFdInRoom($arr, $rooms =[]){
        // return ;
        $fd = $arr['fd'];
        $old_fd = $arr['store_fdKeyInfo']['fd'];
        $uid = $arr['uid'];
        $isLocal = $arr['store_fdKeyInfo']['isLocal'];

        if(empty($rooms)) {
            return ;
        }
       
        if(is_string($rooms)){
            $rooms= [$rooms];
        }

        $this->runWithRedis(function (PHPRedis $redis) use ($rooms, $fd, $old_fd,$isLocal, $uid) {
            // room是否还存在
            $pipe = $redis->multi(PHPRedis::PIPELINE);
            foreach ($rooms as $item){
                $redisKey = $this->getRedisKey($item, self::ROOM_CLIENTS);
                $roomKey = $redisKey.':'.$this->config['local']['ip'].'-'.$this->config['local']['port'];
                $pipe->exists($roomKey);
            }
            $has = $pipe->exec();

            $pipe = $redis->multi(PHPRedis::PIPELINE);
            foreach ($has as $k => $v){
                $item = $rooms[$k];// 房间号
                if($v) {// 还存在的房间，替换旧的fd
                    $redisKey = $this->getRedisKey($item, self::ROOM_CLIENTS);
                    $roomKey = $redisKey.':'.$this->config['local']['ip'].'-'.$this->config['local']['port'];
                    // 是本台机子
                    if($isLocal==1){
                        $rs = (bool)$this->manager->getServer()->getClientInfo($old_fd)['websocket_status'] ?? false;
                        if($rs == false){// fd不在线，即未被使用
                            $redis->sRem($roomKey,$old_fd);
                        }
                        $redis->sAdd($roomKey,$fd);
        
                        $pipe->expire($roomKey, $this->room_expireTime);//设置过期时间
                    }  
                } else {// 不存在的房间，把该房间从用户加入的房间集里去掉
                    $redisKey = $this->getRedisKey($uid, self::CLIENT_ROOMS);
                    $redis->sRem($redisKey,$item);
                }
            }
            $pipe->exec();
        });

    }

    /**
     * 从房间中删除某个人
     * @param $uid
     * @param array $rooms
     * @param int $isDelete 是否完全删除解散房间
     */
    public function deleteUidInRoom($uid, $rooms = [], $uidIp, $isDelete = 0)
    {
        $rooms = is_array($rooms) ? $rooms : [$rooms];
        $rooms = count($rooms) ? $rooms : $this->getRooms($uid);
        $group_socket = $this->config['udpsocket_group'];
        array_push($group_socket,$this->config['local']);

        if($isDelete==0){
            foreach ($rooms as $room) {
                foreach ($group_socket as $ipConfig){
                    if($uidIp['local_ip']&&$uidIp['local_port']&&$uidIp['local_ip']==$ipConfig['ip']&&$uidIp['local_port']==$ipConfig['port']){
                        //$this->removeValue($room.':'.$ipConfig['ip'].'-'.$ipConfig['port'], [$uid], self::ROOM_CLIENTS);
                        $this->removeValue($room.':'.$ipConfig['ip'].'-'.$ipConfig['port'], [$uidIp['fd']], self::ROOM_CLIENTS);
                    }
                }
            }
            $this->removeValue($uid, $rooms, self::CLIENT_ROOMS);
        }else{
            $this->deleteKeyValue('',  self::ROOM_CLIENTS, function ($redisKey, $pipe)use($rooms, $group_socket, $uidIp){
                foreach ($rooms as $room) {
                    foreach ($group_socket as $ipConfig){
                        if($uidIp['local_ip']&&$uidIp['local_port']&&$uidIp['local_ip']==$ipConfig['ip']&&$uidIp['local_port']==$ipConfig['port']){
                            $room = $redisKey.$room.':'.$ipConfig['ip'].'-'.$ipConfig['port'];//拼接room与ip，port的生成key
                            $pipe->del($room);
                        }
                        
                    }
                }
            });

            $this->removeValue($uid, $rooms, self::CLIENT_ROOMS);
        }
    }

    /**
     * 获取房间中的成员fd集合
     * @param string $room
     * @param array $ipConfig [ip,port] //指定ip机子
     * @return array
     */
    public function getClients(string $room, $ipConfig=[])
    {
        if(empty($ipConfig)){//默认获取本机端口fd
            $ipConfig = [
                'ip' => $this->config['local']['ip'],
                'port' => $this->config['local']['port']
            ];
        }
        $room = $room.':'.$ipConfig['ip'].'-'.$ipConfig['port'];//拼接room与ip，port的生成key
        return $this->getValue($room, self::ROOM_CLIENTS) ?? [];
    }
    /**
     * 存储房间成员集合
     * @param string $room
     * @param array  $uids //需转换处理成对应的各服务器的fd
     * @return $this
     */
    protected function setClients(string $room, array $uids)
    {
        return $this->setValue($room, $uids, self::ROOM_CLIENTS,function ($uids, $redisKey, $redis){
            $pipe = $redis->multi(PHPRedis::PIPELINE);
            foreach ($uids as $uid){
                //获取uid映射的fd //查看RedisUserFd的获取fd的相关方法
                $pipe->hMGet("swoole:uidToFd:{$uid}", ['fdKey']);
            }
            $fdKeys = $pipe->exec();

            if(isset($fdKeys['fdKey'])) {// 返回格式可能不同，统一格式
                $arr[0] = $fdKeys;
                $fdKeys = $arr;
            }

            if(!empty($fdKeys[0])){
                $pipe = $redis->multi(PHPRedis::PIPELINE);
                foreach ($fdKeys as $fdKey){
                    //获取uid映射的fd //查看RedisUserFd的获取fd的相关方法
                    if(!empty($fdKey['fdKey'])){
                        $fdKey = explode(':',$fdKey['fdKey']);
                        if(!empty($fdKey[0])&&!empty($fdKey[1])){
                            $local = explode('-',$fdKey[0]);
                            $roomKey = $redisKey.':'.$local[0].'-'.$local[1];//拼接room与ip，port的生成key

                            $pipe->sadd($roomKey, $fdKey[1]);
                            $pipe->expire($roomKey, $this->room_expireTime);//设置过期时间
                        }
                    }
                }
                $pipe->exec();
            }
        });
    }

    /**
     * 获取用户有加入的聊天室集合
     * @param string $uid
     * @return array
     */
    public function getRooms($uid)
    {
        return $this->getValue($uid, self::CLIENT_ROOMS) ?? [];
    }
    /**
     * 存储用户有加入的房间集合
     * @param int   $uid
     * @param array $rooms
     *
     * @return $this
     */
    protected function setRooms($uid, array $rooms)
    {
        return $this->setValue($uid, $rooms, self::CLIENT_ROOMS);
    }


    /**
     * Set value to table
     *
     * @param        $key
     * @param array  $values
     * @param string $table
     * @param null|callable $callFun 结合redis自定义数据处理
     * @return $this
     */
    public function setValue($key, array $values, string $table, $callFun=null)
    {
        if(empty($key)){
            return $this;
        }
        $this->checkTable($table);
        $redisKey = $this->getRedisKey($key, $table);

        $this->runWithRedis(function (PHPRedis $redis) use ($redisKey, $values, $callFun) {
            if($callFun){
                $callFun($values, $redisKey, $redis);
            }else{
                $pipe = $redis->multi(PHPRedis::PIPELINE);
                foreach ($values as $value) {
                    $pipe->sadd($redisKey, $value);
                }
                $pipe->expire($redisKey, $this->room_expireTime);//设置过期时间
                $pipe->exec();
            }
        });

        return $this;
    }
    /**
     * Get value from table
     *
     * @param string $key
     * @param string $table
     *
     * @return array|mixed
     */
    public function getValue(string $key, string $table)
    {
        if(empty($key)){
            return [];
        }
        $this->checkTable($table);

        return $this->runWithRedis(function (PHPRedis $redis) use ($table, $key) {
            $redisKey = $this->getRedisKey($key, $table);
            $redis->expire($redisKey, $this->room_expireTime);//设置过期时间

            return $this->sScanGetKeys($redis, $redisKey, $table);
            //return $redis->smembers($redisKey);
        });
    }
    /**
     * Remove value from reddis.
     * @param        $key
     * @param array $values
     * @param string $table
     *
     * @return $this
     */
    protected function removeValue($key, array $values, string $table)
    {
        if(empty($key)){
            return $this;
        }
        $this->checkTable($table);
        $redisKey = $this->getRedisKey($key, $table);

        $this->runWithRedis(function (PHPRedis $redis) use ($redisKey, $values) {
            $pipe = $redis->multi(PHPRedis::PIPELINE);
            foreach ($values as $value) {
                $pipe->srem($redisKey, $value);
            }
            $pipe->exec();
        });

        return $this;
    }

    /**
     * 删除redis hset 的key
     * delete key,value from reddis.
     * @param        $key
     * @param string $table
     *@param null|callable $callFun
     * @return $this
     */
    protected function deleteKeyValue($key, string $table, $callFun=null){
        $redisKey = $this->getRedisKey($key, $table);

        $this->runWithRedis(function (PHPRedis $redis) use ($redisKey, $callFun) {
            $pipe = $redis->multi(PHPRedis::PIPELINE);
            if($callFun){
                $callFun($redisKey, $pipe);
            }else{
                $pipe->del($redisKey);
            }
            $pipe->exec();
        });

        return $this;
    }

    /**
     * Check table for exists
     *
     * @param string $table
     */
    protected function checkTable(string $table)
    {
        if (!in_array($table, [self::ROOM_CLIENTS, self::CLIENT_ROOMS])) {
            throw new InvalidArgumentException("Invalid table name: `{$table}`.");
        }
    }

    protected function runWithRedis(\Closure $callable)
    {
        $redis = $this->getPool()->borrow();

        try {
            return $callable($redis);
        } finally {
            $this->getPool()->return($redis);
        }
    }


    public function sScanGetKeys($redis, $key, $table, $pattern = null, $count = 500){
        $ret = [];
        $iterator = null;
        $server = $this->manager->getServer();

        $time = time();
        while (true) {
            $result = $redis->sScan($key, $iterator, $pattern, $count);
            if ($result === false) {
                break;
            }

            $unOnLines =[];
            foreach ($result as $k=>$fd){
                if($table==self::ROOM_CLIENTS){//针对是存fd的表时
                    if(!$server->isEstablished($fd)){
                        $unOnLines[] = $fd;
                        unset($result[$k]);
                    }
                }
            }

            if(!empty($unOnLines)){//移除房间中不在线的人
                $pipe = $redis->multi(PHPRedis::PIPELINE);
                foreach ($unOnLines as $fd){
                    $pipe->srem($key, $fd);
                }
                $pipe->exec();
            }
            unset($unOnLines);

            $ret = array_merge($ret, $result);

            if(time()-$time>60*40){//40分钟内未走完强制退出
                break;
            }
        }
        return array_values(array_unique($ret));
    }

    final function delete(){}
}


