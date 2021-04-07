<?php
declare (strict_types = 1);

namespace app\common\lib\wxapi\co;
use app\common\traits\TraitInstance;

use think\App;


/**
 * 协程工作池管理
 */
class CoPoolsManager
{

    protected $pools = [];//存储当前池中的对象池

    /**
     * 要启动的一些协程对象池
     */
    public function startPools(){
        $list = [];
        $this->pools = $list;
    }

    /**
     * 动态添加对象池
     * @param string         $name
     * @param ConnectionPool $pool
     *
     * @return Pool
     */
    public function add(string $name,  $pool)
    {
        $pool->init();
        $this->pools[$name] = $pool;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return ConnectionPool
     */
    public function get(string $name)
    {
        return $this->pools[$name] ?? null;
    }

    public function close(string $key)
    {
        return $this->pools[$key]->close();
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->pools;
    }

    public function closeAll()
    {
        foreach ($this->pools as $pool) {
            $pool->close();
        }
    }



}
