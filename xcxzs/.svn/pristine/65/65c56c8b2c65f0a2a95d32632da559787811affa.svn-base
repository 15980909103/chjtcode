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
include 'Common.php';
include Lib . DS . 'Sms.php';
class UserAjax extends Common{
    //获取首页信息
    public function getHomeInfo(){
        //验证是否助力本人
        $hid=Context::Post('id');
        if(!empty($hid)){
            $hid=Encryption::authcode(base64_decode($hid));
            if($hid==Session::get('user_id')){
                echo json_encode(['success'=>false]);exit;
            }
        }
        $data=[];
        //获取logo广告信息
        $LOGOIDS=$this->db->Name('setting')->select()->where_equalTo('`key`','LOGOIDS')->firstRow()['value'];
        if(!empty($LOGOIDS)){
            $LOGOIDS=explode(',',$LOGOIDS);
        }else{
            $LOGOIDS=[];
        }
        $data['list']=[];
        $tmp=(new Query())->Name('logo')->select()->where_in('id',$LOGOIDS)->execute();
        if(!empty($tmp)){
            foreach($LOGOIDS as $id){
                foreach($tmp as $val){
                    if($val['id']==$id){
                        $val['show']=false;
                        $val['integral']='???';
                        $data['list'][]=$val;
                    }
                }
            }
        }
        //获取所有广告图信息
        $data['listImgs']=[];
        $listImgs=(new Query())->Name('logo')->select()->where_equalTo('status',1)->execute();
        if(!empty($listImgs)){
            foreach($listImgs as $vall){
                $data['listImgs'][]=$vall['logodetail_img'];
            }
        }
        //获取用户信息
        $userInfo=(new Query())->Name('user')->select()->where_equalTo('id',Session::get('user_id'))->firstRow();
        $data['flipnum']=empty($userInfo['flipnum'])?0:$userInfo['flipnum'];
        $data['user_status']=empty(Session::get('user_status'))?false:true;
        $data['is_phone']=empty($userInfo['phone'])?true:false;
        $data['is_phone_switch']=$this->db->Name('setting')->select()->where_equalTo('`key`','ISPHONE')->firstRow()['value'];
        $data['is_phone_switch']=$data['is_phone_switch']=='false'?true:false;
        //获取积分商城开启时间
        $data['exchange']="时间待定";$data['exchangeall']="时间待定";
        $exchangeTime=(new Query())->Name('exchange_time')->select()->execute();
        if(!empty($exchangeTime)){
            $data['exchange']="";$data['exchangeall']="";$arr=[];
            foreach($exchangeTime as $time){
                $arr[$time['star']]=$time;
            }
            ksort($arr);
            foreach($arr as $v){
                if($data['exchange']==""){$data['exchange']=date("y年m月d日 H:i",$v['star']).' 至 '.date("y年m月d日 H:i",$v['end']);}
                $data['exchangeall'].=date("Y年m月d日 H:i",$v['star']).'~'.date("Y年m月d日 H:i",$v['end']).'、';
            }
            $data['exchangeall']=trim($data['exchangeall'],'、');
        }
        //获取助力信息
        $timestar=strtotime(date("Y-m-d"),time());
        $timeend=$timestar+60*60*24;
        $data['helpList']=[];
        $helpList=(new Query())->Name('integral_detail')->select()->where_equalTo('user_id',Session::get('user_id'))->where_equalTo('type',2)->where_greatThanOrEqual('create_time',$timestar)->where_lessThan('create_time',$timeend)->execute();
        $data['helpNum']=0;
        $data['helpProportion']=0;
        if(!empty($helpList)){
            $userDict=[];$ids=[];
            foreach($helpList as $v){
                $ids[]=$v['help_id'];
            }
            $ids=array_unique($ids);
            sort($ids);
            $userd=(new Query())->Name('user')->select()->where_in('id',$ids)->execute();
            foreach($userd as $vv){
                $userDict[$vv['id']]['nickname']=$vv['nickname'];
                $userDict[$vv['id']]['headimgurl']=$vv['headimgurl'];
            }
            foreach($helpList as $key=>$value){
                $data['helpList'][$key]['nickname']=json_decode($userDict[$value['help_id']]['nickname']);
                $data['helpList'][$key]['headimgurl']=$userDict[$value['help_id']]['headimgurl'];
                $data['helpList'][$key]['exchange']=$value['integral_change'];
                $data['helpNum']+=intval($value['integral_change']);
            }
            $data['helpProportion']=($data['helpNum']/500)*100;
        }
        echo json_encode(['success'=>true,'data'=>$data],JSON_UNESCAPED_UNICODE);
    }
    public function arraySort($array,$sort='asc') {
        $newArr = $valArr = array();
        foreach ($array as $key=>$value) {
            $valArr[$key] = $value;
        }
        ($sort == 'asc') ?  asort($valArr) : arsort($valArr);//先利用keys对数组排序，目的是把目标数组的key排好序
        reset($valArr); //指针指向数组第一个值
        foreach($valArr as $key=>$value) {
            $newArr[$key] = $array[$key];
        }
        return $newArr;
    }
    //检查时间是否符合
    public function chechTime(){
        if(JHSWITCH){
            $todayone1 = strtotime(date("Y-m-d"),time());
            $todayone2 = $todayone1+(60*5);
            $todaytow2 = $todayone1+(60*60*24);
            $todaytow1 = $todaytow2-(60*5);
            $newTime=time();
            if($newTime>$todayone2 && $newTime<$todaytow1){
                return true;
            }else{
                echo json_encode(['success'=>false,'message'=>'现在时系统初始化时间，请稍后！'],JSON_UNESCAPED_UNICODE);exit;
            }
        }else{
            echo json_encode(['success'=>false,'message'=>'游戏时间已结束！'],JSON_UNESCAPED_UNICODE);exit;
        }
    }
    //日常翻牌请求
    public function dailyCard(){
        $this->chechTime();
        //type  1:日常翻拍    2:助力翻拍       3:实物兑换
        $userInfo=$this->db->Name('user')->select('flipnum')->where_equalTo('id',Session::get('user_id'))->firstRow()['flipnum'];
        if(empty($userInfo)){
            echo json_encode(['success'=>false,'message'=>'你的翻牌次数已用完'],JSON_UNESCAPED_UNICODE);exit;
        }
        if(empty(Session::get('user_status'))){
            echo json_encode(['success'=>false,'message'=>'您的账号已被限制'],JSON_UNESCAPED_UNICODE);exit;
        }
        try {
            $pdo = new DataBase();
            $pdo->beginTransaction(); // 开启一个事务
            $integral=mt_rand (85,150);
            //添加积分明细
            $data1['user_id']=Session::get('user_id');
            $data1['type']=1;
            $data1['integral_change']='+'.$integral;
            $data1['`describe`']="日常翻牌+".$integral."积分";
            $data1['create_time']=time();
            $data1['update_time']=time();
            $res1=(new Query())->Name('integral_detail')->insert($data1)->execute();
            //修改用户数据
            $sql="UPDATE ".Table_Pre."user SET `integral` = `integral`+".$integral.",`flipnum`=`flipnum`-1 WHERE `id` = :id";
            $arr=[":id"=>Session::get('user_id')];
            $res2=DataBase::Update($sql,$arr);
            if($res1 && $res2){
                $pdo->commit();
                echo json_encode(['success'=>true,'data'=>['flipsNum'=>$integral]],JSON_UNESCAPED_UNICODE);
            }else{
                $pdo->rollBack();
                echo json_encode(['success'=>false,'message'=>'数据有误'],JSON_UNESCAPED_UNICODE);
            }
        } catch (PDOException $e) {
            $pdo->rollback();
            echo json_encode(['success'=>false,'message'=>'数据有误'],JSON_UNESCAPED_UNICODE);
        }
    }
    //分享增加翻牌次数
    public function addShare(){
        $this->chechTime();
        //查询今日是否已分享过
        $is_share=$this->db->Name('user')->select()->where_equalTo('id',Session::get('user_id'))->firstRow();
        if(!empty($is_share['is_share'])){
            $res=(new Query())->Name('user')->update(['is_share'=>'0','flipnum'=>$is_share['flipnum']+1,'update_time'=>time()])->where_equalTo('id',Session::get('user_id'))->execute();
            if($res)
                echo json_encode(['success'=>true]);
            else
                echo json_encode(['success'=>false]);
        }else{
            echo json_encode(['success'=>false]);
        }
    }
    //好友助力积分
    public function addHelp(){
        $this->chechTime();
        $hid=Context::Post('id');
        $hid=Encryption::authcode(base64_decode($hid));
        if(empty($hid) || $hid==Session::get('user_id')){
            echo json_encode(['success'=>false,'message'=>'不能为自己助力!'],JSON_UNESCAPED_UNICODE);
        }else{
            $timestar=strtotime(date("Y-m-d"),time());
            $timeend=$timestar+60*60*24;
            $isHelp=(new Query())->Name('integral_detail')->select()->where_equalTo('user_id',$hid)->where_equalTo('help_id',Session::get('user_id'))->where_equalTo('type',2)->where_greatThanOrEqual('create_time',$timestar)->where_lessThan('create_time',$timeend)->firstRow();
            if(!empty($isHelp)){
                echo json_encode(['success'=>false,'message'=>'今日已帮该好友助力过了!'],JSON_UNESCAPED_UNICODE);
            }else{
                //每天只能帮3个好友助力
                $maxHelp=(new Query())->Name('integral_detail')->select()->where_equalTo('help_id',Session::get('user_id'))->where_equalTo('type',2)->where_greatThanOrEqual('create_time',$timestar)->where_lessThan('create_time',$timeend)->execute();
                if(count($maxHelp)>=3){
                    echo json_encode(['success'=>false,'message'=>'每天只能帮好友助力3次！'],JSON_UNESCAPED_UNICODE);exit;
                }
                $userInfo=$this->db->Name('user')->select()->where_equalTo('id',Session::get('user_id'))->firstRow();
                $heInfo=$this->db->Name('user')->select()->where_equalTo('id',$hid)->firstRow();
                if(!empty($userInfo) && !empty($heInfo)){
                    if(empty($userInfo['status'])){
                        echo json_encode(['success'=>false,'message'=>'您的账号已被限制！'],JSON_UNESCAPED_UNICODE);exit;
                    }
                    if(empty($heInfo['status'])){
                        echo json_encode(['success'=>false,'message'=>'您的好友账号已被限制！'],JSON_UNESCAPED_UNICODE);exit;
                    }
                    if(empty($heInfo['helpintegral'])){
                        echo json_encode(['success'=>false,'message'=>'好友助力积分已到达上限！'],JSON_UNESCAPED_UNICODE);exit;
                    }
                    try {
                        $pdo = new DataBase();
                        $pdo->beginTransaction(); // 开启一个事务
                        $integral=mt_rand (30,60);
                        if(intval($heInfo['helpintegral']) < $integral){
                            $integral=intval($heInfo['helpintegral']);
                        }
                        //添加积分明细
                        $data1['user_id']=$hid;
                        $data1['help_id']=Session::get('user_id');
                        $data1['type']=2;
                        $data1['integral_change']='+'.$integral;
                        $data1['`describe`']=json_decode($userInfo['nickname'])."助力翻牌+".$integral."积分";
                        $data1['create_time']=time();
                        $data1['update_time']=time();
                        $res1=(new Query())->Name('integral_detail')->insert($data1)->execute();
                        //修改用户数据
                        $sql="UPDATE ".Table_Pre."user SET `integral` = `integral`+".$integral.",`helpintegral`=`helpintegral`-".$integral." WHERE `id` = :id";
                        $arr=[":id"=>$hid];
                        $res2=DataBase::Update($sql,$arr);
                        if($res1 && $res2){
                            $pdo->commit();
                            echo json_encode(['success'=>true,'data'=>['flipsNum'=>$integral]],JSON_UNESCAPED_UNICODE);
                        }else{
                            $pdo->rollBack();
                            echo json_encode(['success'=>false,'message'=>'数据有误'],JSON_UNESCAPED_UNICODE);
                        }
                    } catch (PDOException $e) {
                        $pdo->rollback();
                        echo json_encode(['success'=>false,'message'=>'数据有误'],JSON_UNESCAPED_UNICODE);
                    }
                }else{
                    echo json_encode(['success'=>false,'message'=>'数据有误'],JSON_UNESCAPED_UNICODE);
                }
            }
        }
    }
    //发送手机验证码
    public function shenyzm(){
        DataBase::log(__FILE__.__LINE__,'shenyzm');
		if(empty(Session::get('user_status'))){echo json_encode(['success'=>false,'message'=>'您的账号已被限制！'],JSON_UNESCAPED_UNICODE);exit;}
        $phone=Context::Post('phone');
        $yzm=mt_rand(100000,999999);
        $sms=(new Sms())->message($phone,$yzm);
        if($sms){
            Session::set('phone',$phone);
            Session::set('yzm',$yzm);
            echo json_encode(['success'=>true]);
        }else{
            DataBase::log(__FILE__.__LINE__,'shenyzm error');
			echo json_encode(['success'=>false,'message'=>'发送验证码失败'],JSON_UNESCAPED_UNICODE);
        }
    }
    //验证添加手机号码
    public function addPhone(){
        $phone=Context::Post('phone');
        $yzm=Context::Post('yzm');
        if($phone!=Session::get('phone') || $yzm!=Session::get('yzm')){
            echo json_encode(['success'=>false]);
        }else{
            $res=$this->db->Name('user')->update(['phone'=>$phone,'update_time'=>time()])->where_equalTo('id',Session::get('user_id'))->execute();
            if($res)
                echo json_encode(['success'=>true]);
            else
                echo json_encode(['success'=>false]);
        }
    }
    //请求分享数据
    public function getShare(){
        $jsapiTicket = trim($this->getJsApiTicket());
        //$url = $_SERVER['HTTP_REFERER'];
				$url = $_POST["url"];
				//$url = "http://qzg.999house.com/youxiqzylf/index2.html?is_login=1";
        $timestamp = time();
        $nonceStr = $this->createNonceStr();
        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=".$jsapiTicket."&noncestr=".$nonceStr."&timestamp=".$timestamp."&url=".$url;
        $signature = sha1($string);
        $data = array(
            "url"  => $url,
						"user_id"   => base64_encode(Encryption::authcode(Session::get('user_id'),false)),
            "appId"     => $this->db->Name('setting')->select()->where_equalTo('`key`','APPID')->firstRow()['value'],
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "signature" => $signature,
			"share_title"=>$this->db->Name('setting')->select()->where_equalTo('`key`','share_title')->firstRow()['value'],
			"share_desc"=>$this->db->Name('setting')->select()->where_equalTo('`key`','share_desc')->firstRow()['value'],
			"share_img"=>$this->db->Name('setting')->select()->where_equalTo('`key`','share_img')->firstRow()['value']
        );
        echo json_encode(['data'=>$data],JSON_UNESCAPED_UNICODE);
    }
    //获取access_toke
    public function getAccessToken(){
        /*$access_token_time = $this->db->Name('setting')->select()->where_equalTo('`key`','ACCESSTOKENTIME')->firstRow()['value'];
        if(time() > $access_token_time || empty($access_token_time)){
            $appid = $this->db->Name('setting')->select()->where_equalTo('`key`','APPID')->firstRow()['value'];
            $secret = $this->db->Name('setting')->select()->where_equalTo('`key`','APPSECRET')->firstRow()['value'];
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";
            $token = $this->getJson($url);
            //打印获得的数据
            $arr=json_decode($token,true);
            $access_token = $arr['access_token'];
            $expires_in = $arr['expires_in']-3600+time();
            (new Query())->Name('setting')->update(['value'=>$access_token])->where_equalTo('`key`','ACCESSTOKEN')->execute();
            (new Query())->Name('setting')->update(['value'=>$expires_in])->where_equalTo('`key`','ACCESSTOKENTIME')->execute();
            return $access_token;
        }else{
            return $this->db->Name('setting')->select()->where_equalTo('`key`','ACCESSTOKEN')->firstRow()['value'];
        }*/
    }
    //获取jsapi_ticket
    public function getJsApiTicket(){
        /*$jsapi_ticket_time = $this->db->Name('setting')->select()->where_equalTo('`key`','JSAPITICKETTIME')->firstRow()['value'];
        if(time() > $jsapi_ticket_time || empty($jsapi_ticket_time)){
            $accessToken = $this->getAccessToken();
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $token = $this->getJson($url);
            //打印获得的数据
            $arr=json_decode($token,true);
            $jsapi_ticket = $arr['ticket'];
            $expires_in = $arr['expires_in']-3600+time();
            (new Query())->Name('setting')->update(['value'=>$jsapi_ticket])->where_equalTo('`key`','JSAPITICKET')->execute();
            (new Query())->Name('setting')->update(['value'=>$expires_in])->where_equalTo('`key`','JSAPITICKETTIME')->execute();
            return $jsapi_ticket;
        }else{
            return $this->db->Name('setting')->select()->where_equalTo('`key`','JSAPITICKET')->firstRow()['value'];
        }*/
				$url = "http://m.999house.com/api/wxchat/getJsapiTicket2";
				$jsapi_ticket = $this->getJson($url);
				return trim($jsapi_ticket);
    }
    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
}