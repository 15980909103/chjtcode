<?php
namespace app\server\marketing;

use app\common\base\ServerBase;
use think\Db;
use think\Exception;

class Coupon extends ServerBase
{
    //显示所有
    public function getList($search = []){
        $where = [];
        if(!in_array($search['status'],['0','1','2','3'])){
            unset($search['status']);
        }
        if(isset($search['status'])){//状态
            $where[]=  ['status','=', $search['status']];
        }


        $order = ['id'=>'desc'];
        if(!empty($search['sort'])){//排序
            $order = ['sort'=>$search['sort'],'id'=>'desc'];
        }
        if(!empty($search['start_time'])){
            $where[]=  ['start_time','>=', $search['start_time']];
        }
        if(!empty($search['end_time'])){
            $where[]=  ['end_time','<=', $search['end_time']];
        }

        $list = $this->db->name("coupon")->where($where)->order($order)->select()->toArray();
        if(empty($list)){
            $result['list'] = [];
        }else{
            foreach ($list as &$value){
                $value['cover'] = getRealStaticPath($value["cover"]);
            }
            unset($value);
            $result['list'] =   $list;
        }
        return $this->responseOk($result);
    }

    //添加操作
    public function add($data)
    {
        try{
            $id = $this->db->name("coupon")->insertGetId([
                'forid' => $data["forid"],
                'discount'=> $data['discount'],
                'start_time'=> $data["start_time"],
                'end_time'=> $data["end_time"],
                "receive_num"=> 0,
                'total_num' => $data["total_num"],
                'status'=> 0,
                'region_no'=> $data['region_no'],
                'is_delete' => 0,
                'update_time'=> 0,
                'create_time'=> time(),
            ]);   //将数据存入并返回自增 ID
            if(empty($id)){
                throw new Exception('操作失败');
            }
            return $this->responseOk($id);
        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    //修改状态
    public function edit($id,$data){
        try{
            $id = intval($id);
            if(empty($id)){
                throw new Exception('缺少设置参数');
            }
            unset($data['id']);//不可变更id

            $data['update_time'] = time();
            $rs = $this->db->name('coupon')->where(['id'=>$id])->update($data);
            if(empty($rs)){
                throw new Exception('操作失败');
            }

            return $this->responseOk();
        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }


    public function del($id)
    {
        try{

            $res = $this->db->name("coupon")->where("id",$id)->delete();
            if($res){

                return $this->responseOk($res);
            }else{
                throw new Exception('操作失败');
            }

        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }




}