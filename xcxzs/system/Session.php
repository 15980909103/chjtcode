<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Session
 *
 * @author admin
 */
class Session {
    protected static $prefix = 'yx_';

    public static function setId($sid) {
        session_id($sid);
        session_start();
    }

    public static function destory() {
        session_destroy();
    }

    public static function get($name) {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (!isset($_SESSION[self::$prefix . $name])) {
            return NULL;
        }

        return $_SESSION[self::$prefix . $name];
    }

    public static function set($key, $value) {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION[self::$prefix . $key] = $value;
    }

    public static function del($key) {
        if (isset($_SESSION) && isset($_SESSION[self::$prefix . $key])) {
            unset($_SESSION[self::$prefix . $key]);
        }
    }

}
