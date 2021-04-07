<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: pl125 <xskjs888@163.com>
// +----------------------------------------------------------------------

namespace app\common\lib\wxapi\module;

use app\common\lib\TraitInstance;
use think\Exception;

class  WxAuthorize extends WxBase
{
    use TraitInstance;

    /**
     * @param string $dotype
     * @param array $dataArr //[ code,decryptArray ],decryptArray=>[ encryptedData,iv ]
     * @return string
     * @throws \Exception
     */
    public function getOathLogin($dataArr=[]){
        $dotype= $this->dotype;

        if($dotype=='wxWeb'){
            $rs = $this->oathLoginForWxWeb();
            $this->getSession_WxAuth($rs);
            return $rs;
        }else{
            $rs = $this->oathLoginForWxApp($dataArr['code'],$dataArr['decryptArray']);
            $this->getSession_WxAuth($rs);
            return $rs;
        }
    }
    /**
     *
     * @return string
     * @throws \Exception
     */
    public function getUserInfo(){
        if($this->dotype=='wxWeb'){
            return $this->userInfoForWxWeb();
        }
    }
//==================小程序的授权======================//

    /**
     * 小程序的授权登陆
     * @param string $code //小程序客户端授权码
     * @param array $decryptArray [ encryptedData,iv ] //小程序客户端用户加密数据
     * @return 返回用户信息
     * @throws \Exception
     */
    private function oathLoginForWxApp($code='',$decryptArray=[]) {
        if(empty($code)||empty($decryptArray)){
            throw  new \Exception("缺少参数");
        }
        $appid=$this->appid;
        $appsecret=$this->appSecret;
        $url="https://api.weixin.qq.com/sns/jscode2session?appid=".$appid."&secret=".$appsecret."&js_code=".$code."&grant_type=authorization_code";
        /*$content = @file_get_contents ( $url );//ssl链接时需要开启相关配置
        $content = json_decode ( $content, true );*/
        $content = $this->curlGet( $url );

        //进行解密操作
        $errCode =$this->doDecryptDataForWxApp([
            'session_key' => $content['session_key'],
            'encryptedData' => $decryptArray['encryptedData'],
            'iv' => $decryptArray['iv']
        ], $data);

        if ($errCode == 0) {
            $data=json_decode($data, true);
            unset($data['watermark']);
            $data['session_key']=$content['session_key'];
        } else {
            $data=[];
        }

        if(empty($data)){
            throw  new \Exception("登陆凭证校验失败".$errCode);
        }
        return $data;
    }

    /**
     * 登录时获取openid查询数据库
     * @param $code
     * @return bool
     * @throws \Exception
     */
    public function getOpenidByCode($code){
        if(empty($code)){
            throw  new \Exception("缺少参数");
        }
        $appid=$this->appid;
        $appsecret=$this->appSecret;
        $url="https://api.weixin.qq.com/sns/jscode2session?appid=".$appid."&secret=".$appsecret."&js_code=".$code."&grant_type=authorization_code";
        /*$content = @file_get_contents ( $url );//ssl链接时需要开启相关配置
        $content = json_decode ( $content, true );*/
        $content = $this->curlGet( $url );
        if ($content['errcode']){
            return false;
        }else{
            return $content['openid'];
        }
    }

    /**
     * 获取或者设置小程序/公众号授权登陆后的信息
     * @return mixed
     */
    public function getSession_WxAuth($data=[]){
        $dotype = $this->dotype;
        if(!empty($data)){
            session('_wxauth',[
                $dotype => $data
            ]);
        }
        return session('_wxauth');
    }
    /**
     * 获取小程序授权解密需要的 session_key
     * @return mixed
     */
    public function getSession_keyFoWxApp(){

        return $this->getSession_WxAuth()['wxApp']['session_key'];
    }

