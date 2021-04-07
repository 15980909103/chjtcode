<?php


namespace app\admin\controller;
use app\common\base\AdminBaseController;
use app\server\marketing\Coupon;


class CouponController extends AdminBaseController
{

    public function getList(){
        $data = $this->request->param();
        $where = [
            'status' => $data['status'],
            'start_time'=> strtotime($data['startdate']),
            'end_time'=> strtotime($data['enddate'].' +1 day')
        ];

        $rs = (new Coupon())->getList($where)['result'];
        if(empty($rs['list'])){
            $rs = [];
        }else{
            foreach ($rs['list'] as &$item){
                if(!empty($item['forid'])){
                    $item['forname'] = $this->db->name('estates_new')->where([
                        'id' => $item['forid']
                    ])->value('name');
                }
            }
            unset($item);
        }
        $this->success($rs);
    }

    //删除
    public function del()
    {
        $data = $this->request->param();
        $rs = (new Coupon())->del(intval($data['id']));
        $this->success($rs);
    }

    public function edit(){
        $data = $this->request->param();
        $data['id'] = intval($data['id']);
        $data['receive_num'] = intval($data['receive_num']);
        $data['total_num'] = intval($data['total_num']);
        $data['status'] = intval($data['status']);
        
        $data["start_time"] = strtotime($data["start_time"]);
        $data["end_time"] = strtotime($data["end_time"]);
        if(empty($data["start_time"])||empty($data["end_time"])){
            $this->error('请设置有效的时间范围');
        }
        if($data["start_time"]>=$data["end_time"]){
            $this->error('开始时间超过结束时间');
        }

        $data['region_no'] = intval($data['region_no']);
        if(empty($data['region_no'])){
            $this->error('缺少区域参数');
        }

        $indata = [
            'forid' => $data["forid"],
            'discount'=> $data['discount'],
            'start_time'=> $data["start_time"],
            'end_time'=> $data["end_time"],
            "receive_num"=> $data["receive_num"],
            'total_num' => $data["total_num"],
            'status'=> $data['status'],
            'region_no'=> $data['region_no'],
        ];

        if($data['id']){
            $rs = (new Coupon())->edit($data['id'],$indata);
        }else{
            $rs = (new Coupon())->add($indata);
        }
        if($rs['code']==1){
            $this->success();
        }else{
            $this->error();
        }
    }

}