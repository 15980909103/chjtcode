<?php


namespace app\command\crontab;

use app\common\base\CommandBase;
use think\console\Input;
use think\console\Output;
use think\Db;

/**
 * Class OldNewsToNew
 * @package app\command\crontab
 * 旧系统新闻跟新到新系统批任务
 */
class OldNewsToNew extends CommandBase
{
    protected function doJob(Input $input, Output $output)
    {
        return ;
        echo "任务开始".date('Y-m-d H:i:s');
        echo PHP_EOL;
        file_get_contents('http://act.999house.com/index/test1/index'); //直接命令执行
        echo "任务结束".date('Y-m-d H:i:s');
        echo PHP_EOL;
    }
}