    /*
     * 小程序一些授权数据的解密获取
     * 要解密的数据 $decryptArray [ encryptedData,iv ] //小程序客户端用户加密数据
     *
     * */
    public function getDecryptDataForWxApp($decryptArray){
        //进行解密操作
        $errCode =$this->doDecryptDataForWxApp([
            'session_key' => $this->getSession_keyFoWxApp(),
            'encryptedData' => $decryptArray['encryptedData'],
            'iv' => $decryptArray['iv']
        ], $data);
        $rs=[];
        if ($errCode == 0) {
            $data=json_decode($data, true);
            unset($data['watermark']);
            $rs = $data;
        }
        return $rs;
    }
    /**
     * 进行解密小程序授权数据
     * 检验数据的真实性，并且获取解密后的明文.
     * @param $baseparms  加密的用户数据 iv与用户数据一同返回的初始向量
     * @param $data string 解密后的原文
     * @return int 成功0，失败返回对应的错误码
     */
    private function doDecryptDataForWxApp( $baseparms=[], &$data )
    {
        if (strlen($baseparms['session_key']) != 24) {
            return self::ErrorCode()['IllegalAesKey'];
        }
        $aesKey=base64_decode($baseparms['session_key']);//授权请求返回的session_key

        if (strlen($baseparms['iv']) != 24) {
            return self::ErrorCode()['IllegalIv'];
        }
        $aesIV=base64_decode($baseparms['iv']);//小程序返回的初始向量

        $aesCipher=base64_decode($baseparms['encryptedData']);//小程序返回的加密数据

        if(!function_exists('openssl_decrypt')){
            throw new Exception('请开启openssl_decrypt');
        }
        $result=openssl_decrypt( $aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);//使用openssl解密
        $dataObj=json_decode( $result );
        if( $dataObj  == NULL )
        {
            return self::ErrorCode()['IllegalBuffer'];
        }
        if( $dataObj->watermark->appid != $this->appid ) //验证解密后的水印是否正确
        {
            return self::ErrorCode()['IllegalBuffer'];
        }
        $data = $result;
        return self::ErrorCode()['OK'];
    }

    /**
     * 小程序授权后解密用户信息的error code 说明.
     * <ul>

     *    <li>-41001: encodingAesKey 非法</li>
     *    <li>-41003: aes 解密失败</li>
     *    <li>-41004: 解密后得到的buffer非法</li>
     *    <li>-41005: base64加密失败</li>
     *    <li>-41016: base64解密失败</li>
     * </ul>
     */
    private static function ErrorCode(){
        return [
            'OK' =>0,
            'IllegalAesKey' =>-41001,
            'IllegalIv' =>-41002,
            'IllegalBuffer' => -41003,
            'DecodeBase64Error' =>-41004,
        ];
    }

//================小程序的授权end====================//


//================公众号的授权======================//
    /**
     * 公众号获取用户信息
     * @param string $openid 调用【网页授权获取用户信息】接口获取到用户在该公众号下的Openid
     * @return string
     */
    private function userInfoForWxWeb()
    {
        $rs=$this->getOathLogin();
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
     * @return 用户的openid
     */
    private function oathLoginForWxWeb()
    {
        if (!isset($_GET['code'])){

            $scheme = $_SERVER['HTTPS']=='on' ? 'https://' : 'http://';
            //$uri = $_SERVER['PHP_SELF'].$_SERVER['QUERY_STRING'];
            $uri='';
            if($_SERVER['REQUEST_URI']){$uri = $_SERVER['REQUEST_URI'];}
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
            exit();
        } else {
            //获取code码，进行授权登陆
            $code = $_GET['code'];

            $urlObj["appid"] = $this->appid;
            $urlObj["secret"] = $this->appSecret;
            $urlObj["code"] = $code;
            $urlObj["grant_type"] = "authorization_code";
            $bizString = $this->ToUrlParams($urlObj);
            $url = "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;

            return self::curlGet($url);
        }
    }

//================公众号的授权end====================//
    /**
     * 拼接签名字符串
     * @param array $urlObj
     * @return 返回已经拼接好的字符串
     */
    private function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v)
        {
            if($k != "sign") $buff .= $k . "=" . $v . "&";
        }
        $buff = trim($buff, "&");
        return $buff;
    }


}



