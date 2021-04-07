<?php

namespace app\server\admin;

use app\common\traits\TraitInstance;
use app\common\base\ServerBase;
use think\Db;
use think\Exception;

/*
 *
 * */
class Agent extends ServerBase
{

    protected $table = 'estates_agent';

    /**
     * 列表
     */
    public function getList($param)
    {
        try {
            $where = $param['where'] ?? [];
            $fields = $param['fields'] ?? '*';
            $pageSize = $param['page_size'] ?? 0;
            $order = $param['order'] ?? [];
            $join = $param['join'] ?? [];

            $myDB = $this->db->name($this->table)->alias('a');
            if(!empty($join)) {
                foreach($join as $v) {
                    $myDB->join($v['table'], $v['cond'], $v['type']);
                }
            }
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
                $list = $myDB->select()->toArray();
                if(empty($list)) {
                    $list = [];
                }
                $result['list'] = $list;
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
    public function add($data, $building = [])
    {
        try {
            $time = time();
            $data['create_time'] = $time;
            $data['update_time'] = $time;

            $this->db->startTrans();

            $resId = $this->db->name($this->table)->insertGetId($data);
            if(!$resId) {
                $this->db->rollback();
                return $this->responseFail('添加失败-1');
            }

            // if(!empty($building)) {
            //     foreach($building as $v) {
            //         $insertEstates = [
            //             'agent_id' => $resId,
            //             'estate_id' => $v,
            //         ];
            //     }
            //     $res = $this->db->name('agent_estates')->insertAll($insertEstates);
            //     if(empty($res)) {
            //         $this->db->rollback();
            //     }
            // }

            $this->db->commit();
            return $this->responseOk($resId);
        } catch (Exception $e){
            $this->db->rollback();
            return $this->responseFail($e->getMessage());
        }
    }

    /**
     * 编辑
     */
    public function edit($where, $data, $building = [])
    {
        $time = time();
        $data['update_time'] = $time;

        if(empty($where) || empty($data) || empty($where['id'])) {
            return $this->responseFail('缺少必要参数');
        }

        try {
            $this->db->startTrans();

            $res = $this->db->name($this->table)->where($where)->update($data);
            if(!$res) {
                $this->db->rollback();
                return $this->responseFail('编辑失败-1');
            }


            // // 绑定楼盘处理
            // list($newEstates, $delEstates) = $this->dealEstates($where['id'], $building);
            // if(!empty($dealEstates)) {
            //     $resDel = $this->db->name('agent_estates')->where([['id', 'in', $delEstates]])->delete();
            //     if(empty($resDel)) {
            //         $this->db->rollback();
            //         return $this->responseFail('编辑失败-2');
            //     }
            // }
            // if(!empty($newEstates)) {
            //     foreach($newEstates as $e) {
            //         $inserAdd[] = [
            //             'agent_id' => $where['id'],
            //             'estate_id' => $e,
            //             'create_time' => time(),
            //         ];
            //     }
            //     $resAdd = $this->db->name('agent_estates')->insertAll($inserAdd);
            //     if(empty($resAdd)) {
            //         $this->db->rollback();
            //         return $this->responseFail('编辑失败-3');
            //     }
            // }

            $this->db->commit();
            return $this->responseOk();
        } catch (Exception $e){
            $this->db->rollback();
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

    /**
     * 根据关联表获取相应信息
     */
    public function getListByRelation($param)
    {
        try {
            $where = $param['where'] ?? [];
            $fields = $param['fields'] ?? '*';
            $pageSize = $param['page_size'] ?? 0;
            $order = $param['order'] ?? [];
            $join = $param['join'] ?? [];

            $myDB = $this->db->name('agent_estates')->alias('ae');
            if(!empty($join)) {
                foreach($join as $v) {
                    $myDB->join($v['table'], $v['cond'], $v['type']);
                }
            }
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
                $list = $myDB->select()->toArray();
                if(empty($list)) {
                    $list = [];
                }
                $result['list'] = $list;
            }

            return $this->responseOk($result);
        } catch (Exception $e){
            return $this->responseFail($e->getMessage());
        }
    }

    /**
     * 增加绑定楼盘
     */
    public function addRelation($data)
    {
        try {
            // 是否已存在绑定关系
            $count = $this->db->name('agent_estates')->where($data)->count();
            if($count) {
                return $this->responseFail('已绑定过楼盘');
            }

            $time = time();
            $data['create_time'] = $time;

            $resId = $this->db->name('agent_estates')->insertGetId($data);

            return $this->responseOk($resId);
        } catch (Exception $e){
            $this->db->rollback();
            return $this->responseFail($e->getMessage());
        }
    }

    /**
     * 删除绑定楼盘
     */
    public function delRelation($id)
    {
        try {
            if(empty($id)) {
                return $this->responseFail('缺少必要参数');
            }

            $where[] = ['id', '=', $id];

            $res = $this->db->name('agent_estates')->where($where)->delete();

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
     * 处理绑定楼盘
     */
    protected function dealEstates($id, $estates)
    {   
        try {
            $delEstates = [];// 需要删除的楼盘的记录ID
            $newEstates = [];// 需要增加的楼盘的ID
            $tmp = [];
            $where = [
                ['agent_id', '=', $id],
            ];
            $res = $this->db->name('agent_estates')->where($where)->field('id, estate_id')->select()->toArray();
            
            if(!empty($res)) {
                foreach($res as $v) {
                    $tmp[] = $v['estate_id'];
                    if(!in_array($v['estate_id'], $estates)) {// 不在本次选中楼盘内的,删除
                        $delEstates[] = $v['id'];
                    }
                }
                // 新增的楼盘
                $newEstates = array_diff($estates, $tmp);// 在本次选中的楼盘内，但不在原有楼盘内
            } else {
                $newEstates = $estates;
            }
            return [$newEstates, $delEstates];
        } catch(Exception $e) {
            return $this->responseFail($e->getMessage());
        }
    }
}
