<?php

/**
 * Description of ActionCache
 *
 * @author admin
 */
class ActionCache {

    public static function get($expire = 0) {
        $file = BasePath . DS . 'cache' . DS . Context::$module . DS . Context::$controller . DS . Context::$action . implode('-', Context::$para);
        if (!file_exists($file)) {
            return null;
        }
        if ($expire == 0 || time() - filemtime($file) <= $expire) {
            return file_get_contents($file);
        }
        return null;
    }

    public static function set($value) {

        $path = BasePath . DS . 'cache' . DS . Context::$module . DS . Context::$controller;
        if (!is_dir($path)) {
            mkdir($path, '0755', true);
        }
        if (!is_writable($path)) {
            echo "缓存目录不可写";
            exit;
        }

        $file = $path . DS . Context::$action . implode('-', Context::$para);
        file_put_contents($file, $value);
    }

    public static function clear($path) {
        foreach (glob($path . '/*') as $file) {
            if (is_dir($file)) {
                self::clear($file);
            } else {
                unlink($file);
            }
        }
        rmdir($path);
    }

}
