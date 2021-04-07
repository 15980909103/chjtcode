<?php
declare (strict_types = 1);

namespace app\task;


use app\common\base\DelayQueueTaskBase;
use app\common\base\DelayQueueToLogException;
use app\server\notify\WxNotify;

//失败错误直接存储日志，不在进行重试

class CheckProtection extends DelayQueueTaskBase
{
    /**
     * @param $data
     * @param $task
     * @return bool
     */
    public function run($data=null, $task=null){
        $this->db->name('log')->insert([
            'title' => '延时队列操作',
            'content' => json_encode([
                '$data' => $data,
                '$task' => $task,
            ],JSON_UNESCAPED_UNICODE),
            'request' => '获取参数',
            'ctime' => time(),
        ]);

        if(empty($data['order_no'] )){
            throw new DelayQueueToLogException('缺失报备单号');//失败错误直接存储日志，不在进行重试
        }
        if(empty($data['status_type'] )){
            throw new DelayQueueToLogException('缺失报备环节参数');//失败错误直接存储日志，不在进行重试
        }

        $info = $this->db->name('xcx_building_reported')->field('create_time, status_type ,examine_type')->where([
            ['order_no', '=', $data['order_no']],
        ])->find();
        if($info['status_type']!=$data['status_type']){
            throw new DelayQueueToLogException('该流程环节已变更');//失败错误直接存储日志，不在进行重试
        }
        if($info['examine_type']!=1){
            throw new DelayQueueToLogException('该流程环节的状态不可更改');//失败错误直接存储日志，不在进行重试
        }
        if(!in_array($data['status_type'], [1,2,3])){
            throw new DelayQueueToLogException('该流程环节的状态不可变更为失效状态');//失败错误直接存储日志，不在进行重试
        }

        $task['delay'] = empty($task['delay'])?0:intval($task['delay']);
        $endtime = $info['create_time'] + $task['delay'];
        $rs = $this->db->name('xcx_building_reported')->where([
            [ 'order_no', '=', $data['order_no'] ],
            [ 'status_type', 'in', [1,2,3] ],
            [ 'examine_type', '=', 1]
        ])->update([
            'examine_type'=> -2, //变更为失效状态
            'update_time' => $endtime //失效的时间
        ]);

        if($rs){
            go(function () use($data){
                try{
                    (new WxNotify())->transforSendTplType([
                        'order_no' => $data['order_no'],
                        'status_type' => $data['status_type'],
                    ], 'expire');
                }catch (\Throwable $e){

                }
            });

            /*(new WxNotify())->transforSendTplType([
                'order_no' => $data['order_no'],
                'status_type' => $data['status_type'],
            ], 'expire');*/
        }

        return $rs;
    }


}
