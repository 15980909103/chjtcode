<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Load
 *
 * @author admin
 */
class Load {

    public static function Block($name, $para = array()) {
        if (!class_exists($name)) {
            include Module . DS . Context::$module . DS . 'block' . DS . $name . '.php';
        }

        $block = new $name();
        $block->Init();
        echo $block->execute($para);
    }

    public static function Model($name) {
        $class = ucfirst($name) . 'Model';
        if (!class_exists($class)) {
            include Module . DS . Context::$module . DS . 'model' . DS . $class . '.php';
        }
        return new $class();
    }

    public static function Library($name) {
        $paths = explode('/', $name);
        $file = Lib . DS . join(DS, $paths) . '.php';
        require_once $file;
    }

    public static function Json($name) {
        $names = explode('/', $name);
        $path = BasePath . DS . 'json' . DS;
        $file=$path.join(DS, $names).'.json';
        return file_get_contents($file);
    }
	
	public static function Json2($dirname, $lid, $name) {
        $dirnames = explode('/', $dirname);
        $path = BasePath . DS . 'json' . DS;
        $file=$path . join(DS, $dirnames) . DS . $lid . DS . $name .'.json';
		if(!file_exists($file)){
			$file=$path . join(DS, $dirnames) . DS . $name .'.json';
		}
        return file_get_contents($file);
    }

}
