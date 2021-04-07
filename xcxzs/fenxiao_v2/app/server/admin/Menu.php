<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: pl125 <xskjs888@163.com>
// +----------------------------------------------------------------------

namespace app\server\admin;

use app\common\traits\TraitInstance;
use app\common\base\ServerBase;
use think\Db;
use think\Exception;
use tree\Tree;

/*
 * 后台菜单操作 对应 admin_mymenu表
 * */
class Menu extends ServerBase
{
    /**
     * 创建基础菜单与一些设置
     * @return array
     */
    public function createBaseMenu(){
        try{
            $this->db->startTrans();
            $rs = $this->db->name('admin_mymenu')->insertAll([
                [ 'id'=>1, 'parent_id'=>0, 'type' => 0, 'status' => 1, 'sort' => 0, 'url' => '', 'extra_urls'=>'', 'name'=>'系统管理', 'page'=>'', 'active_page'=>'', 'icon'=>'tools', 'remark'=>'', 'btn_show'=>'0' ],
                [ 'id'=>2, 'parent_id'=>1, 'type' => 1, 'status' => 1, 'sort' => 0, 'url' => 'admin/role/index', 'extra_urls'=>'admin/role/edit,admin/role/del,admin/role/enable,admin/role/editRoleMenus,admin/role/getRoleMenusId,admin/menu/index', 'name'=>'角色管理', 'page'=>'admin/role', 'active_page'=>'', 'icon'=>'', 'remark'=>'', 'btn_show'=>'0' ],
                [ 'id'=>3, 'parent_id'=>1, 'type' => 1, 'status' => 1, 'sort' => 0, 'url' => 'admin/menu/index', 'extra_urls'=>'admin/menu/del', 'name'=>'后台菜单', 'page'=>'admin/menulist', 'active_page'=>'', 'icon'=>'', 'remark'=>'', 'btn_show'=>'0' ],
                [ 'id'=>4, 'parent_id'=>1, 'type' => 1, 'status' => 1, 'sort' => 0, 'url' => 'admin/account/index', 'extra_urls'=>'admin/role/index,admin/account/edit,admin/account/del,admin/account/enable', 'name'=>'管理员列表', 'page'=>'admin/list', 'active_page'=>'', 'icon'=>'', 'remark'=>'', 'btn_show'=>'0' ],
                [ 'id'=>5, 'parent_id'=>1, 'type' => 1, 'status' => 0, 'sort' => 0, 'url' => 'admin/menu/getMenuInfo', 'extra_urls'=>'admin/menu/index,admin/menu/edit', 'name'=>'菜单编辑', 'page'=>'admin/menuedit', 'active_page'=>'admin/menulist', 'icon'=>'', 'remark'=>'', 'btn_show'=>'0' ],
                [ 'id'=>6, 'parent_id'=>1, 'type' => 1, 'status' => 1, 'sort' => 0, 'url' => 'admin/sys/sysInfo', 'extra_urls'=>'admin/sys/sysEdit', 'name'=>'基础设置', 'page'=>'admin/baseset', 'active_page'=>'', 'icon'=>'', 'remark'=>'', 'btn_show'=>'0' ],
                [ 'id'=>7, 'parent_id'=>1, 'type' => 1, 'status' => 1, 'sort' => 0, 'url' => 'admin/banner/getBannerList', 'extra_urls'=>'admin/banner/bannerChangeSort,admin/banner/bannerEnable,admin/banner/bannerDel,admin/banner/bannerEdit', 'name'=>'广告图管理', 'page'=>'admin/banner', 'active_page'=>'', 'icon'=>'', 'remark'=>'', 'btn_show'=>'0' ],
                [ 'id'=>8, 'parent_id'=>0, 'type' => 1, 'status' => 0, 'sort' => 0, 'url' => 'admin/index/editPassword', 'extra_urls'=>'', 'name'=>'修改密码', 'page'=>'changePassword/index', 'active_page'=>'', 'icon'=>'tools', 'remark'=>'', 'btn_show'=>'0' ],
            ]);

            if(empty($rs)){
                throw new Exception('操作失败请重试');
            }

            $this->db->commit();
            return $this->responseOk();
        }catch (Exception $e){
            $this->db->rollback();
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    /**
     * 添加菜单
     * @param $data
     * @return array
     */
    public function menuAdd($data){
        try{
            if(!in_array($data['status'],['0','1'])){//是否显示
                throw new Exception('参数错误');
            }
            if(!in_array($data['type'],['0','1','2'])){//菜单类型;1:有界面可访问菜单,2:无界面可访问菜单,0:只作为菜单
                throw new Exception('参数错误');
            }

            $has=$this->db->name('admin_mymenu')->where([
                ['name','=',$data['name']],
                ['parent_id','=',$data['parent_id']]
            ])->value('id');
            if(!empty($has)){
                throw new Exception('该名称已经存在');
            }

            if($data['type']==0){//只作为菜单文字显示
                $data['url'] = '';
                $data['page'] = '';
                $data['extra_urls'] = '';
            }else{
                if(empty($data['url'])){
                    throw new Exception('url设置不能为空');
                }
                $data['url'] = trim_all($data['url']);
                //非单纯的菜单时 查询权限url是否已设置
                $has2 = $this->db->name('admin_mymenu')->where([
                    ['url','=',$data['url']],
                    ['type', 'in', '1,2'],//非简单的菜单文字显示
                ])->value('id');
                if(!empty($has2)){
                    throw new Exception('同样的url记录已经存在');
                }
            }

            if(empty($data['parent_id'])){
                $data['btn_show'] = 0; //是否上级按钮触发显示
            }

            if(!empty($data['extra_urls'])){
                //统一,符号
                $data['extra_urls']=str_replace('，',',',$data['extra_urls']);
            }
            $this->db->name('admin_mymenu')->insert($data);
            return $this->responseOk();
        }catch (Exception $e){

            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    /**
     * 修改菜单
     * @param $id
     * @param $data
     * @return array
     * @throws Exception
     */
    public function menuEdit($id,$data){
        try{
            if(!is_int($id)){
                throw new Exception('参数错误');
            }
            if(!in_array($data['status'],['0','1'])){//是否显示
                throw new Exception('参数错误');
            }
            if(!in_array($data['type'],['0','1','2'])){//菜单类型;1:有界面可访问菜单,2:无界面可访问菜单,0:只作为菜单
                throw new Exception('参数错误');
            }

            if(!empty($data['name'])){
                if(!isset($data['parent_id'])){
                    throw new Exception('参数错误');
                }
                $has = $this->db->name('admin_mymenu')->where([
                    ['name','=',$data['name']],
                    ['parent_id','=',$data['parent_id']]
                ])->value('id');
                if(!empty($has)&&$has!=$id){
                    throw new Exception('该名称已经存在');
                }
            }

            if($data['type']==0){//只作为菜单文字显示
                $data['url'] = '';
                $data['page'] = '';
                $data['extra_urls'] = '';
            }else{
                if(empty($data['url'])){
                    throw new Exception('url设置不能为空');
                }
                $data['url'] = trim_all($data['url']);
                //非单纯的菜单时 查询权限url是否已设置
                $has2 = $this->db->name('admin_mymenu')->where([
                    ['url','=',$data['url']],
                    ['type', 'in', '1,2'],//非简单的菜单文字显示
                ])->value('id');
                if(!empty($has2)&&$has2!=$id){
                    throw new Exception('同样的url记录已经存在');
                }
            }

            if(empty($data['parent_id'])){
                $data['btn_show'] = 0; //是否上级按钮触发显示
            }

            if(!empty($data['extra_urls'])){
                //统一,符号
                $data['extra_urls']= str_replace('，',',',$data['extra_urls']);
            }
            unset($data['id']);//不可变更id
            $rs= $this->db->name('admin_mymenu')->where(['id'=>$id])->update($data);
            if($rs){

                return $this->responseOk();
            }else{
                return $this->responseFail();
            }
        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }


    /**
     * 菜单删除
     * @param $id
     * @param $data
     * @throws Exception
     */
    public function menuDel($id){
        try{
            if(!is_int($id)||empty($id)){
                throw new Exception('参数错误');
            }

            //有子菜单不可删除
            $hassub= $this->db->name('admin_mymenu')->where(['parent_id'=>$id])->value('id');
            if(!empty($hassub)){
                throw new Exception('该信息含有子菜单不可删除');
            }

            $rs= $this->db->name('admin_mymenu')->where(['id'=>$id])->delete();
            if($rs){

                return $this->responseOk();
            }else{
                return $this->responseFail();
            }
        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    /**
     * 全部的菜单列表
     */
    public function getMenuList($dotype= 'getTree'){
        $result = array(
            'list'  =>  [],
        );

        $order = ['sort'=>'desc'];//排序操作

        $list = $this->db->name('admin_mymenu')
            ->order($order)
            ->select();

        if($list->isEmpty()){
            $result['list'] = [];
        }else{
            if($dotype=='getTree'){//转换成树形结构数组
                $tree       =  new \app\common\lib\tree\Menu();
                $tree->init($list->toArray());
                $list=$tree->getTreeArray(0);
                $list=$tree->getTreeArrayForReIndex($list);
            }

            $result['list'] =$list;
        }

        return $this->responseOk($result);
    }

    /**
     * 菜单信息
     */
    public function getMenuInfo($id){
        $info = $this->db->name('admin_mymenu')
            ->where(['id'=>$id])
            ->find();

        if(empty($info['id'])){
            $info = [];
        }

        return $this->responseOk($info);
    }

}
