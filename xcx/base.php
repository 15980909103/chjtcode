<?php
ini_set('display_errors','On');
define('LOKI', '');
define('DS', DIRECTORY_SEPARATOR);
define('BasePath', dirname(__FILE__) );
define('System', BasePath . DS . 'system');
define('Setting', BasePath . DS . 'setting');
define('Module', BasePath . DS . 'module');
define('Lib', BasePath . DS . 'lib');
define('CachePath', BasePath . DS . 'cache');
require_once Setting . DS . 'setting.php';
require_once System . DS . 'DataBase.php';
//require_once System . DS . 'SessionHandler.php';
include System . DS . 'Context.php';
include System.DS.'Query.php';
require_once System.DS.'Model.php';
function getRunTime(&$microtime, $len = 0) {
	$start = explode ( " ", $microtime );
	$microtime = microtime ();
	$now = explode ( " ", $microtime );
	$difTime = ( double ) $now [1] + ( double ) $now [0] - ( double ) $start [1] - ( double ) $start [0];
	if (is_numeric ( $len ) && $len > 0) {
		$difTime = number_format ( $difTime, $len,'.','' );
	}
	return $difTime;
}