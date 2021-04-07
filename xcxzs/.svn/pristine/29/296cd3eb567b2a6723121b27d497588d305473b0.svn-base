<?php
namespace app\server\marketing;

use app\common\base\ServerBase;
use think\Db;
use think\Exception;

class Vote extends ServerBase
{
    public function getList($search = [], $field = '*', $pageSize = 50){
        $where = [];
        if(!in_array($search['status'],['0','1'])){
            unset($search['status']);
        }
        if(isset($search['status'])){//状态
            $where[]=  ['status','=', $search['status']];
        }

        if(!empty($search['region_no'])){
            if(is_array($search['region_no'])) {
                $where[]=  ['region_no','in', $search['region_no']];
            } else {
                $where[]=  ['region_no','=', $search['region_no']];
            }
        }

        $order = ['id'=>'desc'];

        $list = $this->db->name("vote")->field($field)->where($where)->order($order)->paginate($pageSize);
        if($list->isEmpty()){
            $result['list'] = [];
        }else{
            $result['total'] = $list->total();
            $result['last_page'] = $list->lastPage();
            $result['current_page'] = $list->currentPage();
            $result['list'] =$list->items();
        }
        return $this->responseOk($result);
    }

    /**
     * 获取某个投票活动详情
     * @param array $search //$search['getDetail']=1时获取具体内容配置列表
     * @param string $field
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getInfo($search = [],$field='*'){
        $where = [];
        if(!in_array($search['status'],['0','1','2','3'])){
            unset($search['status']);
        }
        if(isset($search['status'])){//状态
            $where[]=  ['status','=', $search['status']];
        }
        $where[]=  ['id','=', $search['id']];

        $info = $this->db->name("vote")->field($field)->where($where)->find();
        if(!empty($info['more_set'])){
            $info['more_set'] = json_decode($info['more_set'],true);
        }
        if(!empty($search['getDetail'])){
            $info['detail'] = $this->getDetails([
                'vote_id' => $search['id']
            ])['result'];
        }

        return $this->responseOk($info);
    }

    //添加操作
    public function add($data)
    {
        try{
            $id = $this->db->name("vote")->insertGetId([
                'name' => $data['name'],
                'status' => intval($data["status"]),
                'page_title' => $data["page_title"],
                'page_keywords' => $data["page_keywords"],
                'page_desc' => $data["page_desc"],
                'region_no'=> $data['region_no'],
                'banner'=> $data['banner'],
                'bgcolor'=> strval($data['bgcolor']),
                'update_time'=> 0,
                'create_time'=> time(),
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
            ]);   //将数据存入并返回自增 ID
            if(empty($id)){
                throw new Exception('操作失败');
            }
            return $this->responseOk($id);
        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    //修改
    public function edit($id,$data){
        try{
            $id = intval($id);
            if(empty($id)){
                throw new Exception('缺少设置参数');
            }
            unset($data['id']);//不可变更id

            if(!empty($data['bgcolor'])){
                $data['bgcolor'] = strval($data['bgcolor']);
            }

            $data['update_time'] = time();
            $rs = $this->db->name('vote')->where(['id'=>$id])->update($data);
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
            $res = $this->db->name("vote")->where("id",$id)->delete();
            if($res){
                return $this->responseOk($res);
            }else{
                throw new Exception('操作失败');
            }

        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    //====================== 投票的具体内容操作 start ======================//
    //获取某个投票配置内容列表
    public function getDetails($search = [], $field='*'){
        $where = [];
        if(!empty($search['region_no'])){
            if(is_array($search['region_no'])) {
                $where[]=  ['region_no','in', $search['region_no']];
            } else {
                $where[]=  ['region_no','=', $search['region_no']];
            }
        }

        if(!empty($search['vote_id'])){
            $where[]=  ['vote_id','=', $search['vote_id']];
        }else{
            if(empty($search['region_no'])){
                return $this->responseOk([]);
            }
        }

        $order = ['id'=>'desc'];

        $list = $this->db->name("vote_detail")->field($field)->where($where)->order($order)->select()->toArray();
        if(empty($list)){
            $result = [];
        }else{
            $result =   $list;
        }
        return $this->responseOk($result);
    }

    //添加配置内容
    public function addDetail($data){
        try{
            $id = $this->db->name("vote_detail")->insertGetId([
                'vote_id'=> $data['vote_id'],
                'forid'=> $data['forid'],
                'forname'=> $data['forname'],
                'module'=> $data['module'],
                'forsort'=> $data['forsort'],
                'vote_num'=> $data['vote_num'],
                'real_vote_num'=> 0,
                'join_num'=> 0,
                'region_no'=> $data['region_no'],
                'update_time'=> 0,
                'introduction' => $data['introduction'],
                'share_desc'   => $data['share_desc'],
                'img'          => $data['img']
            ]);   //将数据存入并返回自增 ID
            if(empty($id)){
                throw new Exception('操作失败');
            }
            return $this->responseOk($id);
        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }
    //修改配置内容
    public function editDetail($id,$data){
        try{
            $id = intval($id);
            if(empty($id)){
                throw new Exception('缺少设置参数');
            }
            unset($data['id']);//不可变更id

            $data['update_time'] = time();
            $rs = $this->db->name('vote_detail')->where(['id'=>$id])->update($data);
            if(empty($rs)){
                throw new Exception('操作失败');
            }

            return $this->responseOk();
        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }
    //删除配置内容
    public function delDetail($id)
    {
        try{
            $res = $this->db->name("vote_detail")->where("id",$id)->delete();
            if($res){
                return $this->responseOk($res);
            }else{
                throw new Exception('操作失败');
            }

        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }
    //====================== 投票的具体内容操作 end ======================//

    //====================== 用户操作投票 start ======================//
    public function getLogList($search = [], $field='vl.*,u.nickname,u.headimgurl,u.phone', $pageSize=50){
        $where = [];

        if(!empty($search['vote_id'])){
            $where[]=  ['vl.vote_id','=', $search['vote_id']];
        }
        if(!empty($search['vote_detail_id'])){
            $where[]=  ['vl.vote_detail_id','=', $search['vote_detail_id']];
        }
        if(!empty($search['user_id'])){
            $where[]=  ['vl.user_id','=', $search['user_id']];
        }
        if(!empty($search['region_no'])){
            $where[]=  ['vl.region_no','=', $search['region_no']];
        }

        if(!empty($search['startdate'])){
            $where[]=  ['vl.create_time','>=', strtotime($search['startdate'])];
        }
        if(!empty($search['enddate'])){
            $where[]=  ['vl.create_time','<', strtotime($search['enddate'].' +1 day')];
        }

        if(!empty($search['nickname'])){
            $where[]=  ['u.nickname','like', '%'.$search['nickname'].'%'];
        }

        $order = ['vl.id'=>'desc'];
        $result = array(
            'list'  =>  [],
            'total' =>  0,
            'last_page' =>  0,
            'current_page'  =>  0
        );

        //以用户id分组显示
        if(!empty($search['getGroupByUserId'])){
            $list = $this->db->name("vote_log")->field('user_id')->alias('vl')->where($where)->order($order)->paginate($pageSize)->toArray();
            $userids = array_column($list['data'],'user_id');
            unset($list['data']);
            if(!empty($userids)){
                $logs = $this->db->name("vote_log")
                    ->field($field)
                    ->join('user u','u.id=vl.user_id','LEFT')
                    ->where([
                        ['vl.user_id', 'in', $userids]
                    ])->select()->toArray();
                $arr = [];
                foreach ($logs as $item){
                    $arr[$item['user_id']][] = $item;
                }

                $list['data'] = $arr;
                unset($arr);
            }
        }else{
            $list = $this->db->name("vote_log")
                ->alias('vl')->field($field)
                ->join('user u','u.id=vl.user_id','LEFT')
                ->where($where)->order($order)->paginate($pageSize)->toArray();
        }

        if(empty($list['data'])){
            $result['list'] = [];
        }else{
            $result['total'] = $list['total'];
            $result['last_page'] = $list['last_page'];
            $result['current_page'] = $list['current_page'];
            $result['list'] =$list['data'];
        }

        return $this->responseOk($result);
    }

    //用户添加投票记录
    public function addLog($data){
        //投票信息是否正确
        $detail = $this->db->name('vote_detail')->where(['id'=>$data['vote_detail_id']])->find();
        if(empty($detail['id'])||empty($detail['vote_id'])){
            return $this->responseFail(['code'=>0,'msg'=> '数据错误']);
        }
        $data['vote_id'] = $detail['vote_id'];
        $vote = $this->db->name('vote')->field('id,status,more_set,start_time,end_time')->where(['id'=>$data['vote_id']])->find();
        if(empty($vote['id'])||empty($vote['status'])){
            return $this->responseFail(['code'=>0,'msg'=> '数据错误']);
        }
        $now_time = time();
        $now_date = date('Y-m-d',$now_time);
        if($vote['start_time']>$now_time){
            return $this->responseFail(['code'=>0,'msg'=> '抱歉，投票时间还未开始']);
        }
        if($vote['end_time']<$now_time){
            return $this->responseFail(['code'=>0,'msg'=> '抱歉，已经过了投票时间']);
        }

        //当前用户是否还可以投票
        $vote['more_set'] = json_decode($vote['more_set'],true);
        $vote['more_set']['day_limit'] = intval($vote['more_set']['day_limit']);
        $has_log = $this->db->name("vote_log")->where([
            ['vote_id', '=', $data['vote_id']],
            ['user_id', '=', $data['user_id']],
            //['create_time', '>=', strtotime($now_date)],
            //['create_time', '<', strtotime($now_date.' +1 day')],
        ])->field('id,create_time')->order(['create_time' => 'desc'])->limit(0,$vote['more_set']['day_limit']+1)->select()->toArray();

        $today_count = 0;//今天的统计
        $is_first = 1;//是否第一次参与
        foreach ($has_log as $item){
            if($item['create_time']>=strtotime($now_date) && $item['create_time']<strtotime($now_date.' +1 day')){
                $today_count++;
            }
            $is_first = 0;
        }
        if($today_count >= $vote['more_set']['day_limit']){
            return $this->responseFail(['code'=>0,'msg'=> '抱歉，您的投票次数今天已用完']);
        }

        try{
            $this->db->startTrans();

            $id = $this->db->name("vote_log")->insertGetId(
                [
                    'vote_detail_id' => $data['vote_detail_id'],
                    'vote_id' => $data['vote_id'],
                    'forid' => $detail['forid'],
                    'forname' => $detail['forname'],
                    'module' => $detail['module'],
                    'region_no' => $detail['region_no'],
                    'user_id' => $data['user_id'],
                    'create_time' => time()
                ]
            );
            /**
            $id = $this->db->name("vote_log")->insertGetId([
                'vote_detail_id' => $data['vote_detail_id'],
                'vote_id'=> $data['vote_id'],
                'forid'=> $data['forid'],
                'forname'=> $data['forname'],
                'module'=> $data['module'],
                'region_no'=> $data['region_no'],
                'user_id'=> $data['user_id'],
                'create_time'=> $now_time,
            ]);   //将数据存入并返回自增 ID
             * **/
            if(empty($id)){
                throw new Exception('操作失败');
            }

            $today_count2 = $this->db->name("vote_log")->where([
                ['vote_id', '=', $data['vote_id']],
                ['user_id', '=', $data['user_id']],
                ['create_time', '>=', strtotime($now_date)],
                ['create_time', '<', strtotime($now_date.' +1 day')],
            ])->count();
            if($today_count2!=$today_count+1 || $today_count2>$vote['more_set']['day_limit']){//保证只插入一次且不超出次数
                throw new Exception('操作失败');
            }

            $rs = $this->db->name('vote_detail')->where(
                [
                    ['vote_id','=',$data['vote_id']],
                    ['id','=',$data['vote_detail_id']],
                ]);
            if($is_first==1){
                $rs = $rs->inc('join_num');//参与人数 +1
            }
            $rs = $rs->inc('vote_num')->inc('real_vote_num')->update();
            if(empty($rs)){
                throw new Exception('操作失败');
            }

            $this->db->commit();
            return $this->responseOk($id);
        }catch (Exception $e){
            $this->db->rollback();
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }


    //====================== 用户操作投票 end ======================//
}