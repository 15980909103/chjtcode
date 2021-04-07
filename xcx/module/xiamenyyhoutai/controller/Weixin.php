<?php

include Lib . DS . 'Sms.php';
class Weixin extends Controller
{
    //每天凌晨12点的初始化
    public function weixin_init(){
        $q = new Query();
		$todayone1 = strtotime(date("Y-m-d"),time());
        $todayone2 = $todayone1+(60*5);
        $todaytow2 = $todayone1+(60*60*24);
        $todaytow1 = $todaytow2-(60*5);
        $newTime=time();
		echo "time:$newTime\n";
        $is_send_admin=$q->Name('setting')->select()->where_equalTo('`key`','ISSENDADMIN')->firstRow()['value'];
        $adminPhone=$q->Name('setting')->select()->where_equalTo('`key`','ADMINPHONE')->firstRow()['value'];
        if(($newTime>=$todayone1 && $newTime <=$todayone2) || ($newTime>=$todaytow1 && $newTime<=$todaytow2)){
            $res=$q->Name('user')->update(['is_share'=>1,'flipnum'=>2,'helpintegral'=>500])->execute();
            if(empty($res)){
                if(!empty($is_send_admin)){(new Sms())->message($adminPhone,"000000");}
                throw new Exception("用户数据初始化失败，数据库修改未成功");
            }else{
                if(!empty($is_send_admin)){(new Sms())->message($adminPhone,"111111");}
            }
        }else{
            if(!empty($is_send_admin)){(new Sms())->message($adminPhone,"000001");}
            throw new Exception("用户数据初始化失败，请在规定时间内初始化");
        }
    }
}