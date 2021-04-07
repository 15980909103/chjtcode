<?php

namespace app\admin\controller;


use app\common\base\AdminBaseController;
use app\common\lib\delayQueue\DelayQueue;
use app\common\manage\TaskManage;
use app\server\notify\WxNotify;


class NotifyController extends AdminBaseController
{

    //微信通知
    public function DistributionWxMsg(){
        $data = $this->request->param();
        $this->db->name('log')->insert([
            'title' => '消息发送',
            'content' => json_encode($data,JSON_UNESCAPED_UNICODE),
            'request' => '前置参数获取',
            'ctime' => time(),
        ]);

        if($data['auth'] = 'Nldo4g59sEkW2v7DCmIOruPc6FAMn'){
            if(empty($data['data']['order_no'])){
                $this->error('抱歉，缺失参数');
            }
            if(empty($data['data']['status_type'])){
                $this->error('抱歉，缺失参数');
            }
            //expire 失效通知 //expire 审批通知 //commission 佣金通知 //log 跟进信息通知
            if(empty($data['data']['do_type'])||!in_array($data['data']['do_type'], ['expire', 'examine', 'commission', 'log'])){
                $this->error('抱歉，缺失参数');
            }

            $wxNotify = (new WxNotify());
            $res = $wxNotify->transforSendTplType([
                'order_no' => $data['data']['order_no'],
                'status_type' => $data['data']['status_type'],
            ], $data['data']['do_type'])['result'];

            $this->db->name('log')->insert([
                'title' => '消息发送',
                'content' => json_encode($res,JSON_UNESCAPED_UNICODE),
                'request' => '返回结果',
                'ctime' => time(),
            ]);
            $this->success();

//            $data = [];
//            $data['data']['order_no'] = 'R202102055197565214230';
//            $data['data']['status_type'] = 1;
//
//
//            $wxNotify = (new WxNotify());
//            $res = $wxNotify->transforSendTplType([
//                'order_no' => $data['data']['order_no'],
//                'status_type' => $data['data']['status_type'],
//            ], 'expire')['result'];

//            $wxNotify = (new WxNotify());
//            $res = $wxNotify->transforSendTplType([
//                'order_no' => $data['data']['order_no'],
//                'status_type' => $data['data']['status_type'],
//            ], 'examine')['result'];
//
//
//            $wxNotify = (new WxNotify());
//            $res = $wxNotify->transforSendTplType([
//                'order_no' => $data['data']['order_no'],
//                'status_type' => $data['data']['status_type'],
//            ], 'commission')['result'];
//            print_r($res);
//
//
//            $wxNotify = (new WxNotify());
//            $res = $wxNotify->transforSendTplType([
//                'order_no' => $data['data']['order_no'],
//                'status_type' => $data['data']['status_type'],
//            ], 'log')['result'];
//            print_r($res);

        }
    }



    //保护期时间到了进行验证
    public function CheckProtection(){
        $data = $this->request->param();
        $data['delay'] = intval($data['delay']);
        if($data['auth'] != 'Nldo4g59sEkW2v7DCmIOruPc6FAMn'){
            $this->error('校验错误');
        }
        if(empty($data['data'])||empty($data['data']['order_no'])||empty($data['data']['status_type'])){
            $this->error('缺失报备订单参数');
        }

        $this->db->name('log')->insert([
            'title' => '过期失效通知',
            'content' => json_encode($data,JSON_UNESCAPED_UNICODE),
            'request' => '前置参数校验成功',
            'ctime' => time(),
        ]);

        DelayQueue::getInstance()->addTask('checkProtection',$data['delay'],\app\task\CheckProtection::class,$data['data']);
        $this->success();

        //==============test===============//
        //添加延时队列
        /*for ($i=0;$i<26;$i++){
            $data['data']= $i;
            echo $data['data'].'-------------';
            DelayQueue::getInstance()->addTask('checkProtection',$data['delay'],\app\task\CheckProtection::class,$data['data']);
        }*/
        /* echo PHP_EOL;
         for ($i=0;$i<100;$i++){
             $data['data'] = $i;
             echo $data['data'].'-------------';
             DelayQueue::getInstance()->addTask('checkProtection2',$data['delay'],\app\task\CheckProtection::class,$data['data']);
         }
         echo PHP_EOL;
         for ($i=0;$i<100;$i++){
             $data['data'] = $i;
             echo $data['data'].'-------------';
             DelayQueue::getInstance()->addTask('checkProtection3',$data['delay'],\app\task\CheckProtection::class,$data['data']);
         }
         echo PHP_EOL;*/
    }

}
