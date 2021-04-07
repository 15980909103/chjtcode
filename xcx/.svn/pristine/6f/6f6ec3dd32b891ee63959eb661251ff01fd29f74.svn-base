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
class SurroundingAjax extends Common{
    //添加意见反馈数据
    public function addSuggest(){
        $data['user_id']=$this->uid();
        if(empty($data['user_id'])){echo json_encode(['success'=>false,'message'=>"保存失败"]);exit;}
        $data['client_side_type']=Context::Post('client_side_type');    //用户来源标识
        $data['ambitus_suggest']=Context::Post('ambitus_suggest');    //反馈内容
        $data['contact_way']=Context::Post('contact_way');    //联系方式
        $data['create_time']=time();
        $data['update_time']=time();
        $res=$this->db->Name('xcx_ambitus_suggest')->insert($data)->execute();
        if($res)
            echo json_encode(['success'=>true,'id'=>$res]);
        else
            echo json_encode(['success'=>false,'message'=>"保存失败"]);
    }
    //上传意见反馈图片
    public function uploadSuggestImg(){
        $id=Context::Post('id');    //反馈id
        $upfile = new UploadFiles(array('filepath'=>BasePath . DS .'upload' . DS .'suggest'));
        if($upfile->uploadeFile('file')){
            $arrfile = $upfile->getnewFile();
            //处理图片数据
            $info=(new Query())->Name('xcx_ambitus_suggest')->select()->where_equalTo('id',$id)->firstRow();
            $image_feedback=empty($info['image_feedback'])?[]:explode('|',$info['image_feedback']);
            $image_feedback[]='/upload/suggest/'.$arrfile;
            $data['image_feedback']=implode('|',$image_feedback);
            $res=$this->db->Name('xcx_ambitus_suggest')->update($data)->where_equalTo('id',$id)->execute();
            if($res){
                echo json_encode(['success'=>true]);
            }else{
                echo json_encode(['success'=>false,'message'=>"保存失败"]);
            }
        }else{
            $err = $upfile->gteerror();
            echo json_encode(['success'=>false,'message'=>$err]);exit;
        }
    }
    //获取用户所收藏的楼盘数据
    public function getBuildingCollection(){
        $page=Context::Post('page');
        $user_id=$this->uid();
        $buildingName=Context::Post('buildingName');    //楼盘名称
        if(empty($buildingName)){
            $collectionRow=$this->db->Name('xcx_user_building_collection')->select("bb.*,ubc.agent_id,ubc.id collection_id",'ubc')->where_equalTo('ubc.user_id',$user_id)->where_equalTo('ubc.status','1')->leftJoin("xcx_building_building","bb","ubc.building_id=bb.id")->where_equalTo('bb.is_delete', 0)->page($page,10)->orderBy('ubc.id','desc')->execute();
        }else{
            $collectionRow=$this->db->Name('xcx_user_building_collection')->select("bb.*,ubc.agent_id,ubc.id collection_id",'ubc')->where_equalTo('ubc.user_id',$user_id)->where_equalTo('ubc.status','1')->where_like('bb.name','%'.$buildingName.'%')->leftJoin("xcx_building_building","bb","ubc.building_id=bb.id")->page($page,10)->orderBy('ubc.id','desc')->execute();
        }
        if(!empty($collectionRow)){
            foreach($collectionRow as &$val){
                $val['fold']=floatval($val['fold']);
                //$val['commission']=floatval($val['commission']);
                unset($val['commission']);
                $val['views_number']=$this->formatting_number($val['views_number']);
                // 房屋类型修改
                $houseType = explode(',', $val['house_type']);
                $val['house_type'] = !empty($houseType['0']) ? $houseType['0'] : "";
            }
            $data['success']=true;
            $data['buildingInfo']=$collectionRow;
            echo json_encode($data,JSON_UNESCAPED_UNICODE);
        }else{
            if(empty($buildingName))
                $data['success']=false;
            else
                $data['success']=true;
            $data['buildingInfo']=[];
            echo json_encode($data,JSON_UNESCAPED_UNICODE);
        }
    }
    //删除楼盘收藏
    public function collectionAllDel(){
        $collection_ids=Context::Post('collection_ids');
        $user_id=$this->uid();
        if(empty($collection_ids)){echo json_encode(['success'=>false,'message'=>"保存失败"]);exit;}
        $collection_ids=trim($collection_ids,',');
        $collection_ids=explode(',',$collection_ids);
        $res=$this->db->Name('xcx_user_building_collection')->update(['status'=>'0','update_time'=>time()])->where_in('id',$collection_ids)->where_equalTo('user_id',$user_id)->execute();
        if($res){
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false,'message'=>"保存失败"]);
        }
    }
    //删除浏览记录
    public function historyAllDel(){
        $history_ids=Context::Post('history_ids');
        $user_id=$this->uid();
        if(empty($history_ids)){echo json_encode(['success'=>false,'message'=>"保存失败"]);exit;}
        $history_ids=trim($history_ids,',');
        $history_ids=explode(',',$history_ids);
        $res=$this->db->Name('xcx_user_browsing_history')->update(['status'=>'0'])->where_in('article_id',$history_ids)->where_equalTo('user_id',$user_id)->execute();
        if($res){
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false,'message'=>"保存失败"]);
        }
    }
    //浏览记录处理
    public function browsingHistory(){
        $user_id=$this->uid();
        if(!empty($user_id)){
            $tag=Context::Post('tag');    //浏览类型'start'|'end' 开始或结束
            $browse_type=strval(Context::Post('browse_type'));    //浏览类型 1：名片  2：文章  3：楼盘
            $agent_id=Context::Post('agent_id');
            $article_id=Context::Post('article_id');
            $building_id=Context::Post('building_id');
            /*if($tag=='start'){  //添加新的浏览纪录
                $data=['user_id'=>$user_id,'agent_id'=>$agent_id,'building_id'=>$building_id,'article_id'=>$article_id,'browse_type'=>$browse_type,'start_time'=>time(),'end_time'=>time()];
                $this->db->Name('xcx_user_browsing_history')->insert($data)->execute();
            }else{  //修改浏览结束记录
                //获取对应记录的最后一条数据
                $historyData=$this->db->Name('xcx_user_browsing_history')->select()->where_equalTo('browse_type',$browse_type)->where_equalTo('user_id',$user_id)->where_equalTo('agent_id',$agent_id)->where_equalTo('article_id',$article_id)->where_equalTo('building_id',$building_id)->orderBy('id','desc')->firstRow();
                if(!empty($historyData)){
                    $data=['end_time'=>time(),'viewing_hours'=>(time()-intval($historyData['start_time']))];
                    $this->db->Name('xcx_user_browsing_history')->update($data)->where_equalTo('id',$historyData['id'])->execute();
                }
            }*/

            //获取对应记录的最后一条数据
            $historyData=$this->db->Name('xcx_user_browsing_history')->select()->where_equalTo('browse_type',$browse_type)->where_equalTo('user_id',$user_id)->where_equalTo('agent_id',$agent_id)->where_equalTo('article_id',$article_id)->where_equalTo('building_id',$building_id)->orderBy('id','desc')->firstRow();
            if(!empty($historyData)){
                //修改浏览结束记录
                $data=['status'=>1,'end_time'=>time(),'viewing_hours'=>(time()-intval($historyData['start_time']))];
                $this->db->Name('xcx_user_browsing_history')->update($data)->where_equalTo('id',$historyData['id'])->execute();
            }else{
                //添加新的浏览纪录
                $data=['user_id'=>$user_id,'agent_id'=>$agent_id,'building_id'=>$building_id,'article_id'=>$article_id,'browse_type'=>$browse_type,'start_time'=>time(),'end_time'=>time()];
                $this->db->Name('xcx_user_browsing_history')->insert($data)->execute();
            }

            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false]);
        }
    }
    //获取楼盘浏览记录
    public function getBuildingRecord(){
        $user_id=$this->uid();
        $page=Context::Post('page');
        $searchName=Context::Post('searchName');    //搜索内容
        if(empty($searchName)){
            $collectionRow=$this->db->Name('xcx_user_browsing_history')->select("bb.*,ubh.agent_id,ubh.id history_id",'ubh')->where_equalTo('ubh.user_id',$user_id)->where_equalTo('ubh.status','1')->where_equalTo('ubh.browse_type','3')->leftJoin("xcx_building_building","bb","ubh.building_id=bb.id")->page($page,10)->orderBy('ubh.id','desc')->execute();
        }else{
            $collectionRow=$this->db->Name('xcx_user_browsing_history')->select("bb.*,ubh.agent_id,ubh.id history_id",'ubh')->where_equalTo('ubh.user_id',$user_id)->where_equalTo('ubh.status','1')->where_equalTo('ubh.browse_type','3')->where_like('bb.name','%'.$searchName.'%')->leftJoin("xcx_building_building","bb","ubh.building_id=bb.id")->page($page,10)->orderBy('ubh.id','desc')->execute();
        }
        if(!empty($collectionRow)){
            foreach($collectionRow as &$val){
                $val['fold']=floatval($val['fold']);
                //$val['commission']=floatval($val['commission']);
                unset($val['commission']);
                $val['views_number']=$this->formatting_number($val['views_number']);
                // 房屋类型
                $houseType = explode(',', $val['house_type']);
                $val['house_type'] = !empty($houseType['0']) ? $houseType['0'] : "";
            }
            $data['success']=true;
            $data['buildingInfo']=$collectionRow;
            echo json_encode($data,JSON_UNESCAPED_UNICODE);
        }else{
            if(empty($searchName))
                $data['success']=false;
            else
                $data['success']=true;
            $data['buildingInfo']=[];
            echo json_encode($data,JSON_UNESCAPED_UNICODE);
        }
    }
    //获取文章浏览记录
    public function getArticleRecord(){
        $user_id=$this->uid();
        $page=Context::Post('page');
        $searchName=Context::Post('searchName');    //搜索内容
        $collectionRow=$this->db->Name('xcx_user_browsing_history')->select('id,agent_id,user_id,status,browse_type,article_id')->where_equalTo('browse_type','2')->where_equalTo('status','1')->where_equalTo('user_id',$user_id)->page($page,10)->execute();

        $coll=array();
        foreach ($collectionRow as $k => $value){
            $coll[$k]=$value['article_id'];
        }
        $tmpData = $this->db2->Name('news')->select('id,title,wap_pic,operator,addtime,click,short_title')->where_like('title','%'.$searchName.'%')->where_in('id',$coll)->execute();

        if(!empty($tmpData)){
        foreach ($tmpData as $k => $value) {
            $Dates[$k]['id'] = $value['id'];
            $Dates[$k]['history_id'] = $value['id'];
            $Dates[$k]['title'] = $value['title'];
            $Dates[$k]['comments_num'] = $this->getCommentsNum($value['id']);
            $Dates[$k]['aname'] = $value['operator'];
            $Dates[$k]['aimg'] = '/upload/default/default_head.png';
            $Dates[$k]['cover'] = 'http://www.999house.com/' . $value['wap_pic'];
            $Dates[$k]['release_time'] = $this->format_dates(strtotime($value['addtime']));
        }

            $data['articleInfo'] = $Dates;
            $data['success']=true;
            echo json_encode($data,JSON_UNESCAPED_UNICODE);
            return;
        }else{
            $data['success']=false;
            $data['articleInfo']=[];
            echo json_encode($data,JSON_UNESCAPED_UNICODE);
            return;
        }
            //获取后台发布者信息
            /*$aids=[];$aDict=[];
            foreach($collectionRow as $v){
                $aids[]=$v['aid'];
            }
            $aids=array_unique($aids);
            $adminRow=(new Query())->Name('admin')->select()->where_in('id',$aids)->execute();
            if(!empty($adminRow)){
                foreach($adminRow as $v2){
                    $aDict[$v2['id']]['name']=$v2['name'];
                    $aDict[$v2['id']]['img']=$v2['img'];
                }
                foreach($collectionRow as &$value){
                    $value['comments_num']=$this->getCommentsNum($value['id']);
                    $value['aname']=$aDict[$value['aid']]['name'];
                    $value['aimg']=$aDict[$value['aid']]['img'];
                    $value['release_time']=$this->format_dates($value['create_time']);
                }
            }*/
            //$data['success']=true;
            //$data['articleInfo']=$collectionRow;
            //echo json_encode($data,JSON_UNESCAPED_UNICODE);

    }
    //获取文章评论数
    public function getCommentsNum($article_id){
        $num=$this->db->Name('xcx_article_comments')->select('count(*)')->where_equalTo('aid',$article_id)->firstColumn();
        return empty($num)?0:$num;
    }

    //转发分享数
    public function shareHistory()
    {
        $user_id = $this->uid();
        if(!empty($user_id)){
            $browse_type=strval(Context::Post('browse_type'));    //分享类型 1：名片  2：文章  3：楼盘
            $agent_id=Context::Post('agent_id');
            $article_id=Context::Post('article_id');
            $building_id=Context::Post('building_id');
            $historyData=$this->db->Name('xcx_user_browsing_history')->select()->where_equalTo('browse_type',$browse_type)->where_equalTo('user_id',$user_id)->where_equalTo('agent_id',$agent_id)->where_equalTo('article_id',$article_id)->where_equalTo('building_id',$building_id)->orderBy('id','desc')->firstRow();
            if(!empty($historyData)){
                $data=['share_num'=>$historyData['share_num']+1];
                $this->db->Name('xcx_user_browsing_history')->update($data)->where_equalTo('id',$historyData['id'])->execute();
            }
            echo json_encode(['success'=>true]);

        }else{
            echo json_encode(['success'=>false]);
        }
    }


}