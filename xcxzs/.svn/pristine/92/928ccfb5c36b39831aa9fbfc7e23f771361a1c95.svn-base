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
class ArticleAjax extends Common{
    //获取经纪人数据
    public function getAgentInfo(){
        $agent_id = Context::Post('agent_id');
        if(!empty($agent_id)){
            $agentInfo = $this->db->Name('xcx_agent_user')->select('id,nickname,headimgurl,phone')->where_equalTo('id',$agent_id)->firstRow();
            if(!empty($agentInfo)){
                $access_token=$this->getAccessToken2();
                $parameter=["scene"=>'agent_id='.$agentInfo['id'],"page"=>"pages/talk/chat/chat"];
                $qrCode=$this->sendPost('https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token='.$access_token,json_encode($parameter));
                $qrarray = json_decode($qrCode,1);
                if($qrarray&&!empty($qrarray['errmsg'])){
                    $qrCode = '';
                }else{
                    //二进制转base64
                    $qrCode = 'data:' . 'image/png' . ';base64,' . base64_encode($qrCode);
                }
            }
        }
        return $this->success([
            "qrCode"=>$qrCode,
            "agentInfo"=>$agentInfo
        ]);
    }

    /*
     * 九房网获取文章详情数据
     */
    public function getArticleDetailData(){
        $id=Context::Post('id');
        $data['agent_id'] = Session::get('agent_id');

        //获取文章广告信息
        $advertisingData=[];
        $advertisingTmp=$this->db->Name('xcx_article_advertising')->select()->where_equalTo('status',1)->execute();
        foreach($advertisingTmp as $val){
            for($i=0;$i<$val['weight'];$i++){
                $advertisingData[]=$val;
            }
        }
        
        if(count($advertisingData) <= 0){
            $int = 0;
        }else{
            $int=mt_rand(0,count($advertisingData)-1);
        }

        $data['advertisingInfo']=$advertisingData[$int];

        //获取文章详情
        $articleInfo=$this->db2->Name('news')->select('title,content,addtime,click,area_id,description')->where_equalTo('id',$id)->firstRow();
        if(empty($articleInfo)){
            $articleInfo=[];
        }else{
            $articleInfo['create_time']=$this->format_dates(strtotime($articleInfo['addtime']));
            $articleInfo['content'] = str_replace("src=\"","src=\"http://www.999house.com",$articleInfo['content']);
            $articleInfo['read_num'] = $articleInfo['click'];
            $articleInfo['logo'] = '/upload/default/default_head.png';
            $articleInfo['author']= "九房网";
            $articleInfo['province']= "福建省";
            if($articleInfo['area_id']==10){$articleInfo['city']='厦门';}
            elseif($articleInfo['area_id']==11){$articleInfo['city']='漳州';}
            elseif($articleInfo['area_id']==12){$articleInfo['city']='泉州';}
            elseif($articleInfo['area_id']==13){$articleInfo['city']='龙岩';}
            elseif($articleInfo['area_id']==591){$articleInfo['city']='福州';}
            else{$articleInfo['city']='厦门';}
            $articleInfo['cover']= '/upload/default/default_head.png';
        }
        $data['articleInfo']=$articleInfo;
        //获取该文章所对应的2条评论
        $commentsData=$this->db->Name('xcx_article_comments')->select()->page(1,6)->where_equalTo('aid',$id)->where_equalTo('status',1)->where_equalTo('root_id',0)->orderBy('create_time')->execute();
        if(empty($commentsData)){
            $commentsData=[];
        }else{
            $uids=[];$uDict=[];     //小程序
            $uids2=[];$uDict2=[];   //经纪人
            foreach($commentsData as $v){
                if($v['user_type']=='1')
                    $uids[]=$v['uid'];
                else
                    $uids2[]=$v['uid'];
            }
            $uids=array_unique($uids);
            $uids2=array_unique($uids2);
            $userRow=(new Query())->Name('xcx_user')->select()->where_in('id',$uids)->execute();
            $user2Row=(new Query())->Name('xcx_agent_user')->select()->where_in('id',$uids2)->execute();
            if(!empty($userRow)){
                foreach($userRow as $v2){
                    $uDict[$v2['id']]['nickName']=$v2['nickName'];
                    $uDict[$v2['id']]['avatarUrl']=$v2['avatarUrl'];
                }
            }
            if(!empty($user2Row)){
                foreach($user2Row as $v22){
                    $uDict2[$v22['id']]['nickName']=$v22['nickname'];
                    $uDict2[$v22['id']]['avatarUrl']=$v22['headimgurl'];
                }
            }
            foreach($commentsData as &$comval){
                $comval['nickName']=$comval['user_type']=='1'?$uDict[$comval['uid']]['nickName']:$uDict2[$comval['uid']]['nickName'];
                $comval['avatarUrl']=$comval['user_type']=='1'?$uDict[$comval['uid']]['avatarUrl']:$uDict2[$comval['uid']]['avatarUrl'];
                $comval['create_time']=$this->format_dates($comval['create_time']);
            }
        }
        $data['commentsInfo']=$commentsData;

        echo json_encode($data,JSON_UNESCAPED_UNICODE);
    }


