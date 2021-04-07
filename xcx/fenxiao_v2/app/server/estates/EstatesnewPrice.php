<?php
namespace app\server\estates;


use app\common\base\ServerBase;
use think\Exception;

class EstatesnewPrice extends ServerBase
{

    // 单条数据
    public function getOne($param = []){
        try {
            // 条件
            $where = $param['where'] ?? [];
            // 字段
            $field = $param['field'] ?? "*";
            // 排序
            $order = [
                'id' => 'desc',
                'create_time' => 'desc',
            ];
            if(!empty($param['order'])) {
                $order = $param['order'];
            }
            
            $result = $this->db->name('price_change_log')->where($where)->field($field)->order($order)->find();
            
            return $this->responseOk($result);
        } catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
        
    }

    // 列表
    public function
    getList($param)
    {
        try {
            $where = $param['where'] ?? [];
            $fields = $param['fields'] ?? '*';
            $pageSize = $param['page_size'] ?? 0;
            $group = $param['group'] ?? '';
            $order = $param['order'] ?? '';

            $myDB = $this->db->name('price_change_log');

            if(!empty($where)) {
                $myDB->where($where);
            }
            if(!empty($fields)) {
                $myDB->field($fields);
            }
            if(!empty($group)) {
                $myDB->group($group);
            }
            if(!empty($order)) {
                $myDB->order($order);
            }

            if(!empty($pageSize)) {
                $result = array(
                    'list'  =>  [],
                    'total' =>  0,
                    'last_page' =>  0,
                    'current_page'  =>  0
                );

                $list = $myDB->paginate($pageSize)->toArray();
                // var_dump($this->db->getLastSql());

                if(empty($list['data'])){
                    $result['list'] = [];
                }else{
                    $result['total'] = $list['total'];
                    $result['last_page'] = $list['last_page'];
                    $result['current_page'] = $list['current_page'];
                    $result['list'] =$list['data'];
                }
            } else {
                $result = $myDB->select()->toArray();
            }

            return $this->responseOk($result);
        } catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }


}