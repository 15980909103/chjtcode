<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: pl125 <xskjs888@163.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\validate\AccountValidate;
use app\common\base\AdminBaseController;
use app\server\admin\Admin;
use app\server\marketing\Coupon;
use app\server\marketing\CouponActivity;
use app\server\marketing\Subject;
use app\server\user\BrowseRecords;
use think\Validate;


class CouponActivityController extends AdminBaseController
{
    public function List()
    {
        $data = $this->request->param();
        $where = [
            'name'          => $data['name'],
            'status'        => $data['status'],
            'activity_type' => 3,//取投票活动
        ];

        // 城市
        if (!empty($data['region_no'])) {
            if (-1 == $data['region_no']) {// 搜索当前全部城市
                $regionRes = $this->getMyCity();

                $cityIds = !empty($regionRes['data']) ? array_column($regionRes['data'], 'id') : [];

                $where['region_no'] = $cityIds;
            } else {
                $where['region_no'] = $data['region_no'];
            }
        }

        $rs = (new Subject())->getList($where)['result'];
        if (empty($rs['list'])) {
            $rs = [];
        } else {
            foreach ($rs['list'] as &$v) {
                $v['context_rule'] = htmlspecialchars_decode($v['context_rule']);
                //list($v['cover_id'], $v['cover_url']) = $this->getImgsIdAndUrl($v['banner']);
                $v['cover_url'] = !empty($v['cover_url']) ? $this->getFormatImgs($v['cover_url']) : [];
                $v['gzh_qrcode'] = !empty($v['gzh_qrcode']) ? $this->getFormatImgs($v['gzh_qrcode']) : [];
                $v['kf_qrcode'] = !empty($v['kf_qrcode']) ? $this->getFormatImgs($v['kf_qrcode']) : [];
//////                $v['bg_img'] = !empty($v['bg_img']) ? $this->getFormatImgs($v['bg_img']) : [];
////                $v['time_show_status'] = 0;
////                if (!empty($v['start_time']) && !empty($v['end_time'])) {
////                    $v['time_show_status'] = 1;//是否显示活动的开始结束时间控件
////                }
                $v['wx_h5'] = '/9house/pages/12/apply.html?active_id=' . $v['id'] . '&source=wx_h5';// . '&region_no=' . $v['region_no'];
                $v['douyin'] = '/9house/pages/12/apply.html?active_id=' . $v['id'] . '&source=douyin';// . '&region_no=' . $v['region_no'];
            }
        }
        $this->success($rs);
    }

    public function editCouponRule(){
        $data = $this->request->param();
        if(empty($data['coupon_id'])){
            $this->error('缺失参数');
        }

        $rs = (new CouponActivity())->editCouponRule([
            'coupon_id'                    => $data['coupon_id'],
            'context_rule'    => $data['context_rule'],
            'enble_contextrule'    => intval($data['enble_contextrule']),
        ])['result'];
        $this->success($rs);
    }
    public function getCouponRule(){
        $data = $this->request->param();
        $data['coupon_id'] = intval($data['coupon_id']);
        if(empty($data['coupon_id'])){
            $this->error('缺失参数');
        }
        $rs = (new CouponActivity())->getCouponRule([
            'coupon_id' => $data['coupon_id'],
        ])['result'];
        $this->success($rs);
    }


    public function edit()
    {
        $data = $this->request->param();
        $data['id'] = intval($data['id']);


        $data['name'] = trim_all($data['name']);



        if (empty($data['name'])) {
            $this->error('请填写名称');
        }

        $data['region_no'] = intval($data['region_no']);
        if (empty($data['region_no'])) {
            $this->error('缺少区域参数');
        }
        $indata = [
            'name' => $data['name'],
            'status' => intval($data["status"]),
            'page_title' => $data["page_title"],
            'page_keywords' => $data["page_keywords"],
            'page_desc' => $data["page_desc"],
            'region_no' => $data['region_no'],
            'context_rule' => $data['context_rule'],
            'activity_type' => 3,
            'cover_url' => !empty($data['cover_url']) ? implode(',', array_column($data['cover_url'], 'url')) : "",
            'gzh_qrcode' => !empty($data['cover_url']) ? implode(',', array_column($data['gzh_qrcode'], 'url')) : "",
            'kf_qrcode' => !empty($data['cover_url']) ? implode(',', array_column($data['kf_qrcode'], 'url')) : "",
        ];
        if (empty($data['start_time']) || empty($data['end_time'])) {
            $this->error('请设置时间范围');
        }
        $indata['start_time'] = strtotime($data['start_time']);
        $indata['end_time'] = strtotime($data['end_time']);

        if ($indata['start_time'] >= $indata['end_time']) {
            $this->error('请设置正确的时间范围');
        }

        if ($data['id']) {
            $rs = (new Subject())->edit($data['id'], $indata);
        } else {
            $rs = (new Subject())->add($indata);
        }
        if ($rs['code'] == 1) {
            $this->success();
        } else {
            $this->error();
        }
    }

    //删除
    public function del()
    {
        $data = $this->request->param();
        $rs = (new CouponActivity())->del(intval($data['id']));
        $this->success($rs);
    }


