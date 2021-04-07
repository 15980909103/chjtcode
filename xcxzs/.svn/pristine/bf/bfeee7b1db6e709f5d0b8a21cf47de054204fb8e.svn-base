<?php

namespace app\server\admin;

use app\common\base\ServerBase;
use think\Exception;

/*
 *
 * */
class InterestRate extends ServerBase
{

    /**
     * 列表
     */
    public function getList($search=[], $fields='*', $pageSize=0)
    {
        try {
            $where = [];

            $myDB = $this->db->name('interest_rate');

            if(!empty($search['type']) && -1 != $search['type']) {
                $where[] = ['type', '=', $search['type']];
            }
            if(!empty($search['status']) && -1 != $search['status']) {
                $where[] = ['status', '=', $search['status']];
            }
            if(!empty($search['start_time'])) {
                $where[] = ['release_time', '>=', $search['start_time']];
            }
            if(!empty($search['end_time'])) {
                $where[] = ['release_time', '<=', $search['end_time']];
            }

            if(!empty($where)) {
                $myDB->where($where);
            }

            $myDB->field($fields);

            if(!empty($pageSize)) {
                $result = array(
                    'list'  =>  [],
                    'total' =>  0,
                    'last_page' =>  0,
                    'current_page'  =>  0
                );

                $list = $myDB->paginate($pageSize)->toArray();

                if(!empty($list['data'])) {
                    $result['total'] = $list['total'];
                    $result['last_page'] = $list['last_page'];
                    $result['current_page'] = $list['current_page'];
                    $result['list'] =$list['data'];
                }
            } else {
                $result['list'] = [];

                $list = $myDB->select()->toArray();

                if(!empty($list)) {
                    $result['list'] = $list;
                }
            }

            if(!empty($result['list'])) {
                foreach($result['list'] as &$v) {
                    $v['content'] = !empty($v['content']) ? json_decode($v['content']) : [];
                    $v['basic_point'] = !empty($v['basic_point']) ? json_decode($v['basic_point']) : [];
                    $v['release_time'] = !empty($v['release_time']) ? date('Y-m-d', $v['release_time']) : '';
                }
            }

            return $this->responseOk($result);
        } catch (Exception $e){
            return $this->responseFail($e->getMessage());
        }
    }

    // 添加
    public function add($data)
    {
        try{
            $time = time();
            $data['create_time'] = $time;
            $data['update_time'] = $time;
            
            $id = $this->db->name('interest_rate')->insertGetId($data);   //将数据存入并返回自增 ID
            if(empty($id)){
                return $this->responseFail(['code'=>0,'msg'=>'操作失败']);
            }
            return $this->responseOk($id);
        } catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    // 修改
    public function edit($where, $data){
        try{
            if(empty($where)){
                return $this->responseFail(['code'=>0,'msg'=>'缺少必要条件']);
            }

            $data['update_time'] = time();
            $rs = $this->db->name('interest_rate')->where($where)->update($data);

            if(empty($rs)){
                return $this->responseFail(['code'=>0,'msg'=>'操作失败']);
            }

            return $this->responseOk();
        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    // 删除
    public function delete($id)
    {
        try{
            $id = intval($id);
            if(empty($id)){
                return $this->responseFail(['code'=>0,'msg'=>'缺少必要参数']);
            }
            
            $res = $this->db->name('interest_rate')->where("id", $id)->delete();
            if($res){
                return $this->responseOk($res);
            }else{
                return $this->responseFail(['code'=>0,'msg'=>'删除失败']);
            }

        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }
}
