<?php
namespace app\common\lib\smsapi;


use app\common\lib\smsapi\module\SmsJiWei;
use think\exception\ClassNotFoundException;

/**
 * Class SmsServer
 */
class SmsServer{

    private $bind =[
        'jiwei'=> SmsJiWei::class
    ];
    private $currentSms;

    /**
     * @param $name
     * @return SmsJiWei::class
     */
    public function make($name){
      if(empty($this->bind[$name])){
          throw new ClassNotFoundException('class not exists: ' .$name);
      }
      $class = $this->bind[$name];
      return $this->currentSms = new $class();
    }

    public function makeSmsJiWei(){
        return $this->currentSms = new SmsJiWei();
    }

}
