<?php


namespace app\admin\controller;


use app\common\base\AdminBaseController;
use app\server\admin\Notification;

class NotificationController extends AdminBaseController
{
    public function list(){
        $params = $this->request->param();
        $pageSize = $params['page_size'] ?? 10;
        $res = (new Notification())->bannerMessage($params,$pageSize);
        if($res['code'] == 1){
            $this->success($res['result']);
        }else{
            $this->error($res['msg']);
        }
    }

    public function change(){

    }
}