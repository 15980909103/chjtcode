<?php

namespace app\admin\controller;


use app\common\base\AdminBaseController;
use app\common\MyConst;
use app\server\admin\Admin;
use think\Exception;
use think\Validate;


class PublicController extends AdminBaseController
{
    // 用户登录
    public function login()
    {
        $validate = new Validate([
            'username' => 'require',
            'password' => 'require'
        ]);
        $validate->message([
            'username.require' => '请输入手机号,邮箱或用户名!',
            'password.require' => '请输入您的密码!'
        ]);

        $data = $this->request->param();
        if (!$validate->check($data)) {
            $this->error($validate->getError());
        }

        $admininfo =(new Admin())->getUserInfo([
            'account' => $data['username']
        ])['result'];

        if (empty($admininfo['id'])) {
            $this->error(['code'=>0, 'msg'=> '用户不存在']);
        } else {
            if ($admininfo['status'] == 0) {
                $this->error(['code'=> 0, 'msg'=> '您已被禁用']);
            }

            if(!empty($admininfo['errlogin_info'])){//判断账号是否异常登陆被锁定
                $admininfo['errlogin_info'] = json_decode($admininfo['errlogin_info'],1);
                $rs_banLoginLockTime = $this->banLoginLockTime($admininfo['errlogin_info']);//获取账号异常锁定时间
                if($rs_banLoginLockTime == -1){
                    $this->error(['code'=>0, 'msg'=> '该账号密码错误过多，已被锁定到'.date('H:i:s',$admininfo['errlogin_info']['locktime'])]);
                }
            }

            if (!password_compare($data['password'], $admininfo['salt'], $admininfo['password'])) {
                //记录错误登陆信息
                $errlogin_info = ['num'=>intval($admininfo['errlogin_info']['num']) +1, 'login_ip'=>$this->appIp, 'lock_time'=>$rs_banLoginLockTime ];
                $this->db->name("admin")->where(['id' =>$admininfo['id']])->update(['errlogin_info'=> json_encode($errlogin_info,JSON_UNESCAPED_UNICODE)]);

                if($rs_banLoginLockTime>0){
                    $this->error(['code'=>0, 'msg'=> '该账号密码错误过多，已被锁定到'.date('H:i:s',$rs_banLoginLockTime )]);
                }else{
                    $this->error(['code'=> 0, 'msg'=> "密码不正确"]);
                }
            }
        }

        $rs = (new Admin())->userUpdateToken([
            'admin_id'=> $admininfo['id'],
            'token'=> $admininfo['token'],
            'ip' => $this->clientIp,
            'device_type' => $this->deviceType
        ]);
        if($rs['code']=='0'){
            $this->error(['code'=> 0, 'msg'=> "操作失败请重试"]);
        }
        //将数据同步到redis,用于聊天校验登录
        $reids      = $this->getReids(0);
        $reids->set(MyConst::JIUFANG_LOGIN_ADMIN.$rs['result']['token'],json_encode([
            '_hasDbCheckedToken' => 0,//标识用于下次是否从数据库验证token
            '_userType' => 'admin', //标识用户类型为管理员
            '_expireTime' => $rs['result']['expire_time'],
            'info'        => $admininfo
        ]),$rs['result']['expire_time']);

//        getUserInts([
//            '_hasDbCheckedToken' => 0,//标识用于下次是否从数据库验证token
//            '_userType' => 'admin', //标识用户类型为管理员
//            '_expireTime' => $rs['result']['expire_time']
//        ]);


        $sid=getSessionId();
        $this->success(['token' => $rs['result']['token'], 'sid' => $sid ]);
    }


    // 用户退出
    public function logout()
    {
        $token = $this->token;
        $redis = $this->getReids(0);
        clearSession();
        $redis->del(MyConst::JIUFANG_LOGIN_ADMIN.$token);
        $this->success();
    }

}
