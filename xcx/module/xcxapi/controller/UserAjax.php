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
include_once "wxBizDataCrypt.php";
include Lib . DS . 'JWT.php';
class UserAjax extends Common{
    //小程序端用户登录
    //获取openid
    protected function getopenid($js_code=''){
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=".APPID."&secret=".SECRET."&js_code=".$js_code."&grant_type=authorization_code";
        $res = $this->sendPost($url);
        $res=json_decode($res,true);
        return $res;
    }
    //解密encryptedData数据并添加用户信息
    public function updateuser(){
        $js_code= Context::Post('js_code');
        if(empty($js_code)){
            return $this->error('参数缺失');
        }

        $res = $this->getopenid($js_code);
        $sessionKey = $res['session_key'];
        $encryptedData=Context::Post('encryptedData');
        $iv=Context::Post('iv');
        $pc = new WXBizDataCrypt(APPID, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data );

        if ($errCode == 0) {
            try{
                DataBase::beginTransaction();
                $data=json_decode($data,true);
                $parameter['nickName']=$data['nickName'];
                $parameter['avatarUrl']=$data['avatarUrl'];
                $parameter['language']=$data['language'];
                $parameter['gender']=$data['gender'];
                $parameter['country']=$data['country'];
                $parameter['province']=$data['province'];
                $parameter['city']=$data['city'];
                $parameter['openId']=$data['openId'];
                $parameter['unionId']=$data['unionId'];
                $parameter['create_time']=time();
                $parameter['update_time']=time();
                $info=$this->db->Name('xcx_user')->select()->where_equalTo('unionId',$data['unionId'])->firstRow();
                if(empty($info)){   //添加数据
                    $resId=$this->db->Name('xcx_user')->insert($parameter)->execute();
                    if(!empty($resId)){
                        $jwtToken=JWT::encode(['from_type'=>'1','agent_id'=>'0','user_id'=>$resId,'create_time'=>time()],'9hhouse');
                        $parameter['user_id']=$resId;
                        $parameter['id']=Encryption::authcode($resId,false);
                        //添加对应的优质经纪人
                        /*$atAgentRow=$this->db->Name('xcx_agent_user')->select('id')->where_equalTo('at_agent','1')->execute();
                        if(!empty($atAgentRow)){
                            foreach($atAgentRow as $val){
                                $this->db->Name('xcx_agent_customer')->insert(['agent_id'=>$val['id'],'user_id'=>$resId,'agent_status'=>1,'user_status'=>1,'create_time'=>time(),'update_time'=>time()])->execute();
                            }
                        }*/
                        $user_id = $resId;
                    }else{
                        throw new Exception('授权登录失败-1:入库失败');
                    }
                }else{
                    //更新用户数据
                    $this->db->Name('xcx_user')->update([
                        'nickName'=>$data['nickName'],
                        'avatarUrl'=>$data['avatarUrl'],
                        'language'=>$data['language'],
                        'gender'=>$data['gender'],
                        'country'=>$data['country'],
                        'province'=>$data['province'],
                        'city'=>$data['city'],
                        'openId'=>$data['openId'],
                        'update_time'=>time(),
                    ])->where_equalTo('id',$info['id'])->execute();

                    $user_id = $info['id'];
                    $jwtToken=JWT::encode(['from_type'=>'1','agent_id'=>'0','user_id'=>$info['id'],'create_time'=>time()],'9hhouse');
                    $parameter['user_id']= $user_id;
                    $parameter['id']=Encryption::authcode($info['id'],false);
                }
                //形成客户关系
                /*if(!empty($agent_id)){
                    $has2 = $this->db->Name('xcx_agent_customer')->select('id')->where_equalTo('agent_id',$agent_id)->where_equalTo('user_id',$user_id)->firstRow();
                    if(empty($has2)){
                        $source = intval(Context::Post('source')); //来源 0自己关注，1：经纪人名片 2：文章 3：楼盘
                        if(!in_array($source,['0','1','2','3'])){
                            $source = 0;
                        }
                        $this->db->Name('xcx_agent_customer')->insert(['agent_id'=>$agent_id,'user_id'=>$user_id,'source'=>$source,'agent_status'=>1,'user_status'=>1,'create_time'=>time(),'update_time'=>time()])->execute();
                    }
                }*/

                DataBase::commit();
                return $this->success(['userInfo'=>$parameter,'token'=>$jwtToken]);
            }catch(Exception $ex){
                DataBase::rollBack();
                return $this->error('授权登录失败-2:' . $ex->getMessage());
            }
        } else {
            return $this->error('授权登录失败-3:错误码：' . $errCode);
        }
    }
    //申请入驻店铺
    public function storelogin(){
        $id = Context::Get('id');     //店铺id
        $appid = WXAPPID;
        $domain_name = WX_HOST;
        $id=Encryption::authcode($id);
        if(empty($id)){
            $myData['message']='不要乱改参数哟，亲！';
            return $this->render('messagePage',$myData);
        }
        $redirect_uri = urlencode($domain_name.'/xcxapi/userAjax/gestoreinfo?store_id='.$id);
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_userinfo&state=0#wechat_redirect";
        Context::Redirect($url);
        exit;
    }
    //申请入驻店铺
    public function gestoreinfo(){
        $appid = WXAPPID;
        $secret = WXSECRET;
        $code = Context::Get('code');
        $oauth2Url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code=$code&grant_type=authorization_code";
        $oauth2 = $this->getJson($oauth2Url);
        $oauth2 = json_decode($oauth2, true);
        if(isset($oauth2['unionid']) && !empty($oauth2['unionid'])){
            $userInfo=(new Query())->Name('xcx_agent_user')->select()->where_equalTo('unionid',$oauth2['unionid'])->firstRow();
            if(empty($userInfo)){
                $resData = $this->getJson("https://api.weixin.qq.com/sns/userinfo?access_token=".$oauth2['access_token']."&openid=".$appid."&lang=zh_CN");
                $resData = json_decode($resData, true);
                if(!empty($resData)){
                    $data['nickname']=$resData['nickname'];
                    $data['headimgurl']=$resData['headimgurl'];
                    $data['sex']=$resData['sex'];
                    $data['language']=$resData['language'];
                    $data['country']=$resData['country'];
                    $data['province']=$resData['province'];
                    $data['city']=$resData['city'];
                    $data['openid']=$resData['openid'];
                    $data['unionid']=$resData['unionid'];
                    $data['create_time']=time();
                    $data['update_time']=time();
                    $resId=$this->db->Name('xcx_agent_user')->insert($data)->execute();
                    if(!empty($resId)){
                        $agent_id=$resId;
                    }else{
                        $myData['message']='用户添加失败！';
                    }
                }else{
                    $myData['message']='获取用户信息失败！';
                }
            }else{
                $agent_id=$userInfo['id'];
            }
            if(!isset($myData['message'])){
                //申请入驻店铺
                $store_id = Context::Get('store_id');   //店铺id
                if(!empty($store_id)){
                    $agentInfo=(new Query())->Name('xcx_agent_user')->select()->where_equalTo('id',$agent_id)->firstRow();
                    $storeAgentInfo=(new Query())->Name('xcx_store_agent')->select()->where_equalTo('agent_id',$agent_id)->firstRow();
                    if(($agentInfo['sq_store_status']=='2' && $agentInfo['sq_store_id']>0) || !empty($storeAgentInfo)){
                        $myData['message']='你已经是经纪人了，无法再次入驻！';
                    }else{
                        $sq['sq_store_id']=$store_id;
                        $sq['sq_store_status']='1';
                        $sq['sq_store_addtime']=time();
                        $sq['sq_store_audittime']=time();
                        $res=(new Query())->Name('xcx_agent_user')->update($sq)->where_equalTo('id',$agent_id)->execute();
                        if(empty($res)){
                            $myData['message']='数据修改失败';
                        }else{
                            $myData['message']='申请成功，请等待审核。。。';
                        }
                    }
                }else{
                    $myData['message']='店铺参数有误！！！';
                }
            }
        }else{
            $myData['message']='登陆参数有误！';
        }
        return $this->render('messagePage',$myData);
    }



    //token验证
    public function tokenValidation(){
        $token=Context::Post('myPath');
        if(empty($token)){echo json_encode(['success'=>false,'msg'=>'token为空']);exit;}
        $payload=JWT::decode($token,'9hhouse');
        if(is_array($payload) || is_object($payload)){
            echo json_encode(['success'=>true,'data'=>$payload]);
        }else{
            echo json_encode(['success'=>false,'msg'=>$payload]);
        }
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

    //获取经纪人名片信息
    public function getCardData(){
        $id=Context::Post('id');
        $data=[];
        $access_token=$this->getAccessToken2();
        $parameter=["scene"=>'agent_id='.$id,"page"=>"pages/store/store_detail/store_detail"];

        $qrCode=$this->sendPost('https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token='.$access_token,json_encode($parameter));
        $qrCode=$this->data_uri($qrCode,'image/png');

        $data=array_merge($data,['userInfo'=>$this->getAgentInfo($id)],["qrCode"=>$qrCode,"agent_id"=>$id]);
        return $this->success($data);
    }
    //二进制转图片image/png
    private function data_uri($contents, $mime)
    {
        $base64   = base64_encode($contents);
        return ('data:' . $mime . ';base64,' . $base64);
    }
}