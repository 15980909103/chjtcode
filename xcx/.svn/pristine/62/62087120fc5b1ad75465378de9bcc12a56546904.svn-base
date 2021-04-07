<?php
/**
 * 管理员后台 管理用户周边控制器
 */
include 'AdminController.php';
class Xcxambitus extends AdminController
{
    /**
     * 用户反馈处理页
     * @return false|string
     */
    public function suggest_index(){
        return $this->render('suggest_index');
    }

    /**
     * 用户反馈处理数据渲染
     */
    public function suggest_page(){
        $curr                       = Context::Post('curr');
        $limit                      = Context::Post('limit');
        $select['client_side_type'] = Context::Post('client_side_type');
        $select['dispose_status']   = Context::Post('dispose_status');

        $select = array_filter($select,function($val){$tmp=$val ===  ''; return !$tmp;});
        $db     = $this->db->Name('xcx_ambitus_suggest');

        if (!empty($select)){
            //分页+条件
            $db->select()->orderBy('id','DESC')->page($curr,$limit);
            $db         = $this->set_where($select,$db);
            $suggestArr = $db->execute();
            //满足查询的记录
            $db->select('COUNT(*)');
            $db    = $this->set_where($select,$db);
            $count = $db->firstColumn();
        }else{
            //分页
            $suggestArr = $db->select()->orderBy('create_time','DESC')->page($curr,$limit)->execute();
            //总记录
            $count      = $db->select('COUNT(*)')->firstColumn();
        }
        if (!empty($suggestArr)){
            $user_id  = [];
            $agent_id = [];
            foreach ($suggestArr as $val){
                //判断小程序用户端
                if ($val['client_side_type']==1){
                    $user_id[] = $val['user_id'];
                }
                //判断公众号经纪人端
                if ($val['client_side_type']==2){
                    $agent_id[] = $val['user_id'];
                }
            }
            $user_id  = array_unique($user_id);
            $agent_id = array_unique($agent_id);
            //通过ID获取小程序用户
            $userDataArr = [];
            $agentDataArr = [];
            if (!empty($user_id)){
                $userDataArr = $this->db->Name('xcx_user')
                                        ->where_in('id',$user_id)
                                        ->select('id,nickName,avatarUrl')->execute();
            }
            //通过ID获取公众号经纪人
            if (!empty($agent_id)){
                $agentDataArr = $this->db->Name('xcx_agent_user')
                                         ->where_in('id',$agent_id)
                                         ->select('id,nickname,name,headimgurl')->execute();
            }
            //将小程序用户数据添加到意见反馈数组
            foreach ($userDataArr as $value1){
                foreach ($suggestArr as &$value2){
                    if ($value1['id']==$value2['user_id'] && $value2['client_side_type']==1){
                        $value2['username']  = $value1['nickName'];
                        $value2['head_portrait'] = $value1['avatarUrl'];
                    }
                }
            }
            //将公众号经纪人数据添加到意见反馈数组
            foreach ($agentDataArr as $value1){
                foreach ($suggestArr as &$value3){
                    if ($value1['id']==$value3['user_id'] && $value3['client_side_type']==2){
                        $value3['username']   = $value1['name']?$value1['name']:$value1['nickname'];
                        $value3['head_portrait'] = $value1['headimgurl'];
                    }
                }
            }
            foreach ($suggestArr as &$val){
                $val['dispose_status']   = $val['dispose_status']?"已处理":"未处理";
                $val['client_side_type'] = $val['client_side_type']==1?"小程序客户端":"公众号经纪人端";
                $val['create_time']      = date('Y-m-s H:i:s',$val['create_time']);
                $val['update_time']      = $val['update_time']?date('Y-m-d H:i:s',$val['update_time']):"";
                $val['image_feedback']   = empty($val['image_feedback'])?[]:explode('|',$val['image_feedback']);
            }
            echo json_encode(['success'=>true,'data'=>$suggestArr,'count'=>$count]);
        }else{
            echo json_encode(['success'=>false]);
        }
    }

