<?php

namespace app\server\admin;

use app\common\traits\TraitInstance;
use app\common\base\ServerBase;
use think\Db;
use think\Exception;

/*
 *
 * */
class SearchWord extends ServerBase
{

    protected $table = 'search_word';

    /**
     * 列表
     */
    public function getList($where = [], $fields = '*', $pageSize = 0, $order = [])
    {
        try {
            $myDB = $this->db->name($this->table);
            if(!empty($where)) {
                $myDB->where($where);
            }
            $myDB->field($fields);
            if(!empty($order)) {
                $myDB->order($order);
            }
            if(!empty($pageSize)) {
                $list = $myDB->paginate($pageSize)->toArray();

                $result = array(
                    'list'  =>  [],
                    'total' =>  0,
                    'last_page' =>  0,
                    'current_page'  =>  0
                );

                if(empty($list['data'])){
                    $result['list'] = [];
                }else{
                    $result['total'] = $list['total'];
                    $result['last_page'] = $list['last_page'];
                    $result['current_page'] = $list['current_page'];
                    $result['list'] = $list['data'];
                }
            } else {
                $res = $myDB->select()->toArray();
                if(!empty($res)) {
                    $result = $res;
                } else {
                    $result = [];
                }
            }

            return $this->responseOk($result);
        } catch (Exception $e){
            return $this->responseFail($e->getMessage());
        }
    }


    /**
     * 获取详情
     */
    public function getInfo($where = [], $fields = '*')
    {
        try {
            $info = $this->db->name($this->table)->field($fields)->where($where)->find();
            if(empty($info)) {
                $info = [];
            }
            return $this->responseOk($info);
        } catch (Exception $e){
            return $this->responseFail($e->getMessage());
        }
    }

    /**
     * 添加
     */
    public function add($data)
    {
        try {
            $time = time();
            $data['create_time'] = $time;
            $data['update_time'] = $time;

            $resId = $this->db->name($this->table)->insertGetId($data);

            if(!empty($resId)) {
                return $this->responseOk($resId);
            } else {
                return $this->responseFail();
            }
        } catch (Exception $e){
            return $this->responseFail($e->getMessage());
        }
    }

    /**
     * 编辑
     */
    public function edit($where = [], $data = [])
    {
        try {
            $time = time();
            $data['update_time'] = $time;

            if(empty($where) || empty($data)) {
                return $this->responseFail('缺少必要参数');
            }

            $res = $this->db->name($this->table)->where($where)->update($data);

            if(!empty($res)) {
                return $this->responseOk();
            } else {
                return $this->responseFail();
            }
        } catch (Exception $e){
            return $this->responseFail($e->getMessage());
        }
    }

    /**
     * 删除
     */
    public function delete($id)
    {
        try {
            if(empty($id)) {
                return $this->responseFail('缺少必要参数');
            }

            $where[] = ['id', '=', $id];

            $res = $this->db->name($this->table)->where($where)->delete();

            if(!empty($res)) {
                return $this->responseOk();
            } else {
                return $this->responseFail();
            }
        } catch (Exception $e){
            return $this->responseFail($e->getMessage());
        }
    }
}
