<?php
namespace app\admin\controller;

use app\admin\validate\LandDate;
use app\common\base\AdminBaseController;
use app\server\admin\Admin;
use app\server\admin\City;
use app\server\admin\CitySite;
use app\server\admin\Land;
use app\server\admin\Subway;
use think\facade\Db;
use think\Validate;

//后台统计模式
class LandController extends AdminBaseController
{

   public function index(){
        $post       = $this->request->post();
        $search     = [
            'type'              => $post['type'],
            'status'            => $post['status'],
            'name'              => $post['name'],
            'transaction_time'  => $post['transaction_time'],
            'auction_time'      => $post['auction_time'],
            'transaction_price' => !empty($post['transaction_price']) && $post['transaction_price'] != -1  ? explode('-',$post['transaction_price']):'',
            'page'              => $post['page'],
            'city_no_list'      => $post['city_lsit'],
            'pageSize'          => $post['pageSize'] ?? 10,
        ];

        if(empty($search['city_no_list']) ){
           return $this->error('请选择操作城市');
        }

//        var_dump($search);
        $server = new Land();
        $list   = $server->getList($search);
        $this->success($list);
   }

   public function edit(){
        $post   = $this->request->post();
        $validate = new LandDate();
        $server   = new Land();
        $data = [
            'id'                => $post['id'],
            'title'             => $post['title'],
            'recipients'        => $post['recipients'],
            'img_url'           => json_encode($post['img_url']),
            'coordinate'        => $post['coordinate'],
            'city_no'           => $post['city_no'],
            'status'            => $post['status'],
            'land_status'            => $post['land_status'],
            'explain'            => $post['explain'],
            'type'              => $post['type'],
            'landaddr'          => $post['landaddr'],
            'transaction_time'  => strtotime($post['transaction_time']),
            'auction_time'      => strtotime($post['auction_time']),
            'premium_rate'      => floatval($post['premium_rate']),
            'transaction_price' => floatval($post['transaction_price']),
            'total_transaction_price' => floatval($post['total_transaction_price']),
            'total_starting_price'    => floatval($post['total_starting_price']),
            'starting_price'          => floatval($post['starting_price']),
            'plot_ratio'              => floatval($post['plot_ratio']),
            'use_year'                => floatval($post['use_year']),
            'area'                    => floatval($post['area']),
        ];
        if(empty($post['id'])){
//            var_dump($data);
            if(! $validate->scene('add')->check($data)){
                return $this->error($validate->getError());
            }
            $data['create_time'] =time();
            $data['update_time'] =time();
            if(!$server->add($data)){
               return $this->error();
            }


        }else{
            if(! $validate->scene('eidt')->check($data)){
                return $this->error($validate->getError());
            }
            $data['update_time'] =time();
            if(!$server->edit($data)){
                return $this->error();
            }
        }

       $this->success();

   }


   public function getInfo(){
       $id = $this->request->post('id');
       if(empty($id)){
           return $this->error();
       }

       $info = (new Land())->getInfo($id);

       if(empty($info)){
           return $this->error();
       }

       $info['img_url']             = json_decode($info['img_url'],true);
       $info['transaction_time']    = empty($info['transaction_time']) ? '':date('Y-m-d H:i:s',$info['transaction_time']);
       $info['auction_time']        = empty($info['auction_time']) ? '':date('Y-m-d H:i:s',$info['auction_time']);
       $this->success($info);
   }

   public function delLand(){
       $id  = $this->request->post('id');
       if(empty($id)){
         return  $this->error();
       }

       $result = (new Land())->delLand($id);

       if(!$result){

           return  $this->error();
       }

       $this->success();
   }

   public function setLandStatus(){
       $post = $this->request->post();
       if( empty($post['ids']) || !in_array($post['status'],[1,0])){
           return $this->error('参数错误');
       }

       $result = (new Land())->setLandStatus(['ids'=>$post['ids'],'status'=>$post['status']]);

       if(!$result){
           $this->error();
       }else{
           $this->success();
       }
   }
}
