<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: pl125 <xskjs888@163.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\validate\AccountValidate;
use app\admin\validate\ArticleDate;
use app\common\base\AdminBaseController;
use app\common\MyConst;
use app\server\admin\Admin;
use app\server\admin\ArticleTag;
use app\server\admin\Column;
use app\server\admin\InformationVideo;
use app\server\admin\News;
use app\server\index\ArtColumn;
use think\Validate;


class TagController extends AdminBaseController
{
    /**
     * 获取父级分类
     */
    public function getParent(){
        $server = new ArticleTag();
        $field  = 'name,id';
        $list = $server->getParent();
        $top  = [['name'=>'顶级分类','id'=>0]];
        if(empty($list)){
            $list = $top;
        }else{
            $list = array_merge($top,$list->toArray());
        }
        $this->success($list);
    }

}

