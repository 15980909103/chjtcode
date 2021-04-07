<?php
namespace app\server\estates;


use app\common\base\ServerBase;
use think\Exception;

class EstatesnewTime extends ServerBase
{

    // 开通时间列表
    public function getList($search = [], $field='*'){
        try {
            $where = [];

            if(!empty($search['estate_id'])) {
                $where[] = ['estate_id', '=', $search['estate_id'] ?? 0];
            }
            if(!empty($search['building_id'])) {
                $where[] = ['building_id', '=', $search['building_id'] ?? 0];
            }

            if(empty($where)) {
                return $this->responseFail('缺少条件');
            }

            $order = ['opening_time'=>'desc'];
            if(!empty($search['order'])){
                $order = $search['order'];
            }

            $result = array(
                'list'  =>  [],
                'total' =>  0,
                'last_page' =>  0,
                'current_page'  =>  0
            );
    
            $list = $this->db->name('estates_new_time')->field($field)->where($where)->order($order)->select()->toArray();
            if(empty($list)){
                $result['list'] = [];
            }else{
                $result['list'] =$list;
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
            $time = time();
            $data['create_time'] = $time;
            $data['update_time'] = $time;
            $id = $this->db->name('estates_new_time')->insertGetId($data);   //将数据存入并返回自增 ID
            if(empty($id)){
                return $this->responseFail(['code'=>0,'msg'=>'操作失败']);
            }
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
            $rs = $this->db->name('estates_new_time')->where(['id'=>$id])->update($data);

            if(empty($rs)){
                return $this->responseFail(['code'=>0,'msg'=>'操作失败']);
            }

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
            
            $res = $this->db->name('estates_new_time')->where("id", $id)->delete();
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
     * 获取单条
     */
    public function getOne($param)
    {
        try {
            $where = $param['where'] ?? [];
            $fields = $param['fields'] ?? '*';
            $order = $param['order'] ?? [];

            $myDB = $this->db->name('estates_new_time');
            if(!empty($where)) {
                $myDB->where($where);
            }
            if(!empty($order)) {
                $myDB->order($order);
            }
            $myDB->field($fields);
            $res = $myDB->find();

            if(empty($res)) {
                $res = [];
            }

            return $this->responseOk($res);
        } catch (Exception $e) {
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }


}