    /**********************************************获取文章详情数据(作废)***********************************************************/
    //获取文章详情数据
    public function getArticleDetailDataDel(){
        $id=Context::Post('id');    //文章id
        $data['agent_id'] = Session::get('agent_id');   //经纪人id
        //获取文章详情
        $articleInfo=$this->db->Name('xcx_article_article')->select()->where_equalTo('id',$id)->firstRow();
        if(empty($articleInfo)){
            $articleInfo=[];
        }else{
            $articleInfo['create_time']=$this->format_dates($articleInfo['create_time']);
            //获取发布者信息
            $adminUser=$this->db->Name('admin')->select()->where_equalTo('id',$articleInfo['aid'])->firstRow();
            if(!empty($adminUser)){
                $articleInfo['logo']=$adminUser['img'];
                $articleInfo['author']=$adminUser['name'];
            } else {
                $articleInfo['logo'] = '/upload/default/default_head.png';
                $articleInfo['author']= "九房网";
            }
            $articleInfo['read_num']=$articleInfo['read_num']>=10000?sprintf("%.1f",$articleInfo['read_num']/10000).'万':$articleInfo['read_num'];
        }
        $data['articleInfo']=$articleInfo;
        //获取该文章所对应的2条评论
        /* $commentsData=$this->db->Name('xcx_article_comments')->select()->page(1,6)->where_equalTo('aid',$id)->where_equalTo('status',1)->where_equalTo('root_id',0)->orderBy('create_time')->execute();
         if(empty($commentsData)){
             $commentsData=[];
         }else{
             $uids=[];$uDict=[];     //小程序
             $uids2=[];$uDict2=[];   //经纪人
             foreach($commentsData as $v){
                 if($v['user_type']=='1')
                     $uids[]=$v['uid'];
                 else
                     $uids2[]=$v['uid'];
             }
             $uids=array_unique($uids);
             $uids2=array_unique($uids2);
             $userRow=(new Query())->Name('xcx_user')->select()->where_in('id',$uids)->execute();
             $user2Row=(new Query())->Name('xcx_agent_user')->select()->where_in('id',$uids2)->execute();
             if(!empty($userRow)){
                 foreach($userRow as $v2){
                     $uDict[$v2['id']]['nickName']=$v2['nickName'];
                     $uDict[$v2['id']]['avatarUrl']=$v2['avatarUrl'];
                 }
             }
             if(!empty($user2Row)){
                 foreach($user2Row as $v22){
                     $uDict2[$v22['id']]['nickName']=$v22['nickname'];
                     $uDict2[$v22['id']]['avatarUrl']=$v22['headimgurl'];
                 }
             }
             foreach($commentsData as &$comval){
                 $comval['nickName']=$comval['user_type']=='1'?$uDict[$comval['uid']]['nickName']:$uDict2[$comval['uid']]['nickName'];
                 $comval['avatarUrl']=$comval['user_type']=='1'?$uDict[$comval['uid']]['avatarUrl']:$uDict2[$comval['uid']]['avatarUrl'];
                 $comval['create_time']=$this->format_dates($comval['create_time']);
             }
         }
         $data['commentsInfo']=$commentsData;*/
        //获取文章广告信息
        $advertisingData=[];
        $advertisingTmp=$this->db->Name('xcx_article_advertising')->select()->where_equalTo('status',1)->execute();
        foreach($advertisingTmp as $val){
            for($i=0;$i<$val['weight'];$i++){
                $advertisingData[]=$val;
            }
        }
        $int=mt_rand(0,count($advertisingData)-1);
        $data['advertisingInfo']=$advertisingData[$int];
        //获取10条新闻推荐
        $zxNeowData=$this->db->Name('xcx_article_article')->select()->page(1,5)->where_notEqualTo('id',$id)->where_equalTo('is_hot',1)->where_equalTo('status',1)->orderBy('update_time','desc')->execute();
        if(empty($zxNeowData)){$zxNeowData=[];}
        $data['zxInfo']=$zxNeowData;
        //获取最新的10条楼盘信息 暂时去掉
//        $buildingData=$this->db->Name('xcx_building_building')->select()->where_equalTo('is_hot',1)->where_equalTo('status',1)->page(1,self::MYLIMIT)->orderBy('create_time','desc')->execute();
//        if(!empty($buildingData)){
//            foreach($buildingData as &$val){
//                $val['fold']=floatval($val['fold']);
//                $val['commission']=floatval($val['commission']);
//                $val['flag']=empty($val['flag'])?[]:explode(',',$val['flag']);
//            }
//        }else{
//            $buildingData=[];
//        }
//        $buildingData=[];
//        $data['buildingInfo']=$buildingData;
        echo json_encode($data,JSON_UNESCAPED_UNICODE);
    }



