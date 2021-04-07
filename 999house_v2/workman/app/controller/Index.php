<?php
namespace app\controller;

use app\BaseController;
use app\common\MyConst;
use app\model\User;
use think\cache\driver\Redis;
use think\facade\Config;
use think\facade\Db;

class Index extends BaseController
{
    public function index()
    {
        //$user = new User();
        Db::name('user')->limit(1)->select();
        echo '234234';
    }

    public function hello($name = 'ThinkPHP6')
    {
        return 'hello,' . $name;
    }
}
