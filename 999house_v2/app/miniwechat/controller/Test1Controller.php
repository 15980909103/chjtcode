<?php


namespace app\miniwechat\controller;


use app\common\base\UserBaseController;
use app\common\traits\News;
use think\App;
use think\facade\Config;
use think\facade\Db;

class Test1Controller extends UserBaseController
{
    use News;
    public function index(){
        $set_img    = $this->request->get('set_img',0);
        $pageSize   = 20;
        $redis      = $this->getReids();

        $start     = $redis->get('9H:oldtonewstart') ?? 1;
        $oldnes    = Db::connect('old9h')
                    ->name('news')
                    ->order('id desc')
                    ->where('type','in',[1,2,4,10,18])
                    ->where('id','<','40831')
                    ->limit($start,$pageSize)
                    ->select()
                    ->toArray();
        $model = new \app\server\admin\News();
        foreach ($oldnes as $k =>$v){
            //保存图片
            if($set_img ==1){
                $img        = $v["pic"];
                $pic_ico    = $v['wap_pic'];
                if( !empty($img) ){
                    //保存图片到本地
                    go(function () use($img){
                        $this->getImg($img);
                    });
                }
            }


            $info       = $this->oldNewstoNewNews($v);
            $data       = $info['info'];
            $cate_id    = $info['cate_id'];
            $new_info  = $this->db->name('article')->where('old_id','=',$data['old_id'])->find();
            if(!empty($data) && !empty($cate_id) && empty($new_info) ){
                try {
                    $this->db->startTrans();
                    $art_id  = $model->add($data);
                    if($art_id ===false ){
                        $this->db->rollback();
                    }
                    foreach ($cate_id as $key => $value){
                        $cate_arr[] = [
                            'column_id'      => $value,
                            'article_id'     => $art_id,
                            'update_time'    => time(),
                            'create_time'    => time(),

                        ];
                    }

                    $result = $this->db->name('article_cloumn')->insertAll($cate_arr);
                    if($result ===false) {
                        $this->db->rollback();
                    }


                    if( !empty($info['lable']) ){
                        //添加文章和标签的关系

                        $tag_arr = [
                            'tag_id'         => $info['lable'],
                            'article_id'     => $art_id,
                            'update_time'    => time(),
                            'create_time'    => time(),
                        ];

                        $result = $this->db->name('article_tag_bind')->insert($tag_arr);
                        if($result ===false) {
                            $this->db->rollback();
                        }
                    }

                    $cate_arr = [];
                    $this->db->commit();
                }catch (\Exception $e){
                    $this->db->rollback();
                }

            }

        }
        $redis->set('9H:oldtonewstart',$start+$pageSize);
        $this->success();
    }

