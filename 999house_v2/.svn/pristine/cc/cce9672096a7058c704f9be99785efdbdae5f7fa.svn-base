<?php
namespace app\server\marketing;

use app\common\base\ServerBase;
use think\Db;
use think\Exception;

class CouponActivity extends ServerBase
{

    public function addShop($data){
        $indata = [
            'id'                  => $data['id'],
            'subject_id'          => $data['subject_id'],
            'sort'                => $data['sort'],
            'shop_img'            => $data['shop_img'],
            'shop_name'           => $data['shop_name'],
            'shop_type_string'    => $data['shop_type_string'],
            'shop_lable_string'   => $data['shop_lable_string'],
            'send_coupon_num'     => $data['send_coupon_num'],
            'create_coupon_num'   => $data['create_coupon_num'],
            'start_time'          => $data['start_time'],
            'end_time'            => $data['end_time'],
            'create_time'         => time(),
            'update_time'         => time(),
        ];

        $res  = $this->db->name('activity_coupon_shop')->insert($indata,true);

        return $res ===false ? false:$res;

    }

    public function editShop($data){
        $indata = [
            'id'                  => $data['id'],
            'subject_id'          => $data['subject_id'],
            'sort'                => $data['sort'],
            'shop_img'            => $data['shop_img'],
            'shop_name'           => $data['shop_name'],
            'shop_type_string'    => $data['shop_type_string'],
            'shop_lable_string'   => $data['shop_lable_string'],
            'send_coupon_num'     => $data['send_coupon_num'],
            'create_coupon_num'   => $data['create_coupon_num'],
            'start_time'          => $data['start_time'],
            'end_time'            => $data['end_time'],
            'create_time'         => time(),
            'update_time'         => time(),
        ];

        $res  = $this->db->name('activity_coupon_shop')->where('id','=',$data['id'])->update($indata);

        return $res ===false ? false:true;
    }

    public function addCouponByShop($shop_id,$data){
        if(empty($shop_id)){
            return $this->responseFail('店铺id不能为空哦！');
        }

        $indata = [
            'shop_id' => $shop_id,
            'coupon_describe'       => $data['coupon_describe'],
            'coupon_send_unm'       => $data['coupon_send_unm'],
            'coupon_surplus_num'    => $data['coupon_send_unm'],
            'start_time'            => $data['start_time'],
            'end_time'              => $data['end_time'],
            'context_rule' => '',
            'enble_contextrule' => 0,
            'create_time'         => time(),
            'update_time'         => time(),
        ];

        $result = $this->db->name('activity_coupon')->insert($indata);

        if($result ===false){
            return $this->responseFail('店铺id不能为空哦！');
        }

        return  $this->responseOk();
    }

    public function editCouponByShop($shop_id,$data){
        unset($data['context_rule']);//防止误操作独立优惠券设置
        unset($data['enble_contextrule']);//防止误操作独立优惠券设置

        $indata = [
            'id'                    => $data['id'],
            'shop_id'               => $shop_id,
            'coupon_describe'       => $data['coupon_describe'],
            'coupon_send_unm'       => $data['coupon_send_unm'],
            'start_time'            => $data['start_time'],
            'end_time'              => $data['end_time'],
            'coupon_surplus_num'    => $data['coupon_send_unm'],
            'create_time'         => time(),
            'update_time'         => time(),
        ];

        $count  = $this->db->name('write_off_information')
            ->where('store_id','=',$shop_id)
            ->where('coupon_id','=',$data['id'])
            ->count();
        $data['coupon_surplus_num'] = $data['coupon_send_unm'] - $count;
        if($data['coupon_surplus_num'] <0){
            return $this->responseFail('优惠券总数量以不能小于已送的优惠券');
        }
        $result = $this->db->name('activity_coupon')->where('id','=',$indata['id'])->update($indata);

        if($result ===false){
            return $this->responseFail();
        }

        return  $this->responseOk();
    }

    public function editCouponRule($data){
        $indata = [
            'context_rule'    => $data['context_rule'],
            'enble_contextrule'    => $data['enble_contextrule'],
            'update_time'         => time(),
        ];

        $result = $this->db->name('activity_coupon')->where('id','=',$data['coupon_id'])->update($indata);

        if($result ===false){
            return $this->responseFail();
        }
        return  $this->responseOk();
    }
    public function getCouponRule($data){
        $result = $this->db->name('activity_coupon')
            ->field('id,context_rule,enble_contextrule')
            ->where('id','=',$data['coupon_id'])->find();
        if(!empty($result['context_rule'])){
            $result['context_rule'] = html_entity_decode($result['context_rule']);
        }
        return  $this->responseOk($result);
    }


    public  function getList($activity_id){
        if(empty($activity_id)){
            return [];
        }
        $list  = $this->db->name('activity_coupon_shop')->where('subject_id','=',$activity_id)->select();

        if(empty($list)){
           return  []  ;
        }
        return  $list->toArray();
    }

