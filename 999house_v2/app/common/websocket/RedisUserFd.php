<?php

namespace app\common\websocket;


use app\common\traits\TraitContext;
use InvalidArgumentException;
use Redis as PHPRedis;
use Smf\ConnectionPool\ConnectionPool;
use Smf\ConnectionPool\Connectors\PhpRedisConnector;
use think\Container;
use think\facade\Config;
use think\swoole\Manager;
use think\swoole\Pool;




/**
 * 用户操作 操作用户uid和fd之间的关系
 */
class RedisUserFd
{
    use TraitContext;

    public const UID_FD = 'uidToFd'; //用户uid与fd的映射
    public const FD_UID = 'fdToUid'; //fd与用户uid的映射

    /** @var ConnectionPool */
    protected $pool;

    /** @var Manager */
    protected $manager;
    
    /**
     * @var array
     */
    protected $config = [
        'redis'=> [
            'host' => '127.0.0.1',
            'port' => 6379,
            'min_active' =>2,
            'max_active' =>6,
            //'password' => 'm-y-redis-cache@999house[888]-y-redis-cache@999house#888-y-rediscache-999house-888-y-redis-999house-888'
        ]
    ];

    protected $prefix = 'swoole';

    protected $localScoket = [];

    protected $pools = null;

    /**
     * TableRoom constructor.
     * @param array $config
     *
     */
    public function __construct($config=[])
    {
        $this->config = array_merge($this->config, $config);
        $this->localScoket = $this->config['local'];

        if(empty($this->localScoket['ip'])||empty($this->localScoket['port'])){
            throw new InvalidArgumentException("Invalid config swoole.distributed local ip|local port");
        }
        unset($this->config['local']);
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
        return $this->prefix.":".$table.":".$key;
    }

    /**
     * Do some init stuffs before workers started.
     * //针对服务启动从初始化时才调用此方法
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
     * 获取fd与ip，port的生成key
     * @param $fd
     * @param string $local_ip 默认当前机子ip
     * @param string $local_port 默认当前机子端口
     * @return string
     */
    public function getFdKey($fd,$local_ip='',$local_port=''){
        if(empty($local_ip)){
            $local_ip = $this->localScoket['ip'];
        }
        if(empty($local_port)){
            $local_port = $this->localScoket['port'];
        }

        return $local_ip.'-'.$local_port.':'.$fd;
    }

    /**
     * 转换fdKey为fd的数组信息
     * @param $fdKey
     * @return array
     */
    public function fdKeyToFd($fdKey){
        $fdKey = str_replace($this->prefix.":".self::FD_UID,'',$fdKey);
        $fdKey = explode(':',$fdKey);
        $local = explode('-',$fdKey[0]);
        return [
            'isLocal' => ($local[0]!=$this->localScoket['ip']||$local[1]!=$this->localScoket['port'])? 0 : 1, //标识是否在当前机子，0不在当前机子1是
            'fd' => $fdKey[1],
            'local_ip' => $local[0],
            'local_port' => $local[1],
        ];
    }

