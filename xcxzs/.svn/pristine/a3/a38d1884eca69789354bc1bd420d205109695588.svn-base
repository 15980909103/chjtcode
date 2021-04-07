<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of main
 *
 * @author Goods0
 */
include 'Common.php';
class ListAjax extends Common{
   public function integral(){
       $my['integral'] = 0;
       $last_id = 0;
       if($this->input('?post.last_id'))$last_id = $this->input('post.last_id');
       $next_id = 0;
       if($this->input('?post.next_id'))$next_id = $this->input('post.next_id');
       $flag = "next";
       if($this->input('?post.flag'))$flag = $this->input('post.flag');
       $user_id =  Session::get('user_id');
       $query = $this->db->Name('integral_detail i')->leftJoin('user','u','i.help_id = u.id')->where_equalTo('user_id',$user_id);
       if($flag == "next"){
           if(!empty($next_id)){
               $query->where_greatThan("i.id",$next_id);
           }
       }else{
           if(!empty($last_id)){
               $query->where_lessThan("i.id",$last_id);
           }
       }
       $list = $query->orderBy('i.id','desc')->page(1, 15)->select('i.id,i.type,i.integral_change,i.describe,i.create_time,u.nickname,u.headimgurl')->execute();
       foreach($list as &$return){
           $return['id'] = Encryption::authcode($return['id'],false);
           $return['create_time'] = date('m-d H:i',$return['create_time']);
       }
       if($this->input('post.firstAjax')){
           $my = $this->db->Name('user')->where_equalTo('id',$user_id)->select('integral,headimgurl')->firstRow();
       }
       echo json_encode(['success'=>true,'list'=>$list,'my'=>$my]);
   }

    public function goods(){
        $exchange_time = $this->db->Name('exchange_time')->orderBy('star')->select('star,end')->execute();
        $i = 0;
        $isCountTime = true;
        $countTime = [];
        foreach($exchange_time as &$return){
            if($return['end'] < time()){
                unset($exchange_time[$i]);
            }else{
                if($isCountTime){
                    $countTime['star'] = $return['star'];
                    $countTime['end'] = $return['end'];
                }
                $return['star'] = date('m-d H:i',$return['star']);
                $return['end'] = date('m-d H:i',$return['end']);
                $isCountTime = false;
            }
            $i++;
        }
        sort($exchange_time);
        $user_id =  Session::get('user_id');
        $integral = $this->db->Name('user')->where_equalTo('id',$user_id)->select('integral')->firstColumn();
        $goods = $this->db->Name('goods')->select('id,title,img,show_img,exchange_integral,inventory')->execute();
        foreach($goods as &$return){
            $return['id'] = Encryption::authcode($return['id'],false);
        }
        $userInfo= $this->db->Name('user')->select('name,phone')->where_equalTo('id',$user_id)->firstRow();
        if(!empty($userInfo)){
            $userInfo['phone']=empty($userInfo['phone'])?'':$userInfo['phone'];
            $userInfo['name']=empty($userInfo['name'])?'':$userInfo['name'];
        }
        echo json_encode(['success'=>true,'exchange_time'=>$exchange_time,'countTime'=>$countTime,'goods'=>$goods,'integral'=>$integral,'info'=>$userInfo]);
    }

    //获取已经兑换的奖品
    public function alreadygoods(){
        if(Session::get('user_id')){
            $orders = $this->db->Name('order')->select()->where_equalTo('user_id',Session::get('user_id'))->execute();
            $goodsIds=[];
            if(!empty($orders)){
                foreach($orders as $val){
                    $goodsIds[]=$val['goods_id'];
                }
            }
            $goodsIds=array_unique($goodsIds);
			if(!empty($goodsIds)){
            	$goods = $this->db->Name('goods')->select('title,img,exchange_integral,inventory')->where_in('id',$goodsIds)->execute();
			}else{
				$goods=[];
			}
            $userInfo= $this->db->Name('user')->select('name,phone')->where_equalTo('id',Session::get('user_id'))->firstRow();
            if(!empty($userInfo)){
                $userInfo['phone']=empty($userInfo['phone'])?'':$userInfo['phone'];
                $userInfo['name']=empty($userInfo['name'])?'':$userInfo['name'];
            }
            if(empty($orders)){
                echo json_encode(['success'=>true,'goods'=>[],'info'=>$userInfo]);
            }else{
                echo json_encode(['success'=>true,'goods'=>$goods,'info'=>$userInfo]);
            }
        }else{
            echo json_encode(['success'=>false,'message'=>'用户数据有误！']);
        }
    }

