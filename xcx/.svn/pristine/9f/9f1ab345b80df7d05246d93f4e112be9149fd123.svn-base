<?php
namespace app\server\estates;


use app\common\base\ServerBase;
use think\Exception;

class EstatesnewNews extends ServerBase
{
    //显示所有
    public function getList($search = []){
        $where = [];

        if(isset($search['estate_id'])){//楼盘id
            $where[]=  ['estate_id','=', $search['estate_id']];
        }
 
        $order = ['create_time'=>'desc'];
  
        $list = $this->db->name('estates_new_news')->where($where)->order($order)->select()->toArray();
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
            $id = $this->db->name('estates_new_news')->insertGetId([
                'estate_id'=> $data['estate_id'],
                'title'=> $data['title'],
                'describe'=> $data['describe'],
                'content'=> $data['content'],
                'cover'=> $data['cover'],
                'create_time'=> $data['create_time'],
                'update_time'=> 0,
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
            $rs = $this->db->name('estates_new_news')->where(['id'=>$id])->update($data);

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
            $res = $this->db->name('estates_new_news')->where("id",$id)->delete();
            if($res){
                return $this->responseOk($res);
            }else{
                throw new Exception('操作失败');
            }

        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    /**
     * 特殊处理的列表
     */
    public function getListByParam($params)
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

            $myDB = $this->db->name('estates_new_news')->alias('en');
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

                $list = $myDB->paginate($params['page_size'])->toArray();

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
                if(empty($result)) {
                    $result = [];
                }
            }

            return $this->responseOk($result);
        } catch (Exception $e) {
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }


}