    /**
     * uid 和 fd 的绑定操作
     * @param array $setUidToFdInfo //uid映射fd要存的数据 [uid,login_token,online_time, ...] online_time 正为上线时间点负为下线时间点,login_token为登录时用户的token标识
     * @param array $setFdToUidData //fd映射uid要存的数据 [fd, ...]
     * @param callable $callFun //$callFun($store_fdKey, $store_uid)
     */
    public function uidfdBind($setUidToFdInfo = [], $setFdToUidData = [], $callFun = null){
        $uid = $setUidToFdInfo['uid'];
        $fd = $setFdToUidData['fd'];
        unset($setUidToFdInfo['uid']);
        unset($setFdToUidData['fd']);
        if(empty($fd)){
            throw new InvalidArgumentException("Invalid fd");
        }
        if(empty($uid)){
            throw new InvalidArgumentException("Invalid uid");
        }
        if(empty($setUidToFdInfo['login_token'])){
            throw new InvalidArgumentException("Invalid login_token");
        }

        $now_fdKey = $this->getFdKey($fd); //ip,port,fd组成的fdKey
        $store_fdKey = $this->getFdByUid($uid);//查找uid映射的旧的fd

        if(false===$store_fdKey||$now_fdKey!=$store_fdKey){//uid映射的fd发送变化
            $store_uid = $this->getUidByFd($store_fdKey);//旧的fd映射找到旧的uid
            //uid为新的时，删除旧的uid映射
            if(!empty($store_uid)&&$store_uid!=$uid){
                //$this->uid2fd->del((string)$store_uid);
                $this->deleteFdInfoByUid($store_uid);
            }
            if(!empty($store_fdKey)&&$store_fdKey!==$now_fdKey){//存在旧的fd映射
                //删除旧的映射
                //$this->fd2uid->del((string)$store_fd);
                $this->deleteUidInfoByFd($store_fdKey);
            }

            $setUidToFdInfo['online_time'] = $setUidToFdInfo['online_time']??time();
            $setUidToFdInfo['fdKey'] = $now_fdKey;

            $setFdToUidData['uid'] = $uid;
            $setFdToUidData['login_token'] = $setUidToFdInfo['login_token'];

            $this->setFdInfoByUid($uid, $setUidToFdInfo);//更新uid映射fd
            $this->setUidInfoByFd($now_fdKey, $setFdToUidData);//更新fd映射uid

            if(!empty($callFun)){
                $callFun([
                    'store_fdKeyInfo' => $this->fdKeyToFd($store_fdKey),
                    'store_uid' => $store_uid,
                    'uid' => $uid,
                    'fd' => $fd
                ]);//用于重置类似旧的fd与room的关系
            }

           // $this->uid2fd->set($uid, ['fd' => $now_fd,'online_time'=>time(), 'local_ip'=>$data['local_ip'], 'local_port'=>$data['local_port'], 'login_token'=> $data['login_token']]);//更新uid映射
           // $this->fd2uid->set($now_fd, ['uid' => $uid,'merch_id'=>$merch_id,'user_id'=>$user_id ]);//更新fd映射
        }
    }

    /**
     * 标识某个用户上线时间点
     * @param $uid
     * @param int $time //正代表上线时间点，负代表下线时间点，0为没有
     */
    public function setOnLineTimeByUid($uid,$time){
        $this->setValue($uid, [ 'online_time'=> $time ],self::UID_FD);
    }
    /**
     * 获取用户的上线时间 +为上线时间点，-为下线时间点，0为没有
     * @param $uid
     * @return int
     */
    public function getOnLineTimeByUid($uid){
        $rs = $this->getFdInfoByUid($uid,['online_time']);
        return $rs['online_time']? $rs['online_time'] : 0;
    }

    /**
     * 针对uid映射fd表 增加自定义字段存储
     * @param $uid
     * @param $field
     * @param $val
     */
    public function setFieldByUid($uid, $field, $val){
        $this->setValue($uid, [ $field=> $val ],self::UID_FD);
    }

    /**
     * 通过 uid 获取 fd
     * @param $uid
     * @param string $type ['string'.'array']
     * @return array|bool|string
     */
    public function getFdByUid($uid, $type = 'string'){
        $rs = $this->getFdInfoByUid($uid,['fdKey']);
        if(empty($rs['fdKey'])){
            return false;
        }

        if($type=='string'){
            return $rs['fdKey'];
        }else{
            $local = $this->fdKeyToFd($rs['fdKey']);
            return [
                'isLocal'=> ($local['local_ip']!=$this->localScoket['ip']||$local['local_port']!=$this->localScoket['port'])? 0 : 1, //标识是否在当前机子，0不在当前机子1是
                'fd'=> $local['fd'],//所在机子fd
                'local_ip'=> $local['local_ip'],//所在机子ip
                'local_port'=> $local['local_port'],//所在机子端口
            ];
        }
    }

    /**
     * 通过 fdKey 获取 uid
     * @param $fdKey  //该值存的时候为$this->getFdKey($fd,$local_ip,$local_port);格式
     * @return bool|mixed
     */
    public function getUidByFd($fdKey){
        return $this->getUidInfoByFd($fdKey,['uid'])['uid']??false;
    }


    /**
     * 通过 uid 设置对应 fd和一些用户信息
     * @param int   $uid
     * @param array $data
     *
     * @return $this
     */
    public function setFdInfoByUid($uid, array $data)
    {
        if(isset($data['fdKey'])&&empty($data['fdKey'])){
            throw new InvalidArgumentException("Invalid fdKey");
        }
        return $this->setValue($uid, $data, self::UID_FD);
    }
    /**
     * 通过 uid 获取对应的 fd和一些用户信息
     * @param $uid
     * @param $fields
     * @return bool|mixed
     */
    public function getFdInfoByUid($uid, $fields=[]){
        return $this->getValue(strval($uid), $fields, self::UID_FD) ?? false;
    }
    /**
     * 通过 uid 删除对应 fd信息
     * @param $uid
     * @return $this
     */
    public function deleteFdInfoByUid($uid){
        return $this->deleteKeyValue($uid,self::UID_FD);
    }

