<?php

namespace app\common\base;

use app\common\MyConst;
use app\common\pool\RedisPool;
use app\server\user\User;
use think\Container;
use think\Db;
use think\facade\Cache;

class UserBaseController extends BaseController
{
    protected $roleId = '';
    protected $openId = '';
    protected $cityNo = '';

    /**
     * 用户初始化操作
     */
    protected function _initUser()
    {
        $token = $this->token;
        $deviceType = $this->deviceType;

        //获取城市编码
//        $this->cityNo = $cityNo = $this->request->header('XX-CityNo');

        //一些必要参数的传入

        if (empty($deviceType) || empty($token)) {
            return;
        }
        $token_hascheck = 0;
        $userInfos = $this->getReids(0)->get(MyConst::JIUFANG_LOGIN . $token);
        if (empty($userInfos)) {
            return;
        } else {
            $userInfo = $userInfos = json_decode($userInfos, true);
            if ($userInfos['expire_time'] < time()) {
                $this->getReids(0)->delete(MyConst::JIUFANG_LOGIN . $token);
                return;
            } else {
                $userInfos['expire_time'] = time() + 7200;
                $this->getReids(0)->set(MyConst::JIUFANG_LOGIN . $token, json_encode($userInfos),$userInfos['expire_time']);
                $token_hascheck = 1;
            }

        }

        if ($token_hascheck == 0) { //进行数据库校验token
                $whereInfo = [
                    'token'        => $token,
                ];
            $rs_member = (new User())->getUserInfo($whereInfo)['result'];
            if (!empty($rs_member['id']) && $rs_member['expire_time'] >= time()) {//数据库验证没有超时
                $userInfo = $rs_member;
            } else {
                return;
            }
        }

        if($deviceType =='mini' ){
            if(!empty($userInfo['mini_mobile'])){
                $this->userId = $userInfo['id'];
            }else{
                $this->userId = 0;
            }
        }else{
            $this->userId = $userInfo['id'];
        }


    }


    public function getRealImageURL($relativePath)
    {
        if (stripos($relativePath, "http") === 0) {
            return $relativePath;
        }
        $imageHost = 'http://' . $_SERVER['HTTP_HOST'];
        return $imageHost . $relativePath;
    }


    
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

    /**
     * 根据id获取视频路径
     */
    protected function getVoidePath($id){
        if(empty($id) ) {
            return '';
        }

        $info  = $this->db->name('video_simple')->where('id','=',$id)->find();
        return $info['dir'].'/'.$info['name'];
    }

//    protected function getImgPath($id){
//        if(empty($id) ) {
//            return '';
//        }
//        if(!is_array($id) && !is_string($id) ) {
//            return '';
//        }
//        if( is_string($id) ) {
//            $info  = $this->db->name('upload_file')->where('file_id','=',$id)->value('file_path');
//        }else{
//            $inof  = [];
//            var_dump($id);
//            foreach ($id as $key =>$value ){
//               $info [] =  $this->db->name('upload_file')->where('file_id','=',$value)->find('file_path as url,file_hash as name')->toArray();
//            }
//
//        }
//
////        var_dump($info);
//
//        return $info;
//    }

}
