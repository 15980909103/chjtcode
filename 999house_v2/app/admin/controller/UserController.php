<?php


namespace app\admin\controller;


use app\common\base\AdminBaseController;
use app\server\user\User;


class UserController extends AdminBaseController
{
    /**
     * 用户列表
     */
    public function list(){
        $pageSize = $this->request->get('pageSize') ?? 10;
        $name = $this->request->param('user_nickname');
        $city_no = $this->request->param('city');
        $status = $this->request->param('status');
        $phone = $this->request->param('phone');
        $startdate = $this->request->param('startdate');
        $enddate = $this->request->param('enddate');


        $res = (new User())->list(['user_nickname' => $name,
                                   'city_no' => $city_no,
                                   'status'=>$status,
                                   'phone'=>$phone,
                                   'startdate'=>$startdate,
                                   'enddate'=>$enddate,
        ], $pageSize);
        if ($res['code'] == 0) {
            $this->error($res['msg']);
        }
        $this->success($res['result']);
    }



    /**
     * 用户封号
     */
    public function userDisableChange(){
        $param = $this->request->param();
        $res = (new User())->userDisableChange($param);
        if($res['code'] == 0){
            $this->error($res['msg']);
        }
        $this->success($res['result']);
    }

    //批量修改状态
    public function userDisableChangeBatch(){
        $param = $this->request->param();
        $res = (new User())->userDisableChangeBatch($param);
        if($res['code'] == 0){
            $this->error($res['msg']);
        }
        $this->success($res['result']);
    }

}