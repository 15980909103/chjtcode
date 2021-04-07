<?php

namespace app\miniwechat\controller;

use app\common\base\HhDb;
use app\common\base\UserBaseController;
use app\common\lib\wxapi\module\WxMini;
use app\common\lib\wxapi\WxServe;
use app\common\MyConst;
use app\index\validate\UserValidate;
use app\server\admin\Sys;
use app\server\marketing\Subject;
use app\server\marketing\Vote;
use app\server\merchant\Activities;
use app\server\user\ShortMessage;
use app\server\user\User;
use think\exception\ValidateException;


class PublicController extends UserBaseController
{


    public  $config = [
        'appid'     => 'wx16b7695f814f1aaf',
        'secret'    => 'eab7c7f2314e3371338ed6582d385023',
    ];

    //发送短信验证码
    public function sendMsg()
    {
        $post = $this->request->post();

        $res = (new ShortMessage())->sendMsg($post);
        if ($res['code'] == 0) {
            return $this->error($res['msg']);
        }
        return $this->success($res['result']);
    }


    public function getWebInfo(){
        $param = $this->request->param();
        $param['wx_config_id'] = $param['city_no']; //缓存id
        $server = new WxServe();
//        $resUrl = $server->setCodeId($param['city_no'])->getWxH5($param)->getJsSdkConfig($param,2); //1取服务号  2 取订阅号
        $param['wx_do_type'] = 2;
        $resUrl = $server->setCodeId($param['city_no'])->getJsSdkConfig($param);
        //$resUrl = $server->getJsSdkConfig($param);
        $this->success($resUrl);
    }

    public function wxConfigurationInfo()
    {
        $server = new Activities();

        $res = $server->wxConfigurationInfo(140400);

        return $this->success($res);
    }

    public function serverCode(){
        $res = (new Sys())->serverCode();
        if($res == 0){
            return $this->error($res);
        }
        return  $this->success($res['result']);
    }

    // 用户退出
    public function logout()
    {
        $param = $this->request->param();
        $this->getReids(0)->del(MyConst::JIUFANG_LOGIN . $param['token']);
        $this->success();
    }

    /**
     * 校验微信文本内容
     */
    public function checkWxText(){
        $post   = $this->request->post();

        if(!in_array($post['type'],[1,2]) ){
            return $this->error('检测类型错误');
        }
        if($post['type'] ==1){
            if(empty($post['text'])){
                return $this->error('请输入需要校验文本');
            }
        }elseif ($post['type'] ==2){
            if(empty($post['url'])){
                return $this->error('请输入需要校验文本');
            }
        }
        try {
            $model  = new WxMini($this->config);
            if($post['type'] ==1){ //文本
                $resutl   = $model->checkWxText($post['text']);
            }else if($post['type'] ==2){ //图片
                $resutl   = $model->checkWxImg($post['url']);
            }else{

            }
        }catch (\Exception $exception){
           return $this->error($exception->getMessage());
        }

        if($resutl['errcode'] ===0){
            $this->success('无违规内容');
        }elseif($resutl['errcode'] ===87014){
            $this->error('内容违规');
        }else{
            $this->error('校验失败');
        }

    }
    /**
     * 获取手机号！
     */


    public function getWxPhone(){
        $token   = $this->token;
        $data    = $this->request->post();
        $user_info = (new User())->getinofByToken($token);
        if(empty($user_info) ){
            return  $this->error('无效得token');
        }
        $user_id = $user_info['id'];
        if(empty($data['encryptedData']) || empty($data['iv'])){
            return $this->error('参数错误');
        }

        $data['session_key'] = $this->getReids()->hGet(MyConst::WXXCX_SESSION_KEY,$token);
        $phone  = [];
        (new WxMini($this->config) )->doDecryptDataForWxApp($data,$phone);
        $phone  = json_decode($phone,true);
        if(empty($phone)){
            return $this->error('获取手机号失败');
        }

        //微信获取手机号与原来绑定不相同 以微信小程序为主
        if($phone['phoneNumber'] != $user_info['phone']) {
            $user_info['phone'] = $phone['phoneNumber'];
        }
        $update_result = (new User())->editUserPhone(['type' =>2,'user_id' => $user_id,'mobile'=> $user_info['phone']]);
        if($update_result['code'] ==0){
            return $this->error('操作失败');
        }
        (new User())->UpdateMiniBing($user_info['phone'],$user_id);
        $this->success(['phone'=>$user_info['phone'],'is_login'=>1]);


    }
    /**
     *
     *微信小程序登录
     */
    public function  oauthLogin(){
        $data      = $this->request->post();
        $redis  = $this->getReids();
        if(empty($data['code']) ||  empty($data['encryptedData']) || empty($data['iv'])){
            return $this->error('参数错误');
        }


        $server = new WxMini($this->config);
        $wx_info = $server->getOauthLogin(['decryptArray' => ['encryptedData'=>$data['encryptedData'],'iv'=>$data['iv'] ], 'code'=>$data['code']]);
        if( empty($wx_info) ) {
           return $this->error('登录失败');
        }

        if( empty($wx_info['unionId']) ){
           return $this->error('非开发平台小程序');
        }
        $user_server = new User();
        $wx_info['unionid']         = $wx_info['unionId'];
        $wx_info['sex']             = $wx_info['gender'];
        $wx_info['headimgurl']      = $wx_info['avatarUrl'];
        $wx_info['nickname']        = $wx_info['nickName'];
        unset($wx_info['unionId']);
        unset($wx_info['gender']);
        unset($wx_info['avatarUrl']);
        unset($wx_info['nickName']);
        $result = $user_server->queryInsert($wx_info,[]);

        $user_info = (new User())->getInfo($result['id']);

        $result['info']['nickname']     = $result['nickname'];
        $result['info']['headimgurl']   = $result['headimgurl'];
        $result['info']['id']           = $result['id'];
//        $result['info']['phone']        = $user_info['phone'];

        unset($result['nickname']);
        unset($result['headimgurl']);
        unset($result['id']);

        if(!empty($user_info['result']['mini_mobile'])){
            $result['is_login'] = 1;

        }else{
            $result['is_login'] = 0;
            unset( $result['info']);

        }
        $result['type']     = 'mini';
        if($result['code'] === 0){
              $this->error($result['msg']);
        }
        //将session_key 存起来
        $redis->hSet(MyConst::WXXCX_SESSION_KEY,$result['token'],$wx_info['session_key']);
        $this->success($result);

    }


}