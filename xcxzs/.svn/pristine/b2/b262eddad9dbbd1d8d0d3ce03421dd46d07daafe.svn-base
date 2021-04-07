<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Route
 *
 * @author admin
 */
function Route() {
    if(isset($_GET['m'])){
	$path = $_GET['m'];
    $arr = explode('/', $path);
    Context::$module = array_shift($arr);
    Context::$controller = array_shift($arr);
    Context::$action = array_shift($arr);
    Context::$para = &$arr;
	}else{
	Context::$module = "index";
    Context::$controller = "index";
    Context::$action = "index";
    Context::$para = array();
	}

    if (empty(Context::$module) || empty(Context::$controller)) {
        header("HTTP/1.0 404 Not Found");
        return;
    }

    $class = ucfirst(Context::$controller);

    $file = Module . DS . Context::$module . DS . 'controller' . DS . $class . '.php';

    if (!is_file($file)) {
    	error_log("file no found:".$file);
        header("HTTP/1.0 404 Not Found");
        return;
    }
    include $file;
    $controller = new $class();
    if (!method_exists($controller, Context::$action)) {
    	error_log("$class cannt find method:".var_export(Context::$action,true));
        header("HTTP/1.0 404 Not Found");
        return;
    }

    if (!$controller->permission()) {
        exit("you don't hava permission to access this");
    }
    $controller->setCache();
    $controller->_exec();

}
