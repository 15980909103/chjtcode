<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of context
 *
 * @author admin
 */
defined("LOKI") || die("you no have right to access here");

class Context {
    public static $module;
    public static $controller;
    public static $action;
    public static $para;
    
    public static function Output(&$var) {
        echo isset($var) ? $var : '';
    }

    public static function Redirect($url) {
        header("Location: $url");
        exit();
    }

    public static function Get($name='',$isHave = '',$open_filter =1) {
        if(empty($name)){
            if($open_filter){
                $_GET = filter_var($_GET, FILTER_CALLBACK, ["options"=>"self::SafeFilter"]);
            }
            return $_GET;
        }
        if (isset($_GET[$name])) {
            if($open_filter){
                $_GET[$name] = filter_var($_GET[$name], FILTER_CALLBACK, ["options"=>"self::SafeFilter"]);
            }
            return $_GET[$name];
        }
        return $isHave;
    }

    public static function Post($name='',$isHave = '',$open_filter =1,$callFun = null) {
        if(empty($name)){
            if($open_filter){
                $_POST = filter_var($_POST, FILTER_CALLBACK, ["options"=>"self::SafeFilter"]);
            }
            return $_POST;
        }
        if (isset($_POST[$name])) {
            if($open_filter){
                if($callFun){
                    $_POST[$name] = $callFun($_POST[$name]);
                }
                $_POST[$name] = filter_var($_POST[$name], FILTER_CALLBACK, ["options"=>"self::SafeFilter"]);
            }
            return $_POST[$name];
        }
        return $isHave;
    }

    public static function Error404() {
        header("HTTP/1.0 404 Not Found");
        exit();
    }

    /**
     * 安全过滤
     * RemoveXSS
     * @param $val
     * @return string
     */
    public static function SafeFilter($val){
        if(self::$module!='agentapi'&&self::$module!='xcxapi'){
            return $val;
        }
        $val = htmlspecialchars($val);
        $val = preg_replace('/([\x00-\x08|\x0b-\x0c|\x0e-\x19])/', '', $val);

        //$sql = [ '/select/i', '/insert/i', '/update/i', '/delete/i', '/join/i', '/union/i', '/where/i', '/like/i', '/rename/i', '/modify/i', '/create/i', '/drop/i', '/truncate/i', '/declare/i', '/alter/i', '/table/i', '/database/i', '/mid/i', '/char/i', '/chr/i', '/count/i', '/execute/i', '/into/i', '/load_file/i', '/outfile/i', '/\.\.\//', '/\.\//' ];
        $sql = [ '/select/i', '/insert/i', '/update/i', '/delete/i', '/union/i', '/where/i', '/like/i', '/rename/i', '/modify/i', '/create/i', '/drop/i', '/truncate/i', '/declare/i', '/alter/i', '/table/i', '/database/i', '/mid/i', '/char/i', '/chr/i', '/count/i', '/execute/i', '/into/i', '/load_file/i', '/outfile/i', '/\.\.\//', '/\.\//' ];
        $val = preg_replace($sql, '', $val);
        unset($sql);

        $ra = [ '/eval/', '/javascript/i', '/document/i', '/vbscript/i', '/expression/i', '/applet/i', '/xml/i', '/blink/i', '/link/i', '/style/i', '/script/i', '/embed/i', '/object/i', '/iframe/i', '/frame/i', '/frameset/i', '/ilayer/i', '/layer/i', '/base/i'];
        $val = preg_replace($ra, '', $val);
        unset($ra);

        $val=trim($val);
        return $val;
    }




}

?>