    /************************************************九房网文章评论**************************************************************/
    public function addComments(){
        $data['aid'] = Context::Post('aid');
        $data['uid'] = Session::get('agent_id');
        $data['user_type'] = '2';
        $data['content'] = Context::Post('content');
        $data['create_time'] = time();
        $data['update_time'] = time();
        $res=$this->db->Name('xcx_article_comments')->insert($data)->execute();
        $pdo = new DataBase();
        $pdo->beginTransaction();
        if($res){
            $commentsData[0] = $data;
            if(1 == $data['user_type']) {// 小程序客户端
                $userTable = 'xcx_user';
                $fields = "nickName, avatarUrl";
            } else {// 经纪人端
                $userTable = 'xcx_agent_user';
                $fields = "nickName, headimgurl as avatarUrl";
            }
            $userRow = (new Query())->Name($userTable)->select($fields)->where_equalTo('id', $data['uid'])->firstRow();
            $commentsData[0]['nickName'] = !empty($userRow['nickName']) ? $userRow['nickName'] : "";
            $commentsData[0]['avatarUrl'] = !empty($userRow['avatarUrl']) ? $userRow['avatarUrl'] : "";

            $commentsData[0]['create_time'] = $this->format_dates($data['create_time']);
            $commentsData[0]['praise_num'] = 0;
            $commentsData[0]['parent_id'] = 0;
            $commentsData[0]['root_id'] = 0;
            $commentsData[0]['id'] = $res;
            $commentsData[0]['status'] = 1;
            return $this->success(['commentsInfo' => $commentsData]);
        }else{
            $pdo->rollBack();
            return $this->error();
        }
    }
    /************************************************文章内部评论（作废）****************************************************************/
    //添加评论
    public function addCommentsDel(){
        $data['aid'] = Context::Post('aid');
        $data['uid'] = Session::get('agent_id');
        $data['user_type'] = '2';
        $data['content'] = Context::Post('content');
        $data['create_time'] = time();
        $data['update_time'] = time();

        $pdo = new DataBase();
        $pdo->beginTransaction(); // 开启一个事务

        $res=$this->db->Name('xcx_article_comments')->insert($data)->execute();
        if($res){
            // 更新文章评论数
            $sqlUpdate = "UPDATE " . Table_Pre . "xcx_article_article SET `comments_num`=`comments_num`+1 WHERE `id` = :id";
            $whereUpdate = [
                ":id" => $data['aid'],
            ];
            $resComment = DataBase::Update($sqlUpdate, $whereUpdate);
            if(!empty($resComment)) {
                $pdo->commit();
            } else {
                $pdo->rollBack();
                return $this->error();
            }

            $commentsData[0] = $data;
            if(1 == $data['user_type']) {// 小程序客户端
                $userTable = 'xcx_user';
                $fields = "nickName, avatarUrl";
            } else {// 经纪人端
                $userTable = 'xcx_agent_user';
                $fields = "nickName, headimgurl as avatarUrl";
            }
            $userRow = (new Query())->Name($userTable)->select($fields)->where_equalTo('id', $data['uid'])->firstRow();
            $commentsData[0]['nickName'] = !empty($userRow['nickName']) ? $userRow['nickName'] : "";
            $commentsData[0]['avatarUrl'] = !empty($userRow['avatarUrl']) ? $userRow['avatarUrl'] : "";

            $commentsData[0]['create_time'] = $this->format_dates($data['create_time']);
            $commentsData[0]['praise_num'] = 0;
            $commentsData[0]['parent_id'] = 0;
            $commentsData[0]['root_id'] = 0;
            $commentsData[0]['id'] = $res;
            $commentsData[0]['status'] = 1;


//            //获取新添加的评论数据
//            $commentsData=$this->db->Name('xcx_article_comments')->select()->where_equalTo('id',$res)->execute();
//            $uids=[];$uDict=[];     //小程序
//            $uids2=[];$uDict2=[];   //经纪人
//            foreach($commentsData as $v){
//                if($v['user_type']=='1')
//                    $uids[]=$v['uid'];
//                else
//                    $uids2[]=$v['uid'];
//            }
//            $uids=array_unique($uids);
//            $uids2=array_unique($uids2);
//            $userRow=(new Query())->Name('xcx_user')->select()->where_in('id',$uids)->execute();
//            $user2Row=(new Query())->Name('xcx_agent_user')->select()->where_in('id',$uids2)->execute();
//            if(!empty($userRow)){
//                foreach($userRow as $v2){
//                    $uDict[$v2['id']]['nickName']=$v2['nickName'];
//                    $uDict[$v2['id']]['avatarUrl']=$v2['avatarUrl'];
//                }
//            }
//            if(!empty($user2Row)){
//                foreach($user2Row as $v22){
//                    $uDict2[$v22['id']]['nickName']=$v22['nickname'];
//                    $uDict2[$v22['id']]['avatarUrl']=$v22['headimgurl'];
//                }
//            }
//            foreach($commentsData as &$comval){
//                $comval['nickName']=$comval['user_type']=='1'?$uDict[$comval['uid']]['nickName']:$uDict2[$comval['uid']]['nickName'];
//                $comval['avatarUrl']=$comval['user_type']=='1'?$uDict[$comval['uid']]['avatarUrl']:$uDict2[$comval['uid']]['avatarUrl'];
//                $comval['create_time']=$this->format_dates($comval['create_time']);
//            }
//            echo json_encode(['success'=>true,'commentsInfo'=>$commentsData],JSON_UNESCAPED_UNICODE);
            return $this->success(['commentsInfo' => $commentsData]);
        }else{
            $pdo->rollBack();
//            echo json_encode(['success'=>false]);
            return $this->error();
        }
    }
    //回复详情评论
    public function addDetailComments(){
        $data['aid'] = Context::Post('aid');
        $data['uid'] = Session::get('agent_id');
        $data['user_type'] = '2';
        $data['content'] = Context::Post('content');
        $data['parent_id'] = Context::Post('parent_id');
        $data['root_id'] = Context::Post('root_id');
        $data['create_time'] = time();
        $data['update_time'] = time();
        //检测是否自己给自己回复
        $parentData=$this->db->Name('xcx_article_comments')->select()->where_equalTo('id',$data['parent_id'])->firstRow();
        if(!empty($parentData['parent_id']) && $parentData['uid']==$data['uid'] && $parentData['user_type']=='2'){
            $data['parent_id'] = Context::Post('root_id');
        }
        // 获取被回复人的昵称
        if(!empty($parentData)) {
            $replyUid = $parentData['uid'];// 被回复人ID
            $replyUserType = $parentData['user_type'];// 被回复人来源 1-小程序 2-经纪人
        }

        $pdo = new DataBase();
        $pdo->beginTransaction(); // 开启一个事务

        $res=$this->db->Name('xcx_article_comments')->insert($data)->execute();
        if($res){
            // 更新文章评论数
            /*$sqlUpdate = "UPDATE " . Table_Pre . "xcx_article_article SET `comments_num`=`comments_num`+1 WHERE `id` = :id";
            $whereUpdate = [
                ":id" => $data['aid'],
            ];
            $resComment = DataBase::Update($sqlUpdate, $whereUpdate);
            if(empty($resComment)) {
                $pdo->rollBack();
                return $this->error();
            }*/
            // 更新评论的评论数
            $sqlComments = "UPDATE " . Table_Pre . "xcx_article_comments SET `comments_num`=`comments_num`+1 WHERE `id` = :id";
            $whereComments = [
                ":id" => $data['root_id'],
            ];
            $resCommentRoot = DataBase::Update($sqlComments, $whereComments);
            if(empty($resCommentRoot)) {
                $pdo->rollBack();
                return $this->error();
            }
            $pdo->commit();

            $commentsData[0] = $data;
            $typeInfo = [// 不同用户来源需要查不同的表和字段
                '1' => [
                    'table' => 'xcx_user',
                    'fields' => "id, nickName, avatarUrl",
                ],
                '2' => [
                    'table' => 'xcx_agent_user',
                    'fields' => "id, nickName, headimgurl as avatarUrl",
                ],
            ];
            // 查询回复双方的信息
            $commentsData[0]['nickName'] = "";
            $commentsData[0]['avatarUrl'] = "";
            $commentsData[0]['replyName'] = "";
            if($replyUserType == $data['user_type']) {// 回复人和被回复人来源是否相同
                $uids = [$data['uid'], $replyUid];
                $uids = array_unique($uids);
                $userRow = (new Query())->Name($typeInfo[$data['user_type']]['table'])->select($typeInfo[$data['user_type']]['fields'])->where_in('id', $uids)->execute();
                if(!empty($userRow)) {
                    foreach ($userRow as $v) {
                        if($data['uid'] == $v['id']) {
                            $commentsData[0]['nickName'] = $v['nickName'];
                            $commentsData[0]['avatarUrl'] = $v['avatarUrl'];
                        }
                        if($replyUid == $v['id']) {
                            $commentsData[0]['replyName'] = $v['nickName'];
                        }
                    }
                }
            } else {
                // 回复人
                $userRow = (new Query())->Name($typeInfo[$data['user_type']]['table'])->select($typeInfo[$data['user_type']]['fields'])->where_equalTo('id', $data['uid'])->firstRow();
                if(!empty($userRow)) {
                    $commentsData[0]['nickName'] = $userRow['nickName'];
                    $commentsData[0]['avatarUrl'] = $userRow['avatarUrl'];
                }
                // 被回复人
                $replyRow = (new Query())->Name($typeInfo[$replyUserType]['table'])->select($typeInfo[$replyUserType]['fields'])->where_equalTo('id', $replyUid)->firstRow();
                if(!empty($replyRow)) {
                    $commentsData[0]['replyName'] = $replyRow['nickName'];
                }
            }
            // 归纳信息
            $commentsData[0]['create_time'] = $this->format_dates($data['create_time']);

            $commentsData[0]['id'] = $res;
            $commentsData[0]['status'] = 1;
            $commentsData[0]['praise_num'] = 0;

//            //获取新添加的评论数据
//            $commentsData=$this->db->Name('xcx_article_comments')->select()->where_equalTo('id',$res)->execute();
//            $uids=[];$uDict=[];     //小程序
//            $uids2=[];$uDict2=[];   //经纪人
//            // 加入查询被回复人昵称
//            if(!empty($replyUid)) {
//                $uids[] = $replyUid;
//                $uids2[] = $replyUid;
//            }
//            foreach($commentsData as $v){
//                if($v['user_type']=='1')
//                    $uids[]=$v['uid'];
//                else
//                    $uids2[]=$v['uid'];
//            }
//            $uids=array_unique($uids);
//            $uids2=array_unique($uids2);
//            $userRow=(new Query())->Name('xcx_user')->select()->where_in('id',$uids)->execute();
//            $user2Row=(new Query())->Name('xcx_agent_user')->select()->where_in('id',$uids2)->execute();
//            if(!empty($userRow)){
//                foreach($userRow as $v2){
//                    $uDict[$v2['id']]['nickName']=$v2['nickName'];
//                    $uDict[$v2['id']]['avatarUrl']=$v2['avatarUrl'];
//                }
//            }
//            if(!empty($user2Row)){
//                foreach($user2Row as $v22){
//                    $uDict2[$v22['id']]['nickName']=$v22['nickname'];
//                    $uDict2[$v22['id']]['avatarUrl']=$v22['headimgurl'];
//                }
//            }
//            foreach($commentsData as &$comval){
//                $comval['nickName']=$comval['user_type']=='1'?$uDict[$comval['uid']]['nickName']:$uDict2[$comval['uid']]['nickName'];
//                $comval['avatarUrl']=$comval['user_type']=='1'?$uDict[$comval['uid']]['avatarUrl']:$uDict2[$comval['uid']]['avatarUrl'];
//                $comval['create_time']=$this->format_dates($comval['create_time']);
//                $comval['replyName'] = $replyUserType == '1' ? $uDict[$replyUid]['nickName'] : $uDict2[$replyUid]['nickName'];
//            }
//            echo json_encode(['success'=>true,'commentsInfo'=>$commentsData],JSON_UNESCAPED_UNICODE);

            return $this->success(['commentsInfo' => $commentsData]);
        }else{
            $pdo->rollBack();
            return $this->error();
        }
    }
    //获取评论数据
    public function getReply(){
        $page = Context::Post('page');
        $aid= Context::Post('aid');
        $agent_id = Session::get('agent_id');

        $commentsData=$this->db->Name('xcx_article_comments')->select()->page($page,6)->where_equalTo('aid',$aid)->where_equalTo('status',1)->where_equalTo('root_id',0)->orderBy('create_time')->execute();
        if(!empty($commentsData)){
            $uids=[];$uDict=[];     //小程序
            $uids2=[];$uDict2=[];   //经纪人
            foreach($commentsData as $v){
                if($v['user_type']=='1')
                    $uids[]=$v['uid'];
                else
                    $uids2[]=$v['uid'];
            }
            $uids=array_unique($uids);
            $uids2=array_unique($uids2);
            $userRow=(new Query())->Name('xcx_user')->select()->where_in('id',$uids)->execute();
            $user2Row=(new Query())->Name('xcx_agent_user')->select()->where_in('id',$uids2)->execute();
            if(!empty($userRow)){
                foreach($userRow as $v2){
                    $uDict[$v2['id']]['nickName']=$v2['nickName'];
                    $uDict[$v2['id']]['avatarUrl']=$v2['avatarUrl'];
                }
            }
            if(!empty($user2Row)){
                foreach($user2Row as $v22){
                    $uDict2[$v22['id']]['nickName']=$v22['nickname'];
                    $uDict2[$v22['id']]['avatarUrl']=$v22['headimgurl'];
                }
            }

            if(!empty($agent_id)){
                $commentsIds = array_column($commentsData, 'id');
                $replyRes = $this->db->Name('xcx_article_comments_praise')->select()->where_in('cid', $commentsIds)->where_equalTo('uid',$agent_id)->where_equalTo('user_type','2')->where_equalTo('status','1')->execute();
                $replyResCids = array_column($replyRes, 'cid');// 获取当前评论里，自己有点赞的评论
                foreach($commentsData as &$comval){
//                $is_reply=$this->db->Name('xcx_article_comments_praise')->select()->where_equalTo('cid',$comval['id'])->where_equalTo('uid',$agent_id)->where_equalTo('user_type','2')->where_equalTo('status','1')->firstRow();
//                $comval['is_reply']=empty($is_reply)?false:true;    //是否点赞
//                $comval['replyNum']=$this->db->Name('xcx_article_comments')->select('count(*)')->where_equalTo('root_id',$comval['id'])->firstColumn();
                    $comval['is_reply'] = in_array($comval['id'], $replyResCids) ? TRUE : FALSE;    //是否点赞
                    $comval['replyNum'] = $comval['comments_num'];
                    $comval['nickName']=$comval['user_type']=='1'?$uDict[$comval['uid']]['nickName']:$uDict2[$comval['uid']]['nickName'];
                    $comval['avatarUrl']=$comval['user_type']=='1'?$uDict[$comval['uid']]['avatarUrl']:$uDict2[$comval['uid']]['avatarUrl'];
                    $comval['create_time']=$this->format_dates($comval['create_time']);
                }
            }

//            echo json_encode(['success'=>true,'data'=>$commentsData],JSON_UNESCAPED_UNICODE);
            return $this->success(['data'=>$commentsData]);
        }else{
//            echo json_encode(['success'=>false]);
            return $this->success();
        }
    }
//    //获取详情评论数据
//    public function getDetailReply(){
//        $page = Context::Post('page');
//        $root_id = Context::Post('root_id');
//        $agent_id = Session::get('agent_id');
//        if(!empty($root_id)){
//            $commentsData=$this->db->Name('xcx_article_comments')->select()->page($page,6)->where_equalTo('root_id',$root_id)->orderBy('create_time')->execute();
//            if(!empty($commentsData)){
//                $uids=[];$uDict=[];     //小程序
//                $uids2=[];$uDict2=[];   //经纪人
//                foreach($commentsData as $v){
//                    if($v['user_type']=='1')
//                        $uids[]=$v['uid'];
//                    else
//                        $uids2[]=$v['uid'];
//                }
//                $uids=array_unique($uids);
//                $uids2=array_unique($uids2);
//                $userRow=(new Query())->Name('xcx_user')->select()->where_in('id',$uids)->execute();
//                $user2Row=(new Query())->Name('xcx_agent_user')->select()->where_in('id',$uids2)->execute();
//                if(!empty($userRow)){
//                    foreach($userRow as $v2){
//                        $uDict[$v2['id']]['nickName']=$v2['nickName'];
//                        $uDict[$v2['id']]['avatarUrl']=$v2['avatarUrl'];
//                    }
//                }
//                if(!empty($user2Row)){
//                    foreach($user2Row as $v22){
//                        $uDict2[$v22['id']]['nickName']=$v22['nickname'];
//                        $uDict2[$v22['id']]['avatarUrl']=$v22['headimgurl'];
//                    }
//                }
//                //获取回复人名称
//                $parentIds=[];
//                $parentDict=[];
//                foreach($commentsData as $v){
//                    $parentIds[]=$v['parent_id'];
//                }
//                $parentData=$this->db->Name('xcx_article_comments')->select()->where_in('id',$parentIds)->execute();
//                $uuids=[];$uuDict=[];     //小程序
//                $uuids2=[];$uuDict2=[];   //经纪人
//                foreach($parentData as $val){
//                    if($val['user_type']=='1')
//                        $uuids[]=$val['uid'];
//                    else
//                        $uuids2[]=$val['uid'];
//                }
//                $uuids=array_unique($uuids);
//                $uuids2=array_unique($uuids2);
//                $userRow=(new Query())->Name('xcx_user')->select()->where_in('id',$uuids)->execute();
//                $user2Row=(new Query())->Name('xcx_agent_user')->select()->where_in('id',$uuids2)->execute();
//                if(!empty($userRow)){
//                    foreach($userRow as $v2){
//                        $uuDict[$v2['id']]['nickName']=$v2['nickName'];
//                    }
//                }
//                if(!empty($user2Row)){
//                    foreach($user2Row as $v22){
//                        $uuDict2[$v22['id']]['nickName']=$v22['nickname'];
//                    }
//                }
//                foreach($parentData as $val2){
//                    if($val2['user_type']=='1')
//                        $parentDict[$val2['id']]=empty($uuDict[$val2['uid']]['nickName'])?'':$uuDict[$val2['uid']]['nickName'];
//                    else
//                        $parentDict[$val2['id']]=empty($uuDict2[$val2['uid']]['nickName'])?'':$uuDict2[$val2['uid']]['nickName'];
//                }
//                foreach($commentsData as &$comval){
//                    $is_reply=$this->db->Name('xcx_article_comments_praise')->select()->where_equalTo('cid',$comval['id'])->where_equalTo('uid',$agent_id)->where_equalTo('user_type','2')->where_equalTo('status','1')->firstRow();
//                    $comval['is_reply']=empty($is_reply)?false:true;    //是否点赞
//                    $comval['replyName']=$parentDict[$comval['parent_id']];
//                    $comval['nickName']=$comval['user_type']=='1'?$uDict[$comval['uid']]['nickName']:$uDict2[$comval['uid']]['nickName'];
//                    $comval['avatarUrl']=$comval['user_type']=='1'?$uDict[$comval['uid']]['avatarUrl']:$uDict2[$comval['uid']]['avatarUrl'];
//                    $comval['create_time']=$this->format_dates($comval['create_time']);
//                }
//                echo json_encode(['success'=>true,'data'=>$commentsData],JSON_UNESCAPED_UNICODE);
//            }else{
//                echo json_encode(['success'=>false]);
//            }
//        }else{
//            echo json_encode(['success'=>false]);
//        }
//    }

