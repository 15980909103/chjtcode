<?php

/**
 * Created by PhpStorm.
 * User: USER022
 * Date: 2019/1/3
 * Time: 14:48
 */
include 'AdminController.php';
include System . DS . 'Upload.php';
include System . DS . 'Encryption.php';
include Lib . DS . 'QRcode.php';
class Xcxstore extends AdminController
{
    /*============================================= 客户列表 ==========================================================*/
    public function user_index(){
        return $this->render('user_index');
    }
    public function set_user_where($select,$Db){
        foreach($select as $k=>$v){
            $Db->where_like($k,'%'.$v.'%');
        }
        return $Db;
    }
    public function user_page(){
        $curr=Context::Post('curr');
        $limit=Context::Post('limit');
        $select['nickName']=Context::Post('nickName');
        $select=array_filter($select,function($val){$tmp=$val ===  ''; return !$tmp;});
        if(!empty($select)){
            $userDb=$this->db->Name('xcx_user');
            $userDb=$this->set_user_where($select,$userDb);
            $data = $userDb->select()->page($curr,$limit)->orderBy('create_time','desc')->execute();
            $userDb=$this->set_user_where($select,$userDb);
            $count = $userDb->select('count(*)')->firstColumn();
        }else{
            $data = $this->db->Name('xcx_user')->select()->page($curr,$limit)->orderBy('create_time','desc')->execute();
            $count = $this->db->Name('xcx_user')->select('count(*)')->firstColumn();
        }
        if(!empty($data)){
            foreach($data as &$val){
                $val['gender']=empty($val['gender'])?'未知':($val['gender']=='1'?'男':'女');
                $val['create_time']=date('Y-m-d H:i:s',$val['create_time']);
                $val['update_time']=date('Y-m-d H:i:s',$val['update_time']);
            }
            echo json_encode(['success'=>true,'data'=>$data,'count'=>$count]);
        }else{
            echo json_encode(['success'=>false,'curr'=>$curr]);
        }
    }




    /*============================================= 经纪人管理(改为用户管理) ========================================================*/
    public function agent_index(){
        return $this->render('agent_index');
    }
    public function set_agent_where($select,$Db){
        foreach($select as $k=>$v){
            $Db->where_like($k,'%'.$v.'%');
        }
        return $Db;
    }

