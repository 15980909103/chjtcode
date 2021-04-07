<?php


namespace app\miniwechat\controller;


use app\common\base\UserBaseController;
use app\server\admin\PublicArticles;

class PublicArticlesController extends UserBaseController
{
    public function list(){
        $indexPublicArticles = new \app\index\controller\PublicArticlesController($this->app);
        $indexPublicArticles->list();
        return ;
        $data = $this->request->param();
        $res = (new PublicArticles())->getList($data);

        if($res['code'] == 0){
            return $this->error($res['msg']);
        }

        return $this->success($res['result']);
    }
}