    //获取详情评论数据  修改版 2020-05-18
    public function getDetailReply(){
        $page = Context::Post('page');
        $root_id = Context::Post('root_id');
        $agent_id = Session::get('agent_id');
        if(!empty($root_id)){
            $commentsData=$this->db->Name('xcx_article_comments')->select()->page($page,6)->where_equalTo('root_id',$root_id)->orderBy('create_time')->execute();
            if(!empty($commentsData)){
                $parentIds=[];
                $parentDict=[];
                foreach($commentsData as $v){
                    $parentIds[]=$v['parent_id'];
                }
                $parentData=$this->db->Name('xcx_article_comments')->select()->where_in('id',$parentIds)->execute();

                // 查找回复人和被回复人的信息
                $uids=[];$uDict=[];     //小程序
                $uids2=[];$uDict2=[];   //经纪人
                $userData = array_merge($commentsData, $parentData);// 将回复人和被回复人的ID集合，统一查询
                foreach($userData as $v){
                    if($v['user_type']=='1')
                        $uids[]=$v['uid'];
                    else
                        $uids2[]=$v['uid'];
                }

                $uids=array_unique($uids);
                $uids2=array_unique($uids2);
                $userRow=(new Query())->Name('xcx_user')->select()->where_in('id',$uids)->execute();
                $user2Row=(new Query())->Name('xcx_agent_user')->select()->where_in('id',$uids2)->execute();
                if(!empty($userRow)){
                    foreach($userRow as $v2){
                        $uDict[$v2['id']]['nickName']=$v2['nickName'];
                        $uDict[$v2['id']]['avatarUrl']=$v2['avatarUrl'];
                    }
                }
                if(!empty($user2Row)){
                    foreach($user2Row as $v22){
                        $uDict2[$v22['id']]['nickName']=$v22['nickname'];
                        $uDict2[$v22['id']]['avatarUrl']=$v22['headimgurl'];
                    }
                }

                foreach($parentData as $val2){
                    if($val2['user_type']=='1')
                        $parentDict[$val2['id']]=empty($uDict[$val2['uid']]['nickName'])?'':$uDict[$val2['uid']]['nickName'];
                    else
                        $parentDict[$val2['id']]=empty($uDict2[$val2['uid']]['nickName'])?'':$uDict2[$val2['uid']]['nickName'];
                }

                $commentsIds = array_column($commentsData, 'id');
                $replyRes = $this->db->Name('xcx_article_comments_praise')->select()->where_in('cid', $commentsIds)->where_equalTo('uid',$agent_id)->where_equalTo('user_type','2')->where_equalTo('status','1')->execute();
                $replyResCids = array_column($replyRes, 'cid');

                foreach($commentsData as &$comval){
                    $comval['is_reply'] = in_array($comval['id'], $replyResCids) ? TRUE : FALSE;    //是否点赞
                    $comval['replyName']=$parentDict[$comval['parent_id']];
                    $comval['nickName']=$comval['user_type']=='1'?$uDict[$comval['uid']]['nickName']:$uDict2[$comval['uid']]['nickName'];
                    $comval['avatarUrl']=$comval['user_type']=='1'?$uDict[$comval['uid']]['avatarUrl']:$uDict2[$comval['uid']]['avatarUrl'];
                    $comval['create_time']=$this->format_dates($comval['create_time']);
                }
                return $this->success(['data' => $commentsData]);
            }else{
                return $this->error();
            }
        }else{
            return $this->error();
        }
    }

