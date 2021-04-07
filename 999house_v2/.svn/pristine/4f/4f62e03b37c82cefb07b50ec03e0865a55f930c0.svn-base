<?php

namespace app\common\base;

use app\server\merchant\Role;
use app\server\merchant\Merchant;

class MerchantBaseController extends BaseController
{
    protected $roleId=''; //-1时为商户的管理员角色
    private $merchantId = 0;//商户号
    protected $merchantOwnerId = 0;//商户拥有者的账号id
    private $regionId = 0;

    /**
     * 获取商户id
     * @return int
     */
    public function getMerchantId(){
        if (empty($this->merchantId)) {
            $this->error(['code' => $this->errCodeForLoginAgain, 'msg' => '商户标识错误']);
        }
        return $this->merchantId;
    }

    public function getRegionId(){
        /*if (empty($this->regionId)) {
            $this->error(['code' => $this->errCodeForLoginAgain, 'msg' => '区域标识错误']);
        }*/
        return $this->regionId;
    }

    /**
     * 用户初始化操作
     */
    protected function _initUser()
    {
        $sid        =$this->sid;
        $token      = $this->token;
        $deviceType = $this->deviceType;

        if (empty($deviceType)||empty($token)||empty($sid)) {
            return;
        }

        $token_hascheck = 0;//用于判断是否已经校验过token
        //getSessionId($sid);
        $userInts = getUserInts();//用户初始化的一些数据
        $userInfo = getUserInfo();

        //判断有userid，且token是否已经有数据库校验过了
        if(!empty($userInfo['id']) && !empty($userInts['token']) && $userInts['token'] == $token && $userInts['_hasDbCheckedToken'] == 1 ){
            $token_hascheck=1;
        }

        if($token_hascheck==0){ //进行数据库校验token
            $userInfo=[];
            $rs_admininfo = (new Merchant())->getAccountInfo([
                'token' => $token,
                'device_type' => $deviceType
            ])['result'];

            if(!empty($rs_admininfo['id']) && $rs_admininfo['expire_time']>= time()){//没有超时
                unset($rs_admininfo['password']);
                unset($rs_admininfo['salt']);

                $userInfo = $rs_admininfo;
            }else{
                clearSession();//移除session_id所对应的数据
            }
        }

        if (!empty($userInfo['id'])) {//进行参数赋值
            $userInts['token'] = $token;
            $userInts['_hasDbCheckedToken'] = 1;
            if($userInts['_userType']!='merchant'){//登录的账号操作类型是否正确
                clearSession();//移除session_id所对应的数据
                return;
            }

            $this->userType = $userInts['_userType'];//获取标识的用户类型，当前用户类型为商户人员
            getUserInts($userInts,1,1);
            $this->user   = $userInfo;
            $this->userId = $userInfo['id'];
            $this->roleId = $userInfo['role_id'];//  //设置权限角色 -1为超级管理员
            $this->merchantId = $userInfo['merchant_id'];
            $this->merchantOwnerId = $userInfo['owner_id'];
            //$this->regionId
            getUserInfo($userInfo);
        }
    }

    protected function _checkAuth($rule_action='')
    {
        //是否为超级管理员
        if($this->roleId==-1 && !empty($this->userId)){ // -1为超级管理员
           return ;
        }else{
            $rs_rolemenusUrl = getUserInfo()['_rolemenusUrl'];//session中读取
            if(empty($rs_rolemenusUrl)){
                //获取所有授权url
                $rs_rolemenusUrl = (new Role())->getRoleMenusUrlAfterFormat($this->roleId)['result'];
                if(!empty($rs_rolemenusUrl)){
                    getUserInfo(['_rolemenusUrl'=>$rs_rolemenusUrl],1);
                }
            }

            $auth = false;
            if($rs_rolemenusUrl && in_array($rule_action,$rs_rolemenusUrl)){
                $auth = true;
            }

            if(!$auth){
                $this->error(['code' => 10004, 'msg' => '接口权限被禁止!']);
            }
        }
    }



}