    /**
     * 通过 fdKey 设置对应 uid和一些用户信息
     * @param string $fdKey //如果是数字类型默认是当前机子端口
     * @param array  $data
     *
     * @return $this
     */
    public function setUidInfoByFd($fdKey, array $data)
    {
        if(empty($data['uid'])){
            throw new InvalidArgumentException("Invalid uid");
        }
        if(is_numeric($fdKey)){
            $fdKey = $this->getFdKey($fdKey);//如果是数字类型默认是当前机子端口
        }

        return $this->setValue($fdKey, $data, self::FD_UID);
    }
    /**
     * 通过 fdKey 获取对应的 uid和一些用户信息
     * @param $fdKey //如果是数字类型默认是当前机子端口, 如果为字符串需要为带有机器ip端口标识的fd
     * @param $field
     * @return bool|mixed
     */
    public function getUidInfoByFd($fdKey, $field=[]){
        if(is_numeric($fdKey)){
            $fdKey = $this->getFdKey($fdKey);//如果是数字类型默认是当前机子端口
        }
        $rs = $this->getValue(strval($fdKey), $field,self::FD_UID) ?? false;
        if(!empty($rs)){
            $rs['fdKey'] = $fdKey;
        }
        return $rs;
    }
    /**
     * 通过 fdKey 删除对应 uid信息
     * @param $fdKey //如果是数字类型默认是当前机子端口
     * @return $this
     */
    public function deleteUidInfoByFd($fdKey){
        if(is_numeric($fdKey)){
            $fdKey = $this->getFdKey($fdKey);//如果是数字类型默认是当前机子端口
        }

        return $this->deleteKeyValue($fdKey,self::FD_UID);
    }


    /**
     * Set value to table
     *
     * @param        $key
     * @param array  $values
     * @param string $table
     *
     * @return $this
     */
    public function setValue($key, array $values, string $table)
    {
        if(empty($key)){
            return $this;
        }
        $redisKey = $this->getRedisKey($key, $table);

        $this->runWithRedis(function (PHPRedis $redis) use ($redisKey, $values) {
            $pipe = $redis->multi(PHPRedis::PIPELINE);

            $pipe->hMSet($redisKey, $values);
            $pipe->expire($redisKey, 3600*24);//设置过期时间

            $pipe->exec();
        });

        return $this;
    }
    /**
     * Get value from table
     *
     * @param string $key
     * @param array $fields
     * @param string $table
     *
     * @return array|mixed
     */
    public function getValue(string $key,array $fields, string $table)
    {
        if(empty($key)){
            return [];
        }
        $redisKey = strval($this->getRedisKey($key, $table));

        return $this->runWithRedis(function (PHPRedis $redis) use ($redisKey, $fields) {
            $pipe = $redis->multi(PHPRedis::PIPELINE);

            $pipe->expire($redisKey, 3600*24);//设置过期时间

            if(!empty($fields)){
                $pipe->hMGet($redisKey, $fields);
            }else{
                $pipe->hGetAll($redisKey);
            }
            return ($pipe->exec())[1];
        });
    }
    /**
     * Remove field from reddis. 移除redis hset中的某个字段
     * @param        $key
     * @param array $fields [field1,field2]
     * @param string $table
     *
     * @return $this
     */
    protected function removeValue($key, array $fields, string $table)
    {
        if(empty($key)){
            return $this;
        }
        $redisKey = $this->getRedisKey($key, $table);

        $this->runWithRedis(function (PHPRedis $redis) use ($redisKey, $fields) {
            $redis->hDel($redisKey,...$fields);
        });

        return $this;
    }

    /**
     * 删除redis hset 的key
     * delete key,value from reddis.
     * @param        $key
     * @param string $table
     *
     * @return $this
     */
    protected function deleteKeyValue($key, string $table){
        if(empty($key)){
            return $this;
        }
        $redisKey = $this->getRedisKey($key, $table);

        $this->runWithRedis(function (PHPRedis $redis) use ($redisKey) {
            $redis->del($redisKey);
        });

        return $this;
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

}