    public function getShopList(){
        $activity_id = $this->request->param('activity_id');
        // 城市

        $rs = (new CouponActivity())->getList($activity_id);
        if (empty($rs)) {
            $rs = [];
        } else {
            foreach ($rs as &$v) {
                $v['shop_img']            = !empty($v['shop_img']) ? $this->getFormatImgs($v['shop_img']) : [];
                $v['coupon_surplus_num']  = $v['send_coupon_num'] - (new CouponActivity())->getSurplusCouponByShopId($v['id']);
                $v['couponlist']          = (new CouponActivity())->getCouponlistByShopId($v['id']);
                $v['user_list']            = (new CouponActivity())->getWrite0ffList($v['id'])['result'];
                foreach($v['user_list'] as $k1 => $v1){
                    $v['user_ids'][]          = $v1['id'];
                }

                foreach ( $v['couponlist'] as $ks => &$vs){
                    $vs['time'] = [date('Y-m-d H:i:s',$vs['start_time']),date('Y-m-d H:i:s',$vs['end_time'])];
                }
            }
        }

        $data['list'] = $rs;
        $this->success($data);
    }

    public function editCoupon(){
        $data = $this->request->post();
        $couponlist   = $data['couponlist'];
        $user_ids     = $data['user_ids'];
        if(empty($user_ids)){
            return $this->error('至少绑定一个核销人员');
        }
        $indata = [
          'id'                  => $data['id'],
          'subject_id'          => $data['activity_id'],
          'sort'                => $data['sort'],
          'shop_img'            => !empty($data['shop_img']) ? implode(',', array_column($data['shop_img'], 'url')) : "",
          'shop_name'           => $data['shop_name'],
          'shop_type_string'    => $data['shop_type_string'],
          'shop_lable_string'   => $data['shop_lable_string'],
          'send_coupon_num'     => $data['send_coupon_num'],
        ];
        if(empty($data['send_coupon_num']) ||  !is_numeric($data['send_coupon_num']) || $data['send_coupon_num']<0 ){
            return $this->error('请输入正确的发放数量');
        }

        foreach ($couponlist as $k => $v){
            $start  = strtotime($v['time'][0]);
            $end  = strtotime($v['time'][1]);
            foreach ($couponlist as $key => $val ){
               if($k != $key && $v['coupon_describe'] == $val['coupon_describe']){
                   if(
                   ($start>=strtotime($val['time'][0]) && $start<=strtotime($val['time'][1]) )
                        ||
                   ($end>=strtotime($val['time'][0]) && $end<=strtotime($val['time'][1]) )
                   ){
                       return  $this->error('相同优惠券时间不能重叠哦！');
                   }
               }
            }
        }
        $server  = new CouponActivity();
        $this->db->startTrans();
        if(empty($indata['id'])){
            $result  = $server->addShop($indata);
            $id      = $result;
            if(!$result){
                $this->db->rollback();
                return $this->error();
            }
            $count = 0;
            foreach ($couponlist as $k => $v){
                if(empty($v['id'])){
                     $v['start_time'] = strtotime($v['time'][0]);
                     $v['end_time']   = strtotime($v['time'][1]);
                     $result  = $server->addCouponByShop($result,$v);
                     if($result['code'] == 0){
                         $this->db->rollback();
                         return   $this->error($result['msg']);
                     }

                }
                $count +=$v['coupon_send_unm'];
            }
            if($count != $data['send_coupon_num']){
                $this->db->rollback();
                return   $this->error('店铺发放的优惠数量和优惠券数量要相等哦!');
            }
            $result = $server->bingShopUser($user_ids,$id);
            if($result['code'] ==0){
                $this->db->rollback();
                return   $this->error($result['msg']);
            }
            $this->db->commit();
            $this->success();
        }else{
            $indata['subject_id'] =  $data['subject_id'];
            $result  = $server->editShop($indata);
            if(!$result){
                $this->db->rollback();
                return $this->error();
            }
            $count = 0;
            foreach ($couponlist as $k => $v){
                if(empty($v['id'])){
                    if(empty($v['id'])){
                        $v['start_time'] = strtotime($v['time'][0]);
                        $v['end_time']   = strtotime($v['time'][1]);
                        $result  = $server->addCouponByShop($indata['id'],$v);

                        if($result['code'] == 0){
                            $this->db->rollback();
                            return   $this->error($result['msg']);
                        }

                    }
                }else{
                    $v['start_time'] = strtotime($v['time'][0]);
                    $v['end_time']   = strtotime($v['time'][1]);
                    $result  = $server->editCouponByShop($indata['id'],$v);

                    if($result['code'] == 0){
                        $this->db->rollback();
                        return   $this->error($result['msg']);
                    }

                }

                $count +=$v['coupon_send_unm'];

            }
            if($count != $data['send_coupon_num']){

                $this->db->rollback();
                return   $this->error('店铺发放的优惠数量和优惠券数量要相等哦!');
            }

            $result = $server->bingShopUser($user_ids,$indata['id']);
            if($result['code'] ==0){
                $this->db->rollback();
                return   $this->error($result['msg']);
            }
            $this->db->commit();
            $this->success();
        }




    }

    public function  delCoupon(){
        $coupon_id = $this->request->post('id');
        if(empty($coupon_id)){
            return $this->error();
        }

        $result = (new CouponActivity())->delCoupon($coupon_id);

        if($result){
            $this->success();
        }

        return  $this->error();
    }



}