    public function goodsExchange(){
        $goods_id = $this->input('post.id');
//        $phone = $this->input('post.phone');
//        $name = $this->input('post.name');
//        $address = $this->input('post.address');
        $user_id =  Session::get('user_id');
        $pdo = new DataBase();
        try {
            $pdo->beginTransaction(); // 开启一个事务
            $exchange_time = $this->db->Name('exchange_time')->where_lessThan('star',time())->where_greatThan('end',time())->select('count(*)')->firstColumn();
            if($exchange_time == 0){
                $pdo->rollBack();
                echo json_encode(['success'=>false,'message'=>'未到兑换时间']);
                exit();
            }
            //检测该商品是否已兑换过
            $isExchange=$this->db->Name('order')->where_equalTo('user_id',$user_id)->where_equalTo('goods_id',$goods_id)->select()->firstRow();
            if(!empty($isExchange)){
                $pdo->rollBack();
                echo json_encode(['success'=>false,'message'=>'相同商品只能兑换一次']);
                exit();
            }
            $goods = $this->db->Name('goods')->where_equalTo('id',$goods_id)->select('id,title,exchange_integral,inventory')->firstRow();
            $integral = $this->db->Name('user')->where_equalTo('id',$user_id)->select('integral')->firstColumn();
            if($integral < $goods['exchange_integral']){
                $pdo->rollBack();
                echo json_encode(['success'=>false,'message'=>'积分不足']);
                exit();
            }elseif($goods['inventory'] < 1){
                $pdo->rollBack();
                echo json_encode(['success'=>false,'message'=>'库存不足']);
                exit();
            }else{
                $order['user_id'] = $user_id;
                $order['goods_id'] = $goods['id'];
                $order['status'] = 1;
                $order['phone'] = '';
                $order['`name`'] = '';
                $order['address'] = '';
                $order['create_time'] = time();
                $order['update_time'] = time();
                $this->db->Name('order')->insert($order)->execute();
                $integral_detail['user_id'] = $user_id;
                $integral_detail['help_id'] = 0;
                $integral_detail['type'] = 3;
                $integral_detail['integral_change'] = '-' . $goods['exchange_integral'];
                $integral_detail['`describe`'] = $goods['title'] . '兑换';
                $integral_detail['create_time'] = time();
                $integral_detail['update_time'] = time();
                $this->db->Name('integral_detail')->insert($integral_detail)->execute();
                $this->db->Name('user')->where_equalTo('id',$user_id)->update(['integral'=>($integral - $goods['exchange_integral'])])->execute();
                $sql = "UPDATE " . Table_Pre ."goods SET `inventory`=`inventory`-1 WHERE `id` = :id AND `inventory` > 0";
                $sqlArr = [":id"=>$goods_id];
                $ok = DataBase::Update($sql,$sqlArr);
                //库存大于0修改,失败回滚
                if($ok){
                    $pdo->commit();
                }else{
                    $pdo->rollBack();
                    echo json_encode(['success'=>false,'message'=>'库存不足']);
                    exit();
                }
                echo json_encode(['success'=>true,'message'=>'兑换成功,请等待发货']);
            }
        } catch (PDOException $e) {
            $pdo->rollback();
            echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
            exit();
        }
    }
    //修改个人资料
    public function updateInfo(){
        $phone = $this->input('post.phone');
        $name = $this->input('post.name');
        $res=$this->db->Name('user')->where_equalTo('id',Session::get('user_id'))->update(['`name`'=>$name,'`phone`'=>$phone])->execute();
        if($res){
            echo json_encode(['success'=>true,'message'=>'保存成功']);
        }else{
            echo json_encode(['success'=>false,'message'=>'保存失败']);
        }
    }
}