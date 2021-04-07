<?php

ini_set('session.gc_maxlifetime', 7200);

ini_set('session.cookie_lifetime', 0);

date_default_timezone_set('Asia/Shanghai');



//$my_user=strtoupper(substr(PHP_OS,0,3))==='WIN'?'root':'youxi2';
//$my_password=strtoupper(substr(PHP_OS,0,3))==='WIN'?'':'df3gj2fb4cnv8la8wr6923g';
//$my_domain1=strtoupper(substr(PHP_OS,0,3))==='WIN'?'http://localhost.youxi.com':'http://chfx.999house.com';

$my_password= 'df3gj2fb4cnv8la8wr6923g';
$my_domain1= 'http://chfx.999house.com';

if(strtoupper(substr(PHP_OS,0,3))==='WIN'){
    $my_user= 'youxi2';
    $my_user_2= 'youxi2';
    define('MYSQL_HOST', "8.129.209.172");
    define('MYSQL_HOST2', "47.107.72.79");
    // redis
    $redisHost = '127.0.0.1';
    $redisPort = '6379';
    $redisPwd = '';
}else{
    $my_user= 'youxi';
    $my_user_2= 'youxi2';
    define('MYSQL_HOST', "localhost");
    define('MYSQL_HOST2', "47.107.72.79");
    // redis
    $redisHost = '47.107.72.79';
    $redisPort = '59091';
    $redisPwd = '999houseredis!@#';
}

define('MYSQL_CHARSET', "utf8mb4");
define('MYSQL_DB', "youxi");
define('MYSQL_USER', $my_user);
define('MYSQL_PASSWORD', $my_password);

define('MYSQL_CHARSET2', "utf8mb4");
define('MYSQL_DB2', "9h");
define('MYSQL_USER2', $my_user_2);
define('MYSQL_PASSWORD2', $my_password);
define('MYSQL_PORT2', 3307);

define('WX_APPID', '');
define("WX_APPSECRET", '');

define("WX_HOST",$my_domain1);

define("WXPAY_ID",'');
define("WXPAY_KEY",'');

define("WXXCX_APPID",'');
define("WXXCX_SECRET",'');

define('ALY_KEYID', '');
define("ALY_KEYSECRET", '');
define("ALY_SIGNNAME",'');
define("ALY_TEMPLATECODE",'');

define("APPID",'wx5e8de6078fa14fd6');//小程序唯一标识
define("SECRET",'5e94c2cd967bacad6ff9d00d26db4d1a');//小程序的 app secret

define("WXAPPID",'wx699388855425afac');//微信唯一标识
define("WXSECRET",'efc48d74046a041e762cd7751a4648b1');//微信的 app secret

define("JHSWITCH",false);//九房网游戏开关  true开启  false关闭

define('MYSQL_PORT', '3306');

define("Table_Pre", '9h_');

define("BLOCKCACHE",TRUE);

define('ACTIONCACHE', TRUE);


$hostname = $_SERVER['HTTP_HOST'];
define("WEB_HOST",$hostname);

// Redis
define('REDIS_HOST', $redisHost);
define('REDIS_PORT', $redisPort);
define('REDIS_PWD', $redisPwd);
