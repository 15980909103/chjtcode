<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of main
 *
 * @author Goods0
 */
include System . DS . 'Encryption.php';
include System . DS . 'Session.php';
include System . DS . 'Upload.php';

include System . DS . 'RedisBase.php';

class Common extends Controller{
    const MYLIMIT=10;
    public function __construct(){
        $this->db = new Query();
        $this->db2 = new Query(new DataBase2());

        $this->redis = RedisBase::getInstance();
    }

    //user_id加解密
    function uid($operation = true){
        $user_id=Context::Post('user_id');
        $user_id=Encryption::authcode($user_id,$operation);
        return $user_id;
    }

    //解析获取的参数
    function getJson($url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    //计算发布时间据当前时间 如1秒前 1分钟前 1小时 1天 1个星期 1个人月 1年
    function format_dates($time) {
        if($time <= 0) return '刚刚';
        $nowtime = time();
        if ($nowtime <= $time) {
            return "刚刚";
        }
        $t = $nowtime - $time;
        $f = array(
            '31536000' => '年',
            '2592000' => '个月',
            '604800' => '星期',
            '86400' => '天',
            '3600' => '小时',
            '60' => '分钟',
            '1' => '秒'
        );
        foreach ($f as $k => $v) {
            $c = floor($t/$k);
            if ($c > 0) {
                return $c . $v . '前';
            }
        }
    }
    //格式化数字转为nk或nw
    function formatting_number($num,$decimal=1){
        $res=0;
        if($num>=10000){
            $res=sprintf("%.".$decimal."f",$num/10000).'w';
        }else if($num>=1000){
            $res=sprintf("%.".$decimal."f",$num/1000).'k';
        }else{
            $res=$num;
        }
        return $res;
    }
    /*=============================================== 内部接口 ====================================================*/
    //获取用户信息
    protected function getAgentInfo($id=0){
        $data=[];
        //获取用户信息
        $agentRow=$this->db->Name('xcx_agent_user')->select()->where_equalTo('id',$id)->firstRow();
        if(!empty($agentRow)){
            //查询是否是经纪人
            $sainfo=$this->db->Name('xcx_store_agent')->select()->where_equalTo('agent_id',$id)->firstRow();
            $data = [
                'storeInfo' => [
                    'store_id'=> 0,
                    'storename'=>"暂无店铺",//所属店铺
                    'province'=> '', //省份
                    'city'=> '', //城市
                    'area'=> '', //区域
                    'status'=> -1, //-1禁用 0申请中 1开通
                ],
                'type'=> -1,
                'typename'=> '', //所属职位
                'phone'=> $agentRow['phone'], //电话
                'signature'=> $agentRow['signature'],  //个性签名
                'special_label'=> explode(',',$agentRow['special_label']), //标签
                'headimgurl'=> $agentRow['headimgurl'], //头像
                'mestatus'=> '-2',  //经纪人综合性整体状态,是否可以进行报备操作，-2还不是经纪人
                'status'=>'',
                'name'=> $agentRow['name'], //姓名
                'nickname'=> $agentRow['nickname'], //微信昵称
                'uname'=> empty($agentRow['name'])?$agentRow['nickname']:$agentRow['name'],//姓名
                'expire_time'=>time()+30*60,
                'manageinfo'=>[
                    'building_ids'=> -1 //未绑定任何楼盘
                ]
            ];

            if(empty($sainfo['said'])){
                return $data;
            }

            if(($sainfo['type']=='0'||$sainfo['type']=='1')){
                //店员信息
                $storeData=$this->db->Name('xcx_store_store')->select()->where_equalTo('id',$sainfo['store_id'])->firstRow();
                if(!empty($storeData)){
                    $data['storeInfo'] = [
                        'store_id'=> $storeData['id'], //店铺id
                        'storename'=> $storeData['title'], //所属店铺
                        'province'=> $storeData['province'], //省份
                        'city'=> $storeData['city'], //城市
                        'area'=> $storeData['area'], //区域
                        'status'=> $storeData['status'], //店铺状态
                    ];
                }
            }

            if($sainfo['type']==2||$sainfo['type']==3){
                //工作人员信息
                $mgData=$this->db->Name('xcx_manager_building')->select('id,building_ids,is_delete')->where_equalTo('said',$sainfo['said'])->firstRow();
                if(!empty($mgData['id'])&&$mgData['is_delete']==0){
                    $data['manageinfo'] = [
                        'building_ids'=> $mgData['building_ids'],
                        //'building_list'=>$building_list
                    ];
                }

                $data['storeInfo']['status'] = 1;
                $data['storeInfo']['storename'] = '工作人员';
            }
            $typename_list = [
                0 => '店员',
                1 => '店长',
                2 => '工作人员',
                3 => '工作人员组长',
            ];
            $data['type'] = $sainfo['type'];//所属职位
            $data['typename'] = $typename_list[$sainfo['type']];//所属职位

            $data['status'] = $sainfo['status'];//该账号状态
            $mestatus = $sainfo['status'];//该账号的整体综合状态是否可以操作报备
            //店铺禁用时 //软删除时
            if($data['storeInfo']['status']==-1||$storeData['is_delete']==-1||$sainfo['is_delete']==-1){
                $mestatus = -1;
            }
            $data['mestatus'] = $mestatus;//经纪人综合性整体状态，是否可以进行报备操作

        }
        return $data;
    }


    //获取小程序的access_token
    protected function getAccessToken2(){
        //获取小程序二维码
        $access_token="";
//        $expires_time=$this->db->Name('xcx_setting')->select()->where_equalTo('`key`','expires_time')->firstRow();
        $key = 'AccessTokenMini';
        $accessTokenData = $this->redis->get($key);
        if(!$accessTokenData) {
            $expires_time = null;
        } else {
            $accessTokenData = json_decode($accessTokenData, TRUE);
            $expires_time = $accessTokenData['expires_in'];
        }

        if(!empty($expires_time) && time()<intval($expires_time)){
//            $access_token=$this->db->Name('xcx_setting')->select()->where_equalTo('`key`','access_token')->firstRow()['value'];
            $access_token = $accessTokenData['access_token'];
        }else{
            //防止本地请求token，使其失效
            $getAccessToken=$this->sendPost("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".APPID."&secret=".SECRET);
            //$getAccessToken=$this->getJson("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx77d12f2497be2502&secret=88579921c6cea5a386e96b1373e1d6bd");
            $getAccessToken=json_decode($getAccessToken,true);
            if(empty($getAccessToken['errcode'])){
                $access_token=$getAccessToken['access_token'];
//                $this->db->Name('xcx_setting')->update(['value'=>$getAccessToken['access_token']])->where_equalTo('`key`','access_token')->execute();
//                $this->db->Name('xcx_setting')->update(['value'=>time()+$getAccessToken['expires_in']-200])->where_equalTo('`key`','expires_time')->execute();
                $expires_in = time() + $getAccessToken['expires_in'] - 200;
                $accessTokenData = [
                    'access_token' => $access_token,
                    'expires_in' => $expires_in,
                ];
                $this->redis->set($key, json_encode($accessTokenData));
                $this->redis->expireAt($key, $expires_in);
            }

        }
        return $access_token;
    }


    /**
     * get请求
     * @param $url
     * @return mixed
     */
    protected function sendGet($url=''){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
    /**
     * post请求
     * @param $url
     * @param $data
     * @return mixed
     */
    protected function sendPost($url='',$data=[]){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    protected function success($data=[],$mag='操作成功'){
        echo json_encode([
            'data'=> $data,
            'success'=> true,
            'message'=> $mag,
            'code' => '1',
        ],JSON_UNESCAPED_UNICODE);
    }
    protected function error($msg='操作失败',$code=0,$data=[]){
        if(is_array($msg)){
            $data = !empty($msg['data'])?$msg['data']:[];
            $code = !empty($msg['code'])?$msg['code']:0;
            $msg = $msg['msg'];
        }

        echo json_encode([
            'data'=> $data,
            'success'=> false,
            'message'=> $msg,
            'code' => $code,
        ],JSON_UNESCAPED_UNICODE);
    }

     //小程序内容安全接口
    public function contentWxCheck($content='')
    {
        $dotype = $_POST['dotype'];
        if(empty($content)){
            $content = $_POST['content'];
        }
        $content = [
            'content'=>$content
        ];
        //小程序
        $access_token = $this->getAccessToken2();
        $url = 'https://api.weixin.qq.com/wxa/msg_sec_check?access_token='.$access_token;
        $response = $this->sendPost($url,json_encode($content,JSON_UNESCAPED_UNICODE));
        if($dotype=='post'){
            return $this->success($response);
        }
        return json_decode($response,TRUE);
    }

}