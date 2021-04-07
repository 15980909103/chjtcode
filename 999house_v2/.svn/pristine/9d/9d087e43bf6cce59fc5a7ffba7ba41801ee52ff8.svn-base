<?php
declare (strict_types = 1);

namespace app\command\crontab;

use app\common\base\CommandBase;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class Dest extends CommandBase
{
    // 工作内容
    protected function doJob($input, $output){
        file_put_contents('aa.txt',time());
        echo 'dest----'.time().PHP_EOL;
    }
}
