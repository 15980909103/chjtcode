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
use think\Exception;
use think\facade\Config;

abstract class  WxBase
{
    protected $appid;
    protected $appSecret;
    protected $mchid;
    protected $cityCode;
    protected $uri;  //网页授权回调地址
    public $data = null;
    protected $dotype = '';

    protected $subscribeJson;//订阅号配置
    protected $is_open; // 1为未开放 2为开放 （是否是开放平台
    protected $wxType;  //1服务号 2订阅号

    public function __construct($config = []) //$dotype = 'wxH5'
    {
        $this->dotype = 'wxH5';
        $this->appid     = $config['h5']['appid'] ?? ''; //微信支付申请对应的公众号的APPID
        $this->appSecret = $config['h5']['secret'] ?? ''; //微信支付申请对应的公众号的APP Key
        $this->uri       = $config['h5']['url']; //网页授权回调地址
//        $this->appid = Config::get('app')['wxH5config']['wxAppId'] ?? ''; //微信支付申请对应的公众号的APPID
//        $this->appSecret = Config::get('app')['wxH5config']['wxAppSecret'] ?? ''; //微信支付申请对应的公众号的APP Key
//        $this->uri = Config::get('app')['wxH5config']['uri']; //网页授权回调地址
        $this->mchid = $config['mchid'] ?? ''; //https://pay.weixin.qq.com 产品中心-开发配置-商户号
        $this->apiSecret = $config['apiSecret'] ?? ''; //https://pay.weixin.qq.com 帐户设置-安全设置-API安全-API密钥-设置API密钥
        $this->cityCode = $config['h5']['city_code'] ?? 0;


        //新增--订阅号-一些类型
        $this->is_open = $config['h5']['is_open']; //网页授权回调地址
        $this->wxType = $config['h5']['type']; //网页授权回调地址
        $this->subscribeJson = empty($config['h5']['subscribe']) ? [] : $config['h5']['subscribe']; //订阅号appid等


        if (empty($this->appid) || empty($this->appSecret)) {
            throw new Exception('请进行微信配置');
        }
    }


    protected static function formatQueryParaMap($paraMap, $urlEncode = false)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v) {
            if (null != $v && "null" != $v) {
                if ($urlEncode) {
                    $v = urlencode($v);
                }
                $buff .= $k . "=" . $v . "&";
            }
        }
        $reqPar = '';
        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }

    /**
     * 获取签名
     */
    public static function makeSign($params, $key)
    {
        ksort($params, SORT_STRING);
        $unSignParaString = self::formatQueryParaMap($params, false);
        $signStr = strtoupper(md5($unSignParaString . "&key=" . $key));
        return $signStr;
    }

    /*
     * 转换请求时需要的xml格式
     * */
    public static function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
        }
        $xml .= "</xml>";
        return $xml;
    }

    /*
     *随机字符串
     * */
    public static function createNonceStr($length = 16)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    public static function curlGet($url = '', $options = array())
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }

//        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); //强制协议为1.0
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect: ')); //头部要送出'Expect: '
//        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); //强制使用IPV4协议解析域名

        //https请求 不验证证书和host
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);

        return json_decode($data, true);
    }

    //需要证书验证时传入options
    // $options = array(
    //     CURLOPT_SSLCERTTYPE =>  'PEM',
    //     CURLOPT_SSLCERT     =>  Config::get('app.wxPaySSL')['certFile'],
    //     CURLOPT_SSLKEYTYPE  =>  'PEM',
    //     CURLOPT_SSLKEY      =>  Config::get('app.wxPaySSL')['keyFile']
    // );
    public static function curlPost($url = '', $postData = '', $options = array())
    {
        if (is_array($postData)) {
            $postData = http_build_query($postData);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //设置cURL允许执行的最长秒数
        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }

//        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); //强制协议为1.0
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect: ')); //头部要送出'Expect: '
//        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); //强制使用IPV4协议解析域名

        //https请求 不验证证书和host
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);
        $jsonData = json_decode($data, true);
        if (is_null($jsonData)) {
            return $data;
        } else {
            return $jsonData;
        }
    }

    function post_file($url,$post_data){
        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt ( $curl, CURLOPT_URL, $url );
        curl_setopt ( $curl, CURLOPT_POST, 1 );
        curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $curl, CURLOPT_CONNECTTIMEOUT, 5 );
        curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, false );
        curl_setopt ( $curl, CURLOPT_POSTFIELDS, $post_data );
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        $data = json_decode($data,true);
        curl_close($curl);
        return $data;
        //显示获得的数据
    }


    /**
     * 拼接签名字符串
     * @param array $urlObj
     * @return string 返回已经拼接好的字符串
     */
    protected function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v) {
            if ($k != "sign") $buff .= $k . "=" . $v . "&";
        }
        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 获取或者设置小程序/公众号授权登陆后的信息
     * @return mixed
     */
    protected function getSession_WxAuth($data = [])
    {
        $dotype = $this->dotype;
        if (!empty($data)) {
            session('_wxauth', [
                $dotype => $data
            ]);
        }
        return session('_wxauth');
    }

}



