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
use app\common\base\AdminBaseController;
use app\server\admin\Admin;
use think\Validate;


class AccountController extends AdminBaseController
{
    /**
     * 获取管理员账号列表
     */
    public function index(){
        $data = $this->request->param();
        $rs = (new Admin())->getUserList(['account'=>$data['account']]);
        if(!empty($rs['result'])){
            $rs= $rs['result'];
        }else{
            $rs = [];
        }
        $this->success($rs);
    }

    /**
     * 管理员账号是否启用
     */
    public function enable(){
        $data = $this->request->param();
        $rs = (new Admin())->userEdit(intval($data['id']),['status'=>$data['status']]);
        if($rs['code'] == 1){
            $this->success();
        }else{
            $this->error($rs['msg']);
        }

    }


    /**
     * 管理员账号编辑
     */
    public function edit(){

        $data = $this->request->param();
        $validate = new AccountValidate();

        if(!empty($data['region_nos_info'])){
            if(is_array($data['region_nos_info'])){
                $data['region_nos_info'] = json_encode($data['region_nos_info'],JSON_UNESCAPED_UNICODE);
            }
        }
        $indata = [
            'account'=>$data['account'],
            'email' => $data['email'],
            'mobile' => $data['mobile'],
            'password' => $data['newpassword'],
            'role_id' => $data['role_id'],
            'status'=> $data['status'],
            'region_nos_info'=> $data['region_nos_info']??'',
            'head_ico_id'   => $data['head_ico_id']??'',
            'head_ico_path' => $data['head_ico_path']??'',
        ];

        if(intval($data['id'])){
            if(empty($data['email'])&&empty($data['mobile'])&&empty($data['newpassword'])&&empty($data['newpassword2'])&&empty($data['role_id'])){
                $this->error(['code'=>0,'msg'=>'数据不能为空']);
            }
            if($data['id']==1){
                if (!$validate->scene('editsuper')->check($data)) {
                    $this->error(['code'=>0,'msg'=>$validate->getError()]);
                }
            }else{
                if (!$validate->scene('edit')->check($data)) {
                    $this->error(['code'=>0,'msg'=>$validate->getError()]);
                }
            }
            unset($indata['account']);

            $rs = (new Admin())->userEdit(intval($data['id']),$indata);
        }else{
            if (!$validate->scene('add')->check($data)) {
                $this->error(['code'=>0,'msg'=>$validate->getError()]);
            }

            $rs = (new Admin())->userAdd($indata);
        }

        if($rs['code'] == 1){
            $this->success();
        }else{
            $this->error($rs['msg']);
        }
    }

    /**
     * 管理员账号删除
     */
    public function del(){
        $data = $this->request->param();
        $rs = (new Admin())->userDel(intval($data['id']));
        if($rs['code'] == 1){
            $this->success();
        }else{
            $this->error($rs['msg']);
        }
    }
}
