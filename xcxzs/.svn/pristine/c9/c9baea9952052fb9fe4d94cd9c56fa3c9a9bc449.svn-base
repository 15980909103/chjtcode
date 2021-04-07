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
class AgentAjax extends Common{
    //添加经纪人对应的客户 形成客户关系
    public function addCustomer(){
		$agent_id=Context::Post('agent_id');
        $user_id=$this->uid();
        $customerRow=$this->db->Name('xcx_agent_customer')->select()->where_equalTo('agent_id',$agent_id)->where_equalTo('user_id',$user_id)->firstRow();
        if(empty($customerRow)){
            $source = intval(Context::Post('source')); //来源 0自己关注，1：经纪人名片 2：文章 3：楼盘
            if(!in_array($source,['0','1','2','3'])){
                $source = 0;
            }

            $res=$this->db->Name('xcx_agent_customer')->insert(['agent_id'=>$agent_id,'user_id'=>$user_id,'source'=>$source,'agent_status'=>1,'user_status'=>1,'create_time'=>time(),'update_time'=>time()])->execute();
            if($res)
                echo json_encode(['success'=>true],JSON_UNESCAPED_UNICODE);
            else
                echo json_encode(['success'=>false],JSON_UNESCAPED_UNICODE);
        }else{
            if(empty($customerRow['user_status'])){
                $res=$this->db->Name('xcx_agent_customer')->update(['user_status'=>1])->where_equalTo('id',$customerRow['id'])->execute();
                if($res)
                    echo json_encode(['success'=>true],JSON_UNESCAPED_UNICODE);
                else
                    echo json_encode(['success'=>false],JSON_UNESCAPED_UNICODE);
            }else{
                echo json_encode(['success'=>false],JSON_UNESCAPED_UNICODE);
            }
        }
    }
    //获取用户对应的经纪人数据
    public function getAgentData(){
        $page=Context::Post('page');
        $user_id=$this->uid();
				DataBase::log(__FILE__.__LINE__,"user_id:".$user_id);
        if(empty($user_id)){echo json_encode(['success'=>false]);exit;}
        $agentList=$this->db->Name('xcx_agent_customer as c')->select("c.id,c.agent_id,c.user_top,c.source,c.create_time,u.nickname,u.name,u.headimgurl,u.special_label,s.title,s.province,s.city,s.area")->leftJoin("xcx_agent_user","u","c.agent_id=u.id")->leftJoin("xcx_store_agent","a","c.agent_id=a.agent_id")->leftJoin("xcx_store_store","s","a.store_id=s.id")->page($page,10)->where_equalTo('c.user_id',$user_id)->where_equalTo('c.user_status',1)->orderBy('c.user_top','desc')->orderBy('c.create_time','desc')->execute();
        if(!empty($agentList)){
            foreach($agentList as &$val){
                $val['special_label']=empty($val['special_label'])?[]:explode(',',$val['special_label']);
                $val['source']=$val['source']==1?'二维码扫描':'未知';
                $val['name']=empty($val['name'])?$val['nickname']:$val['name'];
                $val['create_time']=date('Y/m/d',$val['create_time']);
            }
            echo json_encode(['success'=>true,'agentList'=>$agentList],JSON_UNESCAPED_UNICODE);
        }else{
            echo json_encode(['success'=>false,'agentList'=>[]],JSON_UNESCAPED_UNICODE);
        }
    }
    //经纪人置顶
    public function setAgentTop(){
        $id=Context::Post('id');
        $user_id=$this->uid();
        $data['user_top']='0';
        $res=$this->db->Name('xcx_agent_customer')->update($data)->where_equalTo('user_id',$user_id)->execute();
        $data2['user_top']='1';
        $res2=$this->db->Name('xcx_agent_customer')->update($data2)->where_equalTo('id',$id)->execute();
        if($res && $res2){
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false]);
        }
    }
    //删除经纪人-改状态数据
    public function delUserStatus(){
        $user_id=$this->uid();
        $id=Context::Post('id');
        $data['user_status']=0;
        $data['update_time']=time();
        $res=$this->db->Name('xcx_agent_customer')->update($data)->where_equalTo('id',$id)->where_equalTo('user_id',$user_id)->execute();
        if($res){
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false]);
        }
    }
    //获取经纪人详情页面信息
    public function getAgentDetailData(){
        $data=[];
        $agent_id=Context::Post('agent_id');
        $user_id=$this->uid();
        //获取10条经纪人分享的文章如果不够则用最高点击量的文章
        $shareArticle=$this->db->Name('xcx_agent_share')->select()->where_equalTo('share_type','2')->where_equalTo('client_type','2')->where_equalTo('agent_id',$agent_id)->page(1,self::MYLIMIT)->orderBy('create_time','desc')->execute();
        if(!empty($shareArticle)){
            $articleIds=[];
            foreach($shareArticle as $val){
                $articleIds[]=$val['article_id'];
            }
            $articleIds=array_unique($articleIds);
            //$zxNeowData=$this->db->Name('xcx_article_article')->select()->where_in('id',$articleIds)->execute();
            $zxNeowData=$this->db2->Name('news')->select('id,area_id,title,wap_pic,operator,addtime')->where_in('id',$articleIds)->execute();
            foreach($zxNeowData as &$value){
                $value['cover']=$value['wap_pic'];
                $value['aname']='九房网';
                $value['logo']='/upload/default/default_head.png';
                $value['release_time']=$this->format_dates(strtotime($value['addtime']));
            }

        }else{
            //$zxNeowData=$this->db->Name('xcx_article_article')->select()->page(1,10)->where_equalTo('status',1)->orderBy('read_num','desc')->execute();
            $zxNeowData=$this->db2->Name('news')->select('id,area_id,title,wap_pic,operator,addtime')->page(1,10)->where_equalTo('status',1)->orderBy('addtime','desc')->execute();
            foreach($zxNeowData as &$value){
                $value['cover']=$value['wap_pic'];
                $value['aname']='九房网';
                $value['logo']='/upload/default/default_head.png';
                $value['release_time']=$this->format_dates(strtotime($value['addtime']));
            }
        }
        if(empty($zxNeowData)){$zxNeowData=[];}
        //统计房源、客户、浏览量数
        $fyNum=$this->db->Name('xcx_agent_building')->select('count(*)')->where_equalTo('agent_id',$agent_id)->firstColumn();
        if(empty($fyNum)){$fyNum=0;}
        $khNum=$this->db->Name('xcx_agent_customer')->select('count(*)')->where_equalTo('agent_id',$agent_id)->firstColumn();
        if(empty($khNum)){$khNum=0;}

        $data=array_merge($data,$this->getAgentInfo($agent_id),['zxInfo'=>$zxNeowData,'fyNum'=>$fyNum,'khNum'=>$khNum,'user_id'=>$user_id]);
        echo json_encode(['success'=>true,'data'=>$data],JSON_UNESCAPED_UNICODE);
    }
    //获取经纪人对应的楼盘数据
    public function getAgentBuildingData(){
        $agent_id=Context::Post('agent_id');
        $page=Context::Post('page');
        $buildingIndo=$this->db->Name('xcx_agent_building as a')->select("a.id,a.agent_id,a.building_id,b.name,b.sales_status,b.pic,b.house_type,b.city,b.area,b.fold,b.views_number")->leftJoin("xcx_building_building","b","a.building_id=b.id")->page($page,10)->where_equalTo('a.agent_id',$agent_id)->where_equalTo('b.status', '1')->where_equalTo('a.is_focus',1)->where_equalTo('is_delete', 0)->orderBy('a.create_time','desc')->execute();
//				$buildingIndo=$this->db->Name('xcx_building_building')->select("id as building_id,name,sales_status,pic,house_type,city,area,fold,views_number")->page($page,10)->where_equalTo('status',1)->orderBy('building_id','desc')->execute();
        if(!empty($buildingIndo)){
            foreach($buildingIndo as &$val){
                $val['fold']=floatval($val['fold']);
                $val['views_number']=$this->formatting_number($val['views_number']);
                // 房屋类型
                $houseType = explode(',', $val['house_type']);
                $val['house_type'] = !empty($houseType['0']) ? $houseType['0'] : "";
            }
            echo json_encode(['success'=>true,'data'=>$buildingIndo],JSON_UNESCAPED_UNICODE);
        }else{
            echo json_encode(['success'=>false]);
        }
    }
    //获取经纪人与用户头像名称信息
    public function getPortraitData(){
        $agent_id=Context::Post('agent_id');    //经纪人id
        $user_id=$this->uid();        //用户id
        $agentInfo=$this->db->Name('xcx_agent_user')->select('headimgurl,nickname,name')->where_equalTo('id',$agent_id)->firstRow();
        if(empty($agentInfo)){
            $agentInfo=[];
        }else{
            $agentInfo['name']=empty($agentInfo['name'])?$agentInfo['nickname']:$agentInfo['name'];
        }
        $data['agentInfo']=$agentInfo;
        $userInfo=$this->db->Name('xcx_user')->select('avatarUrl,nickName')->where_equalTo('id',$user_id)->firstRow();
        if(empty($userInfo)){$userInfo=[];}
        $data['userInfo']=$userInfo;
        echo json_encode($data,JSON_UNESCAPED_UNICODE);
    }
    //获取经纪人与用户的聊天记录
    public function getChatMessage(){
        $agent_id=Context::Post('agent_id');    //经纪人id
        $user_id=$this->uid();        //用户id
        $page=Context::Post('page');
        $data=$this->db->Name('xcx_chat_record')->select()->where_equalTo('agent_id',$agent_id)->where_equalTo('user_id',$user_id)->where_equalTo('user_status','1')->orderBy('create_time','desc')->page($page,20)->execute();
        if(!empty($data)){
            $data=array_reverse($data);
            $earliestTime=intval($data[0]['create_time']);
            foreach($data as $key=>&$val){
                $val['sender']=$val['from_type']=='1'?'self':'ta';
                $val['success']=true;
                if((intval($val['create_time'])-$earliestTime)>1800 || $key==0){   //大于半小时显示时间
                    $earliestTime=intval($val['create_time']);
                    $val['create_time_name']=date('Y-m-d H:i:s',$val['create_time']);
                }else{
                    $val['create_time_name']="";
                }
            }
            //将所有消息标记为已读
            $this->db->Name('xcx_chat_record')->update(['user_read'=>'1'])->where_equalTo('agent_id',$agent_id)->where_equalTo('user_id',$user_id)->where_equalTo('user_status','1')->execute();
            echo json_encode(['success'=>true,'data'=>$data]);
        }else{
            echo json_encode(['success'=>false]);
        }
    }
    //获取经纪人对应的消息列表
    public function getChatList(){
        $user_id=$this->uid();        //用户id
        //获取系统未读消息
        $systemNum=$this->db->Name('xcx_announcement_inform_user')->select('COUNT(*)')->where_equalTo('username_id',$user_id)->where_equalTo('username_type',1)->where_equalTo('if_read',0)->firstColumn();
        $data=$this->db->Name('xcx_chat_list')->select("cl.id,cl.agent_id,cl.user_id,cl.create_time,au.nickname,au.name,au.headimgurl","cl")->leftJoin('xcx_agent_user','au','cl.agent_id=au.id')->where_equalTo('cl.user_id',$user_id)->where_equalTo('cl.user_status','1')->execute();
        if(empty($data)){
            echo json_encode(['success'=>false,'systemNum'=>$systemNum]);
        }else{
            foreach($data as &$val){
                $val['name']=empty($val['name'])?$val['nickname']:$val['name'];
                list($val['unread_num'],$val['unread_content'],$val['create_time'])=$this->getUnreadData($val['agent_id'],$val['user_id']);
            }
            echo json_encode(['success'=>true,'data'=>$data,'systemNum'=>$systemNum]);
        }
    }
    //获取经纪人未读数据
    private function getUnreadData($agent_id,$user_id){
        $res[]=$this->db->Name('xcx_chat_record')->select('COUNT(*)')->where_equalTo('agent_id',$agent_id)->where_equalTo('user_id',$user_id)->where_equalTo('user_read','0')->where_equalTo('user_status','1')->firstColumn();
        $unread_content=$this->db->Name('xcx_chat_record')->select()->where_equalTo('agent_id',$agent_id)->where_equalTo('user_id',$user_id)->where_equalTo('user_status','1')->where_equalTo('message_type','1')->orderBy('create_time','desc')->firstRow();
        if(empty($unread_content)){
            $res[]="";
        }else{
            $res[]=$unread_content['content'];
        }
        $res[]=$this->format_dates($unread_content['create_time']);
        return $res;
    }
    //修改消息列表为已读
    public function updateYd(){
        $agent_id=Context::Post('agent_id');    //经纪人id
        $user_id=$this->uid();        //用户id
        $res=$this->db->Name('xcx_chat_record')->update(['user_read'=>'1','update_time'=>time()])->where_equalTo('agent_id',$agent_id)->where_equalTo('user_id',$user_id)->where_equalTo('user_read','0')->execute();
        if(!empty($res)){
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false]);
        }
    }
    //删除消息列表及聊天内容
    public function deleteMessageList(){
        $agent_id=Context::Post('agent_id');    //经纪人id
        $user_id=$this->uid();        //用户id
        $id=Context::Post('id');    //9h_xcx_chat_list id
        //隐藏聊天列表
        $res=$this->db->Name('xcx_chat_list')->update(['user_status'=>'0','update_time'=>time()])->where_equalTo('id',$id)->where_equalTo('agent_id',$agent_id)->where_equalTo('user_id',$user_id)->where_equalTo('user_status','1')->execute();
        if(!empty($res)){
            //修改聊天记录为删除状态
            $this->db->Name('xcx_chat_record')->update(['user_status'=>'0'])->where_equalTo('agent_id',$agent_id)->where_equalTo('user_id',$user_id)->where_equalTo('user_status','1')->execute();
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false]);
        }
    }
    //获取系统通知信息
    public function getSystenInforms(){
        $user_id=$this->uid();        //用户id
        $page=Context::Post('page');
        //$systemInfo=$this->db->Name('xcx_announcement_inform_user')->select('aiu.*,ai.inform_title,ai.inform_content,ai.release_time','aiu')->leftJoin('xcx_announcement_inform','ai','aiu.announcement_id=ai.id')->where_equalTo('aiu.username_type',1)->where_equalTo('aiu.username_id',$user_id)->where_equalTo('ai.if_revocation',0)->page($page,self::MYLIMIT)->orderBy('ai.priority','desc')->orderBy('ai.release_time','desc')->execute();
        $systemInfo=$this->db->Name('xcx_announcement_inform')->select('aiu.*,ai.inform_title,ai.inform_content,ai.release_time','ai')->leftJoin('xcx_announcement_inform_user','aiu','aiu.announcement_id=ai.id AND aiu.username_type="1" AND aiu.username_id='.$user_id)->where_equalTo('ai.if_revocation',0)->page($page,self::MYLIMIT)->orderBy('ai.priority','desc')->orderBy('ai.release_time','desc')->execute();
        if(!empty($systemInfo)){
            $systemInfo=array_reverse($systemInfo);
            foreach($systemInfo as &$val){
                $val['release_time']=date('Y-m-d H:i:s',$val['release_time']);
            }
            echo json_encode(['success'=>true,'data'=>$systemInfo]);
        }else{
            echo json_encode(['success'=>false]);
        }
    }
    //获取系统头像与昵称
    public function getSystemInfo(){
        $data=[];
        $settingRow=$this->db->Name('xcx_setting')->select()->execute();
        if(!empty($settingRow)){
            foreach($settingRow as $val){
                if($val['key']=='system_name'){$data['systemInfo']['system_name']=$val['value'];}
                if($val['key']=='system_logo'){$data['systemInfo']['system_logo']=$val['value'];}
            }
        }
        echo json_encode($data,JSON_UNESCAPED_UNICODE);
    }
    //修改系统消息为已读
    public function updateSystenRead(){
        $user_id=$this->uid();        //用户id
        $this->db->Name('xcx_announcement_inform_user')->update(['if_read'=>1,'read_time'=>time()])->where_equalTo('username_id',$user_id)->where_equalTo('username_type',1)->where_equalTo('if_read',0)->execute();
        echo json_encode(['success'=>true]);
    }
}