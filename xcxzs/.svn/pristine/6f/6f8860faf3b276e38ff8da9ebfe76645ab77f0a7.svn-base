<?php
namespace app\server\estates;


use app\common\base\ServerBase;
use think\Exception;

class EstatesnewHouse extends ServerBase
{

    // 户型列表
    public function getList($search = [], $field='enh.*', $pagesize = 50){
        try {
            $where = [];

            if(!empty($search['building_id'])){//获取以楼栋所有
                $where[] = ['enh.building_id', '=', intval($search['building_id'])];
            }
            if(!empty($search['estate_id'])){//获取以楼盘所有
                $where[] = ['enh.estate_id', '=', intval($search['estate_id'])];
            }

            if(!empty($search['name'])){
                $where[]=  ['enh.name','like', '%'.$search['name'].'%'];
            }

            $order = ['enh.id'=>'desc'];
            if(!empty($search['sort'])){//排序
                $order = ['enh.sort'=>$search['sort'],'enh.id'=>'desc'];
            }

            $result = array(
                'list'  =>  [],
                'total' =>  0,
                'last_page' =>  0,
                'current_page'  =>  0
            );
    
            $list = $this->db->name('estates_new_house')->alias('enh')->field($field)->where($where)->order($order)->paginate($pagesize)->toArray();
            // var_dump($this->db->getLastSql());
            if(empty($list['data'])){
                $result['list'] = [];
            }else{
                $result['total'] = $list['total'];
                $result['last_page'] = $list['last_page'];
                $result['current_page'] = $list['current_page'];
                $result['list'] =$list['data'];
            }
            return $this->responseOk($result);
        } catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
        
    }

    //添加操作
    public function add($data)
    {
        try{
            $estateId = $data['estate_id'];

            $time = time();
            $data['create_time'] = $time;
            $data['update_time'] = $time;
            
            $id = $this->db->name('estates_new_house')->insertGetId($data);   //将数据存入并返回自增 ID
            if(empty($id)){
                return $this->responseFail(['code'=>0,'msg'=>'操作失败']);
            }

            // 修改楼盘建面
            $this->setBuiltArea($estateId);

            return $this->responseOk($id);
        } catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    //修改状态
    public function edit($id, $data){
        try{
            $id = intval($id);
            if(empty($id)){
                return $this->responseFail(['code'=>0,'msg'=>'缺少必要参数']);
            }
            unset($data['id']);//不可变更id

            $data['update_time'] = time();
            $rs = $this->db->name('estates_new_house')->where(['id'=>$id])->update($data);

            if(empty($rs)){
                return $this->responseFail(['code'=>0,'msg'=>'操作失败']);
            }

            // 修改楼盘建面
            $estateId = $data['estate_id'];
            $this->setBuiltArea($estateId);

            return $this->responseOk();
        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    // 删除楼盘
    public function delete($id)
    {
        try{
            $id = intval($id);
            if(empty($id)){
                return $this->responseFail(['code'=>0,'msg'=>'缺少必要参数']);
            }

            $house = $this->db->name('estates_new_house')->where("id", $id)->field('estate_id')->find();
            
            $res = $this->db->name('estates_new_house')->where("id", $id)->delete();

            // 修改楼盘建面
            $estateId = $house['estate_id'] ?? 0;
            $this->setBuiltArea($estateId);

            if($res){
                return $this->responseOk($res);
            }else{
                return $this->responseFail(['code'=>0,'msg'=>'删除失败']);
            }

        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    /**
     * 户型被修改时，维护更新楼盘表的建面字段
     */
    protected function setBuiltArea($estateId)
    {
        try {
            if(empty($estateId)) {
                return ;
            }
            $house = $this->db->name('estates_new_house')->where(['estate_id' => $estateId])->field('built_area')->select()->toArray();
            if(!empty($house)) {
                $houseArea = array_column($house, 'built_area');
                $max = (int)max($houseArea);
                $min = (int)min($houseArea);
                if($min != $max) {
                    if(empty($min) && empty($max)) {
                        $builtArea = "";
                    } elseif(empty($min)) {
                        $builtArea = "{$max}㎡";
                    } elseif(empty($max)) {
                        $builtArea = "{$min}㎡";
                    } else {
                        $builtArea = "{$min}-{$max}㎡";
                    }
                } else {
                    $builtArea = "{$max}㎡";
                }
            } else {
                $builtArea = "";
            }

            $this->db->name('estates_new')->where(['id' => $estateId])->update(['built_area' => $builtArea]);
        } catch (Exception $e){
            throw $e;
        }
    }

}