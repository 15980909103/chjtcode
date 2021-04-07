<?php

/**
 * Created by PhpStorm.
 * User: USER022
 * Date: 2019/1/3
 * Time: 14:48
 */
include 'AdminController.php';
include System . DS . 'Upload.php';
class Xcxarticle extends AdminController
{
    /*============================================= 首页轮播图 ========================================================*/
    public function figure_index(){
        return $this->render('figure_index');
    }
    public function figure_page(){
        $curr=Context::Post('curr');
        $limit=Context::Post('limit');
        $data = $this->db->Name('xcx_article_figure')->select()->page($curr,$limit)->orderBy('status','desc')->orderBy('sort')->execute();
        $count = $this->db->Name('xcx_article_figure')->select('count(*)')->firstColumn();
        if(!empty($data)){
            foreach($data as &$val){
                $val['create_time']=date('Y-m-d H:i:s',$val['create_time']);
                $val['update_time']=date('Y-m-d H:i:s',$val['update_time']);
            }
            echo json_encode(['success'=>true,'data'=>$data,'count'=>$count]);
        }else{
            echo json_encode(['success'=>false,'curr'=>$curr]);
        }
    }
    public function figure_add(){
        return $this->render('figure_add');
    }
    public function figure_doadd(){
        $upfile = new UploadFiles(array('filepath'=>BasePath . DS .'upload' . DS .'figure'));
        if($upfile->uploadeFile('file')){
            $arrfile = $upfile->getnewFile();
            $data['title'] = Context::Post('title');
            $data['img'] = '/upload/figure/'.$arrfile;
            $data['sort'] = Context::Post('sort');
            $data['url'] = Context::Post('url');
            $data['create_time'] = time();
            $data['update_time'] = time();
            $res=$this->db->Name('xcx_article_figure')->insert($data)->execute();
            if($res)
                echo json_encode(['success'=>true]);
            else
                echo json_encode(['success'=>false,'msg'=>"保存失败"]);
        }else{
            $err = $upfile->gteerror();
            echo json_encode(['success'=>false,'msg'=>$err]);
        }
    }
    public function figure_edit(){
        $id=Context::Get('id');
        $data['data']=$this->db->Name('xcx_article_figure')->select()->where_equalTo('id', $id)->firstRow();;
        return $this->render('figure_edit',$data);
    }
    public function figure_doedit(){
        $id=Context::Post('id');
        $type=Context::Post('type');
        $data['title']=Context::Post('title');
        $data['sort']=Context::Post('sort');
        $data['url']=Context::Post('url');
        $data['update_time']=time();
        if($type!='empty'){
            $upfile = new UploadFiles(array('filepath'=>BasePath . DS .'upload' . DS .'figure'));
            if($upfile->uploadeFile('file')){
                $arrfile = $upfile->getnewFile();
                $info=(new Query())->Name('xcx_article_figure')->select()->where_equalTo('id',$id)->firstRow();
                if(!empty($info)){
                    $data['img']='/upload/figure/'.$arrfile;
                }else{
                    echo json_encode(['success'=>false,'message'=>'保存失败']);exit;
                }
            }else{
                $err = $upfile->gteerror();
                echo json_encode(['success'=>false,'msg'=>$err]);exit;
            }
        }
        $res=$this->db->Name('xcx_article_figure')->update($data)->where_equalTo('id',$id)->execute();
        if($res){
            if($type!='empty'){
                if(file_exists(BasePath .$info['img'])){
                    unlink(BasePath .$info['img']);
                }
            }
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false,'message'=>'保存失败']);
        }
    }
    public function figure_status(){
        $id=Context::Post('id');
        $status=Context::Post('status');
        $res=$this->db->Name('xcx_article_figure')->update(['status'=>$status])->where_equalTo('id',$id)->execute();
        if($res)
            echo json_encode(['success'=>true]);
        else
            echo json_encode(['success'=>false]);
    }
    public function figure_del(){
        $id=Context::Post('id');
        $info=(new Query())->Name('xcx_article_figure')->select()->where_equalTo('id',$id)->firstRow();
        $res=$this->db->Name('xcx_article_figure')->delete()->where_equalTo('id',$id)->execute();
        if($res){
            if(file_exists(BasePath .$info['img'])){
                unlink(BasePath .$info['img']);
            }
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false,'message'=>'删除失败']);
        }
    }
    /*============================================= 栏目管理 ========================================================*/
    public function column_index(){
        return $this->render('column_index');
    }
    public function column_page(){
        $curr=Context::Post('curr');
        $limit=Context::Post('limit');
        $data = $this->db->Name('xcx_article_column')->select()->page($curr,$limit)->orderBy('status','desc')->orderBy('sort')->execute();
        $count = $this->db->Name('xcx_article_column')->select('count(*)')->firstColumn();
        if(!empty($data)){
            foreach($data as &$val){
                $val['create_time']=date('Y-m-d H:i:s',$val['create_time']);
                $val['update_time']=date('Y-m-d H:i:s',$val['update_time']);
            }
            echo json_encode(['success'=>true,'data'=>$data,'count'=>$count]);
        }else{
            echo json_encode(['success'=>false,'curr'=>$curr]);
        }
    }
    public function column_add(){
        return $this->render('column_add');
    }
    public function column_doadd(){
        $data['title'] = Context::Post('title');
        $data['sort'] = Context::Post('sort');
        $data['create_time'] = time();
        $data['update_time'] = time();
        $res=$this->db->Name('xcx_article_column')->insert($data)->execute();
        if($res)
            echo json_encode(['success'=>true]);
        else
            echo json_encode(['success'=>false,'message'=>"保存失败"]);
    }
    public function column_edit(){
        $id=Context::Get('id');
        $data['data']=$this->db->Name('xcx_article_column')->select()->where_equalTo('id', $id)->firstRow();;
        return $this->render('column_edit',$data);
    }
    public function column_doedit(){
        $id=Context::Post('id');
        $data['title']=Context::Post('title');
        $data['sort']=Context::Post('sort');
        $data['update_time']=time();
        $res=$this->db->Name('xcx_article_column')->update($data)->where_equalTo('id',$id)->execute();
        if($res){
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false,'message'=>'保存失败']);
        }
    }
    public function column_status(){
        $id=Context::Post('id');
        $status=Context::Post('status');
        $res=$this->db->Name('xcx_article_column')->update(['status'=>$status])->where_equalTo('id',$id)->execute();
        if($res)
            echo json_encode(['success'=>true]);
        else
            echo json_encode(['success'=>false]);
    }
    public function column_del(){
        $id=Context::Post('id');
        $info=(new Query())->Name('xcx_article_article')->select()->where_equalTo('cid',$id)->firstRow();
        if(empty($info)){
            $res=$this->db->Name('xcx_article_column')->delete()->where_equalTo('id',$id)->execute();
            if($res){
                echo json_encode(['success'=>true]);
            }else{
                echo json_encode(['success'=>false,'message'=>'删除失败']);
            }
        }else{
            echo json_encode(['success'=>false,'message'=>'该栏目下有文章不能删除！']);
        }
    }
    /*============================================= 文章管理 ========================================================*/
    public function article_index(){
        return $this->render('article_index');
    }
    public function set_article_where($select,$Db){
        foreach($select as $k=>$v){
            if($k=='title')
                $Db->where_like($k,'%'.$v.'%');
            else
                $Db->where_equalTo($k,$v);
        }
        return $Db;
    }
    public function article_page(){
        $curr=Context::Post('curr');
        $limit=Context::Post('limit');
        $select['title']=Context::Post('title');
        $select['status']=Context::Post('status');
        $select['is_hot']=Context::Post('is_hot');
        $select=array_filter($select,function($val){$tmp=$val ===  ''; return !$tmp;});
        if(!empty($select)){
            $userDb=$this->db->Name('xcx_article_article');
            $userDb=$this->set_article_where($select,$userDb);
            $data = $userDb->select()->page($curr,$limit)->orderBy('status','desc')->orderBy('create_time','desc')->execute();
            $userDb=$this->set_article_where($select,$userDb);
            $count = $userDb->select('count(*)')->firstColumn();
        }else{
            $data = $this->db->Name('xcx_article_article')->select()->page($curr,$limit)->orderBy('status','desc')->orderBy('create_time','desc')->execute();
            $count = $this->db->Name('xcx_article_article')->select('count(*)')->firstColumn();
        }
        if(!empty($data)){
            //获取栏目信息与发布者信息
            $cids=[];$aids=[];$cDict=[];$aDict=[];
            foreach($data as $value){
                $cids[]=$value['cid'];
                $aids[]=$value['aid'];
            }
            $cids=array_unique($cids);
            $aids=array_unique($aids);
            $columnRow=(new Query())->Name('xcx_article_column')->select()->where_in('id',$cids)->execute();
            $adminRow=(new Query())->Name('admin')->select()->where_in('id',$aids)->execute();
            if(!empty($columnRow)){
                foreach($columnRow as $v1){
                    $cDict[$v1['id']]=$v1['title'];
                }
            }
            if(!empty($adminRow)){
                foreach($adminRow as $v2){
                    $aDict[$v2['id']]['name']=$v2['name'];
                    $aDict[$v2['id']]['img']=$v2['img'];
                }
            }
            foreach($data as &$val){
                $val['cname']=empty($cDict[$val['cid']])?'':$cDict[$val['cid']];
                $val['aname']=empty($aDict[$val['aid']]['name'])?'':$aDict[$val['aid']]['name'];
                $val['aimg']=empty($aDict[$val['aid']]['img'])?'':$aDict[$val['aid']]['img'];
                $val['create_time']=date('Y-m-d H:i:s',$val['create_time']);
                $val['update_time']=date('Y-m-d H:i:s',$val['update_time']);
            }
            echo json_encode(['success'=>true,'data'=>$data,'count'=>$count]);
        }else{
            echo json_encode(['success'=>false,'curr'=>$curr]);
        }
    }
    public function article_add(){
        //获取文章栏目
        $data['columnData']=[];
        $columnRow=$this->db->Name('xcx_article_column')->select()->execute();
        if(!empty($columnRow)){
            foreach($columnRow as $key=>$val){
                $data['columnData'][$key]['id']=$val['id'];
                $data['columnData'][$key]['title']=$val['title'];
            }
        }
        return $this->render('article_add',$data);
    }
    public function article_doadd(){
        $data['title'] = Context::Post('title');
        $data['cid'] = Context::Post('cid');
        $data['aid'] = $_SESSION['aid'];
        $data['is_hot'] = Context::Post('is_hot');
        $data['province'] = Context::Post('province');
        $data['city'] = Context::Post('city');
        $data['description'] = empty(Context::Post('description'))?'':Context::Post('description');
        $data['content'] = Context::Post('content');
        $data['content'] =str_replace('"/upload/ueditor/image', "\"".WX_HOST.'/upload/ueditor/image', $data['content']);
        $data['create_time'] = time();
        $data['update_time'] = time();
        $upfile = new UploadFiles(array('filepath'=>BasePath . DS .'upload' . DS .'article'));
        if($upfile->uploadeFile('file')){
            $arrfile = $upfile->getnewFile();
            $data['cover']='/upload/article/'.$arrfile;
            $res=$this->db->Name('xcx_article_article')->insert($data)->execute();
            if($res)
                echo json_encode(['success'=>true]);
            else
                echo json_encode(['success'=>false,'message'=>"保存失败"]);
        }else{
            $err = $upfile->gteerror();
            echo json_encode(['success'=>false,'message'=>$err]);exit;
        }
    }
    public function article_edit(){
        $id=Context::Get('id');
        $data['data']=$this->db->Name('xcx_article_article')->select()->where_equalTo('id', $id)->firstRow();
        //获取文章栏目
        $data['columnData']=[];
        $columnRow=$this->db->Name('xcx_article_column')->select()->execute();
        if(!empty($columnRow)){
            foreach($columnRow as $key=>$val){
                $data['columnData'][$key]['id']=$val['id'];
                $data['columnData'][$key]['title']=$val['title'];
            }
        }
        return $this->render('article_edit',$data);
    }
    public function article_doedit(){
        $id=Context::Post('id');
        $type=Context::Post('type');
        $data['title'] = Context::Post('title');
        $data['cid'] = Context::Post('cid');
        $data['read_num'] = Context::Post('read_num');
        $data['province'] = Context::Post('province');
        $data['city'] = Context::Post('city');
        $data['description'] = empty(Context::Post('description'))?'':Context::Post('description');
        $data['content'] = Context::Post('content');
        $data['content'] =str_replace('"/upload/ueditor/image', "\"".WX_HOST.'/upload/ueditor/image', $data['content']);
        $data['update_time'] = time();
        if($type!='empty'){
            $upfile = new UploadFiles(array('filepath'=>BasePath . DS .'upload' . DS .'article'));
            if($upfile->uploadeFile('file')){
                $arrfile = $upfile->getnewFile();
                $info=(new Query())->Name('xcx_article_article')->select()->where_equalTo('id',$id)->firstRow();
                if(!empty($info)){
                    $data['cover']='/upload/article/'.$arrfile;
                }else{
                    echo json_encode(['success'=>false,'message'=>'保存失败']);exit;
                }
            }else{
                $err = $upfile->gteerror();
                echo json_encode(['success'=>false,'message'=>$err]);exit;
            }
        }
        $res=$this->db->Name('xcx_article_article')->update($data)->where_equalTo('id',$id)->execute();
        if($res){
            if($type!='empty'){
                if(file_exists(BasePath .$info['cover'])){
                    unlink(BasePath .$info['cover']);
                }
            }
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false,'message'=>'保存失败']);
        }
    }
    public function article_status(){
        $id=Context::Post('id');
        $status=Context::Post('status');
        $res=$this->db->Name('xcx_article_article')->update(['status'=>$status])->where_equalTo('id',$id)->execute();
        if($res)
            echo json_encode(['success'=>true]);
        else
            echo json_encode(['success'=>false]);
    }
    public function article_ishot(){
        $id=Context::Post('id');
        $is_hot=Context::Post('is_hot');
        $res=$this->db->Name('xcx_article_article')->update(['is_hot'=>$is_hot])->where_equalTo('id',$id)->execute();
        if($res)
            echo json_encode(['success'=>true]);
        else
            echo json_encode(['success'=>false]);
    }
    public function article_del(){
        $id=Context::Post('id');
        $info=(new Query())->Name('xcx_article_article')->select()->where_equalTo('id',$id)->firstRow();
        $res=$this->db->Name('xcx_article_article')->delete()->where_equalTo('id',$id)->execute();
        if($res){
            if(file_exists(BasePath .$info['cover'])){
                unlink(BasePath .$info['cover']);
            }
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false,'message'=>'删除失败']);
        }
    }
    /*============================================= 评论管理 ========================================================*/
    public function comments_index(){
        return $this->render('comments_index');
    }
    public function set_comments_where($select,$Db){
        foreach($select as $k=>$v){
            if($k=='aa.title')
                $Db->where_like($k,'%'.$v.'%');
            else
                $Db->where_equalTo($k,$v);
        }
        return $Db;
    }
    public function set_comment_where($select,$Db){
        foreach($select as $k=>$v){
            if($k=='aa.typename')
                $Db->where_like($k,'%'.$v.'%');
            else
                $Db->where_equalTo($k,$v);
        }
        return $Db;
    }

    public function comments_page(){
        $curr=Context::Post('curr');
        $limit=Context::Post('limit');
        $select['ac.status']=Context::Post('status');
        $select['aa.title']=Context::Post('title');
        $select=array_filter($select,function($val){$tmp=$val ===  ''; return !$tmp;});
        if(!empty($select)){
            $userDb=$this->db->Name('xcx_article_comments');
            $userDb=$this->set_comments_where($select,$userDb);
            $data = $userDb->select('ac.*','ac')->leftJoin('news','aa','ac.aid=aa.id')->page($curr,$limit)->orderBy('ac.create_time','desc')->execute();
            $userDb=$this->set_comments_where($select,$userDb);
            $count = $userDb->select('count(ac.id)','ac')->leftJoin('news','aa','ac.aid=aa.id')->firstColumn();
        }else{
            $data = $this->db->Name('xcx_article_comments')->select()->page($curr,$limit)->orderBy('create_time','desc')->execute();
            $count = $this->db->Name('xcx_article_comments')->select('count(*)')->firstColumn();
        }
        if(!empty($data)){
            //获取文章信息与评论人信息
            $uids=[];$aids=[];$uDict=[];$aDict=[];$uids2=[];$uDict2=[];
            foreach($data as $value){
                $aids[]=$value['aid'];
                if($value['user_type']=='1')
                    $uids[]=$value['uid'];
                else
                    $uids2[]=$value['uid'];
            }
            $uids=array_unique($uids);
            $uids2=array_unique($uids2);
            $aids=array_unique($aids);
            $articleRow=(new Query(new DataBase2()))->Name('news')->select()->where_in('id',$aids)->execute();
            $userRow=(new Query())->Name('xcx_user')->select()->where_in('id',$uids)->execute();
            $user2Row=(new Query())->Name('xcx_agent_user')->select()->where_in('id',$uids2)->execute();
            if(!empty($articleRow)){
                foreach($articleRow as $v1){
                    $aDict[$v1['id']]=$v1;
                }
            }
            if(!empty($userRow)){
                foreach($userRow as $v2){
                    $uDict[$v2['id']]=$v2;
                }
            }
            if(!empty($user2Row)){
                foreach($user2Row as $v22){
                    $uDict2[$v22['id']]['nickName']=$v22['nickname'];
                    $uDict2[$v22['id']]['avatarUrl']=$v22['headimgurl'];
                }
            }
            foreach($data as &$val){
                $val['a_title']=empty($aDict[$val['aid']]['title'])?'':$aDict[$val['aid']]['title'];
                $val['u_nickName']=$val['user_type']=='1'?$uDict[$val['uid']]['nickName']:$uDict2[$val['uid']]['nickName'];
                $val['u_avatarUrl']=$val['user_type']=='1'?$uDict[$val['uid']]['avatarUrl']:$uDict2[$val['uid']]['avatarUrl'];
                $val['user_type']=$val['user_type']=="1"?'用户':'经纪人';
                $val['create_time']=date('Y-m-d H:i:s',$val['create_time']);
                $val['update_time']=date('Y-m-d H:i:s',$val['update_time']);
            }
            echo json_encode(['success'=>true,'data'=>$data,'count'=>$count]);
        }else{
            echo json_encode(['success'=>false,'curr'=>$curr]);
        }
    }

    /*============================================= 本地评论管理（作废） ========================================================*/
    public function comments_pageDel(){
        $curr=Context::Post('curr');
        $limit=Context::Post('limit');
        $select['ac.status']=Context::Post('status');
        $select['aa.title']=Context::Post('title');
        $select=array_filter($select,function($val){$tmp=$val ===  ''; return !$tmp;});
        if(!empty($select)){
            $userDb=$this->db->Name('xcx_article_comments');
            $userDb=$this->set_comments_where($select,$userDb);
            $data = $userDb->select('ac.*','ac')->leftJoin('xcx_article_article','aa','ac.aid=aa.id')->page($curr,$limit)->orderBy('ac.create_time','desc')->execute();
            $userDb=$this->set_comments_where($select,$userDb);
            $count = $userDb->select('count(ac.id)','ac')->leftJoin('xcx_article_article','aa','ac.aid=aa.id')->firstColumn();
        }else{
            $data = $this->db->Name('xcx_article_comments')->select()->page($curr,$limit)->orderBy('create_time','desc')->execute();
            $count = $this->db->Name('xcx_article_comments')->select('count(*)')->firstColumn();
        }
        if(!empty($data)){
            //获取文章信息与评论人信息
            $uids=[];$aids=[];$uDict=[];$aDict=[];$uids2=[];$uDict2=[];
            foreach($data as $value){
                $aids[]=$value['aid'];
                if($value['user_type']=='1')
                    $uids[]=$value['uid'];
                else
                    $uids2[]=$value['uid'];
            }
            $uids=array_unique($uids);
            $uids2=array_unique($uids2);
            $aids=array_unique($aids);
            $articleRow=(new Query())->Name('xcx_article_article')->select()->where_in('id',$aids)->execute();
            $userRow=(new Query())->Name('xcx_user')->select()->where_in('id',$uids)->execute();
            $user2Row=(new Query())->Name('xcx_agent_user')->select()->where_in('id',$uids2)->execute();
            if(!empty($articleRow)){
                foreach($articleRow as $v1){
                    $aDict[$v1['id']]=$v1;
                }
            }
            if(!empty($userRow)){
                foreach($userRow as $v2){
                    $uDict[$v2['id']]=$v2;
                }
            }
            if(!empty($user2Row)){
                foreach($user2Row as $v22){
                    $uDict2[$v22['id']]['nickName']=$v22['nickname'];
                    $uDict2[$v22['id']]['avatarUrl']=$v22['headimgurl'];
                }
            }
            foreach($data as &$val){
                $val['a_title']=empty($aDict[$val['aid']]['title'])?'':$aDict[$val['aid']]['title'];
                $val['u_nickName']=$val['user_type']=='1'?$uDict[$val['uid']]['nickName']:$uDict2[$val['uid']]['nickName'];
                $val['u_avatarUrl']=$val['user_type']=='1'?$uDict[$val['uid']]['avatarUrl']:$uDict2[$val['uid']]['avatarUrl'];
                $val['user_type']=$val['user_type']=="1"?'用户':'经纪人';
                $val['create_time']=date('Y-m-d H:i:s',$val['create_time']);
                $val['update_time']=date('Y-m-d H:i:s',$val['update_time']);
            }
            echo json_encode(['success'=>true,'data'=>$data,'count'=>$count]);
        }else{
            echo json_encode(['success'=>false,'curr'=>$curr]);
        }
    }
    public function comments_add(){
        //获取所有文章与用户
        $data['articleData']=[];
        $data['userData']=[];
        $data['agentData']=[];
        $articleData=$this->db->Name('xcx_article_article')->select()->execute();
        if(!empty($articleData)){
            foreach($articleData as $key=>$val){
                $data['articleData'][$key]['id']=$val['id'];
                $data['articleData'][$key]['title']=$val['title'];
            }
        }
        $userData=$this->db->Name('xcx_user')->select()->execute();
        if(!empty($userData)){
            foreach($userData as $key=>$val){
                $data['userData'][$key]['id']=$val['id'];
                $data['userData'][$key]['nickName']=$val['nickName'];
            }
        }
        $agentData=$this->db->Name('xcx_agent_user')->select()->execute();
        if(!empty($agentData)){
            foreach($agentData as $key=>$val){
                $data['agentData'][$key]['id']=$val['id'];
                $data['agentData'][$key]['nickname']=$val['nickname'];
            }
        }
        return $this->render('comments_add',$data);
    }
    public function comments_doadd(){
        $data['aid'] = Context::Post('aid');
        $data['uid'] = Context::Post('uid');
        $data['user_type'] = Context::Post('user_type');
        $data['content'] = Context::Post('content');
        $data['create_time'] = time();
        $data['update_time'] = time();
        $res=$this->db->Name('xcx_article_comments')->insert($data)->execute();
        if($res)
            echo json_encode(['success'=>true]);
        else
            echo json_encode(['success'=>false,'message'=>"保存失败"]);
    }

    public function comments_edit(){
        $id=Context::Get('id');
        $data['data']=$this->db->Name('xcx_article_comments')->select()->where_equalTo('id', $id)->firstRow();
        //获取所有文章与用户
        $data['articleData']=[];
        $data['userData']=[];
        $data['agentData']=[];
        $type=[1,2,4];
        $articleData=$this->db2->Name('news')->select()->where_equalTo('status',1)->where_in('type',$type)->orderBy('addtime','desc')->limit(0,100)->execute();

        if(!empty($articleData)){
            foreach($articleData as $key=>$val){
                $data['articleData'][$key]['id']=$val['id'];
                $data['articleData'][$key]['title']=$val['title'];
            }
        }

        $userData=$this->db->Name('xcx_user')->select()->execute();
        if(!empty($userData)){
            foreach($userData as $key=>$val){
                $data['userData'][$key]['id']=$val['id'];
                $data['userData'][$key]['nickName']=$val['nickName'];
            }
        }
        $agentData=$this->db->Name('xcx_agent_user')->select()->execute();
        if(!empty($agentData)){
            foreach($agentData as $key=>$val){
                $data['agentData'][$key]['id']=$val['id'];
                $data['agentData'][$key]['nickname']=$val['nickname'];
            }
        }
        //return print_r($data);
        return $this->render('comments_edit',$data);
    }

    /*********************************************************************************************************/
    public function comments_editDel(){
        $id=Context::Get('id');
        $data['data']=$this->db->Name('xcx_article_comments')->select()->where_equalTo('id', $id)->firstRow();
        //获取所有文章与用户
        $data['articleData']=[];
        $data['userData']=[];
        $data['agentData']=[];
        $articleData=$this->db->Name('xcx_article_article')->select()->execute();
        if(!empty($articleData)){
            foreach($articleData as $key=>$val){
                $data['articleData'][$key]['id']=$val['id'];
                $data['articleData'][$key]['title']=$val['title'];
            }
        }
        $userData=$this->db->Name('xcx_user')->select()->execute();
        if(!empty($userData)){
            foreach($userData as $key=>$val){
                $data['userData'][$key]['id']=$val['id'];
                $data['userData'][$key]['nickName']=$val['nickName'];
            }
        }
        $agentData=$this->db->Name('xcx_agent_user')->select()->execute();
        if(!empty($agentData)){
            foreach($agentData as $key=>$val){
                $data['agentData'][$key]['id']=$val['id'];
                $data['agentData'][$key]['nickname']=$val['nickname'];
            }
        }
        return $this->render('comments_edit',$data);
    }
    public function comments_doedit(){
        $id=Context::Post('id');
        $data['aid'] = Context::Post('aid');
        $data['uid'] = Context::Post('uid');
        $data['user_type'] = Context::Post('user_type');
        $data['content'] = Context::Post('content');
        $data['update_time'] = time();
        $res=$this->db->Name('xcx_article_comments')->update($data)->where_equalTo('id',$id)->execute();
        if($res){
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false,'message'=>'保存失败']);
        }
    }
    public function comments_status(){
        $id=Context::Post('id');
        $status=Context::Post('status');
        $res=$this->db->Name('xcx_article_comments')->update(['status'=>$status])->where_equalTo('id',$id)->execute();
        if($res)
            echo json_encode(['success'=>true]);
        else
            echo json_encode(['success'=>false]);
    }
    public function comments_del(){
        $id=Context::Post('id');
        $res=$this->db->Name('xcx_article_comments')->delete()->where_equalTo('id',$id)->execute();
        if($res){
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false,'message'=>'删除失败']);
        }
    }
    /*============================================= 广告管理 ========================================================*/
    public function advertising_index(){
        return $this->render('advertising_index');
    }
    public function set_advertising_where($select,$Db){
        foreach($select as $k=>$v){
            $Db->where_equalTo($k,$v);
        }
        return $Db;
    }
    public function advertising_page(){
        $curr=Context::Post('curr');
        $limit=Context::Post('limit');
        $select['title']=Context::Post('title');
        $select['status']=Context::Post('status');
        $select=array_filter($select,function($val){$tmp=$val ===  ''; return !$tmp;});
        if(!empty($select)){
            $userDb=$this->db->Name('xcx_article_advertising');
            $userDb=$this->set_article_where($select,$userDb);
            $data = $userDb->select()->page($curr,$limit)->orderBy('status','desc')->orderBy('create_time','desc')->execute();
            $userDb=$this->set_article_where($select,$userDb);
            $count = $userDb->select('count(*)')->firstColumn();
        }else{
            $data = $this->db->Name('xcx_article_advertising')->select()->page($curr,$limit)->orderBy('status','desc')->orderBy('create_time','desc')->execute();
            $count = $this->db->Name('xcx_article_advertising')->select('count(*)')->firstColumn();
        }
        if(!empty($data)){
            foreach($data as &$val){
                $val['create_time']=date('Y-m-d H:i:s',$val['create_time']);
                $val['update_time']=date('Y-m-d H:i:s',$val['update_time']);
            }
            echo json_encode(['success'=>true,'data'=>$data,'count'=>$count]);
        }else{
            echo json_encode(['success'=>false,'curr'=>$curr]);
        }
    }
    public function advertising_add(){
        return $this->render('advertising_add',$data);
    }
    public function advertising_doadd(){
        $data['title'] = Context::Post('title');
        $data['url'] = Context::Post('url');
        $data['weight'] = Context::Post('weight');
        $data['create_time'] = time();
        $data['update_time'] = time();
        $upfile = new UploadFiles(array('filepath'=>BasePath . DS .'upload' . DS .'advertising'));
        if($upfile->uploadeFile('file')){
            $arrfile = $upfile->getnewFile();
            $data['img']='/upload/advertising/'.$arrfile;
            $res=$this->db->Name('xcx_article_advertising')->insert($data)->execute();
            if($res)
                echo json_encode(['success'=>true]);
            else
                echo json_encode(['success'=>false,'message'=>"保存失败"]);
        }else{
            $err = $upfile->gteerror();
            echo json_encode(['success'=>false,'message'=>$err]);exit;
        }
    }
    public function advertising_edit(){
        $id=Context::Get('id');
        $data['data']=$this->db->Name('xcx_article_advertising')->select()->where_equalTo('id', $id)->firstRow();
        return $this->render('advertising_edit',$data);
    }
    public function advertising_doedit(){
        $id=Context::Post('id');
        $type=Context::Post('type');
        $data['title'] = Context::Post('title');
        $data['url'] = Context::Post('url');
        $data['weight'] = Context::Post('weight');
        $data['update_time'] = time();
        if($type!='empty'){
            $upfile = new UploadFiles(array('filepath'=>BasePath . DS .'upload' . DS .'advertising'));
            if($upfile->uploadeFile('file')){
                $arrfile = $upfile->getnewFile();
                $info=(new Query())->Name('xcx_article_advertising')->select()->where_equalTo('id',$id)->firstRow();
                if(!empty($info)){
                    $data['img']='/upload/advertising/'.$arrfile;
                }else{
                    echo json_encode(['success'=>false,'message'=>'保存失败']);exit;
                }
            }else{
                $err = $upfile->gteerror();
                echo json_encode(['success'=>false,'message'=>$err]);exit;
            }
        }
        $res=$this->db->Name('xcx_article_advertising')->update($data)->where_equalTo('id',$id)->execute();
        if($res){
            if($type!='empty'){
                if(file_exists(BasePath .$info['img'])){
                    unlink(BasePath .$info['img']);
                }
            }
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false,'message'=>'保存失败']);
        }
    }
    public function advertising_status(){
        $id=Context::Post('id');
        $status=Context::Post('status');
        $res=$this->db->Name('xcx_article_advertising')->update(['status'=>$status])->where_equalTo('id',$id)->execute();
        if($res)
            echo json_encode(['success'=>true]);
        else
            echo json_encode(['success'=>false]);
    }
    public function advertising_del(){
        $id=Context::Post('id');
        $info=(new Query())->Name('xcx_article_advertising')->select()->where_equalTo('id',$id)->firstRow();
        $res=$this->db->Name('xcx_article_advertising')->delete()->where_equalTo('id',$id)->execute();
        if($res){
            if(file_exists(BasePath .$info['img'])){
                unlink(BasePath .$info['img']);
            }
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false,'message'=>'删除失败']);
        }
    }
}