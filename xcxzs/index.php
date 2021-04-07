<?php
header("content-type:text/html;charset=utf-8");
ini_set('session.gc_maxlifetime',7200);
session_start();
//session_unset();
//session_destroy();
//exit;
define('LOKI', '');
define('DS', DIRECTORY_SEPARATOR);
define('BasePath', dirname(__FILE__));
define('System', BasePath . DS . 'system');
define('Setting', BasePath . DS . 'setting');
define('Module', BasePath . DS . 'module');
define('Lib', BasePath . DS . 'lib');
define('UPLOAD', BasePath . DS . 'upload');
define('Execute', BasePath . DS . 'execute');
define('Extend', BasePath . DS . 'extend');
define('Vendor', BasePath . DS . 'vendor');
define('JsVer', '90');
header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
if(isset($_GET['m'])){
$path = $_GET['m'];
$arr = explode('/', $path);
define('MODULE', array_shift($arr));
define('CONTROLLER', ucfirst(array_shift($arr)));
}else{
define('MODULE', 'html');
define('CONTROLLER', 'main');
}
require_once Setting . DS . 'setting.php';
require_once System . DS . 'DataBase.php';
require_once System . DS . 'DataBase2.php';
require_once Vendor . DS . 'autoload.php';
try {
include System . DS . 'Load.php';
include System . DS . 'Validator.php';
include System . DS . 'Context.php';
include System . DS . 'Route.php';
include System . DS . 'Query.php';
include System . DS . 'Pagination.php';
include System . DS . 'ActionCache.php';
include System . DS . 'BlockCache.php';
include System . DS . 'Block.php';
include System . DS . 'Controller.php';
include System . DS . 'Template.php';
include System . DS . 'Model.php';
include System . DS . 'Function.php';

Route();
}catch (Exception $e){
	DataBase::log(basename(__FILE__).__LINE__,$e);
}
DataBase::close();