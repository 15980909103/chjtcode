<?php


namespace app\command\crontab;


use app\common\base\CommandBase;
use app\server\index\BoCake;

class CardCake extends CommandBase
{
// 工作内容
    protected function doJob($input, $output){
        $server = new BoCake();
        $res = $server->rum();
        echo 'CardCake----'.time().PHP_EOL;
    }
}