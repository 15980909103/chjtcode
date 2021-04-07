<?php

namespace app\server\admin;

use app\common\traits\TraitInstance;
use app\common\base\ServerBase;
use think\Db;
use think\Exception;

/*
 *
 * */
class Subway extends ServerBase
{
    /**
     * 分页列表
     */
    public function getSubwayPage($where = [], $fields = '*', $pagesize = 20)
    {
        try {
            $list = $this->db->name('subway')->where($where)->field($fields)->paginate($pagesize)->toArray();
            // var_dump($this->db->getLastSql());

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
                $result['list'] =$list['data'];
            }

            return $this->responseOk($result);
        } catch (Exception $e){
            return $this->responseFail($e->getMessage());
        }
    }

    /**
     * 列表
     */
    public function getSubwayList($where = [], $fields = '*')
    {
        try {
            $list = $this->db->name('subway')->where($where)->field($fields)->select()->toArray();
            // var_dump($this->db->getLastSql());

            if(empty($list)){
                $list = [];
            }

            return $this->responseOk($list);
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
            $info = $this->db->name('estates_new')->field($fields)->where($where)->find();
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

            $resId = $this->db->name('subway')->insertGetId($data);

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
    public function edit($id, $data)
    {
        try {
            if(empty($id)) {
                return $this->responseFail('缺少必要参数');
            }

            $time = time();
            $data['update_time'] = $time;

            $where[] = ['id', '=', $id];

            $res = $this->db->name('subway')->where($where)->update($data);

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

            $res = $this->db->name('subway')->where($where)->delete();

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
