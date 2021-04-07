<?php
namespace app\common\lib\smsapi\module;

use app\common\traits\TraitAsyncHttp;


abstract class SmsBase{
    protected $config;//短信接口配置

    use TraitAsyncHttp;

    public function __construct()
    {

    }

    abstract protected function getCommonData();
    abstract public function sendVerifyCode();
    abstract public function sendSms();
}

