<?php
declare (strict_types = 1);

namespace app\common\pool\Copool;
use app\common\traits\TraitInstance;
use app\common\traits\TraitPoolInteracts;
use think\Config;
use think\App;

use Swoole\Coroutine;
use Swoole\Coroutine\Channel;

/**
 * 协程工作池参数
 * Class CoPool
 * @package app\common\pool
 */
class TaskParam
{
    /**
     * 当前协程在协程池中的顺序，从0开始编号
     *
     * @var int
     */
    private $index;

    /**
     * 数据
     *
     * @var mixed
     */
    private $data;

    public function __construct($index, $data)
    {
        $this->index = $index;
        $this->data = $data;
    }

    /**
     * 获取数据
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * 获取当前协程在协程池中的顺序，从0开始编号
     *
     * @return int
     */
    public function getCoIndex()
    {
        return $this->index;
    }

}
