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
use think\facade\Config;

class  WxPay extends WxBase
{
    use TraitInstance;
    
    private $logFun=null;
    /**
     * 调用微信支付统一下单
     * @param string $openid 调用【网页授权获取用户信息】接口获取到用户在该公众号下的Openid
     * @param float $totalFee 收款总费用 单位元
     * @param string $outTradeNo 唯一的订单号
     * @param string $orderName 订单名称
     * @param string $notifyUrl
     * @param string $timestamp 支付时间
     * @return string
     */
    public function creatPay($data)
    {
        $config = array(
            'mch_id' => $this->mchid,
            'appid' => $this->appid,
            'apiSecret' => $this->apiSecret,
        );

        $request = request();
        $spbill_create_ip = $request->ip();
        $unified = array(
            'appid' => $config['appid'],
            'attach' => 'pay',             //附加数据用于自定义参数，回调通知时返回，如果填写中文，请注意转换为utf-8
            'body' => $data['orderName'], //商品描述
            'mch_id' => $config['mch_id'],
            'nonce_str' => self::createNonceStr(),
            'notify_url' => $data['notify_url'], //支付结果通知url 不要有问号
            'openid' => $data['openid'],
            'out_trade_no' => $data['out_trade_no'], //商城系统内部订单号
            'spbill_create_ip' => $spbill_create_ip, //终端IP
            'total_fee' => intval($data['totalFee'] * 100),  //单位 转为分
            'trade_type' => 'JSAPI', //rade_type=JSAPI，此参数必传
        );
        $unified['sign'] = self::makeSign($unified, $config['apiSecret']);
        $responseXml = self::curlPost('https://api.mch.weixin.qq.com/pay/unifiedorder', $unified);
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $unifiedOrder = simplexml_load_string($responseXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($unifiedOrder === false) {
            throw new Exception('parse xml error');
        }
        if ($unifiedOrder->return_code != 'SUCCESS') {
            throw new Exception($unifiedOrder->return_msg);
        }
        if ($unifiedOrder->result_code != 'SUCCESS') {
            throw new Exception($unifiedOrder->err_code_des);
        }
        $arr = array(
            "appId" => $config['appid'],
            "timeStamp" => time(),        //这里是字符串的时间戳
            "nonceStr" => self::createNonceStr(),
            "package" => "prepay_id=" . $unifiedOrder->prepay_id, //prepay_id预支付交易会话标识
            "signType" => 'MD5',
        );
        $arr['paySign'] = self::makeSign($arr, $config['apiSecret']);
        return $arr;
    }


    /**
     * 退款
     * @param float $totalFee 订单金额 单位元
     * @param float $refundFee 退款金额 单位元
     * @param string $refundNo 退款单号
     * @param string $wxOrderNo 微信订单号
     * @param string $orderNo 商户订单号
     * @return string
     */
    public function doRefund($totalFee, $refundFee, $refundNo, $wxOrderNo='',$orderNo='', $reason='')
    {
        $config = array(
            'mch_id' => $this->mchid,
            'appid' => $this->appid,
            'apiSecret' => $this->apiSecret,
        );
        $unified = array(
            'appid' => $config['appid'],
            'mch_id' => $config['mch_id'],
            'nonce_str' => self::createNonceStr(),
            'total_fee' => intval($totalFee * 100),       //订单金额	 单位 转为分
            'refund_fee' => intval($refundFee * 100),       //退款金额 单位 转为分
            'sign_type' => 'MD5',           //签名类型 支持HMAC-SHA256和MD5，默认为MD5
            'transaction_id'=>$wxOrderNo,               //微信订单号
            'out_trade_no'=>$orderNo,        //商户订单号
            'out_refund_no'=>$refundNo,        //商户退款单号
            'refund_desc'=> $reason,     //退款原因（选填）
        );
        $unified['sign'] = self::makeSign($unified, $config['apiSecret']);

        $options = array(
            CURLOPT_SSLCERTTYPE =>  'PEM',
            CURLOPT_SSLCERT     =>  Config::get('app.wxPaySSL')['certFile'],
            CURLOPT_SSLKEYTYPE  =>  'PEM',
            CURLOPT_SSLKEY      =>  Config::get('app.wxPaySSL')['keyFile']
        );
        //有证书的post请求
        $responseXml = self::curlPost('https://api.mch.weixin.qq.com/secapi/pay/refund', $unified, $options);
        libxml_disable_entity_loader(true);
        $unifiedOrder = simplexml_load_string($responseXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($unifiedOrder === false) {
            throw new Exception('parse xml error');
        }
        if ($unifiedOrder->return_code != 'SUCCESS') {
            throw new Exception($unifiedOrder->return_msg);
        }
        if ($unifiedOrder->result_code != 'SUCCESS') {
            throw new Exception($unifiedOrder->err_code_des);
        }
        return $unifiedOrder;
    }

    /**
     * 支付结果通知
     * @return array
     */
    public function payNotify()
    {
        $config = array(
            'mch_id' => $this->mchid,
            'appid' => $this->appid,
            'apiSecret' => $this->apiSecret,
        );
        $postStr = file_get_contents('php://input');
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($postObj === false) {
            throw new Exception('parse xml error');
        }
        if ($postObj->return_code == 'SUCCESS' && $postObj->result_code == 'SUCCESS') {
            $arr = (array)$postObj;
            unset($arr['sign']);
            if (self::makeSign($arr, $config['apiSecret']) == $postObj->sign) {
                return $arr;
            } else {
                throw new Exception('sign error');
            }
        }else{
            return false;
        }
    }


    /**
     * 企业付款
     * @param string $openid 调用【网页授权获取用户信息】接口获取到用户在该公众号下的Openid
     * @param float $totalFee 收款总费用 单位元
     * @param string $out_trade_no 唯一的订单号
     * @param string $orderName 订单名称
     * @param string $notifyUrl 支付结果通知url 不要有问号
     * @param string $timestamp 支付时间
     * @return string
     */
    public function createJsBizPackage($data)
    {
        //$openid, $totalFee, $out_trade_no,$trueName,$desc
        $data['desc']=$data['desc']?$data['desc']:'付款';
        $config = array(
            'mch_id' => $this->mchid,
            'appid' => $this->appid,
            'apiSecret' => $this->apiSecret,
        );
        $unified = array(
            'mch_appid' => $config['appid'],
            'mchid' => $config['mch_id'],
            'nonce_str' => self::createNonceStr(),
            'openid' => $data['openid'],
            'check_name'=>'NO_CHECK',
            //'check_name'=>'FORCE_CHECK',        //校验用户姓名选项。NO_CHECK：不校验真实姓名，FORCE_CHECK：强校验真实姓名
            //'re_user_name'=>$data['trueName'],                 //收款用户真实姓名（不支持给非实名用户打款）
            'partner_trade_no' => $data['out_trade_no'], //商城系统内部订单号
            'spbill_create_ip' => '127.0.0.1',
            'amount' => intval($data['totalFee'] * 100),       //单位 转为分
            'desc'=> $data['desc'],            //企业付款操作说明信息
        );
        $unified['sign'] = self::makeSign($unified, $config['apiSecret']);
        $options = array(
            CURLOPT_SSLCERTTYPE =>  'PEM',
            CURLOPT_SSLCERT     =>  Config::get('app.wxPaySSL')['certFile'],
            CURLOPT_SSLKEYTYPE  =>  'PEM',
            CURLOPT_SSLKEY      =>  Config::get('app.wxPaySSL')['keyFile']
        );
        //有证书的post请求
        $responseXml = self::curlPost('https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers',  $unified, $options);
        libxml_disable_entity_loader(true);
        $unifiedOrder = simplexml_load_string($responseXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($unifiedOrder === false) {
            throw new Exception('parse xml error');
        }
        if ($unifiedOrder->return_code != 'SUCCESS') {
            throw new Exception($unifiedOrder->return_msg);
        }
        if ($unifiedOrder->result_code != 'SUCCESS') {
            throw new Exception($unifiedOrder->err_code_des);
        }

        return true;
    }

    public function setLog($logFun){
        $this->logFun = $logFun;
        return $this;
    }

    public function getLog($url,$postData,$response){
        if($this->logFun){
            call_user_func($this->logFun, $url,$postData,$response);
        }
    }

    public static function curlPost($url = '', $postData = '', $options = array()){
        //转换微信xml数据
       $rs = parent::curlPost($url, self::arrayToXml($postData), $options);
       //插入此次请求的日志操作 
       self::getInstance()->getLog($url,$postData,$rs);
       return $rs;
    }
}