    // H5登陆用户列表
    public function agent_page(){
        $curr=Context::Post('curr');
        $limit=Context::Post('limit');
        $select['nickname']=Context::Post('nickname');
        $select['name']=Context::Post('name');
        $select=array_filter($select,function($val){$tmp=$val ===  ''; return !$tmp;});
        if(!empty($select)){
            $userDb=$this->db->Name('xcx_agent_user');
            $userDb=$this->set_agent_where($select,$userDb);
            $data = $userDb->select()->page($curr,$limit)->orderBy('create_time','desc')->execute();
            $userDb=$this->set_agent_where($select,$userDb);
            $count = $userDb->select('count(*)')->firstColumn();
        }else{
            $data = $this->db->Name('xcx_agent_user')->select()->page($curr,$limit)->orderBy('create_time','desc')->execute();
            $count = $this->db->Name('xcx_agent_user')->select('count(*)')->firstColumn();
        }
        if(!empty($data)){
            foreach($data as &$val){
                $val['sex']=empty($val['sex'])?'未知':($val['sex']=='1'?'男':'女');
                $val['name']=empty($val['name'])?'':$val['name'];
                $val['phone']=empty($val['phone'])?'':$val['phone'];
                $val['signature']=empty($val['signature'])?'':$val['signature'];
                $val['create_time']=date('Y-m-d H:i:s',$val['create_time']);
                $val['update_time']=date('Y-m-d H:i:s',$val['update_time']);
            }
            echo json_encode(['success'=>true,'data'=>$data,'count'=>$count]);
        }else{
            echo json_encode(['success'=>false,'curr'=>$curr]);
        }
    }
    public function agent_edit(){
        $id=Context::Get('id');
        $data['data']=$this->db->Name('xcx_agent_user')->select()->where_equalTo('id', $id)->firstRow();
        return $this->render('agent_edit',$data);
    }
    public function agent_doedit(){
        $id=Context::Post('id');
        $data['name']=Context::Post('name');
        $data['phone']=Context::Post('phone');
        $data['signature']=Context::Post('signature');
        $data['update_time']=time();
        $res=$this->db->Name('xcx_agent_user')->update($data)->where_equalTo('id',$id)->execute();
        if($res){
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false,'message'=>'保存失败']);
        }
    }

    /**
     * 查看经纪人勿扰状态
     * @return false|string
     */
    public function do_not_disturb_record(){
        $agent_id = Context::Get('id');
        $record = $this->db->Name('xcx_agent_do_not_disturb')
                    ->select('cycle_type,start_time,end_time,status_type')
                    ->where_equalTo('agent_id',$agent_id)->firstRow();
        if (!empty($record) && $record['status_type']){
            $record['cycle_type'] = $this->cycle_type_toString($record['cycle_type']);
            $record['status_type'] = "请勿打扰";
            $data['record'] = $record;
            return $this->render('do_not_disturb_record',$data);
        }else{
            unset($record);
            $record['status_type'] = "欢迎骚扰";
            $data['record'] = $record;
            return $this->render('do_not_disturb_record',$data);
        }
    }

    /**
     * 勿扰时间周期字符替换
     * @param $cycle_type
     * @return string
     */
    private function cycle_type_toString($cycle_type){
        switch ($cycle_type){
            case 1:
                $cycle_type = '星期一';
                break;
            case 2:
                $cycle_type = '星期二';
                break;
            case 3:
                $cycle_type = '星期三';
                break;
            case 4:
                $cycle_type = '星期四';
                break;
            case 5:
                $cycle_type = '星期五';
                break;
            case 6:
                $cycle_type = '星期六';
                break;
            case 7:
                $cycle_type = '星期日';
                break;
            default:
                $cycle_type = '每天';
        }
        return $cycle_type;
    }

    /**
     * 经纪人名下客户查看页面
     * @return false|string
     */
    public function agent_customer_index(){
         $user_arr = $this->db->Name('xcx_user')->select('id,nickname')->execute();
         $agent_arr = $this->db->Name('xcx_agent_user')->select('id,nickname')->execute();
         $data['user_arr'] = $user_arr;
         $data['agent_arr'] = $agent_arr;
        return $this->render('agent_customer_index',$data);
    }

    /**
     * 经纪人名下客户查看页面 数据渲染
     */
    public function agent_customer_page(){
        $curr = Context::Post('curr');
        $limit = Context::Post('limit');
        $where['user_id'] = Context::Post('user_id');
        $where['agent_id'] = Context::Post('agent_id');
        $where['user_status'] = 1;//客户未删除状态
        $where = array_filter($where,function ($val){$tmp=$val==='';return!$tmp;});
        $select = 'ac.id,xu.nickName AS xunickname,xu.gender AS xugender,xu.avatarUrl,au.name AS auname,
                             au.nickname AS aunickname,au.sex AS ausex,au.headimgurl,ac.agent_focus,
                             ac.user_top,ac.source,ac.user_name,ac.user_phone,ac.create_time';
            //分页+条件
            $agent_customer_arr = $this->set_whereTo($where,$this->agent_customer_db())->select($select)
                                       ->orderBy('ac.id','DESC')->page($curr,$limit)->execute();
            //总记录
            $count = $this->set_whereTo($where,$this->agent_customer_db())->select('COUNT(*)')->firstColumn();
        if (!empty($agent_customer_arr)){
            foreach ($agent_customer_arr as &$val){
                $val['xugender'] = $this->sex_toString($val['xugender']);
                $val['ausex'] = $this->sex_toString($val['ausex']);
                $val['auname'] = $val['auname']?$val['auname']:"";
                $val['user_phone'] = $val['user_phone']?$val['user_phone']:"";
                $val['agent_focus'] = $val['agent_focus']?"是":"否";
                $val['user_top'] = $val['user_top']?"是":"否";
                $val['source'] = $val['source']?"二维码扫描":"未知";
                $val['create_time'] = date('Y-m-d H:i:s',$val['create_time']);
            }
            echo json_encode(['success'=>true,'data'=>$agent_customer_arr,'count'=>$count]);
        }else{
            echo json_encode(['success'=>false]);
        }
    }

    /**
     * 性别字符替换
     * @param $sex
     * @return string
     */
    private function sex_toString($sex){
        switch ($sex){
            case 0:
                $sex = '未知';
                break;
            case 1:
                $sex = '男';
                break;
            case 2:
                $sex = '女';
                break;
        }
        return $sex;
    }
    /**
     * 多个不同字段条件
     * @param $where
     * @param $db
     * @return mixed
     */
    private function set_whereTo($where,$db){
        foreach ($where as $k=>$v){
            $db->where_equalTo($k,$v);
        }
        return $db;
    }

    /**
     * 经纪人对应客户 DB对象
     * @return mixed
     */
    private function agent_customer_db(){
        $db = $this->db->Name('xcx_agent_customer AS ac')
            ->leftJoin('xcx_user','xu','xu.id=ac.user_id')
            ->leftJoin('xcx_agent_user','au','au.id=ac.agent_id');
        return $db;
    }

    
    public function agent_status(){
        $id=Context::Post('id');
        $status=Context::Post('status');
        $res=$this->db->Name('xcx_agent_user')->update(['status'=>$status])->where_equalTo('id',$id)->execute();
        if($res)
            echo json_encode(['success'=>true]);
        else
            echo json_encode(['success'=>false]);
    }
    public function agentAtAgent(){
        $id=Context::Post('id');
        $at_agent=Context::Post('at_agent');
        $res=$this->db->Name('xcx_agent_user')->update(['at_agent'=>$at_agent])->where_equalTo('id',$id)->execute();
        if($res)
            echo json_encode(['success'=>true]);
        else
            echo json_encode(['success'=>false]);
    }
    /*============================================= 店铺管理 ========================================================*/
    public function store_index(){
        return $this->render('store_index');
    }
    public function set_store_where($select,$Db){
        foreach($select as $k=>$v){
            if($k=='title' || 'city' == $k)
                $Db->where_like($k,'%'.$v.'%');
            else
                $Db->where_equalTo($k,$v);
        }
        return $Db;
    }
    public function store_page(){
        $curr=Context::Post('curr');
        $limit=Context::Post('limit');
        $select['status']=Context::Post('status');
        $select['title']=Context::Post('title');
        $select=array_filter($select,function($val){$tmp=$val ===  ''; return !$tmp;});
        // 非超级管理员需对账号所属区域作判断
//        if(!empty($this->gid)) {
//            if(empty($this->city)) {
//                echo json_encode(['success'=>false,'message'=>'没有所属城市，无信息可查看']);
//                exit();
//            }
//            $select['city'] = $this->city;
//        }
//        $this->gid = 5;

        //渠道部门
        if($this->gid == 5 || $this->gid == 8){
            $channeid = $this->db->Name('admin')->where_equalTo('id',$_SESSION['aid'])->select('channel_id')->firstRow();
            $type = $this->db->Name('xcx_store_agent')->where_equalTo('said',$channeid['channel_id'])->select('type,mgid')->firstRow();
            if($type['type'] == 5){
             $aid = $_SESSION['aid'];
            }elseif($type['type'] == 6){
                $mgid = explode(',',$type['mgid']);
                $saidArray = $this->db->Name('xcx_store_agent')->where_in('mgid',$mgid)->where_equalTo('type',5)->select('said')->execute();
                $channelId = array_column($saidArray,'said');
                $adminId = $this->db->Name('admin')->where_in('channel_id',$channelId)->select('id')->execute();
                $aid = array_column($adminId,'id');
            }

        }

        if($this->gid == 5 || $this->gid == 8){
            if(empty($aid)){
                echo json_encode(['success'=>true,'data'=>[],'count'=>0]);
                exit();
            }
        }

        if(!empty($select)){
            $userDb=$this->db->Name('xcx_store_store');
            $userDb=$this->set_store_where($select,$userDb);
            if($this->gid == 5 || $this->gid == 8){
                if(is_array($aid)){
                    $userDb = $userDb->where_in('ss.aid',$aid);
                }else{
                    $userDb = $userDb->where_equalTo('ss.aid',$aid);
                }
            }
            $data = $userDb->select("ss.*, sa.agent_id", "ss")->leftJoin('xcx_store_agent','sa','sa.store_id=ss.id')->where_equalTo('ss.is_delete', 0)->page($curr,$limit)->orderBy('create_time','desc')->execute();
            $userDb=$this->set_store_where($select,$userDb);
            if($this->gid == 5 || $this->gid == 8){
                if(is_array($aid)){
                    $userDb = $userDb->where_in('aid',$aid);
                }else{
                    $userDb = $userDb->where_equalTo('aid',$aid);
                }
            }
            $count = $userDb->select('count(*)')->firstColumn();
        }else{

            $data = $this->db->Name('xcx_store_store');
            if($this->gid == 5 || $this->gid == 8){
                if(is_array($aid)){
                    $data->where_in('ss.aid',$aid);
                }else{
                    $data->where_equalTo('ss.aid',$aid);
                }
            }
            $data = $data->select("ss.*, sa.agent_id", "ss")->leftJoin('xcx_store_agent','sa',"(sa.store_id=ss.id and sa.type=1)")->where_equalTo('ss.is_delete', 0)->page($curr,$limit)->orderBy('create_time','desc')->execute();

            $count = $this->db->Name('xcx_store_store');
            if($this->gid == 5 || $this->gid == 8){
                if(is_array($aid)){
                    $count = $count->where_in('aid',$aid);
                }else{
                    $count = $count->where_equalTo('aid',$aid);
                }
            }
            $count = $count->select('count(*)')->where_equalTo('is_delete', 0)->firstColumn();
        }
        if(!empty($data)){
            //获取经纪人微信信息
            $agids=[];$agDict=[];
            foreach($data as $value){
                $agids[]=$value['agent_id'];
            }
            $agids=array_unique($agids);
            $agentRow=(new Query())->Name('xcx_agent_user')->select()->where_in('id',$agids)->execute();
            if(!empty($agentRow)){
                foreach($agentRow as $v){
                    $agDict[$v['id']]=$v;
                }
            }
            foreach($data as &$val){
                $val['nickname']=empty($val['agent_id'])?'未绑定':$agDict[$val['agent_id']]['nickname'];
                $val['headimgurl']=empty($val['agent_id'])?'未绑定':$agDict[$val['agent_id']]['headimgurl'];
                $val['main_area']=$val['province'].' '.$val['city'].' '.$val['area'];
                $val['create_time']=date('Y-m-d H:i:s',$val['create_time']);
                $val['update_time']=date('Y-m-d H:i:s',$val['update_time']);
            }
            echo json_encode(['success'=>true,'data'=>$data,'count'=>$count]);
        }else{
            echo json_encode(['success'=>false,'curr'=>$curr]);
        }
    }

    public function store_add(){
        if(!empty($this->gid)) {
            $province = $this->province;
            $city = $this->city;
            $disabled = 1;
        }
        $data['province'] = !empty($province) ? $province : '';
        $data['city'] = !empty($city) ? $city : '';
        $data['disabled'] = !empty($disabled) ? $disabled : 0;
        return $this->render('store_add', $data);
    }
    //执行店铺添加操作
    public function store_doadd(){

        // 非超级管理员不能编辑自己所属城市外的店铺
        $city = !empty(Context::Post('city')) ? Context::Post('city') : '';
        $resCheck = $this->checkCity($city);
        if(empty($resCheck['success'])) {
            echo json_encode($resCheck);
            exit();
        }

        $aid = $_SESSION['aid'];

        $pdo = new DataBase();
        $pdo->beginTransaction(); // 开启一个事务
        try {
            $res = $this->db->Name('xcx_store_store')->insert([
                'aid'=> $aid,
                'store_no'=> '',
                'title' => Context::Post('title'),
                'province' => Context::Post('province'),
                'city' => Context::Post('city'),
                'area' => Context::Post('area'),
                'create_time' => time(),
                'update_time' => time(),
            ])->execute();
            if(empty($res)){
                throw new PDOException('保存失败请重试');
            }
            $sn = 'S'.date('y', time());
            $sn.= date('m', time());
            $sn.=sprintf("%05d", $res%100000);
            $res3 = $this->db->Name('xcx_store_store')->update([
                'store_no'=> $sn
            ])->where_equalTo('id',$res)->execute();
            if(empty($res3)){
                throw new PDOException('保存失败请重试');
            }

            $code_key = create_code_key('T1');
            $res2 = $this->db->Name('xcx_store_agent')->insert([
                'store_id' => $res,
                'mgid' => 0,
                'agent_id' => 0,
                'type' => 1,//店长类型
                'create_time' => time(),
                'code_key' => $code_key,
            ])->execute();
            if(empty($res2)){
                throw new PDOException('保存失败请重试');
            }

            $pdo->commit();
            echo json_encode(['success'=>true]);
        } catch (PDOException $e) {
            $pdo->rollback();
            echo json_encode(['success'=>false,'message'=>$e->getMessage()],JSON_UNESCAPED_UNICODE);
        }
    }
    public function store_edit(){
        $id=Context::Get('id');
        $data['data']=$this->db->Name('xcx_store_store')->select()->where_equalTo('id', $id)->firstRow();
        if(!empty($this->gid)) {
            $data['data']['disabled'] = 1;
        } else {
            $data['data']['disabled'] = 0;
        }
        return $this->render('store_edit',$data);
    }
    public function store_doedit(){
        $id=Context::Post('id');
        $data['title']=Context::Post('title');
        $data['province']=Context::Post('province');
        $data['city']=Context::Post('city');
        $data['area']=Context::Post('area');
        $data['update_time']=time();

        // 非超级管理员不能编辑自己所属城市外的店铺
        $resCheck = $this->checkCity($data['city']);
        if(empty($resCheck['success'])) {
            echo json_encode($resCheck);
            exit();
        }

        $res=$this->db->Name('xcx_store_store')->update($data)->where_equalTo('id',$id)->execute();
        if($res){
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false,'message'=>'保存失败']);
        }
    }
    public function store_status(){
        $id=Context::Post('id');
        $status=Context::Post('status');
        $ssDb=$this->db->Name('xcx_store_store')->update(['status'=>$status])->where_equalTo('id',$id);
//        if(!empty($this->gid)) {
//            if(empty($this->city)) {
//                $ssDb->where_equalTo('id', 0);// 无城市权限，用该条件使记录不被修改
//            } else {
//                $ssDb->where_like('city', "%{$this->city}%");
//            }
//        }
        $res=$ssDb->execute();
        if($res)
            echo json_encode(['success'=>true]);
        else
            echo json_encode(['success'=>false]);
    }
    public function store_del(){
        $id = intval(Context::Post('id'));
        $resAgnt = $this->db->Name('xcx_store_agent')
                        ->select('count(*) as count')
                        ->where_equalTo('store_id', $id)
                        ->where_equalTo('is_delete', 0)
                        ->where_notEqualTo('agent_id', 0)
                        ->firstRow();
        if(!empty($resAgnt['count'])) {
            return json_encode(['success'=>false,'message'=>'该店铺还有店员存在，不可删除']);
        }

        $ssDb=$this->db->Name('xcx_store_store')->update([
            'update_time' => time(),
            'is_delete' => 1,
        ])->where_equalTo('id',$id);
//        if(!empty($this->gid)) {
//            if(empty($this->city)) {
//                $ssDb->where_equalTo('id', 0);// 无城市权限，用该条件使记录不被修改
//            } else {
//                $ssDb->where_like('city', "%{$this->city}%");
//            }
//        }
        $res = $ssDb->execute();
        if($res){
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false,'message'=>'删除失败']);
        }

        /*$id = intval(Context::Post('id'));
        $pdo = new DataBase();
        if(empty($id)){
            echo json_encode(['success'=>false,'message'=>'id参数缺失']);
            return;
        }
        try {
            $pdo->beginTransaction();
            $res=$this->db->Name('xcx_store_store')->delete()->where_equalTo('id',$id)->execute();
            $res2=$this->db->Name('xcx_store_agent')->delete()->where_equalTo('store_id',$id)->execute();
            if($res && $res2){
                $pdo->commit();
                echo json_encode(['success'=>true]);
            }else{
                $pdo->rollBack();
                echo json_encode(['success'=>false,'message'=>'删除失败']);
            }
        } catch (PDOException $e) {
            $pdo->rollback();
            echo json_encode(['success'=>false,'message'=>'删除失败']);
        }*/
    }
    /*============================================= 公司管理 ========================================================*/
    public function company_index(){
        return $this->render('company_index',$data);
    }
    public function company_page(){
        $curr=Context::Post('curr');
        $limit=Context::Post('limit');

        // 非超级管理员需要判断所属区域
//        if(!empty($this->gid)) {
//            if(empty($this->city)) {
//                echo json_encode(['success'=>false,'message'=>'您没有该地区的权限']);
//                exit();
//            }
//            $isCity = TRUE;
//        }
        $dbSQL = $this->db->Name('xcx_manager_user_gx');
        if(!empty($isCity)) {
            $dbSQL->where_like("city", "%{$this->city}%");
        }
        $count = $dbSQL->select('count(*)')->where_equalTo('is_delete', 0)->firstColumn();

        $dbSQL = $this->db->Name('xcx_manager_user_gx');
        if(!empty($isCity)) {
            $dbSQL->where_like("city", "%{$this->city}%");
        }
        $data = $dbSQL->select('id,title,create_time,update_time,is_delete,status,type')->where_equalTo('is_delete', 0)->page($curr,$limit)->orderBy('create_time','desc')->execute();


        if(!empty($data)){


            //获取楼盘信息
            foreach($data as $key => $value){
                $agentRow[$key]=$this->db->Name('xcx_store_agent')->select('said')->find_in_set($value['id'],'mgid')->execute();

                $data[$key]['saids']=array_column($agentRow[$key],'said');
                $SaidRow[$key]=$this->db->Name('xcx_manager_building')->select('building_ids, store_ids')->where_in('said',$data[$key]['saids'])->execute();

                if(0 == $value['type']) {// 项目组
                    $build = array_column($SaidRow[$key],'building_ids');
                } elseif(1 == $value['type']) {// 渠道组
                    $build = array_column($SaidRow[$key],'store_ids');
                } else {
                    $build = [];
                }
                $buildStr = implode(",", $build);
                $buildArr = explode(",", $buildStr);
                $buildArr = array_filter($buildArr);
                $buildArr = array_unique($buildArr);
                $data[$key]['building_ids'] = sizeof($buildArr);

//                $data[$key]['build']=array_column($SaidRow[$key],'building_ids');//array_unique
//
//                $data[$key]['said']=implode(",",$data[$key]['saids']);
//                $data[$key]['builds']=implode(",",$data[$key]['build']);
//                $array=array_unique(explode(",",$data[$key]['builds']));
//                $data[$key]['builds']=$array;
//
//                $data[$key]['building_ids']=in_array(0,$data[$key]['builds'])?'无绑定':count($data[$key]['builds']);

                $data[$key]['create_time']=date('Y-m-d H:i:s',$data[$key]['create_time']);
                if($data[$key]['update_time']){
                    $data[$key]['update_time']=date('Y-m-d H:i:s',$data[$key]['update_time']);
                }else{
                    $data[$key]['update_time']= '';
                }

                // 工作组类型
                switch ($value['type']) {
                    case 0:
                        $data[$key]['type'] = '项目组';
                        break;
                    case 1:
                        $data[$key]['type'] = '渠道组';
                        break;
                    default:
                        $data[$key]['type'] = '未知';
                        break;
                }


//                $status=$this->db->Name('xcx_store_agent')->select('status')->find_in_set($value['id'],'mgid')->execute();
//                $data[$key]['status']= $status[0]['status'];


            }
            
            echo json_encode(['success'=>true,'data'=>$data,'count'=>$count]);
        }else{
            echo json_encode(['success'=>false,'curr'=>$curr]);
        }
    }


    public function company_edit(){
        $id=Context::Get('id');
        $managerUserGx=$this->db->Name('xcx_manager_user_gx')->select()->where_equalTo('id', $id)->firstRow();

//        //获取楼盘ids
//        $said=$this->db->Name('xcx_store_agent')->select('said')->find_in_set($managerUserGx['id'],'mgid')->execute();
//        $saiddata=array_column($said,said);
//        $builRow=$this->db->Name('xcx_manager_building')->select('building_ids')->where_in('said',$saiddata)->execute();
//        $builddata=array_column($builRow,building_ids);
//        $builddatas=implode(",",$builddata);
//        $buildarray=array_unique(explode(",",$builddatas));
//        $buildarray=in_array(0,$buildarray)?'':$buildarray;
//        $managerUserB['building_ids']=empty($buildarray)?'':implode(",",$buildarray);
//
//
//        if(empty($managerUserB['building_ids'])){
//            $managerUserGx['is_all']='1';
//            $managerUserGx['resList']=json_encode([]);
//        }else{
//            $temp=[];
//            $managerUserGx['is_all']='0';
//            $building_ids=explode(',',$managerUserB['building_ids']);
//            $buildingData=$this->db->Name('xcx_building_building')->select('id,name')->where_in('id',$building_ids)->execute();
//            foreach($buildingData as $val){
//                $temp[]=['id'=>$val['id'],'name'=>$val['name']];
//            }
//            $managerUserGx['resList']=json_encode($temp,JSON_UNESCAPED_UNICODE);
//        }
        $data['data']=$managerUserGx;
        if(!empty($this->gid)) {
            $data['data']['disabled'] = 1;
        } else {
            $data['data']['disabled'] = 0;
        }
        return $this->render('company_edit',$data);
    }


    public function company_doedit(){
        $id=Context::Post('id');
        $data['title']=Context::Post('title');
        $data['province']=Context::Post('province');
        $data['city']=Context::Post('city');
        $data['area']=Context::Post('area');

        $resCheck = $this->checkCity($data['city']);
        if(empty($resCheck['success'])) {
            echo json_encode($resCheck);
            exit();
        }

        $building_ids="0";
        if(!empty(Context::Post('building_ids'))){
            $building_ids=trim(Context::Post('building_ids'),',');
            $building_ids=explode(',',$building_ids);
            $building_ids=array_unique($building_ids);
            $building_ids=implode(',',$building_ids);
        }
        $data['update_time'] = time();
        $res=$this->db->Name('xcx_manager_user_gx')->update($data)->where_equalTo('id',$id)->execute();

        $dat['building_ids'] = $building_ids;
        $dat['update_time'] = time();

        $said=$this->db->Name('xcx_store_agent')->select('said')->find_in_set($id,'mgid')->execute();
        $saiddata=array_column($said,said);

        $re=$this->db->Name('xcx_manager_building')->update($dat)->where_in('said',$saiddata)->execute();
        if($res||$re){
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false,'message'=>"保存失败"]);
        }
    }

    public function company_doedit_bind(){
        $id=Context::Post('id');
        $building_ids="0";
        if(!empty(Context::Post('building_ids'))){
            $building_ids=trim(Context::Post('building_ids'),',');
            $building_ids=explode(',',$building_ids);
            $building_ids=array_unique($building_ids);
            $building_ids=implode(',',$building_ids);
        }
        $data['building_ids'] = $building_ids;
        $data['update_time'] = time();
        $res=$this->db->Name('xcx_manager_building')->update($data)->where_equalTo('said',$id)->execute();
        if($res){
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false,'message'=>"保存失败"]);
        }
    }


    public function company_agent_index(){
        $id=Context::Get('id');
        $store = $this->db->Name('xcx_manager_user_gx')->select('title')->where_equalTo('id', $id)->firstRow();
        $data['data']=$this->db->Name('xcx_store_agent')->select()->find_in_set($id,'mgid')->where_equalTo('is_delete', 0)->firstRow();
        $data['data']['id']=$id;
        $data['data']['title'] = !empty($store['title']) ? $store['title'] : "";
        return $this->render('company_agent_index',$data);
    }


    public function company_agent_page(){
        $id=Context::Post('id');
        $curr=Context::Post('curr');
        $limit=Context::Post('limit');

        $start=($curr-1)*$limit;
        $sql = "SELECT * FROM 9h_xcx_store_agent WHERE `type` in (2, 3, 4, 5, 6) AND FIND_IN_SET($id,mgid) AND is_delete=0  ORDER BY `type` DESC LIMIT $start,$limit";

        $data = DataBase::Select($sql);

        $sql1 = "SELECT count(*) FROM 9h_xcx_store_agent WHERE `type` in (2, 3, 4, 5, 6) AND FIND_IN_SET($id,mgid) AND is_delete=0";
        $count = DataBase::Select($sql1);

        //$data = $this->db->Name('xcx_store_agent')->select()->find_in_set($id,'mgid')->where_equalTo('is_delete',0)->page($curr,$limit)->orderBy('type','desc')->orderBy('create_time','desc')->execute();
        //$count = $this->db->Name('xcx_store_agent')->select('count(*)')->find_in_set($id,'mgid')->where_equalTo('is_delete',0)->firstColumn();
        if(!empty($data)){
            //获取经纪人微信信息
            $agids=[];$agDict=[];
            foreach($data as $value){
                $agids[]=$value['agent_id'];
            }
            $agids=array_unique($agids);
            $agentRow=(new Query())->Name('xcx_agent_user')->select()->where_in('id',$agids)->execute();
            if(!empty($agentRow)){
                foreach($agentRow as $v){
                    $agDict[$v['id']]=$v;
                }
            }
            foreach($data as &$val){
                $val['nickname']=empty($val['agent_id'])?'未绑定':$agDict[$val['agent_id']]['nickname'];
                $val['headimgurl']=empty($val['agent_id'])?'未绑定':$agDict[$val['agent_id']]['headimgurl'];
                switch ($val['type']) {
                    case 1:
                        $val['type']='店长';
                        break;
                    case 2:
                        $val['type']='项目组员';
                        break;
                    case 3:
                        $val['type']='项目组长';
                        break;
                    case 4:
                        $val['type']='财务';
                        break;
                    case 5:
                        $val['type']='渠道组员';
                        break;
                    case 6:
                        $val['type']='渠道组长';
                        break;
                    default:
                        $val['type']='店员';
                        break;
                }

                $val['create_time']=date('Y-m-d H:i:s',$val['create_time']);
                if(!empty($val['update_time'])){
                    $val['update_time']=date('Y-m-d H:i:s',$val['update_time']);
                }else{
                    $val['update_time'] = '';
                }
                $val['active_code_url'] = getActive_code_url($val['code_key']);

            }
            echo json_encode(['success'=>true,'data'=>$data,'count'=>$count]);
        }else{
            echo json_encode(['success'=>false,'curr'=>$curr]);
        }
    }

    public function company_del(){
        $id=Context::Post('id');
        $mugDb=$this->db->Name('xcx_manager_user_gx')->update([
            'is_delete' => 1,
            'update_time'=>time(),
        ])->where_equalTo('id',$id);
//        if(!empty($this->gid)) {
//            if(empty($this->city)) {
//                $mugDb->where_equalTo('id', 0);// 无城市权限，用该条件使记录不被修改
//            } else {
//                $mugDb->where_like('city', "%{$this->city}%");
//            }
//        }
        $res = $mugDb->execute();
        if($res){
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false,'message'=>'删除失败']);
        }
    }

    public function company_status(){
//        $id=Context::Post('id');
//        $status=Context::Post('status');
//        $ids=explode(',',$id);
//        //print_r($ids);
//        $res=$this->db->Name('xcx_store_agent')->update(['status'=>$status])->where_in('said',$ids)->execute();
        if(empty(Context::Post('id'))) {
            echo json_encode(['success'=>false,'message'=>'参数缺失']);
        }
        $id = Context::Post('id');
        $status = Context::Post('status');
        $mugDb = $this->db->Name('xcx_manager_user_gx')->update(['status'=>$status])->where_equalTo('id', $id);
//        if(!empty($this->gid)) {
//            if(empty($this->city)) {
//                $mugDb->where_equalTo('id', 0);// 无城市权限，用该条件使记录不被修改
//            } else {
//                $mugDb->where_like('city', "%{$this->city}%");
//            }
//        }
        $res=$mugDb->execute();
        if($res)
            echo json_encode(['success'=>true]);
        else
            echo json_encode(['success'=>false]);
    }

    /*============================================= 人员管理 ========================================================*/
    public function staff_index(){
        return $this->render('staff_index',$data);
    }

    public function staff_page(){
        $curr=Context::Post('curr');
        $limit=Context::Post('limit');
        $type=[3,6,7,8];
        $count = $this->db->Name('xcx_store_agent')->select('count(*)')->where_in('type', $type)->where_equalTo('is_delete', 0)->firstColumn();
        $data = $this->db->Name('xcx_store_agent')->select('*')->where_in('type', $type)->where_equalTo('is_delete', 0)->page($curr,$limit)->orderBy('create_time','desc')->execute();
        if(!empty($data)){
            //获取经纪人微信信息
            $agids=[];$agDict=[];
            foreach($data as $value){
                if($value['agent_id']){
                    $agids[]=$value['agent_id'];
                }
            }
            $agids=array_unique($agids);
            $agentRow=(new Query())->Name('xcx_agent_user')->select()->where_in('id',$agids)->execute();
            if(!empty($agentRow)){
                foreach($agentRow as $v){
                    $agDict[$v['id']]=$v;
                }
            }

            foreach($data as &$val){
                // 绑定数计算
                switch ($val['type']) {
                    // 项目组长
                    case 3:
                        $val['mgids']=empty($val['mgid'])?'所有':count(explode(',',$val['mgid']));
                        break;
                    // 渠道组长
                    case 6:
                        $val['mgids'] = 0;
                        break;
                    // 项目负责人
                    case 7:
                        $val['mgids'] = 0;
                        break;
                    default:
                        $val['mgids'] = 0;
                        break;
                }
                if(empty($val['agent_id'])){
                    $val['nickname'] = '未绑定';
                    $val['headimgurl'] = '未绑定';
                }else{
                    $val['nickname'] = $agDict[$val['agent_id']]['nickname'];
                    $val['headimgurl'] = $agDict[$val['agent_id']]['headimgurl'];
                }
                $val['create_time']=date('Y-m-d H:i:s',$val['create_time']);
                if($val['update_time']){
                    $val['update_time']=date('Y-m-d H:i:s',$val['update_time']);
                }else{
                    $val['update_time']= '';
                }
                $val['active_code_url'] = getActive_code_url($val['code_key']);
            }
            echo json_encode(['success'=>true,'data'=>$data,'count'=>$count]);
        }else{
            echo json_encode(['success'=>false,'curr'=>$curr]);
        }
    }

    public function staff_add(){
        return $this->render('staff_add');
    }

    public function staff_edit(){
        $said=Context::Get('id');
        $managerUserB=$this->db->Name('xcx_store_agent')->select('said,mgid,type,name,province,city,area')->where_equalTo('said', $said)->firstRow();
        if(empty($managerUserB['mgid'])){
            $managerUserB['is_all']='0';// 去掉所有情况
            $managerUserB['resList']=json_encode([]);
        }else{
            $temp=[];
            $managerUserB['is_all']='0';
            $mgids=explode(',',$managerUserB['mgid']);
            $buildingData=$this->db->Name('xcx_manager_user_gx')->select('id,title')->where_in('id',$mgids)->execute();
            foreach($buildingData as $val){
                $temp[]=['id'=>$val['id'],'title'=>$val['title']];
            }
            $managerUserB['resList']=json_encode($temp,JSON_UNESCAPED_UNICODE);
        }
        $data['data']=$managerUserB;
        return $this->render('staff_edit',$data);
    }


    public function staff_doadd(){
        try {
            $name = !empty(Context::Post('name')) ? Context::Post('name') : "";
            $province = !empty(Context::Post('province')) ? Context::Post('province') : "";
            $city = !empty(Context::Post('city')) ? Context::Post('city') : "";
            $area = !empty(Context::Post('area')) ? Context::Post('area') : "";
            $pdo = new DataBase();
            $pdo->beginTransaction();
            $mgids ="0";
            if(!empty(Context::Post('mgids'))){
                $mgids=trim(Context::Post('mgids'),',');
                $mgids=explode(',',$mgids);
                $mgidsArr = $mgids;
                $mgids=array_unique($mgids);
                $mgids=implode(',',$mgids);
            } else {
                if(3 == Context::Post('type') || 6 == Context::Post('type')) {
//                    echo json_encode(['success'=>false,'message'=>'请选择绑定组']);
//                    exit();
                    $mgids ="-1";
                }
            }
            $type=Context::Post('type');
            // 查询要绑定的组是否已被其他组长绑定
            if(!empty($mgidsArr)) {
                foreach ($mgidsArr as $v) {
                    $res = $this->db->Name('xcx_store_agent')->select()
                                ->where_equalTo('is_delete', 0)
                                ->where_equalTo('type', $type)
                                ->where_express(
                                    "(FIND_IN_SET(:mgid, mgid))",
                                    [':mgid' => $v]
                                )
                                ->firstRow();
                    if(!empty($res)) {
                        $gx = $this->db->Name('xcx_manager_user_gx')->select('title')->where_equalTo('id', $v)->firstRow();
                        $gxTitle = !empty($gx['title']) ? $gx['title'] : $v;
                        echo json_encode(['success'=>false,'message'=>"{$gxTitle}组已被绑定"]);
                        exit();
                        break;
                    }
                }
            }
            $code_key = create_code_key('T3');
            $res2 = $this->db->Name('xcx_store_agent')->insert([
                'mgid' => $mgids,
                'name' => $name,
                'store_id' => 0,
                'agent_id' => 0,
                'type' => $type,//工作组长类型
                'create_time' => time(),
                'code_key' => $code_key,
                'province' => $province,
                'city' => $city,
                'area' => $area,
            ])->execute();
            if(empty($res2)){
                throw new PDOException('保存失败请重试');
            }
            $res3=$this->db->Name('xcx_manager_building')->insert([
                'said' => $res2,
                'create_time' => time(),
            ])->execute();
            if(empty($res3)){
                throw new PDOException('保存失败请重试');
            }
            $pdo->commit();
            echo json_encode(['success'=>true]);
        } catch (PDOException $e) {
            $pdo->rollback();
            echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
        }
    }

    public function staff_doedit(){
        $said=Context::Post('said');
        $data['type']=Context::Post('type');
        $data['name'] = !empty(Context::Post('name')) ? Context::Post('name') : "";
        $province = !empty(Context::Post('province')) ? Context::Post('province') : "";
        $city = !empty(Context::Post('city')) ? Context::Post('city') : "";
        $area = !empty(Context::Post('area')) ? Context::Post('area') : "";

        // 获取原数据
        $saInfo = $this->db->Name('xcx_store_agent')->select('said, type')->where_equalTo('said', $said)->firstRow();
        if(empty($saInfo)) {
            echo json_encode(['success'=>false,'message'=>'该账号不存在']);
            exit();
        }

        $mgids="0";
        if(3 == $data['type'] || 6 == $data['type']) {
            if(!empty(Context::Post('mgids'))){
                $mgids=trim(Context::Post('mgids'),',');
                $mgids=explode(',',$mgids);
                $mgidsArr = $mgids;
                $mgids=array_unique($mgids);
                $mgids=implode(',',$mgids);
            } else {
//                echo json_encode(['success'=>false,'message'=>'请选择绑定组']);
//                exit();
                $mgids ="-1";
            }
        }
        // 查询要绑定的组是否已被其他组长绑定
        if(!empty($mgidsArr)) {
            foreach ($mgidsArr as $v) {
                $res = $this->db->Name('xcx_store_agent')->select()
                    ->where_equalTo('is_delete', 0)
                    ->where_notEqualTo('said', $said)
                    ->where_equalTo('type', $data['type'])
                    ->where_express(
                        "(FIND_IN_SET(:mgid, mgid))",
                        [':mgid' => $v]
                    )
                    ->firstRow();
                if(!empty($res)) {
                    $gx = $this->db->Name('xcx_manager_user_gx')->select('title')->where_equalTo('id', $v)->firstRow();
                    $gxTitle = !empty($gx['title']) ? $gx['title'] : $v;
                    echo json_encode(['success'=>false,'message'=>"{$gxTitle}组已被绑定"]);
                    exit();
                    break;
                }
            }
        }
        $data['mgid'] = $mgids;
        $data['update_time'] = time();

        $data['province'] = $province;
        $data['city'] = $city;
        $data['area'] = $area;

        $pdo = new DataBase();
        $pdo->beginTransaction();

        try {
            // 修改身份信息
            $res=$this->db->Name('xcx_store_agent')->update($data)->where_equalTo('said',$said)->execute();
            if(!$res) {
                $pdo->rollback();
                echo json_encode(['success'=>false,'message'=>"保存失败"]);
                exit();
            }

            if($saInfo['type'] != $data['type']) {// 类型有变化，需修改关联数据
                switch ($saInfo['type']) {
                    case 3:

                        break;
                    case 6:
                        $adminUpdate = [
                            'channel_id' => 0,
                        ];
                        $this->db->Name('admin')->update($adminUpdate)->where_equalTo('channel_id', $said)->execute();
                        break;
                    case 7:
                        $adminUpdate = [
                            'charge_id' => 0,
                        ];
                        $this->db->Name('admin')->update($adminUpdate)->where_equalTo('charge_id', $said)->execute();
                        break;
                }
            }
        } catch (\ErrorException $e) {
            $pdo->rollback();
            echo json_encode(['success'=>false,'message'=>"保存失败 {$e->getMessage()}"]);
        }

        $pdo->commit();
        echo json_encode(['success'=>true]);
    }

    //搜索公司
    public function searchCompany(){
        $name=Context::Post('name');
        $type=Context::Post('type');
        if($type == 6){
            $type = 1;
        }elseif($type == 3){
            $type = 0;
        }
        $companyData=$this->db->Name('xcx_manager_user_gx')->select('id,title')->where_equalTo('type',$type)->where_like('title','%'.$name.'%')->execute();

        if($companyData){
            echo json_encode(['success'=>true,'data'=>$companyData]);
        }else{
            echo json_encode(['success'=>false]);
        }
    }



    /*============================================= 工作人员管理（部分已废弃） ========================================================*/
    public function manager_index(){
        return $this->render('manager_index',$data);
    }
    public function manager_page(){
        $curr=Context::Post('curr');
        $limit=Context::Post('limit');
        $count = $this->db->Name('xcx_manager_user_gx mg')->select('count(*)')->innerJoin('xcx_store_agent','sa','sa.mgid=mg.id AND mg.delete=0 AND sa.type=3')->where_equalTo('sa.is_delete', 0)->firstColumn();
        $data = $this->db->Name('xcx_manager_user_gx mg')->select('mg.building_ids,mg.title,sa.*')->innerJoin('xcx_store_agent','sa','sa.mgid=mg.id AND mg.delete=0 AND sa.type=3')->where_equalTo('sa.is_delete', 0)->page($curr,$limit)->orderBy('create_time','desc')->execute();

        if(!empty($data)){
            //获取经纪人微信信息
            $agids=[];$agDict=[];
            foreach($data as $value){
                if($value['agent_id']){
                    $agids[]=$value['agent_id'];
                }
            }
            $agids=array_unique($agids);
            $agentRow=(new Query())->Name('xcx_agent_user')->select()->where_in('id',$agids)->execute();
            if(!empty($agentRow)){
                foreach($agentRow as $v){
                    $agDict[$v['id']]=$v;
                }
            }
            foreach($data as &$val){
                $val['building_ids']=empty($val['building_ids'])?'所有':count(explode(',',$val['building_ids']));
                if(empty($val['agent_id'])){
                    $val['nickname'] = '未绑定';
                    $val['headimgurl'] = '未绑定';
                }else{
                    $val['nickname'] = $agDict[$val['agent_id']]['nickname'];
                    $val['headimgurl'] = $agDict[$val['agent_id']]['headimgurl'];
                }
                $val['create_time']=date('Y-m-d H:i:s',$val['create_time']);
                if($val['update_time']){
                    $val['update_time']=date('Y-m-d H:i:s',$val['update_time']);
                }else{
                    $val['update_time']= '';
                }
                $val['active_code_url'] = getActive_code_url($val['code_key']);
            }
            echo json_encode(['success'=>true,'data'=>$data,'count'=>$count]);
        }else{
            echo json_encode(['success'=>false,'curr'=>$curr]);
        }
    }
    //搜索楼盘
    public function searchBuilding(){
        $name=Context::Post('name');
        $buildingData=$this->db->Name('xcx_building_building')->select('id,name')->where_notEqualTo('is_delete', 1)->where_like('name','%'.$name.'%')->execute();
        if($buildingData){
            echo json_encode(['success'=>true,'data'=>$buildingData]);
        }else{
            echo json_encode(['success'=>false]);
        }
    }
    //搜索店铺
    public function searchStore(){
        if(!empty(Context::Post('name'))) {
            $name = Context::Post('name');
            $storeData = $this->db->Name('xcx_store_store')->select('id, title')->where_like('title','%'.$name.'%')->execute();
        } else {
            $storeData = [];
        }
        if($storeData){
            echo json_encode(['success'=>true,'data'=>$storeData]);
        }else{
            echo json_encode(['success'=>false]);
        }
    }
    public function manager_add(){
        if(!empty($this->gid)) {
            $province = $this->province;
            $city = $this->city;
            $disabled = 1;
        }
        $data['province'] = !empty($province) ? $province : '';
        $data['city'] = !empty($city) ? $city : '';
        $data['disabled'] = !empty($disabled) ? $disabled : 0;
        return $this->render('manager_add', $data);
    }

    public function manager_doadd(){
        try {
            $city = !empty(Context::Post('city')) ? Context::Post('city') : '';
            $resCheck = $this->checkCity($city);
            if(empty($resCheck['success'])) {
                echo json_encode($resCheck);
                exit();
            }

            $pdo = new DataBase();
            $pdo->beginTransaction();
            $building_ids ="0";
            if(!empty(Context::Post('building_ids'))){
                $building_ids=trim(Context::Post('building_ids'),',');
                $building_ids=explode(',',$building_ids);
                $building_ids=array_unique($building_ids);
                $building_ids=implode(',',$building_ids);
            }

            $res=$this->db->Name('xcx_manager_user_gx')->insert([
                'title' => Context::Post('title'),
                'province' => Context::Post('province'),
                'city' => Context::Post('city'),
                'area' => Context::Post('area'),
                'type' => Context::Post('type'),
                'create_time' => time(),
            ])->execute();
            if(empty($res)){
                throw new PDOException('保存失败请重试');
            }

//            $code_key = create_code_key('T3');
//            $res2 = $this->db->Name('xcx_store_agent')->insert([
//                'mgid' => $res,
//                'store_id' => 0,
//                'agent_id' => 0,
//                'type' => 3,//工作组长类型
//                'create_time' => time(),
//                'code_key' => $code_key,
//            ])->execute();
//            if(empty($res2)){
//                throw new PDOException('保存失败请重试');
//            }
//
//            $res3=$this->db->Name('xcx_manager_building')->insert([
//                'said' => $res2,
//                'building_ids' => $building_ids,
//                'create_time' => time(),
//            ])->execute();
//            if(empty($res3)){
//                throw new PDOException('保存失败请重试');
//            }


            $pdo->commit();
            echo json_encode(['success'=>true]);
        } catch (PDOException $e) {
            $pdo->rollback();
            echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
        }
    }
    public function manager_edit(){
        $id=Context::Get('id');
        $said=Context::Get('said');
        $managerUserGx=$this->db->Name('xcx_manager_user_gx')->select()->where_equalTo('id', $id)->firstRow();
        $managerUserB=$this->db->Name('xcx_manager_building')->select('said,building_ids')->where_equalTo('said', $said)->firstRow();
        $managerUserGx['said']=$said;
        if(empty($managerUserB['building_ids'])){
            $managerUserGx['is_all']='1';
            $managerUserGx['resList']=json_encode([]);
        }else{
            $temp=[];
            $managerUserGx['is_all']='0';
            $building_ids=explode(',',$managerUserB['building_ids']);
            $buildingData=$this->db->Name('xcx_building_building')->select('id,name')->where_in('id',$building_ids)->execute();
            foreach($buildingData as $val){
                $temp[]=['id'=>$val['id'],'name'=>$val['name']];
            }
            $managerUserGx['resList']=json_encode($temp,JSON_UNESCAPED_UNICODE);
        }
        $data['data']=$managerUserGx;
        return $this->render('manager_edit',$data);
    }

    // 渠道绑定店铺查看页面
    public function admin_store_index(){
        $data['said'] = Context::Get('id');
        return $this->render('admin_store',$data);
    }

    // 渠道绑定店铺查看数据
    public function admin_store_page(){
        $curr = Context::Post('curr');
        $limit = Context::Post('limit');
        $said = Context::Post('said');
        $data = $this->db->Name('xcx_store_agent sa')
                            ->select('ss.title')
                            ->innerJoin('admin', 'a', 'sa.said=a.channel_id')
                            ->leftJoin('xcx_store_store', 'ss', 'a.id=ss.aid')
                            ->where_equalTo('ss.is_delete', 0)
                            ->where_equalTo('sa.said', $said)
                            ->page($curr,$limit)
                            ->execute();

        if(!empty($data)){
            $count = $this->db->Name('xcx_store_agent sa')
                            ->select('count(ss.id) as count')
                            ->innerJoin('admin', 'a', 'sa.said=a.channel_id')
                            ->leftJoin('xcx_store_store', 'ss', 'a.id=ss.aid')
                            ->where_equalTo('ss.is_delete', 0)
                            ->where_equalTo('sa.said', $said)
                            ->firstRow();
            echo json_encode(['success'=>true,'data'=>$data,'count'=>$count]);
        }else{
            echo json_encode(['success'=>false,'curr'=>$curr]);
        }
    }

    // 项目负责人绑定楼盘查看页面
    public function admin_building_index(){
        $data['said'] = Context::Get('id');
        return $this->render('admin_building',$data);
    }

    // 渠道绑定楼盘查看数据
    public function admin_building_page(){
        $curr = Context::Post('curr');
        $limit = Context::Post('limit');
        $said = Context::Post('said');
        $data = $this->db->Name('xcx_store_agent sa')
            ->select('bb.name')
            ->innerJoin('admin', 'a', 'sa.said=a.charge_id')
            ->leftJoin('xcx_building_building', 'bb', 'a.id=bb.aid')
            ->where_equalTo('bb.is_delete', 0)
            ->where_equalTo('sa.said', $said)
            ->page($curr,$limit)
            ->execute();

        if(!empty($data)){
            $count = $this->db->Name('xcx_store_agent sa')
                ->select('count(bb.id) as count')
                ->innerJoin('admin', 'a', 'sa.said=a.charge_id')
                ->leftJoin('xcx_building_building', 'bb', 'a.id=bb.aid')
                ->where_equalTo('bb.is_delete', 0)
                ->where_equalTo('sa.said', $said)
                ->firstRow();
            echo json_encode(['success'=>true,'data'=>$data,'count'=>$count]);
        }else{
            echo json_encode(['success'=>false,'curr'=>$curr]);
        }
    }


    /**********************************************楼盘绑定（作废）***********************************************************************/
    public function manager_edit_bind(){
        $id=Context::Get('id');
        $managerUserGx=$this->db->Name('xcx_manager_building')->select()->where_equalTo('said', $id)->firstRow();
        if(empty($managerUserGx)){
            $manager=$this->db->Name('xcx_store_agent')->select('said,mgid,type')->where_equalTo('said', $id)->firstRow();
            $managerSaid=$this->db->Name('xcx_store_agent')->select('said,mgid,type')->where_equalTo('mgid', $manager['mgid'])->where_equalTo('type', '3')->firstRow();
            $managerBind=$this->db->Name('xcx_manager_building')->select('said,building_ids')->where_equalTo('said', $managerSaid['said'])->firstRow();
            $res = $this->db->Name('xcx_manager_building')->insert([
                'said' => $id,
                'building_ids' => $managerBind['building_ids'],
                'create_time' => time(),
            ])->execute();
            if($res) {
                if(empty($managerBind['building_ids'])){
                    $managerUserGx['is_all']='1';
                }else{
                    $managerUserGx['is_all']='0';
                }
                $managerUserGx['resList'] = json_encode([]);
            }
        }else{
            $temp=[];
            if(empty($managerUserGx['building_ids'])){
                $managerUserGx['is_all']='1';
            }else{
                $managerUserGx['is_all']='0';
            }
            $building_ids=explode(',',$managerUserGx['building_ids']);
            $buildingData=$this->db->Name('xcx_building_building')->select('id,name')->where_in('id',$building_ids)->execute();
            foreach($buildingData as $val){
                $temp[]=['id'=>$val['id'],'name'=>$val['name']];
            }
            $managerUserGx['resList']=json_encode($temp,JSON_UNESCAPED_UNICODE);
        }
        $data['data']=$managerUserGx;
        //print_r($data);
        return $this->render('manager_edit_bind',$data);
    }

    public function manager_doedit_bind(){
        if(empty(Context::Post('id'))) {
            echo json_encode(['success'=>false,'message'=>"参数缺失"]);
            exit();
        }
        $id=Context::Post('id');
        $resMB = $this->db->Name('xcx_manager_building')->select('id, type')->where_equalTo('said',$id)->firstRow();
        if(!empty($resMB)) {
            if(2 != $resMB['type']) {
                echo json_encode(['success'=>false,'message'=>"用户类型不正确"]);
                exit();
            }
        }

//        $building_ids="0";
        if(!empty(Context::Post('building_ids'))){
            $building_ids=trim(Context::Post('building_ids'),',');
            $buildingIdsArr=explode(',',$building_ids);
            $buildingIdsArr=array_unique($buildingIdsArr);
            $building_ids=implode(',',$buildingIdsArr);
        } else {
            if('0' === Context::Post('building_ids')) {
                $building_ids = "0";
            } else {
                $building_ids = '';
            }
        }
        $data['building_ids'] = $building_ids;
        $data['update_time'] = time();

        // 查找店铺是否被绑定
        if(!empty($buildingIdsArr)) {
            foreach ($buildingIdsArr as $v) {
                if(0 != $v) {// 非绑定所有
                    $resMS = $this->db->Name('xcx_manager_building')
                        ->select('id')
                        ->where_express("find_in_set(:buildId, building_ids)", [':buildId' => $v])
                        ->where_notEqualTo('said', $id)
                        ->firstRow();
                    if(!empty($resMS)) {
                        $resBuild = $this->db->Name('xcx_building_building')
                            ->select('name')
                            ->where_equalTo('id', $v)
                            ->firstRow();
                        $buildName = !empty($resBuild['name']) ? $resBuild['name'] : "";
                        echo json_encode(['success'=>false,'message'=>"楼盘{$buildName}已被绑定"]);
                        exit();
                    }
                }
            }
        }

        if(!empty($resMB)) {
            $res=$this->db->Name('xcx_manager_building')->update($data)->where_equalTo('said',$id)->execute();
        } else {
            // 查看用户类型
            $resUser = $this->db->Name('xcx_store_agent')->select('said, type')->where_equalTo('said', $id)->firstRow();
            if(empty($resUser)) {
                echo json_encode(['success'=>false,'message'=>"用户不存在"]);
                exit();
            }
            $data['said'] = $id;
            $data['type'] = $resUser['type'];
            $data['store_ids'] = '';
            $res = $this->db->Name('xcx_manager_building')->insert($data)->execute();
        }

        if($res){
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false,'message'=>"保存失败"]);
        }
    }

    // 绑定店铺
    public function manager_bind_store(){
        if(empty(Context::Post('id'))) {
            echo json_encode(['success'=>false,'message'=>"参数缺失"]);
            exit();
        }
        $id = Context::Post('id');

        $resMB = $this->db->Name('xcx_manager_building')->select('id, type')->where_equalTo('said',$id)->firstRow();
        if(!empty($resMB)) {
            if(5 != $resMB['type']) {
                echo json_encode(['success'=>false,'message'=>"用户类型不正确"]);
                exit();
            }
        }

//        $storeIds = "0";
        if(!empty(Context::Post('store_ids'))){
            $storeIds = trim(Context::Post('store_ids'),',');
            $storeIdsArr = explode(',', $storeIds);
            $storeIdsArr = array_unique($storeIdsArr);
            $storeIds = implode(',', $storeIdsArr);
        } else {
            if('0' === Context::Post('store_ids')) {
                $storeIds = "0";
            } else {
                $storeIds = '';
            }
        }
        $data['store_ids'] = $storeIds;
        $data['update_time'] = time();

        // 查找店铺是否被绑定
        if(!empty($storeIdsArr)) {
            foreach ($storeIdsArr as $v) {
                if(0 != $v) {// 非绑定所有
                    $resMS = $this->db->Name('xcx_manager_building')
                        ->select('id')
                        ->where_express("find_in_set(:storeId, store_ids)", [':storeId' => $v])
                        ->where_notEqualTo('said', $id)
                        ->firstRow();
                    if(!empty($resMS)) {
                        $resStore = $this->db->Name('xcx_store_store')
                            ->select('title')
                            ->where_equalTo('id', $v)
                            ->firstRow();
                        $storeName = !empty($resStore['title']) ? $resStore['title'] : "";
                        echo json_encode(['success'=>false,'message'=>"店铺{$storeName}已被绑定"]);
                        exit();
                    }
                }
            }
        }

        if(!empty($resMB)) {
            $res = $this->db->Name('xcx_manager_building')->update($data)->where_equalTo('said', $id)->execute();
        } else {
            // 查看用户类型
            $resUser = $this->db->Name('xcx_store_agent')->select('said, type')->where_equalTo('said', $id)->firstRow();
            if(empty($resUser)) {
                echo json_encode(['success'=>false,'message'=>"用户不存在"]);
                exit();
            }
            $data['said'] = $id;
            $data['type'] = $resUser['type'];
            $data['building_ids'] = '';
            $res = $this->db->Name('xcx_manager_building')->insert($data)->execute();
        }

        if($res){
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false,'message'=>"保存失败"]);
        }
    }

    public function manager_doedit(){
        $id=Context::Post('id');
        $said=Context::Post('said');
        $data['title']=Context::Post('title');
        $data['province']=Context::Post('province');
        $data['city']=Context::Post('city');
        $data['area']=Context::Post('area');
        $building_ids="0";
        if(!empty(Context::Post('building_ids'))){
            $building_ids=trim(Context::Post('building_ids'),',');
            $building_ids=explode(',',$building_ids);
            $building_ids=array_unique($building_ids);
            $building_ids=implode(',',$building_ids);
        }
        $data['update_time'] = time();
        $res=$this->db->Name('xcx_manager_user_gx')->update($data)->where_equalTo('id',$id)->execute();

        $dat['building_ids'] = $building_ids;
        $dat['update_time'] = time();
        $re=$this->db->Name('xcx_manager_building')->update($dat)->where_equalTo('said',$said)->execute();
        if($res||$re){
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false,'message'=>"保存失败"]);
        }
    }
    public function manager_del(){
        $id=Context::Post('id');
        $res=$this->db->Name('xcx_store_agent')->update([
            'is_delete' => 1,
            'update_time'=>time(),
        ])->where_equalTo('said',$id)->execute();
        if($res){
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false,'message'=>'删除失败']);
        }
    }
    public function manager_reset(){
        $said = Context::Post('said');
        $sa_info= $this->db->Name('xcx_store_agent')->select('mgid,type')->where_equalTo('id', $said)->firstRow();
        if($sa_info['type']!=2){
            echo json_encode(['success'=>false,'message'=>'数据错误,该记录人员类型错误']);
            return;
        }
        $agRow = $this->db->Name('xcx_manager_user_gx')->select()->where_equalTo('id', $sa_info['mgid'])->firstRow();
        if(!empty($agRow)){
            try {
                //重置绑定的经纪人id
                $code_key = create_code_key('T2');
                $res=$this->db->Name('xcx_store_agent')->update([
                    'agent_id' => 0,
                    'code_key' => $code_key,
                    'create_time' => time(),
                    'update_time' => time(),
                ])->where_equalTo('said',$said)->execute();
                if($res){
                    echo json_encode(['success'=>true]);
                }else{
                    echo json_encode(['success'=>false,'message'=>'重置失败']);
                }
            } catch (PDOException $e) {
                echo json_encode(['success'=>false,'message'=>'重置失败']);
            }
        }else{
            echo json_encode(['success'=>false,'message'=>'数据错误']);
        }
    }

    //权限绑定
    public function power_bind(){
        $id=Context::Get('id');
        $managerUserGx=$this->db->Name('xcx_manager_building')->select()->where_equalTo('said', $id)->firstRow();
        if(empty($managerUserGx)){
            $manager=$this->db->Name('xcx_store_agent')->select('said,mgid,type')->where_equalTo('said', $id)->firstRow();
            $managerSaid=$this->db->Name('xcx_store_agent')->select('said,mgid,type')->where_equalTo('mgid', $manager['mgid'])->where_equalTo('type', '3')->firstRow();
            $managerBind=$this->db->Name('xcx_manager_building')->select('said,building_ids')->where_equalTo('said', $managerSaid['said'])->firstRow();
            $res = $this->db->Name('xcx_manager_building')->insert([
                'said' => $id,
                'building_ids' => $managerBind['building_ids'],
                'create_time' => time(),
            ])->execute();
            if($res) {
                $managerUserGx=$this->db->Name('xcx_manager_building')->select()->where_equalTo('said', $id)->firstRow();
                $data['resList'] = $managerUserGx;
            }
        }else {
            $data['resList'] = $managerUserGx;
        }
        return $this->render('power_edit_bind',$data);
    }
    //楼盘绑定
    public function estat_edit_bind(){
        $id=Context::Get('id');
        $isAll = 0;
        $managerUserGx=$this->db->Name('xcx_manager_building')->select()->where_equalTo('said', $id)->firstRow();
        if(empty($managerUserGx)){
//            $manager=$this->db->Name('xcx_store_agent')->select('said,mgid,type')->where_equalTo('said', $id)->firstRow();
//            $managerSaid=$this->db->Name('xcx_store_agent')->select('said,mgid,type')->where_equalTo('mgid', $manager['mgid'])->where_equalTo('type', '3')->firstRow();
//            $managerBind=$this->db->Name('xcx_manager_building')->select('said,building_ids')->where_equalTo('said', $managerSaid['said'])->firstRow();
//            $res = $this->db->Name('xcx_manager_building')->insert([
//                'said' => $id,
//                'building_ids' => $managerBind['building_ids'],
//                'create_time' => time(),
//            ])->execute();
//            if($res) {
//                $managerUserGx=$this->db->Name('xcx_manager_building')->select()->where_equalTo('said', $id)->firstRow();
//                $temp=[];
//                if(empty($managerUserGx['building_ids'])){
//                    $managerUserGx['is_all']='1';
//                }else{
//                    $managerUserGx['is_all']='0';
//                }
//                $building_ids=explode(',',$managerUserGx['building_ids']);
//                $buildingData=$this->db->Name('xcx_building_building')->select('id,name')->where_in('id',$building_ids)->execute();
//                foreach($buildingData as $val){
//                    $temp[]=['id'=>$val['id'],'name'=>$val['name']];
//                }
//                $managerUserGx['resList']=json_encode($temp,JSON_UNESCAPED_UNICODE);
//            }
            $managerUserGx['resList']=json_encode([],JSON_UNESCAPED_UNICODE);
            $managerUserGx['said'] = $id;
        }else{
            $temp=[];
//            if(empty($managerUserGx['building_ids'])){
//                $managerUserGx['is_all']='1';
//            }else{
//                $managerUserGx['is_all']='0';
//            }
            $building_ids=explode(',',$managerUserGx['building_ids']);
            $buildingData=$this->db->Name('xcx_building_building')->select('id,name')->where_in('id',$building_ids)->execute();
            foreach($buildingData as $val){
                $temp[]=['id'=>$val['id'],'name'=>$val['name']];
            }
            $managerUserGx['resList']=json_encode($temp,JSON_UNESCAPED_UNICODE);
            if('0' == $managerUserGx['store_ids']) {
                $isAll = 1;
            }
        }
        $managerUserGx['is_all'] = $isAll;
        $data['data']=$managerUserGx;
        return $this->render('manager_edit_bind',$data);
    }

    // 店铺绑定页面
    public function store_edit_bind(){
        $id = Context::Get('id');
        $isAll = 0;
        $managerUserGx=$this->db->Name('xcx_manager_building')->select()->where_equalTo('said', $id)->firstRow();
        if(empty($managerUserGx)){
            $managerUserGx['resList']=json_encode([],JSON_UNESCAPED_UNICODE);
            $managerUserGx['said'] = $id;
        }else{
            $temp=[];
            $storeIds = explode(',',$managerUserGx['store_ids']);
            $storeData=$this->db->Name('xcx_store_store')->select('id, title')->where_in('id', $storeIds)->execute();
            foreach($storeData as $val){
                $temp[]=['id'=>$val['id'], 'title'=>$val['title']];
            }
            $managerUserGx['resList'] = json_encode($temp,JSON_UNESCAPED_UNICODE);
            if('0' == $managerUserGx['store_ids']) {
                $isAll = 1;
            }
        }
        $managerUserGx['is_all'] = $isAll;
        $data['data']=$managerUserGx;
        return $this->render('store_edit_bind',$data);
    }

    //删除
    public function power_agent_del(){
        $said=Context::Post('said');
        $res=$this->db->Name('xcx_store_agent')->update([
            'update_time' => time(),
            'is_delete' => 1,
        ])->where_equalTo('said',$said)->where_notEqualTo('type',1)->execute();
        $res2=$this->db->Name('xcx_manager_building')->update([
            'update_time' => time(),
            'is_delete' => 1,
        ])->where_equalTo('said',$said)->execute();

        if($res&&$res2){
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false,'message'=>'删除失败']);
        }
    }



    public function power_doedit_bind(){
        $id=Context::Post('id');
        $data['auth_report_types'] =trim(Context::Post('auth_report_types'),',');
        $res=$this->db->Name('xcx_manager_building')->update($data)->where_equalTo('said',$id)->execute();
        if($res){
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false,'message'=>"保存失败"]);
        }
    }


    public function store_agent_status(){
        $id=Context::Post('id');
        $status=Context::Post('status');
        $res=$this->db->Name('xcx_store_agent')->update(['status'=>$status])->where_equalTo('said',$id)->execute();
        if($res)
            echo json_encode(['success'=>true]);
        else
            echo json_encode(['success'=>false]);
    }
    /*============================================= 人员管理 ========================================================*/
    public function store_agent_index(){
        $id=Context::Get('id');
        $data['data']=$this->db->Name('xcx_store_store')->select()->where_equalTo('id', $id)->firstRow();
        return $this->render('store_agent_index',$data);
    }
    public function manager_agent_index(){
        $id=Context::Get('id');
        $data['data']=$this->db->Name('xcx_store_agent')->select()->where_equalTo('said', $id)->where_equalTo('is_delete', 0)->firstRow();
        return $this->render('manager_agent_index',$data);
    }
    public function store_agent_page(){
        $id=Context::Post('id');
        $mgid = !empty(Context::Post('mgid')) ? Context::Post('mgid') : "";
        if ($mgid) {
            $id = $mgid;
            $idname = 'mgid';
        } else {
            $idname = 'store_id';
        }
        $curr=Context::Post('curr');
        $limit=Context::Post('limit');
        $data = $this->db->Name('xcx_store_agent')->select()->where_equalTo($idname,$id)->where_equalTo('is_delete',0)->page($curr,$limit)->orderBy('type','desc')->orderBy('create_time','desc')->execute();
        $count = $this->db->Name('xcx_store_agent')->select('count(*)')->where_equalTo($idname,$id)->where_equalTo('is_delete',0)->firstColumn();
        if(!empty($data)){
            //获取经纪人微信信息
            $agids=[];$agDict=[];
            foreach($data as $value){
                $agids[]=$value['agent_id'];
            }
            $agids=array_unique($agids);
            $agentRow=(new Query())->Name('xcx_agent_user')->select()->where_in('id',$agids)->execute();
            if(!empty($agentRow)){
                foreach($agentRow as $v){
                    $agDict[$v['id']]=$v;
                }
            }
            foreach($data as &$val){
                $val['nickname']=empty($val['agent_id'])?'未绑定':$agDict[$val['agent_id']]['nickname'];
                $val['headimgurl']=empty($val['agent_id'])?'未绑定':$agDict[$val['agent_id']]['headimgurl'];
                switch ($val['type']) {
                    case 1:
                        $val['type']='店长';
                        break;
                    case 2:
                        $val['type']='组员';
                        break;
                    case 3:
                        $val['type']='组长';
                        break;
                    default:
                        $val['type']='店员';
                        break;
                }

                $val['create_time']=date('Y-m-d H:i:s',$val['create_time']);
                if(!empty($val['update_time'])){
                    $val['update_time']=date('Y-m-d H:i:s',$val['update_time']);
                }else{
                    $val['update_time'] = '';
                }
                $val['active_code_url'] = getActive_code_url($val['code_key']);

            }
            echo json_encode(['success'=>true,'data'=>$data,'count'=>$count]);
        }else{
            echo json_encode(['success'=>false,'curr'=>$curr]);
        }
    }
    public function store_agent_add(){
        $data['store_id'] = !empty(Context::Post('id')) ? Context::Post('id') : "0";
        if($data['store_id']){$type=0;}
        $data['mgid'] = !empty(Context::Post('mgid')) ? Context::Post('mgid') : "0";
        if($data['mgid']){
            $gx = $this->db->Name('xcx_manager_user_gx')->select('id, type, title')->where_equalTo('id', $data['mgid'])->firstRow();
            if(empty($gx)) {
                echo json_encode(['success'=>false,'message'=>"工作组不存在"]);
                exit();
            }
            switch ($gx['type']) {
                // 项目组
                case 0:
                    $type = 2;// 项目组员
                    break;
                // 工作组
                case 1:
                    $type = 5;// 工作组员
                    break;
                default:
                    echo json_encode(['success'=>false,'message'=>"工作组类型非法"]);
                    exit();
                    break;
            }
        }
        $code_key = create_code_key('T1');
        $res2 = $this->db->Name('xcx_store_agent')->insert([
            'store_id' => $data['store_id'],
            'name' => !empty($gx['title']) ? $gx['title'] : '',
            'mgid' => $data['mgid'],
            'agent_id' => 0,
            'type' => $type,//店员类型
            'create_time' => time(),
            'code_key' => $code_key,
        ])->execute();

        if($res2){
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false,'message'=>"保存失败"]);
        }
    }
    public function store_agent_del(){
        $said=Context::Post('said');
        $res=$this->db->Name('xcx_store_agent')->update([
           'update_time' => time(),
           'is_delete' => 1,
        ])->where_equalTo('said',$said)->where_notEqualTo('type',1)->execute();
        if($res){
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false,'message'=>'删除失败']);
        }
    }
    public function store_agent_reset(){
        $said=Context::Post('said');
        if(empty($said)){
            echo json_encode(['success'=>false,'message'=>'重置失败']);
            return;
        }

        $agent = $this->db->Name('xcx_store_agent')->select('agent_id')->where_equalTo('said', $said)->firstRow();

        try {
            $pdo = new DataBase();
            $pdo->beginTransaction();

            //重置绑定的经纪人id
            $code_key = create_code_key('T1');
            $res=$this->db->Name('xcx_store_agent')->update([
                'agent_id' => 0,
                'code_key' => $code_key,
                'create_time' => time(),
                'update_time' => time(),
            ])->where_equalTo('said',$said)->execute();
            if($res){
                if(!empty($agent['agent_id'])) {
                    $res1 = $this->db->Name('xcx_agent_user')->update(['sq_store_status' => 0])->where_equalTo('id', $agent['agent_id'])->execute();
//                    if(empty($res1)) {
//                        $pdo->rollBack();
//                        echo json_encode(['success'=>false,'message'=>'重置失败']);
//                    }
                }
                $pdo->commit();
                echo json_encode(['success'=>true]);
            }else{
                $pdo->rollBack();
                echo json_encode(['success'=>false,'message'=>'重置失败']);
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo json_encode(['success'=>false,'message'=>'重置失败']);
        }

    }

    /*==================================经纪人管理================================================*/
    // 经纪人页面
    public function agent_list_index(){
        return $this->render('agent_list_index');
    }

    // 经纪人列表
    public function agent_list_page() {
        $curr=Context::Post('curr');
        $limit=Context::Post('limit');
        $select['au.nickname']=Context::Post('nickname');
        $select['au.name']=Context::Post('name');
        $select=array_filter($select,function($val){$tmp=$val ===  ''; return !$tmp;});
        if(!empty($select)){
            $userDb=$this->db->Name('xcx_store_agent');
            $userDb=$this->set_agent_where($select,$userDb);
            $data = $userDb->select("au.id, au.nickname, au.headimgurl, au.name, au.sex, au.city, au.country, au.province, au.phone, au.signature, sa.status, au.at_agent, sa.create_time, sa.update_time, sa.said", "sa")->innerJoin("xcx_agent_user", "au", "au.id=sa.agent_id")->page($curr,$limit)->orderBy('sa.create_time','desc')->execute();
            $userDb=$this->set_agent_where($select,$userDb);
            $count = $userDb->select('count(*)', "sa")->innerJoin("xcx_agent_user", "au", "au.id=sa.agent_id")->firstColumn();
        }else{
            $data = $this->db->Name('xcx_store_agent')->select("au.id, au.nickname, au.headimgurl, au.name, au.sex, au.city, au.country, au.province, au.phone, au.signature, sa.status, au.at_agent, sa.create_time, sa.update_time, sa.said", "sa")->innerJoin("xcx_agent_user", "au", "au.id=sa.agent_id")->page($curr,$limit)->orderBy('create_time','desc')->execute();
            $count = $this->db->Name('xcx_store_agent')->select('count(*)', "sa")->innerJoin("xcx_agent_user", "au", "au.id=sa.agent_id")->firstColumn();
        }
        if(!empty($data)){
            foreach($data as &$val){
                $val['sex']=empty($val['sex'])?'未知':($val['sex']=='1'?'男':'女');
                $val['name']=empty($val['name'])?'':$val['name'];
                $val['phone']=empty($val['phone'])?'':$val['phone'];
                $val['signature']=empty($val['signature'])?'':$val['signature'];
                $val['create_time']=date('Y-m-d H:i:s',$val['create_time']);
                $val['update_time']=date('Y-m-d H:i:s',$val['update_time']);
            }
            echo json_encode(['success'=>true,'data'=>$data,'count'=>$count]);
        }else{
            echo json_encode(['success'=>false,'curr'=>$curr]);
        }
    }

    // 经纪人状态变更 -1-禁用 0-申请中 1-启用
    public function agent_set_status(){
        $id=Context::Post('id');
        $status=Context::Post('status');
        $res=$this->db->Name('xcx_store_agent')->update(['status'=>$status])->where_equalTo('said',$id)->execute();
        if($res)
            echo json_encode(['success'=>true]);
        else
            echo json_encode(['success'=>false]);
    }
    ##########一键复制###################################################

    public function one_click_copy_index(){
        return $this->render('one_click_copy_index');
    }

    public function get_one_click_copy_list(){
        $list = $this->db->Name('report_copy')->select('id,name,status')->execute();
        echo json_encode(['success'=>true,'data'=>$list]);
    }

    public function copy_switch(){
        $id     = Context::Post('id');
        $status = Context::Post('status');
        $res    = $this->db->Name('report_copy')->update(['status'=>$status])
            ->where_equalTo('id',$id)->execute();
        if($res)
            echo json_encode(['success'=>true]);
        else
            echo json_encode(['success'=>false]);
    }

    // 非超级管理员需要判断所属城市
    protected function checkCity($city)
    {
        return ['success'=>true];
        if(!empty($this->gid)) {
            if(empty($this->city)) {
                return ['success'=>false,'message'=>'没有所属城市，不可修改'];
            }
            if($city != $this->city) {
                return ['success'=>false,'message'=>'您没有该城市权限'];
            }
        }
        return ['success'=>true];
    }
}