    //获取文章所对应的评论
    public function getArticleCommentsData(){
        $aid=Context::Post('aid');
        $page=Context::Post('page');
        $commentsData=$this->db->Name('xcx_article_comments')->select()->where_equalTo('aid',$aid)->where_equalTo('status',1)->page($page,self::MYLIMIT)->orderBy('create_time','desc')->execute();
        if(!empty($commentsData)){
            $uids=[];$uDict=[];     //小程序
            $uids2=[];$uDict2=[];   //经纪人
            foreach($commentsData as $v){
                if($v['user_type']=='1')
                    $uids[]=$v['uid'];
                else
                    $uids2[]=$v['uid'];
            }
            $uids=array_unique($uids);
            $uids2=array_unique($uids2);
            $userRow=(new Query())->Name('xcx_user')->select()->where_in('id',$uids)->execute();
            $user2Row=(new Query())->Name('xcx_agent_user')->select()->where_in('id',$uids2)->execute();
            if(!empty($userRow)){
                foreach($userRow as $v2){
                    $uDict[$v2['id']]['nickName']=$v2['nickName'];
                    $uDict[$v2['id']]['avatarUrl']=$v2['avatarUrl'];
                }
            }
            if(!empty($user2Row)){
                foreach($user2Row as $v22){
                    $uDict2[$v22['id']]['nickName']=$v22['nickname'];
                    $uDict2[$v22['id']]['avatarUrl']=$v22['headimgurl'];
                }
            }
            foreach($commentsData as &$comval){
                $comval['nickName']=$comval['user_type']=='1'?$uDict[$comval['uid']]['nickName']:$uDict2[$comval['uid']]['nickName'];
                $comval['avatarUrl']=$comval['user_type']=='1'?$uDict[$comval['uid']]['avatarUrl']:$uDict2[$comval['uid']]['avatarUrl'];
                $comval['create_time']=$this->format_dates($comval['create_time']);
            }
            echo json_encode(['success'=>true,'data'=>$commentsData],JSON_UNESCAPED_UNICODE);
        }else{
            echo json_encode(['success'=>false]);
        }
    }
    //设置点赞
    public function setPraise(){
        $cid=Context::Post('cid');
        $uid=Session::get('agent_id');
        $status=empty(Context::Post('status'))?'0':'1';
        $praiseRow=$this->db->Name('xcx_article_comments_praise')->select()->where_equalTo('cid',$cid)->where_equalTo('uid',$uid)->where_equalTo('user_type','2')->firstRow();
        if(empty($praiseRow)){  //添加操作
            $res=$this->db->Name('xcx_article_comments_praise')->insert(['cid'=>$cid,'uid'=>$uid,'user_type'=>'2','status'=>$status,'create_time'=>time(),'update_time'=>time()])->execute();
        }else{  //修改服务
            $res=$this->db->Name('xcx_article_comments_praise')->update(['status'=>$status,'update_time'=>time()])->where_equalTo('cid',$cid)->where_equalTo('uid',$uid)->where_equalTo('user_type','2')->execute();
        }
        if($res){
            if(empty($status)){ //减少
                $commentsData=$this->db->Name('xcx_article_comments')->select()->where_equalTo('id',$cid)->firstRow();
                if(!empty($commentsData) && !empty($commentsData['praise_num'])){
                    $sql="UPDATE ".Table_Pre."xcx_article_comments SET `praise_num` = `praise_num`-1 WHERE `id` = :id";
                    $arr=[":id"=>$cid];
                    DataBase::Update($sql,$arr);
                }
            }else{  //添加
                $sql="UPDATE ".Table_Pre."xcx_article_comments SET `praise_num` = `praise_num`+1 WHERE `id` = :id";
                $arr=[":id"=>$cid];
                DataBase::Update($sql,$arr);
            }
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false]);
        }
    }
    //搜索文章列表
    public function searchArticleList(){
        $page=Context::Post('page');
        $searchText=Context::Post('searchText');
        if(empty($searchText)){echo json_encode(['success'=>false,'message'=>'请输入搜索内容']);exit;}
        $articleData=$this->db->Name('xcx_article_article')->select()->where_like('title','%'.$searchText.'%')->page($page,self::MYLIMIT)->execute();
        if(!empty($articleData)){
            //获取后台发布者信息
            $aids=[];$aDict=[];
            foreach($articleData as $v){
                $aids[]=$v['aid'];
            }
            $aids=array_unique($aids);
            $adminRow=(new Query())->Name('admin')->select()->where_in('id',$aids)->execute();
            if(!empty($adminRow)){
                foreach($adminRow as $v2){
                    $aDict[$v2['id']]['name']=$v2['name'];
                    $aDict[$v2['id']]['img']=$v2['img'];
                }
                foreach($articleData as &$value){
                    $value['comments_num']=$this->getCommentsNum($value['id']);
                    $value['aname']=$aDict[$value['aid']]['name'];
                    $value['aimg']=$aDict[$value['aid']]['img'];
                    $value['release_time']=$this->format_dates($value['create_time']);
                }
            }
        }else{
            $articleData=[];
        }
        echo json_encode(['success'=>true,'articleList'=>$articleData]);
    }
    //获取首页数据信息
    public function getArticleHome()
    {
        //方式一:九房网新闻获取
        $figure=$this->db2->Name('news')->select('id,title,wap_pic,addtime')->where_equalTo('flag','t,i')->where_equalTo('belong',15)->orderBy('addtime','desc')->orderBy('orders')->limit(0,5)->execute();
        foreach ($figure as $key=>$value){
            $data['figure'][$key]['id']=$value['id'];
            $data['figure'][$key]['create_time']=strtotime($value['addtime']);
            $data['figure'][$key]['update_time']=strtotime($value['addtime']);
            $data['figure'][$key]['img']='http://www.999house.com/'.$value['wap_pic'];
            $data['figure'][$key]['title']=$value['title'];
            $data['figure'][$key]['url']='';
            $data['figure'][$key]['sort']='';
            $data['figure'][$key]['status']='';
        }

        /*$uids=[15,1,10,14,13,4,9];
        $newdata=$this->db2->Name('newstype')->select()->where_in('id',$uids)->execute();
        foreach ($newdata as $key=>$value){
            $data['column'][$key]['id']=$value['id'];
            $data['column'][$key]['title']=$value['typename'];
            $data['column'][$key]['sort']=$value['orders'];
        }*/

        $data['column'][0]['id']=15;
        $data['column'][0]['title']='九房原创';
        $data['column'][1]['id']=1;
        $data['column'][1]['title']='本地咨讯';
        //$data['column'][2]['id']=10;
        //$data['column'][2]['title']='国内新闻';
        $data['column'][2]['id']=14;//2
        $data['column'][2]['title']='最新城建';
        $data['column'][3]['id']=13;//2
        $data['column'][3]['title']='最新开盘';
        $data['column'][4]['id']=4;
        $data['column'][4]['title']='新房快讯';
        $data['column'][5]['id']=9;//2
        $data['column'][5]['title']='土地拍卖';

        //方式二:本地新闻获取
        //获取首页轮播图
        /*$data['figure']=$this->db->Name('xcx_article_figure')->select()->where_equalTo('status',1)->orderBy('sort')->execute();
        //获取文章栏目
        $data['column']=$this->db->Name('xcx_article_column')->select()->where_equalTo('status',1)->orderBy('sort')->execute();
        if(!empty($data['column'])){
            array_unshift($data['column'],['id'=>0,'title'=>'推荐']);
        }*/
        return $this->success($data);
    }
    public function getDataInfo(){
        //方式一:九房网新闻获取
        $column = !empty(Context::Post('column')) ? Context::Post('column') : 0;
        $page = !empty(Context::Post('page')) ? Context::Post('page') : 1;
        $pageSize = !empty(Context::Post('page_size')) ? Context::Post('page_size') : self::MYLIMIT;
        if($pageSize > 100) {
            return $this->error('获取数据条数超过限制');
        }
        $data['article'] = $this->get9hArticleData($column, $page, $pageSize);
        return $this->success($data);

        //方式二:本地新闻获取
        /*$column = !empty(Context::Post('column')) ? Context::Post('column') : 0;
        $page = !empty(Context::Post('page')) ? Context::Post('page') : 1;
        $pageSize = !empty(Context::Post('page_size')) ? Context::Post('page_size') : self::MYLIMIT;
        if($pageSize > 100) {
            return $this->error('获取数据条数超过限制');
        }
        $data['article'] = $this->getArticleData($column, $page, $pageSize);
        return $this->success($data);*/


//        //获取首页轮播图
//        $data['figure']=$this->db->Name('xcx_article_figure')->select()->where_equalTo('status',1)->orderBy('sort')->execute();
//        //获取文章栏目
//        $data['column']=$this->db->Name('xcx_article_column')->select()->where_equalTo('status',1)->orderBy('sort')->execute();
//        //获取栏目说对应的文章信息
//        $data['article']=[];
//        if(!empty($data['column'])){
//            array_unshift($data['column'],['id'=>0,'title'=>'推荐']);
//            foreach($data['column'] as $key=>$val){
//                $data['column'][$key]['page']=1;    //初始化页数
//                $data['article'][$key]=$this->getArticleData($val['id']);
//            }
//        }
//        echo json_encode(['success'=>true,'data'=>$data],JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param $cid     //栏目id
     * @param $curr     //页数
     * @param $limit    //条数
     * 获取九房网文章数据
     */
    public function get9hArticleData($cid,$curr=1,$limit=10){
        $searchText=Context::Post('searchText');
        $searchText= empty($searchText)?'':$searchText;
        if(empty($cid)){
            $tmpData=$this->db2->Name('news')->select('id,area_id,title,wap_pic,operator,addtime')->where_like('title','%'.$searchText.'%')->where_equalTo('status',1)->page($curr,$limit)->orderBy('addtime','desc')->orderBy('id','desc')->execute();
        }else{
            if($cid==15){$cid=2;$bid=15;}
            elseif($cid==14){$cid=2;$bid=14;}
            elseif($cid==13){$cid=2;$bid=13;}
            elseif($cid==9){$cid=2;$bid=9;}
            else{$bid=0;}
            $tmpData=$this->db2->Name('news')->select('id,area_id,title,wap_pic,operator,addtime')->where_like('title','%'.$searchText.'%')->where_equalTo('status',1)->where_equalTo('type',$cid)->where_equalTo('belong',$bid)->page($curr,$limit)->orderBy('addtime','desc')->orderBy('id','desc')->execute();
        }
        if(!empty($tmpData)){

            foreach($tmpData as &$value){
                $value['cover']='http://www.999house.com/'.$value['wap_pic'];
                $value['aname']='九房网';
                $value['logo']='/upload/default/default_head.png';
                if($value['area_id']==10){$value['area']='厦门';}
                elseif($value['area_id']==11){$value['area']='漳州';}
                elseif($value['area_id']==12){$value['area']='泉州';}
                elseif($value['area_id']==13){$value['area']='龙岩';}
                elseif($value['area_id']==591){$value['area']='福州';}
                else{$value['area']='厦门';}
                $value['comments_num']=$this->getCommentsNum($value['id']);
                //$value['comments_num']=$value['click'];
                $value['release_time']=$this->format_dates(strtotime($value['addtime']));
            }

        }else{
            $tmpData=[];
        }
        return $tmpData;
    }

    /**
     * @param $cid     //栏目id
     * @param $curr     //页数
     * @param $limit    //条数
     * 获取文章数据
     */
    public function getArticleData($cid,$curr=1,$limit=10){
        $field = 'id, cover, title, comments_num';
        if(empty($cid)){
            $tmpData=$this->db->Name('xcx_article_article')->select($field)->where_equalTo('status',1)->where_equalTo('is_hot',1)->page($curr,$limit)->orderBy('create_time','desc')->orderBy('read_num','desc')->execute();
        }else{
            $tmpData=$this->db->Name('xcx_article_article')->select($field)->where_equalTo('status',1)->where_equalTo('cid',$cid)->page($curr,$limit)->orderBy('create_time','desc')->orderBy('read_num','desc')->execute();
        }
        if(!empty($tmpData)){
            //获取后台发布者信息
            $aids=[];$aDict=[];
            foreach($tmpData as $v){
                $aids[]=$v['aid'];
            }
            $aids=array_unique($aids);
            $adminRow=(new Query())->Name('admin')->select()->where_in('id',$aids)->execute();
            if(!empty($adminRow)){
                foreach($adminRow as $v2){
                    $aDict[$v2['id']]['name']=$v2['name'];
                    $aDict[$v2['id']]['img']=$v2['img'];
                }
                foreach($tmpData as &$value){
//                    $value['comments_num']=$this->getCommentsNum($value['id']);
                    $value['aname']=$aDict[$value['aid']]['name'];
                    $value['aimg']=$aDict[$value['aid']]['img'];
                    $value['release_time']=$this->format_dates($value['create_time']);
                }
            } else {
                foreach($tmpData as &$value){
//                    $value['comments_num']=$this->getCommentsNum($value['id']);
                    $value['release_time']=$this->format_dates($value['create_time']);
                }
            }
        }else{
            $tmpData=[];
        }
        return $tmpData;
    }
    //获取文章评论数
    public function getCommentsNum($article_id){
        $num=$this->db->Name('xcx_article_comments')->select('count(*)')->where_equalTo('aid',$article_id)->firstColumn();
        return empty($num)?0:$num;
    }
    //获取文章数据
    public function getArticle(){
        $cid=Context::Post('cid');
        $page=intval(Context::Post('page'))+1;
        $data=$this->getArticleData($cid,$page);
        if(!empty($data)){
            echo json_encode(['success'=>true,'data'=>$data,'page'=>$page],JSON_UNESCAPED_UNICODE);
        }else{
            echo json_encode(['success'=>false]);
        }
    }
}