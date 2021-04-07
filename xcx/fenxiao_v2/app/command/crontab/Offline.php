<?php
declare (strict_types = 1);

namespace app\command\crontab;

use app\common\base\CommandBase;
use app\server\index\BoRes;

class Offline extends CommandBase
{
    /**
     * 结算线下名单
     */
    protected function doJob($input, $output){
        $boServer = new BoRes();
        $boServer->setOfflineUser();
    }
}