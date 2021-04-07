<?php
declare (strict_types = 1);

namespace app\common\pool;
use app\common\traits\TraitInstance;
use app\common\traits\TraitPoolInteracts;
use think\Config;
use think\App;

/**
 * Class RedisPool 连接池
 * @package app\common\pool
 * //调用示例 * $RedisPoolObj = RedisPool::getInstance();
 * //调用示例 * $config = $RedisPoolObj->setConfig(); //先获取当前配置后注入参数，避免并发时变量污染
 * //调用示例 * $RedisPoolObj->init($config)->set('test',55);
 */
class RedisPool
{
    /**
     * 调用基础接口
     */
    use TraitPoolInteracts;

    /**
     * 单例
     * $config = RedisPool::getInstance()->setConfig();
     * RedisPool::getInstance()->init($config)->set('test',55);
     * @return RedisPool
     */
    use TraitInstance;


    public function setConfig(string $name = 'swoole.pool.redis',$newconfig = []){
        return $this->getConnectionConfig($name,$newconfig);
    }
    /**
     * 创建redis连接实例
     * @param bool $force 强制重新连接
     * @param array $config 连接的配置['database'=>选择redis库,'max_active'=>最大连接数量,...] 拓展个性化配置覆盖
     * @return \Redis|void
     */
    public function init(array $config = [], bool $force = false)
    {
        if(empty($config)){
            throw new \InvalidArgumentException(sprintf('Failed to connect Redis server: %s', '参数缺失'));
        }
        if (!$config['enable']) {//是否开启链接池
            return;
        }

        $flag_name = $config['_flag_name']; //带上标识
        if(empty($flag_name)){
            throw new \InvalidArgumentException(sprintf('Failed to connect Redis server: %s', '标识参数缺失'));
        }
        if ($force) {
            return $this->createConnection($config);
        }

        return $this->getPoolConnectionInContext($config);
    }


    /**
     * 创建连接
     * @param array $config
     * @return \Redis
     */
    protected function createConnection($config)
    {
        $connection = new \Redis();
        $ret = $connection->connect($config['host'], $config['port'], $config['timeout'] ?? 10);
        if ($ret === false) {
            throw new \InvalidArgumentException(sprintf('Failed to connect Redis server: %s', $connection->getLastError()));
        }
        if (isset($config['password'])) {
            $config['password'] = (string)$config['password'];
            if ($config['password'] !== '') {
                $connection->auth($config['password']);
            }
        }
        if (isset($config['database'])) {
            $connection->select(intval($config['database']));
        }

        foreach ($config['options'] ?? [] as $key => $value) {
            $connection->setOption($key, $value);
        }

        return $connection;
    }


    /**
     * 检测是否建立连接
     * @param $connection
     * @return bool
     */
    public function isConnected($connection): bool
    {
        return $connection->isConnected();
    }
}