    public function getSurplusCouponByShopId($shop_id){
        if(empty($shop_id)){
            return 0;
        }

        $count  = $this->db->name('write_off_information')->where('store_id','=',$shop_id)->count();

        return  $count;
    }

    public function getCouponlistByShopId($shop_id){
        if(empty($shop_id)){
            return [];
        }

        $list  = $this->db->name('activity_coupon')->where('shop_id','=',$shop_id)->select();

        if(empty($list)){
            return [];
        }

        return  $list->toArray();
    }

    public function delCoupon($coupon_id){
        if(empty($coupon_id)){
            return false;
        }

        $result = $this->db->name('activity_coupon')->where('id','=',$coupon_id)->delete();

        return  $result===false ? false:true;
    }


//    public function delShop($id){
//        if(empty($id)){
//            return false;
//        }
//
//        $this->db->startTrans();
//
//        $res1 = $this->db->name('activity_coupon_shop')->where('id','=',$id)->delete();
//
//        $res2 = $this->db->name()
//    }

      public function getListByActivityId($activity_id){
        if(empty($activity_id)){
            return [];
        }

        $list = $this->db->name('subject')->alias('s')
                ->leftJoin('activity_coupon_shop acs','acs.subject_id=s.id')
                ->leftJoin('activity_coupon ac','ac.shop_id = acs.id')
                ->where('s.status','=',1)
                ->where('s.id','=',$activity_id)
                ->field('acs.id as shop_id,ac.id as cop_id,acs.shop_name,acs.shop_img,
                              acs.shop_type_string,acs.shop_lable_string,ac.coupon_describe,
                              ac.coupon_send_unm,ac.coupon_surplus_num,ac.start_time,ac.end_time')
                ->order('ac.start_time asc ac.end_time asc')
                ->select();
        if(empty($list)){
            return  [];
        }

        return  $list->toArray();

      }

      public function isReceiveCoupon($user_id,$act_id){
        if(empty($user_id) || empty($act_id)){
            return $this->responseFail();
        }

        $info = $this->db->name('write_off_information')->where('subject_id','=',$act_id)
            ->where('user_id','=',$user_id)
            ->find();

        return  empty($info) ? $this->responseOk(true):$this->responseOk(false);

      }


      public function getCouponInfo($id){
        if(empty($id)){
            return $this->responseFail('参数错误');
        }

        $info = $this->db->name('activity_coupon')->alias('ac')
            ->leftJoin('activity_coupon_shop acs','acs.id=ac.shop_id')
            ->field('ac.*,acs.shop_name')
            ->where('ac.id','=',$id)->find();

        return  $this->responseOk($info);
      }

      public function  getQualificationsRecord($user_id,$act_id,$start_time,$end_tiem){
        if(empty($user_id) ||empty($act_id)){
            return $this->responseFail('参数错误');
        }
        $where  = [];
        if(!empty($start_time)){
            $where[] = ['woi.write_off_time','>=',$start_time];
        }

          if(!empty($end_tiem)){
              $where[] = ['woi.write_off_time','<=',$end_tiem];
          }
        $list = $this->db->name('write_off_information')->alias('woi')
            ->leftJoin('activity_coupon_shop acs','woi.store_id = acs.id')
            ->leftJoin('activity_coupon ac','ac.id=woi.coupon_id')
            ->where('woi.subject_id','=',$act_id)
            ->where('woi.user_id','=',$user_id)
            ->field('acs.shop_img,acs.id,acs.shop_type_string,acs.shop_lable_string,woi.write_off_time,ac.coupon_describe,acs.shop_name')
            ->order('woi.write_off_time asc');

            if(!empty($where)){
                $list = $list->where($where);
            }
          $list = $list ->select();
//          echo $this->db->getLastSql();

        if(empty($list)){
            $list =[];
        }else{
            $list=$list->toArray();
        }

        return $this->responseOk($list);

      }

      public function bingShopUser($user_ids,$shop_id){
        if(empty($user_ids) || empty($shop_id)){
            return $this->responseFail('参数错误');
        }
        //将旧的人员绑定去除
        $old_result = $this->db->name('user')->where('store_id','=',$shop_id)->update(['store_id'=>0]);
        $result     = $this->db->name('user')->where('id','in',$user_ids)->update(['store_id'=>$shop_id]);
        return  $result !== false ? $this->responseOk():$this->responseFail();

      }

    /**
     * 获取核销人员
     */
      public function getWrite0ffList($shop_id){
          if( empty($shop_id)){
              return $this->responseFail('参数错误');
          }

          $list = $this->db->name('user')->where('store_id','=',$shop_id)->field('id,nickname')->select();

          return  empty($list) ? $this->responseOk([]):$this->responseOk($list->toArray());
      }


}