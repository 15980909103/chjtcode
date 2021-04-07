<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: pl125 <xskjs888@163.com>
// +----------------------------------------------------------------------

namespace app\common\lib\wxapi\module;

use app\common\base\HhDb;
use think\facade\Config;

class  WxWeb extends WxBase
{
//    use TraitInstance;

    public function __construct($dotype = 'wxWeb')
    {
        parent::__construct($dotype);
    }

    /**
     * 公众号授权登陆
     * @return mixed
     * @throws \Throwable
     */
    public function getOauthLogin($dataArr=[]){
        try {
            $rs = $this->oauthLogin($dataArr);
            $this->getSession_WxAuth($rs);
            return $rs;
        } catch (\Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     * 公众号获取用户信息
     * @param string $openid 调用【网页授权获取用户信息】接口获取到用户在该公众号下的Openid
     * @return string
     */
    public function getUserInfo($param = [])
    {
        $rs=$this->getOauthLogin($param);
        $openid=$rs['openid'];
        $access_token=$rs['access_token'];

        return $response = self::curlGet('https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN');
    }

    /**
     * 公众号获取用户授权
     * 通过跳转获取用户的openid，跳转流程如下：
     * 1、设置自己需要调回的url及其其他参数，跳转到微信服务器https://open.weixin.qq.com/connect/oauth2/authorize
     * 2、微信服务处理完成之后会跳转回用户redirect_uri地址，此时会带上一些参数，如：code
     *
     * @return string 用户的openid
     */
    private function oauthLogin($dataArr = [])
    {
        if (!isset($dataArr['code'])){// 授权登陆
            $scheme = $_SERVER['HTTPS']=='on' ? 'https://' : 'http://';
            $uri='';
//            if($_SERVER['REQUEST_URI']){$uri = $_SERVER['REQUEST_URI'];}
            $uri = empty($dataArr['redirect_uri']) ? empty($_SERVER['REQUEST_URI']) ? '' : $_SERVER['REQUEST_URI'] : $dataArr['redirect_uri'];
            $redirectUrl = urlencode($scheme.$_SERVER['HTTP_HOST'].$uri);

            $urlObj["appid"] = $this->appid;
            $urlObj["redirect_uri"] = "$redirectUrl";
            $urlObj["response_type"] = "code";
            $urlObj["scope"] = "snsapi_userinfo";
            $urlObj["state"] = "STATE";
            $bizString = $this->ToUrlParams($urlObj);
            $url = "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
            //触发微信返回code码
            Header("Location: $url");
//            exit();
        } else {// 授权后回调
            //获取code码，进行授权登陆
            $code = $dataArr['code'];

            $urlObj["appid"] = $this->appid;
            $urlObj["secret"] = $this->appSecret;
            $urlObj["code"] = $code;
            $urlObj["grant_type"] = "authorization_code";
            $bizString = $this->ToUrlParams($urlObj);
            $url = "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;

            return self::curlGet($url);
        }
    }

    //用于第一步微信公众号官网token配置
    public function validateWxTonkenConfig($param){
        $signature = $param['signature'];
        $nonce = $param['nonce'];
        $timestamp = $param['timestamp'];
        $token = Config::get('app')['wxH5config']['token'];
//        (new HhDb())->init()->name('log')->insert(['content' => json_encode([$nonce, $timestamp, $token, $signature])]);
        if($signature&&$timestamp&&$nonce){
            $arr=[$nonce,$timestamp,$token];
            sort($arr, SORT_STRING);

            $tmpstr = implode('',$arr);
            $tmpstr = sha1($tmpstr);
            $arr = [$tmpstr, $signature];
//            (new HhDb())->init()->name('log')->insert(['content' => json_encode($arr)]);
            if($tmpstr == $signature){
                return $param['echostr'];
            } else {
                return '验签错误';
            }
        }
    }

    public function getAccessToken(){
        $session_AccessToken=$this->getCache_AccessToken();
        if(empty($session_AccessToken)){
            $appid =  $this->appid;
            $appSecret =  $this->appSecret;

            $rs = self::curlGet('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$appSecret);
            if(!empty($rs['access_token'])){
                $session_AccessToken = $this->getCache_AccessToken($rs['access_token']);
            }
        }
        return $session_AccessToken;
    }

    /**
     * 获取公众号jssdk配置需要的临时票据
     * @return mixed
     */
    private function getJsapiTicket(){
        //https://mp.weixin.qq.com/wiki?action=doc&id=mp1421141115&t=0.15947710316920038#62
        $session_Ticket = $this->getCache_Ticket();
        if(empty($session_Ticket)){
            $access_token = $this->getAccessToken();
            $rs = self::curlGet('https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$access_token.'&type=jsapi');
            if(!empty($rs['ticket'])){
                $session_Ticket = $this->getCache_Ticket($rs['ticket']);
            }
        }
        return $session_Ticket;
    }

    /**
     * 获取公众号jssdk的api调用时需要的配置信息
     */
    public function getJsSdkConfig(){
        $rs['noncestr'] = $this->createNonceStr();
        $rs['timestamp'] = time();
        //$url = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $url = 'https://'.$_SERVER['HTTP_HOST'];

        $arr = [
            'noncestr' =>$rs['noncestr'],
            'timestamp' =>$rs['timestamp'],
            'jsapi_ticket' =>$this->getJsapiTicket(),
            'url' => $url
        ];
        ksort($arr);
        $arr = http_build_query($arr);
        $arr= urldecode($arr);

        $rs['signature'] = sha1($arr);
        $rs['appid'] = $this->appid;
        return $rs;
    }

    private function getCache_AccessToken($data=null){
        if(!empty($data)){
            cache('_wxaccesstoken', $data, 5400); //缓存1个半小时
        }
        return cache('_wxaccesstoken');
    }

    private function getCache_Ticket($data=null){
        if(!empty($data)){
            cache('_wxjsticket', $data, 5400); //缓存1个半小时
        }
        return cache('_wxjsticket');
    }


}



