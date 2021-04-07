<?php

/**
 * Description of BlockCache
 *
 * @author admin
 */
class BlockCache {

    public static function get($name, $expire = 0) {
        $file = BasePath . DS . 'cache' . DS . Context::$module . DS . 'Block' . DS . $name . implode('-', Context::$para);
        if (!file_exists($file)) {
            return null;
        }
        if ($expire == 0 || time() - filemtime($file) <= $expire) {
            return file_get_contents($file);
        }
        return null;
    }

    public static function set($name, $value) {
        $path = BasePath . DS . 'cache' . DS . Context::$module . DS . 'Block';
        if (!is_dir($path)) {
            mkdir($path, '0755', true);
        }
        if (!is_writable($path)) {
            echo "缓存目录不可写";
            exit;
        }

        $file = $path . DS . $name . implode('-', Context::$para);
        file_put_contents($file, $value);
    }

}
