<?php


namespace app\admin\controller;


use app\common\base\AdminBaseController;
use app\server\admin\PublicArticles;

class PublicArticlesController extends AdminBaseController
{
    public function list(){
        $data = $this->request->param();
        $res = (new PublicArticles())->getList($data,1);

        if($res['code'] == 0){
            return $this->error($res['msg']);
        }

        return $this->success($res['result']);
    }

    public function edit(){
        $data = $this->request->param();
        $res = (new PublicArticles())->edit($data);

        if($res['code'] == 0){
            return $this->error($res['msg']);
        }

        return $this->success($res['result']);
    }

    public function delete(){
        $data = $this->request->param();
        $res = (new PublicArticles())->delete($data['id']);

        if($res['code'] == 0){
            return $this->error($res['msg']);
        }

        return $this->success($res['result']);
    }


}