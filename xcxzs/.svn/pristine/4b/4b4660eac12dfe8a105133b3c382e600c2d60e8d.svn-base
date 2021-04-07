<?php

namespace app\server\admin;

use Exception;
use app\common\base\ServerBase;


/*
 * 公用城市操作
 * */
class ArticleTagBing extends ServerBase
{
    public static $table =  'article_tag_bind';
    //接触文章和 标签绑定
    public function delArticleTagBing($article_id){
        if(empty($article_id)){
            return false;
        }
        $resutl  = $this->db->name('article_tag_bind')->where('article_id','=',$article_id)->delete();
        return $resutl === false ? false:true;

    }

    /**
     * @param $data
     * @return bool
     * 批量添加标签
     */
    public function addAllArticleTagBing($data){
        if(empty($data)){
            return false;
        }

        $resutl  = $this->db->name('article_tag_bind')->insertAll($data);
        return $resutl === false ? false:true;

    }

    /**
     * @param $data
     * @return bool
     * 通过tag 获取 栏目
     */
    public function getCateBytag($tagArr){
        if(empty($tagArr) ){
            return  [];
        }
        $tagArr =array_values($tagArr);
        $lsit  = $this->db->name('column')->where('status','=',1)
            ->field('id,title')
            ->where('place','=','h5_fx_home');
        foreach ($tagArr  as $k => $v){
            if($k == 0){
                $lsit =  $lsit->whereFindInSet('tags',$v,'AND');
            }else{
                $lsit =  $lsit->whereFindInSet('tags',$v,'OR');
            }

        }
        $lsit =  $lsit->column('id');
        return  empty($lsit) ? [] :$lsit;
    }
}
