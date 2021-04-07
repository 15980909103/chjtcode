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

    public function cronTabNotify(){
        //一天第一次
        go(function (){
            $has = $this->db->name('xcx_building_reported')->field('order_no,day_notify_num,status_type')->where([
                ['status_type','in',[1,2,3]],
                ['examine_type', '=', 1],
                ['update_time', '>', time()+3600],
                ['last_notify_time', '<', strtotime(date('Y-m-d 10:30:00'))],
                //['protect_day', '>', 3]
            ])->limit(0,100)->select();
            if(!empty($has['order_no'])){
                foreach ($has as $item){
                    go(function () use($item){
                        $wxNotify = (new WxNotify());
                        $wxNotify->transforSendTplType([
                            'order_no' => $item['order_no'],
                            'status_type' => $item['status_type'],
                        ], 'examine');
                    });
                }
                $this->db->name('xcx_building_reported')->where([
                    ['order_no','in',$has['order_no']],
                ])->update([
                    'last_notify_time' => strtotime(date('Y-m-d 10:30:00'))+mt_rand(0,360),
                    'day_notify_num' => 1
                ]);
            }
        });

        //一天第二次
        if(time()<strtotime(date('Y-m-d 15:30:00'))){
            $this->success();
        }
        go(function (){
            $has2 = $this->db->name('xcx_building_reported')->field('order_no,day_notify_num,status_type')->where([
                ['status_type','in',[1,2,3]],
                ['examine_type', '=', 1],
                ['update_time', '>', time()+3600],
                ['last_notify_time', '<', strtotime(date('Y-m-d 15:30:00'))], //第二次执行在下午
                ['protect_day', '<', 3] //为需要两次的

            ])->limit(0,100)->select();
            if(!empty($has2['order_no'])){
                foreach ($has2 as $item){
                    go(function () use($item){
                        $wxNotify = (new WxNotify());
                        $wxNotify->transforSendTplType([
                            'order_no' => $item['order_no'],
                            'status_type' => $item['status_type'],
                        ], 'examine');
                    });
                }
                $this->db->name('xcx_building_reported')->where([
                    ['order_no','in',$has2['order_no']],
                ])->update([
                    'last_notify_time' => strtotime(date('Y-m-d 15:30:00'))+mt_rand(0,360),
                    'day_notify_num' => 2
                ]);
            }
        });

        $this->success();
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

    public function aa(){
        print_r((new WxNotify())->getAccessToken());
    }

}
