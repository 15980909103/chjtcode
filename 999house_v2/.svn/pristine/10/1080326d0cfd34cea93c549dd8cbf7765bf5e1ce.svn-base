<?php

namespace app\admin\controller;

use app\common\base\AdminBaseController;
use app\server\admin\Role;
use think\Validate;


class RoleController extends AdminBaseController
{
    /**
     * 角色列表
     */
    public function index(){
        $rs = (new Role())->getRoleList();
        if(!empty($rs['result'])){
            $rs= $rs['result'];
        }else{
            $rs = [];
        }
        $this->success($rs);
    }

    /**
     * 角色是否启用
     */
    public function enable(){
        $data = $this->request->param();
        $rs = (new Role())->roleEdit(intval($data['id']),['status'=>$data['status']]);
        if($rs['code'] == 1){
            $this->success();
        }else{
            $this->error($rs['msg']);
        }

    }


    /**
     * 角色信息编辑
     */
    public function edit(){
        $data = $this->request->param();
        if(intval($data['id'])){
            $rules=[
                'name'   => 'require',
            ];
            $rules_msg =[
                'name.require' => '请输入角色名称',
            ];
            $validate = new Validate($rules);
            $validate->message($rules_msg);
            if (!$validate->check($data)) {
                $this->error(['code'=>0,'msg'=>$validate->getError()]);
            }

            $indata=[
                'name' => $data['name'],
                'remark' => $data['remark'],
                'status' => $data['status']
            ];

            $rs = (new Role())->roleEdit(intval($data['id']),$indata);
        }else{
            $rules=[
                'name'   => 'require',
            ];
            $rules_msg =[
                'name.require' => '请输入角色名称',
            ];
            $validate = new Validate($rules);
            $validate->message($rules_msg);
            if (!$validate->check($data)) {
                $this->error(['code'=>0,'msg'=>$validate->getError()]);
            }

            $indata=[
                'name' => $data['name'],
                'remark' => $data['remark'],
                'status' => $data['status']
            ];

            $rs = (new Role())->roleAdd($indata);
        }

        if($rs['code'] == 1){
            $this->success();
        }else{
            $this->error($rs['msg']);
        }
    }

    /**
     * 管理员账号是否启用
     */
    public function del(){
        $data = $this->request->param();
        $rs = (new Role())->roleDel(intval($data['id']));
        if($rs['code'] == 1){
            $this->success();
        }else{
            $this->error($rs['msg']);
        }
    }

    public function getRoleMenusId(){
        $data = $this->request->param();
        $rs = (new Role())->getRoleMenusId(intval($data['role_id']),1);
        if(!empty($rs['result'])){
            $rs= $rs['result'];
        }else{
            $rs = [];
        }
        
        $this->success($rs);
    }
    public function getRoleMenus(){
        $data = $this->request->param();
        $rs = (new Role())->getRoleMenus(intval($data['role_id']));
        if(!empty($rs['result'])){
            $rs= $rs['result'];
        }else{
            $rs = [];
        }

        $this->success($rs);
    }

    public function editRoleMenus(){
        $data = $this->request->param();
        $rs = (new Role())->editRoleMenus(intval($data['id']),$data['mymenu_ids']);

        if($rs['code'] == 1){
            $this->success();
        }else{
            $this->error($rs['msg']);
        }
    }
}
