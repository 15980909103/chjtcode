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
use think\Db;
use think\Validate;

class  WxOrder extends WxBase
{
    use TraitInstance;

    /**
     * 订单查询
     * @param string $outTradeNo
     * @return mixed
     */
    public function orderQuery($outTradeNo='')
    {
        $config = array(
            'mchid' => $this->mchid,
            'appid' => $this->appid,
            'apiSecret' => $this->apiSecret,
        );
        //$orderName = iconv('GBK','UTF-8',$orderName);
        $unified = array(
            'appid' => $config['appid'],
            'mch_id' => $config['mchid'],
            'out_trade_no' => $outTradeNo,
            'nonce_str' => self::createNonceStr(),
        );
        $unified['sign'] = self::makeSign($unified, $config['apiSecret']);
        $responseXml = self::curlPost('https://api.mch.weixin.qq.com/pay/orderquery', self::arrayToXml($unified));
        $queryResult = simplexml_load_string($responseXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($queryResult === false) {
            die('parse xml error');
        }
        if ($queryResult->return_code != 'SUCCESS') {
            die($queryResult->return_msg);
        }
        $trade_state = $queryResult->trade_state;
        $data['code'] = $trade_state=='SUCCESS' ? 0 : 1;
        $data['data'] = $trade_state;
        $data['msg'] = $this->getTradeSTate($trade_state);
        $data['time'] = date('Y-m-d H:i:s');
        return $data;
    }
    public function getTradeSTate($str)
    {
        switch ($str){
            case 'SUCCESS';
                return '支付成功';
            case 'REFUND';
                return '转入退款';
            case 'NOTPAY';
                return '未支付';
            case 'CLOSED';
                return '已关闭';
            case 'REVOKED';
                return '已撤销（刷卡支付）';
            case 'USERPAYING';
                return '用户支付中';
            case 'PAYERROR';
                return '支付失败';
        }
    }

    /**
     * 退款查询
     * @param string $refundNo 商户退款单号
     * @param string $wxOrderNo 微信订单号
     * @param string $orderNo 商户订单号
     * @param string $refundId 微信退款单号
     * @return string
     */
    public function refundQuery($refundNo='', $wxOrderNo='',$orderNo='',$refundId='')
    {
        $config = array(
            'mchid' => $this->mchid,
            'appid' => $this->appid,
            'apiSecret' => $this->apiSecret,
        );
        $unified = array(
            'appid' => $config['appid'],
            'mch_id' => $config['mchid'],
            'nonce_str' => self::createNonceStr(),
            'sign_type' => 'MD5',           //签名类型 支持HMAC-SHA256和MD5，默认为MD5
            'transaction_id'=> $wxOrderNo,               //微信订单号
            'out_trade_no' => $orderNo,        //商户订单号
            'out_refund_no' => $refundNo,        //商户退款单号
            'refund_id' => $refundId,     //微信退款单号
        );
        $unified['sign'] = self::makeSign($unified, $config['apiSecret']);
        $responseXml = $this->curlPost('https://api.mch.weixin.qq.com/pay/refundquery', self::arrayToXml($unified));
        //file_put_contents('2.txt',$responseXml);
        $unifiedOrder = simplexml_load_string($responseXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($unifiedOrder === false) {
            die('parse xml error');
        }
        if ($unifiedOrder->return_code != 'SUCCESS') {
            die($unifiedOrder->return_msg);
        }
        if ($unifiedOrder->result_code != 'SUCCESS') {
            die($unifiedOrder->err_code);
        }
        return true;
    }


}



