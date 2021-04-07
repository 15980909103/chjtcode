<?php
namespace app\server\estates;


use app\common\base\ServerBase;
use think\Exception;

class BuildingPhotos extends ServerBase
{
    //显示所有
    public function getList($search = []){
        $where = [];

        if(isset($search['estate_id'])){//楼盘id
            $where[]=  ['estate_id','=', $search['estate_id']];
        }
        if(intval($search['category_id'])>0){//类型
            $where[]=  ['category_id','=', $search['category_id']];
        }
 
        $order = ['id'=>'desc'];
  
        $list = $this->db->name('estates_buildingphotos')->where($where)->order($order)->select()->toArray();
        if(empty($list)){
            $result['list'] = [];
        }else{
            $result['list'] =   $list;
        }
        return $this->responseOk($result);
    }

    //添加操作
    public function add($data)
    {
        try{
            $id = $this->db->name('estates_buildingphotos')->insertGetId([
                'estate_id'=> $data['estate_id'],
                'category_id'=> $data['category_id'],
                'cover'=> $data['cover'],
                'update_time'=> 0
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

            if(!empty($data['cover'])){
                $has=$this->db->name('estates_buildingphotos')
                    ->field('cover')->where([
                        ['id','=',$id],
                    ])->find();
            }

            $data['update_time'] = time();
            $rs = $this->db->name('estates_buildingphotos')->where(['id'=>$id])->update($data);

            if(empty($rs)){
                throw new Exception('操作失败');
            }

            if(!empty($has['cover'])&&!empty($data['cover'])&&$has['cover']!=$data['cover']){
                //删除旧的图片
                $this->delFile($has['cover']);
            }

            return $this->responseOk();
        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }


    public function del($id)
    {
        try{
            $res = $this->db->name('estates_buildingphotos')->where("id",$id)->delete();
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