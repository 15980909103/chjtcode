<?php
namespace app\common\traits;

/**
 * 兼容协程的单例复用
 * Trait TraitInstance
 * @package app\common\traits
 */
trait TraitInstance{
    protected static $_myinstance = null;
    protected static $_myinstance_type = null;

    /**
     * 单例调用不释放资源
     * 使用单例时针对一些全局的数值会变化的变量须更改为函数方法调用结合上下文的切换进行存储调用，否则会引起并发时变量污染
     * @return static::class
     */
    static function getInstance()
    {
        static::$_myinstance_type = 1;
        if(!static::$_myinstance){
            $config = func_get_args();

            if(!empty($config)){
                static::$_myinstance = new static(...$config);
            }else{
                static::$_myinstance = new static();
            }
        }

        return static::$_myinstance;
    }

    /**
     * 用于于协程中单例调用可自动释放资源
     * @return static::class
     */
    protected function getInstanceInCid(){
        static::$_myinstance_type = 2;
        $cid = -1;
        if (extension_loaded('swoole')==true){//判断是否拥有swoole环境
            $cid = \Swoole\Coroutine::getCid();//获取当前协程id
        }
        if(empty(static::$_myinstance)){
            static::$_myinstance=[];
        }

        if(!static::$_myinstance[$cid]){
            $config = func_get_args();

            if(!empty($config)){
                static::$_myinstance[$cid] = new static(...$config);
            }else{
                static::$_myinstance[$cid] = new static();
            }
            //如果当前不在协程环境中，Coroutine::getCid()返回-1
            if($cid!='-1'){
                //Coroutine::defer 用于协程执行完毕时资源的释放
                \Swoole\Coroutine::defer(function ()use($cid){
                    unset(static::$_myinstance[$cid]);
                });
            }
        }

        return static::$_myinstance[$cid];
    }


    protected function destroy(){
        if(static::$_myinstance_type==2){
            $cid = -1;
            if (extension_loaded('swoole')==true){//判断是否拥有swoole环境
                $cid = \Swoole\Coroutine::getCid();//获取当前协程id
            }
            unset(static::$_myinstance[$cid]);
        }else{
            static::$_myinstance = null;
        }
    }
}

//trait TraitInstance{
//    private static $_myinstance = null;
//    static function getInstance()
//    {
//        if(!static::$_myinstance){
//            $config = func_get_args();
//            if(!empty($config)){
//                static::$_myinstance = new static(...$config);
//            }else{
//                static::$_myinstance = new static();
//            }
//        }
//
//        return static::$_myinstance;
//    }
//}