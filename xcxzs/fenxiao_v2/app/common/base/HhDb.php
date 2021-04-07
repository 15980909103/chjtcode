<?php
namespace app\common\base;


use think\Container;
use think\DbManager;

//分表操作示例
//$rs=$this->db->setPartition([
//    'merchant_id'=>1,
//    '_num' => 10
//])->name('user')->alias('a')->setPartition([
//    'merchant_id'=>10,
//    '_num' => 10
//])->join('user b','b.id=a.id')->where('a.id',1)->select();
/**
 * 数据库基类
 */
class HhDb
{
    /**
     * @var HhDbQuery
     */
    private $db = null;

    /**
     * @param string $stroe
     * @return $this|HhDbQuery
     */
    public function init($stroe=''){
        return $this->db = Container::getInstance()->make(DbManager::class);
    }

}


trait MetaTrait
{
    private $methods = array();

    public function addMethod($methodName, $methodCallable)
    {
        if (!is_callable($methodCallable)) {
            throw new InvalidArgumentException('Second param must be callable');
        }
        $this->methods[$methodName] = Closure::bind($methodCallable, $this, get_class());
    }

    public function __call($methodName, array $args)
    {
        if (isset($this->methods[$methodName])) {
            return call_user_func_array($this->methods[$methodName], $args);
        }

        throw RunTimeException('There is no method with the given name to call');
    }

}