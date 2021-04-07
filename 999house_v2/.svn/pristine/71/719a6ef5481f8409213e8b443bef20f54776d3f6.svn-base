<?php

namespace app\common\base;

use app\common\MyConst;
use app\common\pool\RedisPool;
use app\server\admin\Admin;
use app\server\admin\City;
use app\server\admin\Role;
use Exception;

class AdminBaseController extends BaseController
{
    protected $roleId=''; //-1时为超级管理员
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
            $rs_admininfo = (new Admin())->getUserInfo([
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
            if($userInts['_userType']!='admin'){//登录的账号操作类型是否正确
                clearSession();//移除session_id所对应的数据
                return;
            }

            $this->userType = $userInts['_userType'];//获取标识的用户类型，当前用户类型为后台管理员
            getUserInts($userInts,1,1);
            $this->user   = $userInfo;
            $this->userId = $userInfo['id'];
            $this->roleId = $userInfo['role_id'];//  //设置权限角色 1为超级管理员
            getUserInfo($userInfo);
        }
    }

    protected function _checkAuth($rule_action='')
    {
        //是否为超级管理员
        if($this->roleId==-1 && !empty($this->userId)){ // -1为超级管理员
           return ;
        }else{
            //获取权限跟新标识，后台权限有跟新 跟新账号对应权限
            $rs_rolemenusUrl=getUserInfo()['_rolemenusUrl'];//session中读取
            if(!empty($rs_rolemenusUrl) ){
                //获取所有授权url
                $rs_rolemenusUrl = (new Role())->getRoleMenusUrlAfterFormat($this->roleId)['result'];
//                var_dump($rs_rolemenusUrl);
                if(!empty($rs_rolemenusUrl)){
                    getUserInfo(['_rolemenusUrl'=>$rs_rolemenusUrl],1);
                }
            }
            $auth = false;
//            var_dump($rs_rolemenusUrl);
            if($rs_rolemenusUrl && in_array($rule_action,$rs_rolemenusUrl)){
                $auth = true;
            }

            if(!$auth){
                $this->error(['code' => 10004, 'msg' => '接口权限被禁止!']);
            }
        }
    }

    // 获取所拥有的城市
    protected function getMyCity()
    {
        try {
            $useInfo = (new Admin())->getUserInfo(['userid'=>$this->getUserId()]);

            if(empty($rs['result']['region_nos_info'])){
                $region_nos_info = (new City())->getSiteCitys([],'id,cname,pid,pcname')['result'];
            }else{
                $region_nos_info = json_decode($useInfo['result']['region_nos_info'],true);
            }

            return ['code' => 1, 'msg' => '', 'data' => $region_nos_info];
        } catch (Exception $e){
            return ['code' => 0, 'msg' => $e->getMessage(), 'data' => []];
        }
    }

    /**
     * 根据id获取视频路径
     */
    protected function getVoidePath($id){
        if(empty($id) ) {
            return '';
        }

        $info  = $this->db->name('video_simple')->where('id','=',$id)->find();
        return $info['name'];
    }

    /**
     * 获取redis
     */
    protected function getReids($select = 2)
    {
        $reidsObj = $this->redis = RedisPool::getInstance();
        $config = [
            'database'   => $select,
            'max_active' => 5
        ];
        $config = $reidsObj->setConfig('swoole.pool.redis', $config);
        return $reidsObj->init($config);
    }

    protected function checkCanCity($region_no = ''){
        $regionRes = $this->getMyCity();
        $cityIds = !empty($regionRes['data']) ? array_column($regionRes['data'], 'id') : [];
        if(empty($cityIds)){
            $this->error('该城市你不可操作');
        }
        // 城市
        if(-1 == $region_no) {// 搜索当前全部城市
            $region_no = $cityIds;
        } else {
            if(!in_array($region_no,$cityIds)){
                $this->error('该城市你不可操作');
            }
        }

        return $region_no;
    }


    public function getFormatImgs($urls){
        $imgs = [];
        if(!is_array($urls)){
            $urls = explode(',',$urls);
        }
        foreach ($urls as $item){
            $imgs[] = [
                'name' => basename($item),
                'url' => $item,
            ];
        }

        return $imgs;
    }
}
