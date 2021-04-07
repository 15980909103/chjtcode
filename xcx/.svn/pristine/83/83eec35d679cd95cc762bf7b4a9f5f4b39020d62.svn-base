<?php

namespace app\server\estates;

use app\common\base\ServerBase;
use think\Exception;

/*
 *
 * */
class SelectLog extends ServerBase
{

    /**
     * 列表
     */
    public function getList($params)
    {
        try {
            // 条件
            $where = $params['where'] ?? [];
            // 字段
            $fields = $params['fields'] ?? "*";
            // 排序
            $order = $params['order'] ?? [];
            // 每页记录数
            $pageSize = $params['page_size'] ?? 0;
            // 联表
            $join = $params['join'] ?? [];
            // 分组
            $group = $params['group'] ?? "";


            $myDB = $this->db->name('estates_select_log')->alias('esl');

            // 条件
            if(!empty($where)) {
                $myDB->where($where);
            }
            // 联表
            if(!empty($join)) {
                foreach($join as $v) {
                    if(!empty($v['table']) && !empty($v['cond'])) {
                        $type = $v['type'] ?? 'left';
                        $myDB->join($v['table'], $v['cond'], $type);
                    }
                }
            }
            // 字段
            $myDB->field($fields);
            // 排序
            if(!empty($order)) {
                $myDB->order($order);
            }
            // 分组
            if(!empty($group)) {
                $myDB->group($group);
            }

            // 分页
            if(!empty($pageSize)) {
                $result = array(
                    'list'  =>  [],
                    'total' =>  0,
                    'last_page' =>  0,
                    'current_page'  =>  0
                );

                $list = $myDB->paginate($pageSize)->toArray();

                if(!empty($list['data'])){
                    $result['total'] = $list['total'];
                    $result['last_page'] = $list['last_page'];
                    $result['current_page'] = $list['current_page'];
                    $result['list'] = $list['data'];
                }
            } else {
                $list = $myDB->select()->toArray();
                if(empty($list)) {
                    $list = [];
                }
                $result['list'] = $list;
            }
            // var_dump($list);
            // var_dump($this->db->getLastSql());

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
            $info = $this->db->name('estates_select_log')->field($fields)->where($where)->find();
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

            $resId = $this->db->name('estates_select_log')->insertGetId($data);

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
    public function edit($where, $data)
    {
        try {
            if(empty($where)) {
                return $this->responseFail('缺少必要参数');
            }

            $time = time();
            $data['update_time'] = $time;

            $res = $this->db->name('estates_select_log')->where($where)->update($data);

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

            $res = $this->db->name('estates_select_log')->where($where)->delete();

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
