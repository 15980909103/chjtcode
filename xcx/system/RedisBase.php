<?php

class RedisBase {

    private static $instance = null;

    private function __construct() {

    }

    private function __clone() {

    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Redis();
            self::$instance->connect(REDIS_HOST, REDIS_PORT);
            self::$instance->auth(REDIS_PWD);
        }
        return self::$instance;
    }

}

?>