    /**
     * 拉取远程图片到本地
     * @param $url
     */
    public function getImg($url){
       if(empty($url)){
         return;
       }

       $imgarr  = explode('/',$url);
       $path = App::getInstance()->getRootPath();
       $path    = $path.'public/upload/images/old/upload/'.$imgarr[1].'/'.$imgarr['2'];
       if( !is_dir($path)){
               $res =  mkdir($path,0777,true);
       }
       $path = $path.'/'.$imgarr[3];
       if(!file_exists($path)){
           $url     = 'http://www.999house.com/'.$url;
           $file    = file_get_contents($url);
           $res     = file_put_contents($path,$file);
       }


    }
    public function updateOldnews(){
        $set_img    = $this->request->get('set_img',1);
        $id    = $this->request->get('id',0);
        $flag    = $this->request->get('flag',0);

        if(empty($id) && empty($flag) ){
           return $this->error();
        }
        $new_info  = $this->db->name('article')->where('old_id','=',$id)->find();
        $old_info  = Db::connect('old9h')
            ->name('news')
            ->where('id','=',$id)
            ->find();

        if(empty($old_info)){
            return  $this->error('id不存在');
        }
        if($set_img ==1){
            $img        = $old_info["pic"];
            if( !empty($img) ){
                //保存图片到本地
                go(function () use($img){
                    $this->getImg($img);
                });
            }
        }

        $model = new \app\server\admin\News();
        $info       = $this->oldNewstoNewNews($old_info);
        $data       = $info['info'];
        $cate_id    = $info['cate_id'];
//        var_dump($data['lable']); return ;
        //新增
        //判断新房源id
        if($data['is_propert_news']==1 && $data['oldforid'] ){
           $old_est_info =  $this->db->name('estates_new')->where('old_id','=',$data['oldforid'])->find();
           if( !empty($old_est_info) ){
               $data['forid'] = $old_est_info['id'];
           }

        }
        if(($flag ==1  && empty($new_info)) || ($flag==2 && empty($new_info))){
            $this->db->startTrans();
            $art_id  = $model->add($data);
            if($art_id ===false ){
                $this->db->rollback();
                return $this->error();
            }
            foreach ($cate_id as $key => $value){
                $cate_arr[] = [
                    'column_id'      => $value,
                    'article_id'     => $art_id,
                    'update_time'    => time(),
                    'create_time'    => time(),

                ];
            }

            $result = $this->db->name('article_cloumn')->insertAll($cate_arr);
            if($result ===false) {
                $this->db->rollback();
                return $this->error();
            }

            if( !empty($data['lable']) ){
                //添加文章和标签的关系
                $tag_arr = [
                    'tag_id'         => $data['lable'],
                    'article_id'     => $art_id,
                    'update_time'    => time(),
                    'create_time'    => time(),
                ];

                $result = $this->db->name('article_tag_bind')->insert($tag_arr);
                if($result ===false) {
                    $this->db->rollback();
                    return $this->error();
                }
            }
//            $this->db->rollback();
            $this->db->commit();

            //修改
        }elseif ($flag ==2 && $new_info){
            $data['id']  = $new_info['id'];
            $this->db->startTrans();
            $result  = $model->edit($data);
            if($result ===false ){
                $this->db->rollback();
                return $this->error();
            }
            //删除旧分类
            $result = $this->db->name('article_cloumn')->where('article_id','=',$data['id'])->delete();
            if($result ===false) {
                $this->db->rollback();
                return $this->error();
            }
            //新增新分类
            foreach ($cate_id as $key => $value){
                $cate_arr[] = [
                    'column_id'      => $value,
                    'article_id'     =>  $data['id'],
                    'update_time'    => time(),
                    'create_time'    => time(),

                ];
            }

            $result = $this->db->name('article_cloumn')->insertAll($cate_arr);
            if($result ===false) {
                $this->db->rollback();
                return $this->error();
            }
            //删除旧的tag和文章关联
            $result = $this->db->name('article_tag_bind')->where('article_id','=',$data['id'])->delete();

            if($result ===false) {
                $this->db->rollback();
                return $this->error();
            }

            if( !empty($data['lable']) ){
                //添加文章和标签的关系

                $tag_arr = [
                    'tag_id'         => $data['lable'],
                    'article_id'     =>  $data['id'],
                    'update_time'    => time(),
                    'create_time'    => time(),
                ];
                $result = $this->db->name('article_tag_bind')->insert($tag_arr);
                if($result ===false) {
                    $this->db->rollback();
                    return $this->error();
                }
            }

            $this->db->commit();
        }elseif ($flag ==3 && !empty($new_info)){

            $data['id']  = $new_info['id'];
            $this->db->startTrans();
            $result  = $model->delNews($data['id']);
            if($result ===false ){
                $this->db->rollback();
                return $this->error();
            }
            $result = $this->db->name('article_cloumn')->where('article_id','=',$data['id'])->delete();
            if($result ===false) {
                $this->db->rollback();
                return $this->error();
            }

            //删除旧的tag和文章关联
            $result = $this->db->name('article_tag_bind')->where('article_id','=',$data['id'])->delete();

            if($result ===false) {
                $this->db->rollback();
                return $this->error();
            }
            $this->db->commit();
        }

        $this->success();


    }


}