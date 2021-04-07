<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of main
 *
 * @author Goods0
 */
include 'Common.php';
class BuildingAjax extends Common{
    //获取楼盘详情页数据
    public function getBuildingDetail(){
        $id=Context::Post('id');    //楼盘id
        $agent_id=Context::Post('agent_id');    //经纪人id
        $user_id=$this->uid();    //用户id
        //获取该用户是否关注楼盘
        $collection=$this->db->Name('xcx_user_building_collection')->select()->where_equalTo('user_id',$user_id)->where_equalTo('building_id',$id)->where_equalTo('agent_id',$agent_id)->firstRow();
        if(empty($collection) || empty($collection['status']))
            $is_collection=false;
        else
            $is_collection=true;
        $data['is_collection']=$is_collection;
        //获取经纪人与楼盘对应信息
        $circularize=$this->db->Name('xcx_building_circularize')->select()->where_equalTo('user_id',$user_id)->where_equalTo('building_building_id',$id)->where_equalTo('agent_user_id',$agent_id)->where_equalTo('user_type','1')->firstRow();
        if(empty($circularize)){
            $data['agentBuildingInfo']=['kaipan_notice'=>'0','jianjia_notice'=>'0'];
        }else{
            $data['agentBuildingInfo']=['kaipan_notice'=>$circularize['kaipan_notice'],'jianjia_notice'=>$circularize['jianjia_notice']];
        }
        //获取经纪人信息
        $data['agentInfo']=$this->getAgentInfo($agent_id);
        //获取楼盘信息
        $data['buildingInfo']=$this->db->Name('xcx_building_building')->select()->where_equalTo('id',$id)->firstRow();
        $data['buildingInfo']['fold']=floatval($data['buildingInfo']['fold']);
        unset($data['buildingInfo']['commission']);//不显示佣金
        $data['buildingInfo']['kaipang_time']=date('Y-m-d',$data['buildingInfo']['kaipang_time']);
        // 楼盘信息的房屋类型转换
        $data['buildingInfo']['house_type_str'] = $data['buildingInfo']['house_type'];
        $houseType = explode(',', $data['buildingInfo']['house_type']);
        $data['buildingInfo']['house_type'] = !empty($houseType['0']) ? $houseType['0'] : "";
        //获取楼盘轮播图信息
        $shuffleInfo=$this->db->Name('xcx_building_shuffle')->select()->where_equalTo('building_id',$id)->execute();
        if(empty($shuffleInfo)){$shuffleInfo=[];}
        $data['shuffleInfo']=$shuffleInfo;
        //获取主力户型信息
        $doorInfo=$this->db->Name('xcx_building_floor')->select("bd.*,bf.year_number","bf")->where_equalTo('bf.building_id',$id)->rightJoin("xcx_building_unit","bu","bf.id=bu.floor_id")->rightJoin("xcx_building_door","bd","bu.id=bd.unit_id")->orderBy('bd.is_hot','desc')->page(1,4)->execute();
        if(!empty($doorInfo)){
            foreach($doorInfo as &$doorval){
                $doorval['construction_area']=floatval($doorval['construction_area']);
            }
        }else{
            $doorInfo=[];
        }
        $data['doorInfo']=$doorInfo;
        //获取楼盘周边地图信息
        $mapInfoArr=[];$temp=[];
        $mapInfo=$this->db->Name('xcx_building_map')->select()->where_equalTo('building_id',$id)->execute();
        if(!empty($mapInfo)){
            foreach($mapInfo as $mapval){
                $mapval['distance']=intval($mapval['distance']);
                $temp[$mapval['keyword']][]=$mapval;
            }
            foreach($temp as $k=>$v){
                $mapInfoArr[]=['title'=>$k,'is_show'=>false,'data'=>$v];
            }
            $mapInfoArr[0]['is_show']=true;
        }
        $data['mapInfo']=$mapInfoArr;
        //获取楼栋信息
        $floorInfo=$this->db->Name('xcx_building_floor')->select()->where_equalTo('building_id',$id)->where_equalTo('status',1)->execute();
        if(empty($floorInfo)){$floorInfo=[];}
        $data['floorInfo']=$floorInfo;
        //获取楼盘推荐信息
        $lpList=$this->db->Name('xcx_agent_building')->select("bb.*","ab")->leftJoin("xcx_building_building","bb","ab.building_id=bb.id")->where_equalTo('ab.agent_id',$agent_id)->where_notEqualTo('ab.building_id',$id)->page(1,10)->orderBy('ab.create_time','desc')->execute();
        if(empty($lpList)){
            $lpList=[];
        }else{
            foreach($lpList as &$val){
                $val['fold']=floatval($val['fold']);
                $val['views_number']=$this->formatting_number($val['views_number']);
            }
        }
        $data['lpList']=$lpList;
        echo json_encode($data,JSON_UNESCAPED_UNICODE);
    }
    //收藏|未收藏事件
    public function updateCollection(){
        $id=Context::Post('id');    //楼盘id
        $status=Context::Post('status');    //修改值
        $user_id=$this->uid();    //用户id
        $agent_id=Context::Post('agent_id');    //经纪人id
        $res=null;
        if(!empty($status)){    //收藏操作
            $is_cunzai=$this->db->Name('xcx_user_building_collection')->select()->where_equalTo('user_id',$user_id)->where_equalTo('building_id',$id)->where_equalTo('agent_id',$agent_id)->firstRow();
            if(!empty($is_cunzai)){ //修改操作
                $res=$this->db->Name('xcx_user_building_collection')->update(['status'=>$status,'update_time'=>time()])->where_equalTo('user_id',$user_id)->where_equalTo('building_id',$id)->where_equalTo('agent_id',$agent_id)->execute();
            }else{  //新增操作
                $res=$this->db->Name('xcx_user_building_collection')->insert(['user_id'=>$user_id,'building_id'=>$id,'agent_id'=>$agent_id,'create_time'=>time(),'update_time'=>time()])->execute();
            }
        }else{  //取消收藏操作
            $res=$this->db->Name('xcx_user_building_collection')->update(['status'=>$status,'update_time'=>time()])->where_equalTo('user_id',$user_id)->where_equalTo('building_id',$id)->where_equalTo('agent_id',$agent_id)->execute();
        }
        if(!empty($res))
            echo json_encode(['success'=>true]);
        else
            echo json_encode(['success'=>false,'message'=>'保存失败']);
    }
    //楼盘浏览量增加方法
    public function addViewsNumber(){
        $id=Context::Post('id');    //楼盘id
        $sql="UPDATE ".Table_Pre."xcx_building_building SET `views_number` = `views_number`+1 WHERE `id` = :id";
        $arr=[":id"=>$id];
        DataBase::Update($sql,$arr);
        echo json_encode(['success'=>true]);
    }
    //修改开盘/降价提醒
    public function updateNotice(){
        $id=Context::Post('id');    //楼盘id
        $agent_id=Context::Post('agent_id');    //经纪人id
        $user_id=$this->uid();    //用户id
        $tag=Context::Post('tag');    //标识
        $notice=Context::Post('notice');    //提醒开关
        if($tag=="kp"){
            $data['kaipan_notice']=$notice;
            $parameter['kaipan_notice']=$notice;
            $parameter['jianjia_notice']='0';
        }else{
            $data['jianjia_notice']=$notice;
            $parameter['kaipan_notice']='0';
            $parameter['jianjia_notice']=$notice;
        }
        $circularize=$this->db->Name('xcx_building_circularize')->select()->where_equalTo('user_id',$user_id)->where_equalTo('building_building_id',$id)->where_equalTo('agent_user_id',$agent_id)->where_equalTo('user_type','1')->firstRow();
        if(empty($circularize)){     //添加操作
            $res=$this->db->Name('xcx_building_circularize')->insert(['user_id'=>$user_id,'building_building_id'=>$id,'agent_user_id'=>$agent_id,'user_type'=>'1','kaipan_notice'=>$parameter['kaipan_notice'],'jianjia_notice'=>$parameter['jianjia_notice'],'create_time'=>time(),'update_time'=>time()])->execute();
        }else{  //修改
            $res=$this->db->Name('xcx_building_circularize')->update($data)->where_equalTo('id',$circularize['id'])->execute();
        }
        if($res){
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false,'message'=>'保存失败']);
        }
    }
    //获取楼盘所对应的户型信息
    public function getBuildingDoor(){
        $id=Context::Post('id');    //楼盘id
        $doorInfo=$this->db->Name('xcx_building_floor')->select("bd.*","bf")->where_equalTo('bf.building_id',$id)->rightJoin("xcx_building_unit","bu","bf.id=bu.floor_id")->rightJoin("xcx_building_door","bd","bu.id=bd.unit_id")->orderBy('bd.is_hot','desc')->execute();
        if(!empty($doorInfo)){
            foreach($doorInfo as &$doorval){
                $doorval['construction_area']=floatval($doorval['construction_area']);
            }
        }else{
            $doorInfo=[];
        }
        $data['doorInfo']=$doorInfo;
        echo json_encode($data,JSON_UNESCAPED_UNICODE);
    }
    //获取户型的详细信息
    public function getBuildingDoorDetail(){
        $id=Context::Post('id');    //户型id
        //获取户型信息
        $data['doorInfo']=$this->db->Name('xcx_building_door')->select()->where_equalTo('id',$id)->firstRow();
        $data['doorInfo']['construction_area']=floatval($data['doorInfo']['construction_area']);
        //获取户型轮播图
        $data['doorImgInfo']=$this->db->Name('xcx_building_doorimg')->select()->where_equalTo('door_id',$id)->orderBy('sort')->execute();
        //获取楼盘信息
        $data['buildingInfo']=$this->db->Name('xcx_building_door')->select("bb.*","bd")->rightJoin("xcx_building_unit","bu","bd.unit_id=bu.id")->rightJoin("xcx_building_floor","bf","bu.floor_id=bf.id")->rightJoin("xcx_building_building","bb","bf.building_id=bb.id")->firstRow();
        $data['buildingInfo']['fold']=floatval($data['buildingInfo']['fold']);
        unset($data['buildingInfo']['commission']);//不显示佣金
        //获取楼盘下的其余2个户型
        $remainingDoor=$this->db->Name('xcx_building_floor')->select("bd.*","bf")->where_equalTo('bf.building_id',$data['buildingInfo']['id'])->where_notEqualTo('bd.id',$id)->rightJoin("xcx_building_unit","bu","bf.id=bu.floor_id")->rightJoin("xcx_building_door","bd","bu.id=bd.unit_id")->orderBy('bd.is_hot','desc')->orderBy('is_hot','desc')->page(1,2)->execute();
        if(!empty($remainingDoor)){
            foreach($remainingDoor as &$doorval){
                $doorval['construction_area']=floatval($doorval['construction_area']);
            }
        }else{
            $remainingDoor=[];
        }
        $data['remainingDoorInfo']=$remainingDoor;
        echo json_encode($data,JSON_UNESCAPED_UNICODE);
    }
    //获取楼栋详情数据
    public function getBuildingFloor(){
        $id=Context::Post('id');    //楼盘id
        $agent_id=Context::Post('agent_id');    //经纪人id
        //获取经纪人信息
        $data['agentInfo']=$this->getUserInfo($agent_id)['userInfo'];
        //获取楼盘信息
        $data['buildingInfo']=$this->db->Name('xcx_building_building')->select()->where_equalTo('id',$id)->firstRow();
        //获取楼栋信息
        $floorInfo=$this->db->Name('xcx_building_floor')->select("bf.*,COUNT(bu.id) unit_num","bf")->leftJoin("xcx_building_unit","bu","bf.id=bu.floor_id")->where_equalTo('bf.building_id',$id)->where_equalTo('bf.status',1)->groupBy("bf.id")->execute();
        if(empty($floorInfo)){
            $floorInfo=[];
        }else{
            foreach($floorInfo as &$floorVal){
                $floorVal['kaipan_time']=date('y.m.d',$floorVal['kaipan_time']);
                $floorVal['jiaofan_time']=date('y.m.d',$floorVal['jiaofan_time']);
            }
        }
        $data['floorInfo']=$floorInfo;
        echo json_encode($data,JSON_UNESCAPED_UNICODE);
    }
    //获取楼栋单元户型数据
    public function getBuildingUnit(){
        $floor_id=Context::Post('id');    //楼栋id
        $agent_id=Context::Post('agent_id');    //经纪人id
        //获取经纪人信息
        $data['agentInfo']=$this->getUserInfo($agent_id)['userInfo'];
        //获取楼栋单元信息
        $unitInfo=[];
        $unitTemp=$this->db->Name('xcx_building_unit')->select()->where_equalTo('floor_id',$floor_id)->orderBy('sort')->execute();
        if(!empty($unitTemp)){
            foreach($unitTemp as $val){
                $unitInfo[]=['title'=>$val['title'],'floor_number'=>$val['floor_number'],'stairs_number'=>$val['stairs_number'],'data'=>$this->getBuildingDoorData($val['id'])];
            }
        }
        $data['unitInfo']=$unitInfo;
        echo json_encode($data,JSON_UNESCAPED_UNICODE);
    }
    //根据户型单元id获取所有户型
    public function getBuildingDoorData($id){
        $res=[];
        $doorData=$this->db->Name('xcx_building_door')->select()->where_equalTo('unit_id',$id)->orderBy('sort')->execute();
        if(!empty($doorData)){
            foreach($doorData as &$doorVal){
                $doorVal['construction_area']=floatval($doorVal['construction_area']);
            }
            $res=$doorData;
        }
        return $res;
    }
    //获取楼盘详情页子数据
    public function getBuildingDetail2(){
        $id=Context::Post('id');    //楼盘id
        //获取楼盘信息
        $data['buildingInfo']=$this->db->Name('xcx_building_building')->select()->where_equalTo('id',$id)->firstRow();
        $data['buildingInfo']['fold']=floatval($data['buildingInfo']['fold']);
        unset($data['buildingInfo']['commission']);//不显示佣金
        $data['buildingInfo']['kaipang_time']=date('Y-m-d',$data['buildingInfo']['kaipang_time']);
        $data['buildingInfo']['jiaofang_time']=date('Y-m-d',$data['buildingInfo']['jiaofang_time']);
        $data['buildingInfo']['license_time']=date('Y-m-d',$data['buildingInfo']['license_time']);
        //获取楼盘周边地图信息
        $mapInfoArr=[];$temp=[];
        $mapInfo=$this->db->Name('xcx_building_map')->select()->where_equalTo('building_id',$id)->execute();
        if(!empty($mapInfo)){
            foreach($mapInfo as $mapval){
                $mapval['distance']=intval($mapval['distance']);
                $temp[$mapval['keyword']][]=$mapval;
            }
            foreach($temp as $k=>$v){
                if($k=="公交"){
                    $mapInfoArr[]=['title'=>$k,'img'=> '/image/icon-map-traffic.png', 'show_img'=>'/image/icon-map-traffic_actice.png','data'=>$v];
                }else if($k=="学校"){
                    $mapInfoArr[]=['title'=>$k,'img'=> '/image/icon-map-education.png', 'show_img'=>'/image/icon-map-education_active.png','data'=>$v];
                }else if($k=="医院"){
                    $mapInfoArr[]=['title'=>$k,'img'=> '/image/icon-map-hospital.png', 'show_img'=>'/image/icon-map-hospital_active.png','data'=>$v];
                }else if($k=="购物"){
                    $mapInfoArr[]=['title'=>$k,'img'=> '/image/icon-map-shopping.png', 'show_img'=>'/image/icon-map-shopping_active.png','data'=>$v];
                }else if($k=="美食"){
                    $mapInfoArr[]=['title'=>$k,'img'=> '/image/icon-map-food.png', 'show_img'=>'/image/icon-map-food_active.png','data'=>$v];
                }
            }
        }
        $data['mapInfo']=$mapInfoArr;
        echo json_encode($data,JSON_UNESCAPED_UNICODE);
    }
    //获取楼盘页面所需数据
    public function getBuildingHome(){
        $agent_id=Context::Post('agent_id');
        //获取经纪人姓名
        $agentName="";
        $agentRow=$this->db->Name('xcx_agent_user')->select()->where_equalTo('id',$agent_id)->firstRow();
        $agentName=empty($agentRow['name'])?$agentRow['nickname']:$agentRow['name'];
        $data['agentName']=$agentName;  //标题
        $data['sortData']['2']=[
              ['text'=>'单价', 'data'=>[['val'=>'0-10000','name'=>'1万以内'],['val'=>'10000-30000','name'=>'1万-3万'], ['val'=>'30000-50000','name'=>'3万-5万'], ['val'=>'50000-70000','name'=>'5万-7万'], ['val'=>'70000-90000','name'=>'7万-9万'], ['val'=>'90000-0','name'=>'9万以上'],  ['val'=>'0-0','name'=>'不限']]]
//              ['text'=>'总价', 'data'=>['100万以内', '100万-150万', '150万-200万', '200万-300万', '300万-500万', '500万以上', '不限']]
        ];
        $data['sortData']['3']= ["普通住宅", "别墅", "商铺", "写字楼"];
        $data['sortData']['4']= [
          ['title'=>'面积', 'data'=>[["val"=>'0-50','title'=>"50m²以下", 'is_checked'=>false], ["val"=>'50-70','title'=>"50-70m²", 'is_checked'=>false], ["val"=>'70-90','title'=>"70-90m²", 'is_checked'=>false], ["val"=>'90-110','title'=>"90-110m²", 'is_checked'=>false], ["val"=>'110-130','title'=>"110-130m²", 'is_checked'=>false], ["val"=>'130-150','title'=>"130-150m²", 'is_checked'=>false], ["val"=>'150-200','title'=>"150-200m²", 'is_checked'=>false], ["val"=>'200-0','title'=>"200m²以上", 'is_checked'=>false]] ],
          ['title'=>'特色标签', 'data'=> [['title'=> "闪电结佣", 'is_checked'=> false], ['title'=> "电商优惠", 'is_checked'=> false ], ['title'=>"九房验真", 'is_checked'=> false],['title'=> "带看卷", 'is_checked'=>false]]],
          ['title'=> '装修情况', 'data'=> [['title'=> "毛坯", 'is_checked'=> false], ['title'=> "简装", 'is_checked'=>false], ['title'=> "中装", 'is_checked'=> false], ['title'=> "精装", 'is_checked'=> false]]],
          ['title'=>'销售状态', 'data'=>[['title'=>"在售", 'is_checked'=>false], ['title'=>"售完", 'is_checked'=>false]]]
//          ['title'=>'关注状态', 'data'=>[['title'=>"已关注", 'is_checked'=>false], ['title'=>"未关注", 'is_checked'=>false]]]
        ];
        $data['sortData']['5']=["默认排序", "佣金最高", "人气最旺"];
        echo json_encode($data,JSON_UNESCAPED_UNICODE);
    }
    //获取经纪人所对应的楼盘信息
    public function getBuildingData(){
       /* $moreData = Context::Post('moreData',$isHave = '',$open_filter =1,function($val){
            return json_decode($val,1);
        });*/
        $moreData = Context::Post('moreData');
//        print_r($moreData);
//        return;

        $agent_id=Context::Post('agent_id');
        $page=Context::Post('page');
        $is_serach=empty(Context::Post('is_serach'))?false:true;
        //条件搜索
        $myDB=$this->db->Name('xcx_agent_building')->select("a.id,a.agent_id,a.building_id,a.create_time,b.name,b.sales_status,b.pic,b.house_type,b.city,b.area,b.fold,b.views_number","a")->leftJoin("xcx_building_building","b","a.building_id=b.id")->page($page,10)->where_equalTo('a.agent_id',$agent_id)->where_equalTo('a.is_focus',1)->where_equalTo('is_delete', 0);
//				$myDB=$this->db->Name('xcx_building_building')->select("id,name,sales_status,pic,house_type,city,area,fold,commission,views_number","b")->page($page,10)->where_equalTo('status',1);
        if(!empty(Context::Post('searchText'))){    //楼盘名称搜索
            $myDB->where_like('name',"%".Context::Post('searchText')."%");
        }
        if(!empty(Context::Post('city'))){    //城市搜索
            $myDB->where_like('city',"%".Context::Post('city')."%");
        }
        if(!empty(Context::Post('area'))){    //区域搜索
            $myDB->where_like('area',"%".Context::Post('area')."%");
        }
        if(!empty(Context::Post('fold'))){    //价格搜索
            $fold=explode('-',Context::Post('fold'));
            if(!empty($fold[1])){
                $myDB->where_greatThanOrEqual('fold',$fold[0]);
                $myDB->where_lessThanOrEqual('fold',$fold[1]);
            }else{
                if(!empty($fold[0])){
                    $myDB->where_greatThanOrEqual('fold',$fold[0]);
                }
            }
        }
        if(!empty(Context::Post('house_type'))){    //房屋类型搜索
            $myDB->where_like('house_type',"%".Context::Post('house_type')."%");
        }

        $moreData = htmlspecialchars_decode($moreData);
        $moreData = json_decode($moreData, 1);
        if(!empty($moreData)){    //更多搜索
//            $moreData = Context::Post('moreData');
            $buildingIds=[];
            foreach($moreData as $value){
                if($value['title']=='面积'){
                    $is_arr=[];
                    $doorRow=(new Query())->Name('xcx_building_door')->select("bb.id","bd");
                    $where_express="";
                    foreach($value['data'] as $v){
                        if(!empty($v['is_checked'])){
                            $is_arr[]=true;
                            $construction_area=explode('-',$v['val']);
                            if(!empty($construction_area[1])){
                                $where_express.=" (construction_area>=".$construction_area[0].' AND construction_area<'.$construction_area[1]." ) OR";
                            }else{
                                $where_express.=" (construction_area>=".$construction_area[0]." ) OR";
                            }
                        }
                    }
                    if(!empty($where_express)){
                        $where_express=trim($where_express,"OR");
                        $where_express="(".$where_express.") ";
                        $doorRes=$doorRow->where_express($where_express)->leftJoin("xcx_building_unit","bu","bd.unit_id=bu.id")->leftJoin("xcx_building_floor","bf","bu.floor_id=bf.id")->leftJoin("xcx_building_building","bb","bf.building_id=bb.id")->execute();
                        if(!empty($doorRes)){
                            foreach($doorRes as $vv){
                                $buildingIds[]=$vv['id'];
                            }
                        }
                    }
                    if(!empty($is_arr)){
                        $buildingIds=array_unique($buildingIds);
                        if(empty($buildingIds)){$buildingIds=[0];}
                        $myDB->where_in('b.id',$buildingIds);
                    }
                }
                if($value['title']=='特色标签'){
                    $where_express="";
                    $is_arr2=[];
                    foreach($value['data'] as $v){
                        if(!empty($v['is_checked'])){
                            $is_arr2[]=true;
                            $where_express.=" flag like \"%".$v['title']."%\" OR";
                        }
                    }
                    if(!empty($is_arr2)){
                        $where_express=trim($where_express,"OR");
                        $where_express="(".$where_express.") ";
                        $myDB->where_express($where_express);
                    }
                }
                if($value['title']=='装修情况'){
                    $where_express="";
                    $is_arr3=[];
                    foreach($value['data'] as $v){
                        if(!empty($v['is_checked'])){
                            $is_arr3[]=true;
                            $where_express.=" decoration=\"".$v['title']."\" OR";
                        }
                    }
                    if(!empty($is_arr3)){
                        $where_express=trim($where_express,"OR");
                        $where_express="(".$where_express.") ";
                        $myDB->where_express($where_express);
                    }
                }
                if($value['title']=='销售状态'){
                    $where_express="";
                    $is_arr4=[];
                    foreach($value['data'] as $v){
                        if(!empty($v['is_checked'])){
                            $is_arr4[]=true;
                            $where_express.=" sales_status=\"".$v['title']."\" OR";
                        }
                    }
                    if(!empty($is_arr4)){
                        $where_express=trim($where_express,"OR");
                        $where_express="(".$where_express.") ";
                        $myDB->where_express($where_express);
                    }
                }
            }
        }
        if(!empty(Context::Post('my_sort'))){    //排序搜索
            $my_sort=Context::Post('my_sort');
            if($my_sort=="默认排序"){
                $myDB->orderBy('sort','desc');
            }else if($my_sort=="人气最旺"){
                $myDB->orderBy('views_number','desc');
            }
        }else{
            $myDB->orderBy('sort','desc');
        }
        $buildingIndo=$myDB->execute();
        if(!empty($buildingIndo)){
            foreach($buildingIndo as &$val){
                $val['fold']=floatval($val['fold']);
                $val['views_number']=$this->formatting_number($val['views_number']);
                // 房屋类型
                $houseType = explode(',', $val['house_type']);
                $val['house_type'] = !empty($houseType['0']) ? $houseType['0'] : "";
            }
//            echo json_encode(['success'=>true,'is_serach'=>$is_serach,'data'=>$buildingIndo],JSON_UNESCAPED_UNICODE);
            return $this->success($buildingIndo);
        }else{
            if(!empty($is_serach)){
//                echo json_encode(['success'=>true,'is_serach'=>$is_serach,'data'=>[]]);
            }else{
//                echo json_encode(['success'=>false]);
            }
            return $this->success([], '无数据');
        }
    }
    //添加楼盘意见反馈数据
    public function addFeedback(){
        $data['building_id']=Context::Post('id');    //楼盘id
        $data['user_id']=$this->uid();    //用户id
        $data['matter_type']=Context::Post('matter_type');
        $data['building_correct_info']=Context::Post('building_correct_info');
        $data['client_side_type']=1;
        $data['create_time']=time();
        $data['update_time']=time();
        $res=$this->db->Name('xcx_building_correct')->insert($data)->execute();
        if($res)
            echo json_encode(['success'=>true]);
        else
            echo json_encode(['success'=>false]);
    }
}