    /**
     * 用户反馈处理状态
     */
    public function ambitus_suggest_dispose_status(){
        $id             = Context::Post('id');
        $dispose_status = Context::Post('dispose_status');
        $update_time    = $dispose_status?time():0;

        $result = $this->db->Name('xcx_ambitus_suggest')
                          ->update(['dispose_status'=>$dispose_status,'update_time'=>$update_time])
                          ->where_equalTo('id',$id)->execute();
        if ($result){
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false]);
        }
    }

    /**
     * 用户楼盘收藏页
     * 渲染 用户搜索
     * @return false|string
     */
    public function collection_index(){
        $userArr = $this->db->Name('xcx_user_building_collection AS bc')
                            ->leftJoin('xcx_user','user','user.id=bc.user_id')
                            ->select('DISTINCT user.id,user.nickName')
                            ->execute();
        $data['userArr'] = $userArr;
        return $this->render('collection_index',$data);
    }

    /**
     * 楼盘收藏页数据渲染
     */
    public function collection_page(){
        $curr    = Context::Post('curr');
        $limit   = Context::Post('limit');
        $user_id = Context::Post('user_id');

        $db = $this->db->Name('xcx_user_building_collection AS bc')
                       ->select('bc.id,bc.user_id,bc.create_time,user.nickName,user.avatarUrl,au.nickname AS auname,au.name AS username,bb.name,bb.pic')
                       ->leftJoin('xcx_user','user','user.id=bc.user_id')
                       ->leftJoin('xcx_agent_user','au','au.id=bc.agent_id')
                       ->leftJoin('xcx_building_building','bb','bb.id=bc.building_id')
                       ->where_equalTo('bc.status','1')
                       ->page($curr,$limit)
                       ->orderBy('bc.create_time','DESC');
        //分页+总记录
        if (empty($user_id)){
            $collectionArr = $db->execute();
            $db            = $this->db->Name('xcx_user_building_collection');
            $count         = $db->select('COUNT(*)')->firstColumn();
        }else{
            //分页+条件+满足条件的记录
            $collectionArr = $db->where_equalTo('bc.user_id',$user_id)->execute();
            $count         = $this->db->Name('xcx_user_building_collection')
                                     ->where_equalTo('user_id',$user_id)
                                     ->select('COUNT(*)')->firstColumn();
        }
        foreach ($collectionArr as &$val){
            $val['create_time'] = date('Y-m-d H:i:s',$val['create_time']);
            $val['username']    = $val['username']?$val['username']:['auname'];
        }
        if (empty($collectionArr)){
            echo json_encode(['success'=>false]);
        }else{
            echo json_encode(['success'=>true,'data'=>$collectionArr,'count'=>$count]);
        }
    }

    /**
     * 后台用户浏览记录页面
     * @return false|string
     */
    public function browsing_history_index(){
        $userArr = $this->db->Name('xcx_user_browsing_history AS ubh')
                        ->leftJoin('xcx_user','xu','xu.id=ubh.user_id')
                        ->select('DISTINCT xu.id,xu.nickName')
                        ->execute();
        $data['userArr'] = $userArr;
        return $this->render('browsing_history_index',$data);
    }

    /**
     * 后台用户浏览记录数据渲染
     */
    public function browsing_history_page(){
        $curr  = Context::Post('curr');
        $limit = Context::Post('limit');

        $where['browse_type'] = Context::Post('browse_type');
        $where['user_id']     = Context::Post('user_id');
        $where                = array_filter($where,function($val){$tmp=$val ===  ''; return !$tmp;});

        $db = $this->db->Name('xcx_user_browsing_history AS ubh')
                       ->leftJoin('xcx_user','xu','xu.id=ubh.user_id')
                       ->leftJoin('xcx_building_building','bb','bb.id=ubh.building_id')
                       ->leftJoin('xcx_article_article','aa','aa.id=ubh.article_id')
                       ->leftJoin('xcx_agent_user','au','au.id=ubh.agent_id')
                       ->select('ubh.id,xu.nickName,xu.avatarUrl,bb.name AS bbname,
                                bb.pic,aa.title,aa.cover,au.nickname AS aunickname,
                                au.name AS auname,ubh.browse_type,ubh.status,
                                ubh.start_time,ubh.end_time,ubh.viewing_hours')
                       ->orderBy('ubh.id')
                       ->page($curr,$limit);
        //分页+总记录
        if (empty($where)){
            $browsing_history_arr = $db->execute();
            $count                = $this->db->Name('xcx_user_browsing_history')
                                         ->select('COUNT(*)')->firstColumn();
        }else{
            //分页+条件+满足条件记录
            $db                   = $this->set_where($where,$db);
            $browsing_history_arr = $db->execute();

            $db    = $this->db->Name('xcx_user_browsing_history');
            $db    = $this->set_where($where,$db);
            $count = $db->select('COUNT(*)')->firstColumn();
        }
        if (empty($browsing_history_arr)){
            echo json_encode(['success'=>false]);
        }else{
            foreach ($browsing_history_arr as &$val){
                $val['auname']        = $val['auname']?$val['auname']:$val['aunickname'];
                $val['auname']        = $val['auname']?$val['auname']:' ';
                $val['start_time']    = date('Y-m-d H:i:s',$val['start_time']);
                $val['end_time']      = date('Y-m-d H:i:s',$val['end_time']);
                $val['bbname']        = $val['bbname']?$val['bbname']:' ';
                $val['title']         = $val['title']?$val['title']:' ';
                $val['viewing_hours'] = $this->timeStr($val['viewing_hours']);
                switch ($val['browse_type']){
                    case 1:
                        $val['browse_type'] = '名片';
                        break;
                    case 2:
                        $val['browse_type'] = '文章';
                        break;
                    case 3:
                        $val['browse_type'] = '楼盘';
                        break;
                }
            }
            echo json_encode(['success'=>true,'data'=>$browsing_history_arr,'count'=>$count]);
        }
}

    /**
     * 时间转换
     * @param $second
     * @return string
     */
    private function timeStr($second){
        $s = $second%60;
        $m = floor($second/60);
        $h = floor($second/60/60);
        if ($second){
            if ($h){
                $str = $h.'时'.$m.'分'.$s.'秒';
                return $str;
            }
            if ($m){
                $str = $m.'分'.$s.'秒';
                return $str;
            }else{
                $str = $second.'秒';
                return $str;
            }
        }
        return '';
    }

    /**
     * 用户楼盘纠错反馈页面
     * @return false|string
     */
    public function building_correct_index(){
        return $this->render('building_correct_index');
    }
    /**
     * 用户楼盘纠错反馈页面数据渲染
     */
    public function building_correct_page(){
        $curr  = Context::Post('curr');
        $limit = Context::Post('limit');

        $where['matter_type']      = Context::Post('matter_type');
        $where['client_side_type'] = Context::Post('client_side_type');
        $where['dispose_status']   = Context::Post('dispose_status');

        $where  = array_filter($where,function ($val){$tmp=$val==='';return!$tmp;});
        $select = 'bc.id,bc.matter_type,bc.building_correct_info,bc.client_side_type,
                   bc.dispose_status,bc.create_time,bc.update_time,xu.nickName AS xunickname,
                   xu.avatarUrl,au.headimgurl,au.nickname AS aunickname,au.name AS auname,bb.name AS bbname';
        //分页+总记录
        if (empty($where)){
            $building_correct_arr = $this->building_correct_db()->select($select)->page($curr,$limit)->execute();
            $count                = $this->building_correct_db()->select('COUNT(*)')->firstColumn();
        }else{
            //条件+分页+满足条件记录
            $building_correct_arr = $this->set_where($where,$this->building_correct_db())->select($select)
                                         ->page($curr,$limit)->execute();
            $count                = $this->set_where($where,$this->building_correct_db())
                                         ->select('COUNT(*)')->firstColumn();
        }
        if (!empty($building_correct_arr)){
            foreach ($building_correct_arr as &$val){
                $val['client_side_type'] = $val['client_side_type']==1?"小程序客户端":"公众号经纪人端";
                $val['dispose_status']   = $val['dispose_status']?"已处理":"未处理";
                $val['create_time']      = date('Y-m-d H:i:s',$val['create_time']);
                $val['update_time']      = $val['update_time']?date('Y-m-d H:i:s',$val['update_time']):"";
                $val['auname']           = $val['auname']?$val['auname']:$val['aunickname'];
                $val['username']         = $val['auname']?$val['auname']:$val['xunickname'];
                $val['head_portrait']    = $val['avatarUrl']?$val['avatarUrl']:$val['headimgurl'];
                unset($val['avatarUrl']);unset($val['headimgurl']);
                unset($val['auname']);   unset($val['aunickname']);unset($val['xunickname']);
                switch ($val['matter_type']){
                    case 1:
                        $val['matter_type'] = '基本信息';
                        break;
                    case 2:
                        $val['matter_type'] = '建筑信息';
                        break;
                    case 3:
                        $val['matter_type'] = '物业参数';
                        break;
                    case 4:
                        $val['matter_type'] = '配套信息';
                        break;
                }
            }
            echo json_encode(['success'=>true,'data'=>$building_correct_arr,'count'=>$count]);
        }else{
            echo json_encode(['success'=>false]);
        }
    }

    /**
     * 用户楼盘纠错反馈业务处理
     */
    public function building_correct_building_status(){
        $id             = Context::Post('id');
        $dispose_status = Context::Post('dispose_status');

        $update_time    = $dispose_status?time():0;
        $result         = $this->db->Name('xcx_building_correct')
                                   ->update(['dispose_status'=>$dispose_status,'update_time'=>$update_time])
                                   ->where_equalTo('id',$id)->execute();
        if ($result){
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false]);
        }
    }

    /**
     * 用户楼盘纠错反馈 DB对象
     * @return mixed
     */
    private function building_correct_db(){
        $db = $this->db->Name('xcx_building_correct AS bc')
                    ->leftJoin('xcx_user','xu','xu.id=bc.user_id')
                    ->leftJoin('xcx_agent_user','au','au.id=bc.agent_id')
                    ->leftJoin('xcx_building_building','bb','bb.id=bc.building_id')
                    ->orderBy('bc.id','DESC');
        return $db;
    }

    /**
     * 打开统计页面
     * @return false|string
     */
    public function statistics_index(){
        return $this->render('statistics_index');
    }

    /**
     * 各项数据总和统计
     */
    public function get_statsx(){
        //总客户数
        $userCount             = $this->db->Name('xcx_user')->select('COUNT(*)')->firstColumn();
        //总经纪人数
        $agentCount            = $this->db->Name('xcx_agent_user')->select('COUNT(*)')
                                          ->where_equalTo('status','1')->firstColumn();
        //总楼盘数
        $buildingCount         = $this->db->Name('xcx_building_building')->select('COUNT(*)')
                                          ->where_equalTo('status','1')->firstColumn();
        //总楼盘带看数
        $buildingReportedCount = $this->db->Name('xcx_building_reported')->select('COUNT(*)')
                                          ->where_greatThanOrEqual('status_type','2')->firstColumn();
        //楼盘浏览总数
        $browsingBuildingCount = $this->browsingHistoryDB()->select('COUNT(*)')
                                      ->where_equalTo('browse_type','3')->firstColumn();
        //文章浏览总数
        $browsingEssayCount    = $this->browsingHistoryDB()->select('COUNT(*)')
                                      ->where_equalTo('browse_type','2')->firstColumn();
        //经纪人名片分享总数
        $shareVisitingCardCount= $this->agentShareDB()->select('COUNT(*)')
                                      ->where_equalTo('share_type','1')->firstColumn();
        //经纪人文章分享总数
        $shareArticleCount     = $this->agentShareDB()->select('COUNT(*)')
                                      ->where_equalTo('share_type','2')->firstColumn();
        //经纪人楼盘分享总数
        $shareBuildingCount    = $this->agentShareDB()->select('COUNT(*)')
                                      ->where_equalTo('share_type','3')->firstColumn();

        $data['userCount']             = $userCount;
        $data['agentCount']            = $agentCount;
        $data['buildingCount']         = $buildingCount;
        $data['buildingReportedCount'] = $buildingReportedCount;
        $data['browsingBuildingCount'] = $browsingBuildingCount;
        $data['browsingEssayCount']    = $browsingEssayCount;
        $data['shareVisitingCardCount']= $shareVisitingCardCount;
        $data['shareArticleCount']     = $shareArticleCount;
        $data['shareBuildingCount']    = $shareBuildingCount;
        if (!empty($data)){
            echo json_encode(['success'=>true,'data'=>$data]);
        }else{
            echo json_encode(['success'=>false]);
        }
    }

    /**
     * 近七天 数据
     */
    public function get_day_statsx(){
        $myTime=time();
        for ($i=-1;$i>=-7;$i--){
            $start_time = $this->subtract_time($myTime,$i,'day',false);
            $end_time   = $this->subtract_time($myTime,$i+1,'day',false);
            //一天注册客户数
            $userDayCount[$i]             = $this->db->Name('xcx_user')->select('COUNT(*)')
                                                ->where_greatThanOrEqual('create_time',$start_time)
                                                ->where_lessThan('create_time',$end_time)->firstColumn();
            //一天注册经纪人数
            $agentDayCount[$i]            = $this->db->Name('xcx_agent_user')->select('COUNT(*)')
                                                ->where_equalTo('status','1')->where_greatThanOrEqual('create_time',$start_time)
                                                ->where_lessThan('create_time',$end_time)->firstColumn();
            //一天楼盘带看数
            $buildingReportedDayCount[$i] = $this->db->Name('xcx_building_reported')
                                                ->select('COUNT(*)')
                                                ->where_greatThanOrEqual('status_type','2')
                                                ->where_greatThanOrEqual('create_time',$start_time)
                                                ->where_lessThan('create_time',$end_time)
                                                ->firstColumn();
            //一天楼盘浏览数
            $browsingBuildingDayCount[$i] = $this->browsingHistoryDB()->select('COUNT(*)')
                                                ->where_equalTo('browse_type','3')
                                                ->where_greatThanOrEqual('start_time',$start_time)
                                                ->where_lessThan('start_time',$end_time)
                                                ->firstColumn();
            //一天文章浏览数
            $browsingEssayDayCount[$i]    = $this->browsingHistoryDB()->select('COUNT(*)')
                                                ->where_equalTo('browse_type','2')
                                                ->where_greatThanOrEqual('start_time',$start_time)
                                                ->where_lessThan('start_time',$end_time)
                                                ->firstColumn();
            //一天 经纪人名片分享数
            $shareVisitingCardDayCount[$i]= $this->agentShareDB()->select('COUNT(*)')
                                                ->where_equalTo('share_type','1')
                                                ->where_greatThanOrEqual('create_time',$start_time)
                                                ->where_lessThan('create_time',$end_time)
                                                ->firstColumn();
            //一天 经纪人文章分享数
            $shareArticleDayCount[$i]     = $this->agentShareDB()->select('COUNT(*)')
                                                ->where_equalTo('share_type','2')
                                                ->where_greatThanOrEqual('create_time',$start_time)
                                                ->where_lessThan('create_time',$end_time)
                                                ->firstColumn();
            //一天 经纪人楼盘分享数
            $shareBuildingDayCount[$i]    = $this->agentShareDB()->select('COUNT(*)')
                                                ->where_equalTo('share_type','2')
                                                ->where_greatThanOrEqual('create_time',$start_time)
                                                ->where_lessThan('create_time',$end_time)
                                                ->firstColumn();
            //前七天日期
            $Date[$i] = $this->subtract_time($myTime,$i,'day');
        }
        $data_arr = ['success'=>true,'userDayCount'=>$userDayCount,'agentDayCount'=>$agentDayCount,'date'=>$Date,
                 'buildingReportedDayCount'=>$buildingReportedDayCount,'browsingBuildingDayCount'=>$browsingBuildingDayCount,
                 'browsingEssayDayCount'=>$browsingEssayDayCount,'shareVisitingCardDayCount'=>$shareVisitingCardDayCount,
                 'shareArticleDayCount'=>$shareArticleDayCount,'shareBuildingDayCount'=>$shareBuildingDayCount];
        echo json_encode($data_arr);
    }

    /**
     * 打开聊天记录页面
     * @return false|string
     */
    public function chatting_records_index(){
        return $this->render('chatting_records_index');
    }

    /**
     * 聊天记录页面数据渲染
     */
    public function chatting_records_page(){
        $curr  = Context::Post('curr');
        $limit = Context::Post('limit');

        $where['from_type']  = Context::Post('from_type');
        $where['agent_read'] = Context::Post('agent_read');
        $where['user_read']  = Context::Post('agent_read');

        $where  = array_filter($where,function ($val){$tmp=$val==='';return !$tmp;});
        $db     = $this->db->Name('xcx_chat_record AS cr')->leftJoin('xcx_user','xu','xu.id=cr.user_id')
                                                          ->leftJoin('xcx_agent_user','au','au.id=cr.agent_id');
        $select = 'cr.id,xu.nickName AS xunickname,xu.avatarUrl,au.nickname AS aunickname,au.headimgurl,au.name AS auname,
                   cr.from_type,cr.message_type,cr.content,cr.agent_read,cr.user_read,cr.create_time';
        //分页+总记录数
        if (empty($where)){
            $chatting_records_arr = $db->select($select)->orderBy('cr.id','DESC')->page($curr,$limit)->execute();
            $count                = $db->select('COUNT(*)')->firstColumn();
        }else{
            //读取状态条件+记录数
            if (empty($where['from_type'])){
                $chatting_records_arr = $this->set_where($where,$db,'OR')->select($select)
                                             ->orderBy('cr.id','DESC')->page($curr,$limit)->execute();
                $count                = $this->set_where($where,$db,'OR')->select('COUNT(*)')->firstColumn();
                //发送方向条件+记录数
            }else if (!isset($where['agent_read'])){
                $chatting_records_arr = $this->set_where($where,$db)->select($select)->orderBy('cr.id','DESC')
                                             ->page($curr,$limit)->execute();
                $count                = $this->set_where($where,$db)->select('COUNT(*)')->firstColumn();
            }else{
                //发送方向为小程序和读取状态+记录数
                if ($where['from_type']==1){
                    $chatting_records_arr = $db->select($select)->where_equalTo('cr.from_type',$where['from_type'])
                                               ->where_equalTo('cr.agent_read',$where['agent_read'])
                                               ->orderBy('cr.id','DESC')->page($curr,$limit)->execute();
                    $count                = $db->select('COUNT(*)')->where_equalTo('cr.from_type',$where['from_type'])
                                               ->where_equalTo('cr.agent_read',$where['agent_read'])
                                               ->firstColumn();
                }else{
                    //发送方向为公众号和读取状态+记录数
                    $chatting_records_arr = $db->select($select)->where_equalTo('cr.from_type',$where['from_type'])
                                               ->where_equalTo('cr.user_read',$where['user_read'])
                                               ->orderBy('cr.id','DESC')->page($curr,$limit)->execute();
                    $count                = $db->select('COUNT(*)')->where_equalTo('cr.from_type',$where['from_type'])
                                               ->where_equalTo('cr.user_read',$where['user_read'])
                                               ->firstColumn();
                }
            }
        }
        if (!empty($chatting_records_arr)){
            $read_arr = [];
            foreach ($chatting_records_arr as &$val){
                $val['read_type']        = $val['from_type']==1?$val['agent_read']:$val['user_read'];
                $val['read_type']        = $val['read_type']?"已读":"未读";
                $val['from_type']        = $val['from_type']==1?"小程序向经纪人发消息":"经纪人向小程序发消息";
                $val['auname']           = $val['auname']?$val['auname']:$val['aunickname'];
                $val['transmit_name']    = $val['from_type']==1?$val['xunickname']:$val['auname'];//发送人名称
                $val['receive_name']     = $val['from_type']==2?$val['auname']:$val['xunickname'];//接收人名称
                $val['transmit_headimg'] = $val['from_type']==1?$val['avatarUrl']:$val['headimgurl'];//发送人头像
                $val['receive_headimg']  = $val['from_type']==2?$val['headimgurl']:$val['avatarUrl'];//接收人头像
                $val['create_time']      = date('Y-m-d H:i:s',$val['create_time']);
                //判断内容类型为图片
                if ($val['message_type']==2){
                    $val['image']   = $val['content'];
                    $val['content'] = '';
                }
                //判断有读取状态 与 没有选择发送方向
                if (isset($where['agent_read']) && empty($where['from_type'])){
                    //判断已读
                    if ($where['agent_read']=='1' && $val['read_type']=='已读'){
                        $read_arr[] = $val;
                    }
                    //判断未读
                    if ($where['agent_read']=='0' && $val['read_type']=='未读'){
                        $read_arr[] = $val;
                    }
                }
            }
            if (!empty($read_arr)){
                $chatting_records_arr = null;
                $chatting_records_arr = $read_arr;
            }
            echo json_encode(['success'=>true,'data'=>$chatting_records_arr,'count'=>$count]);
        }else{
            echo json_encode(['success'=>false]);
        }
}
    /**
     * 用户浏览记录DB对象
     * @return mixed
     */
    private function browsingHistoryDB(){
        $browsingHistoryDB = $this->db->Name('xcx_user_browsing_history');
        return $browsingHistoryDB;
    }

    /**
     * 经纪人分享记录DB对象
     * @return mixed
     */
    private function agentShareDB(){
        $agentShareDB = $this->db->Name('xcx_agent_share');
        return $agentShareDB;
    }

    /**
     * 打开公告通知页面
     * @return false|string
     */
    public function announcement_inform_index(){
        return $this->render('announcement_inform_index');
    }

    /**
     * 共公通知Db
     * @return mixed
     */
    private function announcement_inform_DB(){
        $Db = $this->db->Name('xcx_announcement_inform_user AS aiu')
                      ->leftJoin('xcx_announcement_inform','ai','ai.id=aiu.announcement_id');
        return $Db;
    }

    /**
     * 公告通知页面数据渲染
     */
    public function announcement_inform_page(){
        $curr  = Context::Post('curr');
        $limit = Context::Post('limit');
        $where['username_type'] = Context::Post('username_type');
        $where['if_revocation'] = Context::Post('if_revocation');
        $where                  = array_filter($where,function ($val){$tmp=$val==='';return !$tmp;});

        if (empty($where)){
            //分页+总记录
            $announcement_inform_arr = $this->announcement_inform_DB()
                                                ->select('DISTINCT ai.*')
                                                ->orderBy('id','DESC')
                                                ->page($curr,$limit)->execute();
            $count                   = $this->announcement_inform_DB()
                                                ->select('COUNT(DISTINCT ai.id)')->firstColumn();
            $announcement_user_arr   = $this->announcement_inform_DB()
                                                ->select('ai.id,aiu.if_read')->orderBy('id','DESC')
                                                ->execute();
        }else{
            //条件+分页+记录
            $announcement_inform_arr = $this->set_where($where,$this->announcement_inform_DB())
                                                ->select('DISTINCT ai.*')->orderBy('id','DESC')
                                                ->page($curr,$limit)->execute();
            $count                   = $this->set_where($where,$this->announcement_inform_DB())
                                                ->select('COUNT(DISTINCT ai.id)')
                                                ->firstColumn();
            $announcement_user_arr   = $this->set_where($where,$this->announcement_inform_DB())
                                                ->select('ai.id,aiu.if_read')
                                                ->orderBy('id','DESC')
                                                ->execute();
        }
        if (!empty($announcement_inform_arr)){
            foreach ($announcement_inform_arr as &$val2){
                $number1 = 0;
                $number2 = 0;
                $val2['if_revocation']   = $val2['if_revocation']?"是":"否";
                $val2['release_time']    = date('Y-m-d H:i:s',$val2['release_time']);
                $val2['revocation_time'] = $val2['revocation_time']?date('Y-m-d H:i:s',$val2['revocation_time']):"";
                $val2['priority']        = $val2['priority']?$val2['priority']==1?"高":"紧急":"普通";
                foreach ($announcement_user_arr as $value){
                    //判断未读 设置多少用户未读
                    if ($val2['id']==$value['id'] && $value['if_read']==0){
                        $number1++;
                        $val2['unread'] = $number1;
                    }
                    //判断已读 设置多少用户已读
                    if ($val2['id']==$value['id'] && $value['if_read']==1){
                        $number2++;
                        $val2['read'] = $number2;
                    }
                }
                //没有已读设置为0
                if (!isset($val2['read'])){
                    $val2['read'] = 0;
                }
                //没有未读设置为0
                if (!isset($val2['unread'])){
                    $val2['unread'] = 0;
                }
            }
            echo json_encode(['success'=>true,'data'=>$announcement_inform_arr,'count'=>$count]);
        }else{
            echo json_encode(['success'=>false]);
        }
    }

    /**
     * 公告通知是否撤销
     */
    public function if_revocation_status(){
        $id              = Context::Post('id');
        $status          = Context::Post('status');
        $revocation_time = $status?time():0;
        $result          = $this->db->Name('xcx_announcement_inform')
                                ->update(['revocation_time'=>$revocation_time,'if_revocation'=>$status])
                                ->where_equalTo('id',$id)->execute();
        if ($result){
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false]);
        }
    }

    /**
     * 打开公告添加页面
     * @return false|string
     */
    public function announcement_inform_add(){
        $DB                     = $this->user_agent_DB();
        $data['user_arr']       = $DB['user_arr'];
        $data['agent_user_arr'] = $DB['agent_user_arr'];
        return $this->render('announcement_inform_add',$data);
    }

    /**
     * 添加公告通知
     */
    public function announcement_inform_doAdd(){
            $inform_title    = Context::Post('inform_title');//公告标题
            $inform_content  = Context::Post('inform_content');//公告内容
            $transmitter     = Context::Post('transmitter');//发送方向
            $group           = Context::Post('group');//发送群体
            $priority        = Context::Post('priority');//优先级
            $user_id         = Context::Post('user_id');//小程序用户ID
            $agent_id        = Context::Post('agent_id');//公众号用户ID
            $Mytime          = time();//发布时间
            $if_revocation   = 0;//是否撤销
            $revocation_time = 0;//撤销时间
        //判断是否有标题内容
        if (empty($inform_title) || empty($inform_content)){
            echo json_encode(['success'=>false,'message'=>'请输入标题或内容']);
            return;
        }

        $PDO = new DataBase();
        $PDO::beginTransaction();//开启事务
        //插入公告
        $announcement_values = ['id'=>null,'inform_title'=>$inform_title,'inform_content'=>$inform_content,
                                'release_time'=>$Mytime,'if_revocation'=>$if_revocation,
                                'revocation_time'=>$revocation_time,'priority'=>$priority];
        $announcement_id = $this->db->Name('xcx_announcement_inform')->insert($announcement_values)->execute();
        //判断公告通知是否插入
        if (!$announcement_id){
            $PDO::rollBack();
            echo json_encode(['success'=>false,'message'=>'公告通知保存失败']);
            return;
        }

        $sql   = "INSERT INTO 9h_xcx_announcement_inform_user VALUES ";
        $DB    = $this->user_agent_DB();
        $array = [];
        //所有人
        if ($group==2){
            //公众号
            if ($transmitter==1){
                $agent_user_arr = $DB['agent_user_arr'];//经纪人ID
                //SQL优化拼接
                $bumber = 1;
                foreach ($agent_user_arr as $val){
                    $bumber++;
                    $sql.= "(?,?,?,?,?,?,?),";
                    $announcement_user_values = $this->data_insert($announcement_id,$val['id'],2);
                    $array                    = array_merge($array,$announcement_user_values);//将数组并在一块
                    $index = true;//开关
                    if ($bumber%200==0){//200条数数据插一次
                        $sql = rtrim($sql,',');
                        $result = $PDO::Insert($sql,$array);
                        if (!$result){
                            $PDO::rollBack();
                            echo json_encode(['success'=>false,'message'=>'发送所有公众号用户公告通知保存失败']);
                            return;
                        }
                        $PDO::commit();
                        $PDO::beginTransaction();//重开事务
                        $array = [];//200条插入完清空
                        $sql   = "INSERT INTO 9h_xcx_announcement_inform_user VALUES ";//重新默认回sql
                        $index = false;
                    }
                }
                //执行小于200条的数据插入
                if ($index){
                    $sql    = rtrim($sql,',');
                    $result = $PDO::Insert($sql,$array);//将数据插入
                    if (!$result){
                        $PDO::rollBack();
                        echo json_encode(['success'=>false,'message'=>'发送所有公众号用户公告通知保存失败']);
                        return;
                    }
                }
            }
            //小程序
            if ($transmitter==2){
                $user_arr = $DB['user_arr'];//经纪人ID
                //SQL优化拼接
                $bumber = 1;
                foreach ($user_arr as $val){
                    $bumber++;
                    $sql.= "(?,?,?,?,?,?,?),";
                    $announcement_user_values = $this->data_insert($announcement_id,$val['id'],1);
                    $array                    = array_merge($array,$announcement_user_values);//将值合并在一块
                    $index = true;//开关
                    if ($bumber%200==0){//200条数数据插一次
                        $sql = rtrim($sql,',');
                        $result = $PDO::Insert($sql,$array);
                        if (!$result){
                            $PDO::rollBack();
                            echo json_encode(['success'=>false,'message'=>'发送所有小程序用户公告通知保存失败']);
                            return;
                        }
                        $PDO::commit();
                        $PDO::beginTransaction();//重开事务
                        $array = [];//200条插入完清空
                        $sql   = "INSERT INTO 9h_xcx_announcement_inform_user VALUES ";//重新默认回sql
                        $index = false;
                    }
                }
                //执行小于200条的数据插入
                if ($index){
                    $sql    = rtrim($sql,',');
                    $result = $PDO::Insert($sql,$array);//将数据插入
                    if (!$result){
                        $PDO::rollBack();
                        echo json_encode(['success'=>false,'message'=>'发送所有小程序用户公告通知保存失败']);
                        return;
                    }
                }
            }
        }
        //个人
        if ($group==1){
            $sql.= "(?,?,?,?,?,?,?)";
            //公众号
            if ($transmitter==1){
                $announcement_user_values = $this->data_insert($announcement_id,$agent_id,2);
                $result                   = $PDO::Insert($sql,$announcement_user_values);
            }
            //小程序
            if ($transmitter==2){
                $announcement_user_values = $this->data_insert($announcement_id,$user_id,1);
                $result                   = $PDO::Insert($sql,$announcement_user_values);
            }
        }
        //判断是否成功插入
        if ($result){
            $PDO::commit();
            echo json_encode(['success'=>true]);
            return;
        }
        //插入失败
        $PDO::rollBack();
        echo json_encode(['success'=>false,'message'=>'发送该用户公告通知失败']);
        return;
    }

    /**
     * 客户和经纪人DB
     * @return array
     */
    private function user_agent_DB(){
        $user_arr       = $this->db->Name('xcx_user')->select('id,nickName')->execute();
        $agent_user_arr = $this->db->Name('xcx_agent_user')->select('id,nickname')->execute();
        return ['user_arr'=>$user_arr,'agent_user_arr'=>$agent_user_arr];
    }

    /**
     * 多个字段查询
     * @param $select 要查询的字段值
     */
    private function set_where($select,$Db,$type='and'){
        foreach($select as $k=>$v){
            $Db->where_equalTo($k,$v,$type);
        }
        return $Db;
    }

    /**
     * 公告通知用户数据插入
     * @param $announcement_id 公告ID
     * @param $username_id 用户ID
     * @param $username_type 用户类型
     */
    private function data_insert($announcement_id,$username_id,$username_type){
        $announcement_user_values = [null,$announcement_id,$username_id,$username_type,0,time(),0];
        return $announcement_user_values;
    }

    /**
     * 返回 时间名称以前开始时间
     * @param $time 时间戳
     * @param $num  要减去的数
     * @param $timeName 时间名称
     * day、week、month、year
     * @param bool $bool 默认为时间格式 时间
     * @return false|int|string 返回处理时间
     */
    private function subtract_time($time,$num,$timeName,$bool=true){
        $Time = '';
        if (trim($timeName)=='day'){
            $Time = strtotime(date('Y-m-d',$time)." ".$num."".$timeName);
            if ($bool){
                $Time = date('Y-m-d',$Time);
            }
        }
        if (trim($timeName)=='week'){
            $Time = mktime(0,0,0,date('m'),date('d')-date('w')+1-(7*$num),date('Y'));
            if ($bool){
                $Time = date('Y-m-d',$Time);
            }
        }
        if (trim($timeName)=='month'){
            $Time = strtotime(date('Y-m-01',time())." ".$num." month");
        }
        if (trim($timeName)=='year'){
            $Time = strtotime(date('Y-01-01',time())." ".$num." year");
        }
        return $Time;//若为时间戳 则零点整
    }
}