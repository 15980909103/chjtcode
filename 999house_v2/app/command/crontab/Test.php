<?php
declare (strict_types = 1);

namespace app\command\crontab;

use app\common\base\CommandBase;
use app\common\manage\TaskManage;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class Test extends CommandBase
{
    
    // 工作内容
    protected function doJob($input, $output){
        $time = time();
        echo 'test----'.time().PHP_EOL;
        /*while (true){
            if(time()-$time>5){
                break;
            }
        }
        echo 'test--end--'.time().PHP_EOL;*/
    }
}
