<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
namespace think;

require __DIR__ . '/../vendor/autoload.php';


// 定义网站入口目录
if(!defined('WEB_ROOT')){
    define('WEB_ROOT', __DIR__ . '/');
}
if(!defined('APP_ROOT')){
    // 定义根目录
    define('APP_ROOT', dirname(__DIR__) . '/');
}
if(!defined('APP_PATH_ROOT')){
    // 定义应用目录
    define('APP_PATH_ROOT', APP_ROOT . 'app/');
}
//具体应用内时使用app_path()方法

// 执行HTTP应用并响应
$http = (new App())->debug(true)->http;
$response = $http->run();
$response->send();
$http->end($response);
