<?php


namespace app\controller;


use app\BaseController;
use think\cache\driver\Redis;
use think\Collection;
use think\facade\Config;
use think\Response;

class Common extends BaseController
{

    public static $key = 'user_table';
    public function register(){
        $redis      = (new Redis(Config::get('cache.stores.redis')))->handler();
        $username   = $this->request->post('name');
        $paw        = $this->request->post('password','md5');
        $paw        = md5($paw);
        if(empty($username) || empty($paw))
        {
            return Response::create(['success'=>0,'msg'=>'注册失败','data'=>[]],'json',200)->send();
        }
        $result     = $redis->hset(self::$key,$username,$paw);
        return Response::create(['success'=>1,'data'=>[],'msg'=>'注册成功'],'json',200)->send();
    }

    public function login(){
        $redis      = (new Redis(Config::get('cache.stores.redis')))->handler();
        $username   = $this->request->post('name');
        $paw        = $this->request->post('password','md5');
        $paw        = md5($paw);
        $info       = $redis->hget(self::$key,$username);
        if( empty($info) ){
            return Response::create(['success'=>0,'msg'=>'用户没注册','data'=>[]],'json',200)->send();
        }
        if($paw != $info){
            return Response::create(['success'=>0,'msg'=>'密码不对','data'=>[]],'json',200)->send();
        }
        $data =[
          'name'        =>   $username,
          'token'       =>   md5($username),
          'userId'      =>   1,
        ];
        return Response::create(['success'=>1,'data'=>['userInfo'=>$data],'msg'=>'登录成功'],'json',200)->send();


    }

}