<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: pl125 <xskjs888@163.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\validate\MerchantAccountValidate;
use app\admin\validate\MerchantMenuValidate;
use app\common\base\AdminBaseController;
use app\server\merchant\Menu;
use app\server\merchant\Merchant;


class MerchantController extends AdminBaseController
{
    /**
     * 获取商户账号列表
     */
    public function merchantList(){
        $data = $this->request->param();
        $rs = (new Merchant())->getList([
            'name'=>$data['name'],
            'account'=>$data['account'],
            'mobile'=>$data['mobile']
        ]);
        if(!empty($rs['result'])){
            $rs= $rs['result'];
        }else{
            $rs = [];
        }
        $this->success($rs);
    }

    /**
     * 商户账号是否启用
     */
    public function merchantEnable(){
        $data = $this->request->param();
        $rs = (new Merchant())->edit(intval($data['id']),['status'=>$data['status']]);
        if($rs['code'] == 1){
            $this->success();
        }else{
            $this->error($rs['msg']);
        }
    }

    /**
     * 商户账号编辑
     */
    public function merchantEdit(){

        $data = $this->request->param();
        if(!empty($data['wx_setting'])){
            $data['wx_setting'] = json_encode($data['wx_setting'],JSON_UNESCAPED_UNICODE);
        }
        $validate = new MerchantAccountValidate();
        $data['role_id'] = -1;
        $indata = [
            'name'=>$data['name'],
            'mobile' => $data['mobile'],
            'email' => $data['email'],
            'account'=>$data['account'],
            'password' => $data['newpassword'],
            'wx_setting' => $data['wx_setting'],
            'status'=> $data['status'],
        ];

        if(intval($data['id'])){
            if (!$validate->scene('editsuper')->check($data)) {
                $this->error(['code'=>0,'msg'=>$validate->getError()]);
            }

            $rs = (new Merchant())->edit(intval($data['id']),$indata);
        }else{
            if (!$validate->scene('addsuper')->check($data)) {
                $this->error(['code'=>0,'msg'=>$validate->getError()]);
            }

            $rs = (new Merchant())->add($indata);
        }

        if($rs['code'] == 1){
            $this->success();
        }else{
            $this->error($rs['msg']);
        }
    }

    /**
     * 商户账号删除
     */
    public function merchantDel(){
        $data = $this->request->param();
        $rs = (new Merchant())->del(intval($data['id']));
        if($rs['code'] == 1){
            $this->success();
        }else{
            $this->error($rs['msg']);
        }
    }

    //========商户菜单设置 start========//
    public function getMenuList(){
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
    public function menuEdit(){
        $data = $this->request->param();
        $validate = new MerchantMenuValidate();
        $indata=[
            'parent_id'  => $data['parent_id'],
            'name'       => $data['name'],
            'url'        => $data['url'],
            'extra_urls' => $data['extra_urls'],
            'page'       => $data['page'],
            'active_page'=> $data['active_page'],
            'icon'       => $data['icon'],
            'sort'       => $data['sort'],
            'remark'     => $data['remark'],
            'type'       => $data['type'],
            'status'     => $data['status'],
            'btn_show'     => $data['btn_show'],
            'enable'     => $data['enable'],
        ];

        if(intval($data['id'])){
            if($data['type']==0){
                $scenename= 'edit_onlymenu';
            }else{
                $scenename= 'edit';
            }
            if (!$validate->scene($scenename)->check($data)) {
                $this->error(($validate->getError()));
            }

            $rs = (new Menu())->menuEdit(intval($data['id']),$indata);
        }else{
            if($data['type']==0){
                $scenename= 'add_onlymenu';
            }else{
                $scenename= 'add';
            }
            if (!$validate->scene($scenename)->check($data)) {
                $this->error(($validate->getError()));
            }

            $rs = (new Menu())->MenuAdd($indata);
        }

        if($rs['code'] == 1){
            $this->success('','操作成功');
        }else{
            $this->error($rs['msg']);
        }
    }

    public function menuEnable(){
        $data = $this->request->param();
        $data['id'] = intval($data['id']);
        $rs = (new Menu())->menuEdit($data['id'],['enable'=>$data['enable']]);
        if($rs['code'] == 1){
            $this->success();
        }else{
            $this->error($rs['msg']);
        }
    }

    public function menuDel(){
        $data = $this->request->param();
        $rs = (new Menu())->menuDel(intval($data['id']));
        if($rs['code'] == 1){
            $this->success();
        }else{
            $this->error($rs['msg']);
        }
    }
    //========商户菜单设置 end==========//
}
