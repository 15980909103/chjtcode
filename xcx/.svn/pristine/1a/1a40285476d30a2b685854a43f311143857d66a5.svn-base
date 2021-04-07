<?php
declare (strict_types = 1);

namespace app\common\base;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

abstract class CommandBase extends Command
{
    /**
     * 配置指令
     */
    protected function configure(){
        $this->setName(static::class)->setDescription('计划任务 '.static::class);
    }
   
    /**
     * 执行指令，当调用该类时自动执行该方法
     * @param Input  $input
     * @param Output $output
     * @return null|int
     * @throws LogicException
     * @see setCode()
     */
    protected function execute(Input $input, Output $output){
        $output->writeln('Crontab job '.static::class.' start');

        //@todo something
        $this->doJob($input,$output);
        
        $output->writeln('Crontab job '.static::class.' end');
    }

    // 工作内容
    protected abstract function doJob(Input $input, Output $output);
}
