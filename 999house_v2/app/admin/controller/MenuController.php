<?php

namespace app\admin\controller;

use app\admin\validate\MenuValidate;
use app\common\base\AdminBaseController;
use app\server\admin\Menu;


class MenuController extends AdminBaseController
{
    /**
     * 后台列表
     */
    public function index(){
        $rs = (new Menu())->getMenuList();
        if(!empty($rs['result'])){
            $rs= $rs['result'];
        }else{
            $rs = [];
        }
        $this->success($rs);
    }

    public function getMenuInfo(){
        $data = $this->request->param();
        $rs = (new Menu())->getMenuInfo($data['id'])['result'];
        $this->success($rs);
    }

    /**
     * 菜单编辑
     */
    public function edit(){
        $data = $this->request->param();
        $validate = new MenuValidate();
        $indata=[
            'parent_id'  => $data['parent_id'],
            'name'       => trim_all($data['name']),
            'url'        => trim_all($data['url']),
            'extra_urls' => trim_all($data['extra_urls']),
            'page'       => trim_all($data['page']),
            'active_page'=> trim_all($data['active_page']),
            'icon'       => $data['icon'],
            'sort'       => $data['sort'],
            'remark'     => $data['remark'],
            'type'       => $data['type'],
            'status'     => $data['status'],
            'btn_show'     => $data['btn_show'],
        ];

        if(intval($data['id'])){
            if($data['type']==0){
                $scenename= 'edit_onlymenu';
            }else{
                $scenename= 'edit';
            }
            if (!$validate->scene($scenename)->check($data)) {
                $this->error($validate->getError());
            }

            $rs = (new Menu())->menuEdit(intval($data['id']),$indata);
        }else{
            if($data['type']==0){
                $scenename= 'add_onlymenu';
            }else{
                $scenename= 'add';
            }
            if (!$validate->scene($scenename)->check($data)) {
                $this->error($validate->getError());
            }

            $rs = (new Menu())->MenuAdd($indata);
        }

        if($rs['code'] == 1){
            $this->success('','操作成功');
        }else{
            $this->error($rs['msg']);
        }
    }

    /**
     * 菜单删除
     */
    public function del(){
        $data = $this->request->param();
        $rs = (new Menu())->menuDel(intval($data['id']));
        if($rs['code'] == 1){
            $this->success();
        }else{
            $this->error($rs['msg']);
        }
    }
}
