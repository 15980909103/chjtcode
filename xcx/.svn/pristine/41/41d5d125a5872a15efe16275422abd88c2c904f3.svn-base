<?php
namespace app\server\admin;

use app\common\traits\TraitInstance;
use app\common\base\ServerBase;
use think\Db;
use think\Exception;

class Land extends ServerBase
{
    public static  $table = 'land';

    /**
     * @param $data
     * @return bool
     * @throws \think\db\exception\DbException
     */
    public function edit($data){
       if( !$data['id'] ){
           return  false;
       }
       $res =  $this->db->name(self::$table)->where('id','=',$data['id'])->update($data);
       return $res ===false ? false:true;
    }

    /**
     * @param $data
     * @return bool
     */
    public function add($data){
        $res =  $this->db->name(self::$table)->insert($data);
        return $res ===false ? false:true;
    }

    public function getList($search){
       $list =  $this->db->name(self::$table);
       foreach ($search['city_no_list'] as $v){
           $list =  $list->whereOr('city_no','=',$v);
       }
       if(!empty($search['status']) && $search['status'] !=-1){
           $list =  $list->where('status','=',$search['status']);
       }
       if(!empty($search['type']) && $search['type'] !=-1){
            $list =  $list->where('type','=',$search['type']);
       }

        if(!empty($search['auction_time'])){
            $list =  $list->where('auction_time','>=',strtotime($search['auction_time'][0]));
            $list =  $list->where('auction_time','<=',strtotime($search['auction_time'][1]));
        }

        if(!empty($search['transaction_time'])){
            $list =  $list->where('transaction_time','>=',strtotime($search['transaction_time'][0]));
            $list =  $list->where('transaction_time','<=',strtotime($search['transaction_time'][1]));
        }


        if(!empty($search['transaction_price']) && $search['transaction_price'] != -1){
            $list =  $list->where('transaction_price','>=',$search['transaction_price'][0]);
            $list =  $list->where('transaction_price','<=',$search['transaction_price'][1]);
        }

        if(!empty($search['name']) ){
            $list =  $list->where('title','like',"%{$search['name']}%");
            $list =  $list->whereOr('landaddr','like',"%{$search['name']}%");
        }
        $list = $list->paginate($search['pageSize'])->order('update_time desc id desc')->toArray();
//        echo $this->db->getLastSql();
        $result = array(
            'list'  =>  [],
            'total' =>  0,
            'last_page' =>  0,
            'current_page'  =>  0
        );

        foreach ($list['data'] as $k=> $v){
//            $list['data'][$k]['transaction_time'] = date('Y-m-d H:i:s',$v['transaction_time']);
            $list['data'][$k]['index_img']        = json_decode($v['img_url'],true)[0]['url'];
        }
        if(empty($list['data'])){
            $result['list'] = [];
        }else{
            $result['total'] = $list['total'];
            $result['last_page'] = $list['last_page'];
            $result['current_page'] = $list['current_page'];
            $result['list'] = $list['data'];
        }

       return $result;
    }

    /**
     * @param $id
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     *
     *
     */
    public function getInfo($id){
        return $this->db->name(self::$table)
            ->where('id','=',$id)
            ->find();
    }

    /**
     *
     */
    public function delLand($id){
        if(empty($id)){
            return false;
        }
        $res = $this->db->name(self::$table)->where('id','=',$id)->delete();

        return  $res=== false? false:true;
    }



    public function setLandStatus($data){

        if( empty($data['ids']) || !in_array($data['status'],[1,0])){
            return $this->error('参数错误');
        }

        $res = $this->db->name(self::$table)

            ->where('id','in',$data['ids'] )
            ->update(['land_status' => $data['status']]);
        return  $res=== false? false:true;
    }

}