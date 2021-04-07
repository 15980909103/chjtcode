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
class LoginAjax extends Controller{
    public function __construct(){
        $this->db = new Query();
				$this->baseUrl='http://qzg.999house.com';
    }
    public function login(){
        $jumpUrl=Context::Get('url');
        $user_id=Context::Get('id');
        if(empty($jumpUrl))
            $jumpUrl="youxiqzylf/index.html";
				$jumpUrl=$this->baseUrl."/".$jumpUrl;
        /*$appid = $this->db->Name('setting')->select()->where_equalTo('`key`','APPID')->firstRow()['value'];
        $domain_name = $this->db->Name('setting')->select()->where_equalTo('`key`','DOMAINNAME')->firstRow()['value'];
        if(empty($user_id))
            $redirect_uri = urlencode($domain_name.'/api/loginAjax/getinfo?url='.$jumpUrl);
        else
            $redirect_uri = urlencode($domain_name.'/api/loginAjax/getinfo?url='.$jumpUrl.'&id='.$user_id);
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_userinfo&state=0#wechat_redirect";*/
				if(empty($user_id))
            $redirect_uri = urlencode($this->baseUrl.'/api/loginAjax/getinfo?url='.urlencode($jumpUrl));
        else
            $redirect_uri = urlencode($this->baseUrl.'/api/loginAjax/getinfo?url='.urlencode($jumpUrl).'&id='.$user_id);
				$url = "http://m.999house.com/api/wxchat/login?url=".$redirect_uri;
        Context::Redirect($url);
        exit;
    }
    //获取用户信息与unionid
    public function getinfo(){
        $errurl=$this->baseUrl.'/youxiqzylf/qrcode.html';
        $code = Context::Get('code');
        $oauth2Url = "http://m.999house.com/api/wxchat/getwxopenid?code=".$code;
        $oauth2 = $this->getJson($oauth2Url);
        $oauth2 = json_decode($oauth2, true);
//        echo $oauth2['unionid'];exit;
        if(isset($oauth2['unionid']) && !empty($oauth2['unionid'])){
            $userInfo=$this->db->Name('user')->select()->where_equalTo('unionid',$oauth2['unionid'])->firstRow();
            //if(empty($userInfo)){Context::Redirect($errurl);}
            //if(empty($userInfo['subscribe'])){Context::Redirect($errurl);}
						if(empty($userInfo)){
							$url = "http://m.999house.com/api/wxchat/getwxinfo?access_token=".$oauth2['access_token']."&openid=".$oauth2['openid'];
							$arr = $this->getJson($url);
							$arr = json_decode($arr, true);
							$addArr=[
								"subscribe" => 1,
								"openid" => $arr['openid'],
								"unionid" => $arr['unionid'],
								'nickname' => json_encode($arr['nickname']),
								'headimgurl' => $arr['headimgurl'],
								'sex' => $arr['sex'],
								'city' => $arr['city'],
								'country' => $arr['country'],
								'province' => $arr['province'],
								'language' => $arr['language'],
								'create_time' => !empty($arr['subscribe_time'])?$arr['subscribe_time']:0,
								'update_time' => !empty($arr['subscribe_time'])?$arr['subscribe_time']:0,
							];
							$this->db->Name('user')->insert($addArr)->execute();
							$userInfo=$this->db->Name('user')->select()->where_equalTo('unionid',$arr['unionid'])->firstRow();
						}
            Session::set('user_id',$userInfo['id']);
            Session::set('user_status',$userInfo['status']);
            if(empty(Context::Get('id'))){
                $url=Context::Get('url').'?is_login=1';
            }else{
                $yzID=Encryption::authcode(base64_decode(Context::Get('id')));
                if($yzID==$userInfo['id'])
                    $url=Context::Get('url').'?is_login=1';
                else
                    $url=Context::Get('url').'?is_login=1&id='.Context::Get('id');
            }
            Context::Redirect($url);
        }else{
            Context::Redirect($errurl);
        }
				/*$appid = $this->db->Name('setting')->select()->where_equalTo('`key`','APPID')->firstRow()['value'];
        $secret = $this->db->Name('setting')->select()->where_equalTo('`key`','APPSECRET')->firstRow()['value'];
        $domain_name = $this->db->Name('setting')->select()->where_equalTo('`key`','DOMAINNAME')->firstRow()['value'];
        $errurl=$domain_name.'/youxiqzylf/qrcode.html';
        $code = Context::Get('code');

        $oauth2Url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code=$code&grant_type=authorization_code";
        $oauth2 = $this->getJson($oauth2Url);
        $oauth2 = json_decode($oauth2, true);
//        echo $oauth2['unionid'];exit;
        if(isset($oauth2['unionid']) && !empty($oauth2['unionid'])){
            $userInfo=(new Query())->Name('user')->select()->where_equalTo('unionid',$oauth2['unionid'])->firstRow();
            if(empty($userInfo)){Context::Redirect($errurl);}
            if(empty($userInfo['subscribe'])){Context::Redirect($errurl);}
            Session::set('user_id',$userInfo['id']);
            Session::set('user_status',$userInfo['status']);
            if(empty(Context::Get('id'))){
                $url=$domain_name.'/'.Context::Get('url').'?is_login=1';
            }else{
                $yzID=Encryption::authcode(base64_decode(Context::Get('id')));
                if($yzID==$userInfo['id'])
                    $url=$domain_name.'/'.Context::Get('url').'?is_login=1';
                else
                    $url=$domain_name.'/'.Context::Get('url').'?is_login=1&id='.Context::Get('id');
            }
            Context::Redirect($url);
        }else{
            Context::Redirect($errurl);
        }*/
    }
    //获取公众号二维码
    public function qrcode(){
        $img=$this->db->Name('setting')->select()->where_equalTo('`key`','QRCODE')->firstRow()['value'];
        echo json_encode(['success'=>true,'qrcode'=>$img]);
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
}