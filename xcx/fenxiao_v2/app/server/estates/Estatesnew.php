<?php
namespace app\server\estates;


use app\common\base\ServerBase;
use app\common\MyConst;
use think\Exception;

class Estatesnew extends ServerBase
{

    // 楼盘列表
    public function getList($search = [], $field='en.*', $pagesize = 50){
        try {
            $where = [];
            // 状态
            if(!in_array($search['status'],['0','1','2'])){
                unset($search['status']);
            }
            if(isset($search['status'])){
                $where[]=  ['en.status','=', $search['status']];
            }
            // 如果是特殊来源的页面，要去掉草稿状态的楼盘
            if(!empty($search['from']) && in_array($search['from'], ['innerTable'])) {
                $where[]=  ['en.status','<>', 2];
            }
            // 楼盘名称
            if(!empty($search['name'])){
                $where[]=  ['en.name','like', '%'.$search['name'].'%'];
            }
            // 城市
            if(!empty($search['city'])){
                if(is_array($search['city'])) {
                    $where[]=  ['en.city','in', $search['city']];
                } else {
                    $where[]=  ['en.city','=', $search['city']];
                }
            }
            if(!empty($search['area'])) {
                $where[]=  ['en.area','=', $search['area']];
            }
            if(!empty($search['business_area'])) {
                $where[]=  ['en.business_area','=', $search['business_area']];
            }
            if(!empty($search['street'])) {
                $where[]=  ['en.street','=', $search['street']];
            }
            // 销售状态
            if(!array_key_exists($search['sale_status'], MyConst::ESTATESNEW_SALE_STATUS)){
                unset($search['sale_status']);
            }
            if(isset($search['sale_status'])){
                $where[]=  ['en.sale_status','=', $search['sale_status']];
            }
            // 是否推荐
            if(!in_array($search['recommend'],['0','1'])){
                unset($search['recommend']);
            }
            if(isset($search['recommend'])){
                $where[]=  ['en.recommend','=', $search['recommend']];
            }

            // 未被软删除
            $where[] = ['is_delete', '=', 0];

            $order = ['id'=>'desc'];
            if(!empty($search['sort'])){//排序
                $order = ['en.sort'=>$search['sort'],'id'=>'desc'];
            }

            $result = array(
                'list'  =>  [],
                'total' =>  0,
                'last_page' =>  0,
                'current_page'  =>  0
            );
    
            $list = $this->db->name('estates_new')->alias('en')->field($field)->where($where)->order($order)->paginate($pagesize)->toArray();
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

    // 楼盘详情
    public function getInfo($where = [], $fields = "*")
    {
        try {
            // 未被软删除
            $where[] = ['is_delete', '=', 0];

            $info = $this->db->name('estates_new')->field($fields)->where($where)->find();
            if(empty($info)) {
                $info = [];
            }
            return $this->responseOk($info);
        } catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    //添加操作
    public function add($data, $other = [])
    {
        try{
            $time = time();
            $data['create_time'] = $time;
            $data['update_time'] = $time;

            $this->db->startTrans();

            // 存入楼盘信息
            $id = $this->db->name('estates_new')->insertGetId($data);   //将数据存入并返回自增 ID
            if(empty($id)){
                $this->db->rollback();
                return $this->responseFail(['code'=>0,'msg'=>'操作失败-1']);
            }

            // // 存入开盘信息
            // if(!empty($other['openTimeData'])) {
            //     $resOpen = $this->db->name('estates_new_time')->insertAll($other['openTimeData']);
            //     if(empty($resOpen)) {
            //         $this->db->rollback();
            //         return $this->responseFail(['code'=>0,'msg'=>'操作失败-2']);
            //     }
            // }

            // 存入初始价格变化
            if(!empty($other['priceData'])) {
                $priceData = $other['priceData'];
                $priceData['type'] = 1;
                $priceData['estate_id'] = $id;
                $resPrice = $this->db->name('price_change_log')->insert($priceData);
                if(empty($resPrice)) {
                    $this->db->rollback();
                    return $this->responseFail(['code'=>0,'msg'=>'操作失败-3']);
                }
            }

            if(!empty($other['newTags'])) {// 新增部分
                $insertData = [];
                foreach($other['newTags'] as $v) {
                    $insertData[] = [
                        'estate_id' => $id,
                        'tag_id' => $v,
                        'type' => 1,
                        'create_time' => $time,
                        'update_time' => $time,
                    ];
                }
                $resNewTags = $this->db->name('estates_has_tag')->insertAll($insertData);
                if(empty($resNewTags)) {
                    $this->db->rollback();
                    return $this->responseFail(['code'=>0,'msg'=>'操作失败-4']);
                }
            }

            $this->db->commit();

            return $this->responseOk($id);
        } catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    //修改楼盘
    public function edit($id, $data, $other = []){
        try{
            $id = intval($id);
            if(empty($id)){
                throw new Exception('缺少必要参数');
            }
            unset($data['id']);//不可变更id

            $time = time();

            $data['update_time'] = $time;

            $this->db->startTrans();

            // 楼盘修改
            $rs = $this->db->name('estates_new')->where(['id'=>$id])->update($data);
            if(empty($rs)){
                $this->db->rollback();
                return $this->responseFail(['code'=>0,'msg'=>'操作失败-1']);
            }

            // 新增价格变化
            if(!empty($other['priceData'])) {
                $priceData = $other['priceData'];
                $priceData['type'] = 1;
                if(!empty($priceData['id'])) {
                    $priceId = $priceData['id'];
                    unset($priceData['id']);
                    $priceData['update_time'] = $time;
                    $resPrice = $this->db->name('price_change_log')->where(['id' => $priceId])->update($priceData);
                } else {
                    $priceData['month_time'] = strtotime(date('Y-m', $time));
                    $priceData['create_time'] = $time;
                    $priceData['update_time'] = $time;
                    $resPrice = $this->db->name('price_change_log')->insert($priceData);
                }
                if(empty($resPrice)) {
                    $this->db->rollback();
                    return $this->responseFail(['code'=>0,'msg'=>'操作失败-3']);
                }
            }

            // 标签变化
            if(!empty($other['newTags'])) {// 新增部分
                $insertData = [];
                foreach($other['newTags'] as $v) {
                    $insertData[] = [
                        'estate_id' => $id,
                        'tag_id' => $v,
                        'type' => 1,
                        'create_time' => $time,
                        'update_time' => $time,
                    ];
                }
                $resNewTags = $this->db->name('estates_has_tag')->insertAll($insertData);
                if(empty($resNewTags)) {
                    $this->db->rollback();
                    return $this->responseFail(['code'=>0,'msg'=>'操作失败-4']);
                }
            }
            if(!empty($other['delTags'])) {// 删除部分
                $resDelTags = $this->db->name('estates_has_tag')->delete($other['delTags']);
                if(empty($resDelTags)) {
                    $this->db->rollback();
                    return $this->responseFail(['code'=>0,'msg'=>'操作失败-5']);
                }
            }

            $this->db->commit();
            return $this->responseOk();
        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    //修改
    public function editByWhere($where, $data){
        try{
            $time = time();

            $data['update_time'] = $time;

            // 楼盘修改
            $rs = $this->db->name('estates_new')->where($where)->update($data);
            if(empty($rs)){
                $this->db->rollback();
                return $this->responseFail(['code'=>0,'msg'=>'操作失败-1']);
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
            $res = $this->db->name('estates_new')->where("id", $id)->delete();
            if($res){
                return $this->responseOk($res);
            }else{
                return $this->responseFail(['code'=>0,'msg'=>'删除失败']);
            }

        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    // 获取许可证
    public function getSalesLicense($where)
    {
        try {
            $where[] = ['is_delete', '=', 0];

            $info = $this->db->name('estates_new')->field('sales_license')->where($where)->find();

            $res = [];

            if(!empty($info)) {
                $licenses = json_decode($info['sales_license'], TRUE);
                if(!empty($licenses)) {
                    $res = $licenses;
                }
            }

            return $this->responseOk($res);
        } catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }


    // ============= 手机端 =================== //

    /**
     * 列表
     */
    public function getListByParams($params)
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

            // 不分页并且没有任何条件时，要限制条数
            $notPageLimit = 0;
            if(empty($where)) {
                $notPageLimit = 100;
            }

            // 默认条件
            $where[] = ['en.is_delete', '=', 0];// 未软删除
            $where[] = ['en.status', '=', 1];// 启用

            /**
             * 构造查询
             */
            $myDB = $this->db->name('estates_new')->alias('en');
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
            $myDB->orderRaw("field(en.sale_status, 2, 1, 4, 3)");
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
//                 var_dump($this->db->getLastSql());

                if(empty($list['data'])){
                    $result['list'] = [];
                }else{
                    $result['total'] = $list['total'];
                    $result['last_page'] = $list['last_page'];
                    $result['current_page'] = $list['current_page'];
                    $result['list'] =$list['data'];
                }
            } else {
                if(!empty($notPageLimit)) {// 没有任何条件时限制一定的条数
                    $myDB->limit($notPageLimit);
                }
                $result = $myDB->select()->toArray();
            }

            return $this->responseOk($result);
        } catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    /**
     * 获取榜单
     */
    public function getEstatesRank($fieldType = 'area', $sortType = 'read')
    {
        try {
            // 限制条数
            $num = 20;
            // 排行类型
            switch($fieldType) {
                case 'area':
                    $field = 'area';
                    break;
                case 'city':
                    $field = 'city';
                    break;
                default:
                    break;
            }
            // 排行依据
            switch($sortType) {
                case 'read':
                    $sort = 'num_read';
                    break;
                case 'search':
                    $sort = 'num_read_search_real';
                    break;
                default:
                    break;
            }
            if(empty($field) || empty($sort)) {
                return $this->responseFail('类型错误');
            }
            $where = [
                ['status', '=', 1],
                ['is_delete', '=', 0],
            ];
            // 分组数据
            $res = $this->db->name('estates_new')
                            ->fieldRaw("{$field}, substring_index(GROUP_CONCAT(id ORDER BY {$sort} desc), ',', {$num}) as ids")
                            ->group($field)
                            ->where($where)
                            ->select()
                            ->toArray();
            // var_dump($this->db->getLastSql());
            if(!empty($res)) {
                // foreach($res as &$v) {
                //     $v['ids'] = explode(',', $v['ids']);
                // }
            } else {
                $res = [];
            }
            // return $this->responseOk($res);
            return $res;
        } catch (Exception $e){
            // return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
            throw $e;
        }
    }
}