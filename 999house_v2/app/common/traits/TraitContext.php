<?php
namespace app\common\traits;

use Swoole\Coroutine;

/**
 * 上下文切换操作
 * @package app\common\traits
 */
trait TraitContext{
    protected static $context=[]; //存储此次操作的上下文切换

    public static function contextSetData(string $key, $value, $cid= null)
    {
        $cid = $cid??getmypid().'_'.Coroutine::getCid();

        if(empty(static::$context[$cid])){
            Coroutine::defer(function ()use($cid){//注册清理此次协程内容
                static::contextClearInCid($cid);
            });
        }

        static::$context[$cid][$key] = $value;
    }
    public static function contextGetData(string $key, $default = null, $cid= null)
    {
        $cid = $cid??getmypid().'_'.Coroutine::getCid();
        return static::$context[$cid][$key] ?? $default;
    }

    public static function contextHasData(string $key, $cid= null)
    {
        $cid = $cid??getmypid().'_'.Coroutine::getCid();
        return isset(static::$context[$cid]) && !empty(static::$context[$cid][$key]);
    }
    /**
     * @param string $key
     * @param $value
     * @param null $cid
     * @return mixed|null
     */
    public static function contextRememberData(string $key, $value, $cid= null)
    {
        $cid = $cid??getmypid().'_'.Coroutine::getCid();
        if (static::contextHasData($key, $cid)) {
            return static::contextGetData($key, $cid);
        }
        if ($value instanceof \Closure) {
            // 获取缓存数据
            $value = $value();
        }

        static::contextSetData($key, $value, $cid);
        return $value;
    }
    public static function contextRemoveData(string $key, $cid=null)
    {
        $cid = $cid??getmypid().'_'.Coroutine::getCid();
        unset(static::$context[$cid][$key]);
        if(empty(static::$context[$cid])){
            unset(static::$context[$cid]);
        }
    }

    public static function contextClearInCid($cid=null)
    {
        $cid = $cid??getmypid().'_'.Coroutine::getCid();
        unset(static::$context[$cid]);
    }
}
