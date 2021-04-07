<?php

namespace app\server\admin;

use app\common\traits\TraitInstance;
use app\common\base\ServerBase;
use think\Exception;

/*
 * 后台用户操作
 * */
class Role extends ServerBase
{
    /**
     * 添加角色
     * @param $data
     * @return array
     */
    public function roleAdd($data){
        try{
            if(empty($data['name'])){
                throw new Exception('参数错误');
            }
            $has=$this->db->name('admin_myrole')->where(['name'=>$data['name']])->value('id');
            if(!empty($has)){
                throw new Exception('该名称已经存在');
            }

            if(isset($data['status'])&&!in_array($data['status'],['0','1'])){//是否启用
                throw new Exception('参数错误');
            }

            $this->db->name('admin_myrole')->insertGetId([
                'name' => $data['name'],
                'remark' => $data['remark'],
                'status' => $data['status']
            ]);

            return $this->responseOk();
        }catch (Exception $e){

            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    /**
     * 修改角色
     * @param $id
     * @param $data
     * @return array
     * @throws Exception
     */
    public function roleEdit($id,$data){
        try{
            $id = intval($id);
            if(empty($id)){
                throw new Exception('参数错误');
            }
            if( $id==-1 ){
                throw new Exception('该信息不可修改');
            }

            if(isset($data['status'])&&!in_array($data['status'],['0','1'])){//是否启用
                throw new Exception('参数错误');
            }
            if(!empty($data['name'])){
                $has=$this->db->name('admin_myrole')->where([
                    ['name','=',$data['name']],
                ])->value('id');
                if(!empty($has)&&$has!=$id){
                    throw new Exception('该名称已经存在');
                }
            }

            if( $id==1 ){
                throw new Exception('该信息不可修改');
            }

            unset($data['id']);//不可变更id
            $rs= $this->db->name('admin_myrole')->where(['id'=>$id])->update($data);
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
     * 角色删除
     * @param $id
     * @param $data
     * @throws Exception
     */
    public function roleDel($id){
        try{
            $id = intval($id);
            if(empty($id)){
                throw new Exception('参数错误');
            }
            if( $id==-1 ){ //为超级管理员角色id
                throw new Exception('该信息不可删除');
            }

            $rs= $this->db->name('admin_myrole')->where(['id'=>$id])->delete();
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
     * 角色列表
     */
    public function getRoleList(){
        $result = array(
            'list'  =>  [],
        );

        $list = $this->db->name('admin_myrole')
            ->order('id desc')
            ->select();

        if($list->isEmpty()){
            $result['list'] = [];
        }else{
            $result['list'] =$list;
        }

        return $this->responseOk($result);
    }

    /**
     * 获取角色信息
     * @param $role_id
     * @return array
     */
    public function getRoleInfo($role_id){
        $info = $this->db->name('admin_myrole')
            ->where(['id'=>$role_id])
            ->find();
        if(empty($info)) {
            $info = [];
        }

        $result['info'] =$info;
        return $this->responseOk($result);
    }

    /**
     * 获取角色权限菜单id集合 对应admin_myrole_auth_access表
     * @param $role_id
     * @param int $toarray
     * param strint $dotype //直接返回数组数据还是api返回
     * @return array
     * @throws Exception
     */
    public function getRoleMenusId($role_id,$toarray=0,$dotype='api'){
        $result = array(
            'list'  =>  [],
        );
        if(!($role_id > 0)){
            return $this->responseFail(['code'=>0,'msg'=>'参数错误']);
        }
        //校验role_id
        $check_role_id=$this->db->name('admin_myrole')->where(['id'=>$role_id])->value('id');
        if(!$check_role_id){
            return $this->responseFail(['code'=>0,'msg'=>'role_id不存在']);
        }

        $list = $this->db->name('admin_myrole_auth_access')
            ->alias('access')
            ->where(['role_id'=>$role_id])
            ->value('access.mymenu_ids');

        if(empty($list)){
            $result['list'] = [];
        }else{
            $result['list'] =$list;
            if($toarray == 1){
                $result['list'] = explode(',',$list);
            }
        }
        if($dotype=='api'){
            return $this->responseOk($result);
        }else{
            return $result;
        }
    }

    /**
     * 获取某个角色的权限的所有菜单列表
     * @param $role_id
     * @param $dotype toarray,getTree
     * @return array
     * @throws Exception
     */
    public function getRoleMenus($role_id,$dotype = 'getArray'){
        $result = array(
            'list'  =>  [],
        );
        $role_id = intval($role_id);
        if($role_id==-1){//超级管理员角色时,获取所有菜单
            $list = $this->db->name('admin_mymenu')->order(['sort'=>'desc'])->select()->toArray();
        }else{
            $rs_ids=$this->getRoleMenusId($role_id,0,'data');//获取id集合
            if(!empty($rs_ids['list'])){
                $list = $this->db->name('admin_mymenu')->where([
                    ['id' ,'in' ,$rs_ids['list']]
                ])->order(['sort'=>'desc'])->select()->toArray();
            }
        }

        if(empty($list)){
            $result['list'] = [];
        }else{
            if($dotype=='getTree'){
                $tree = new \app\common\lib\tree\Menu();
                $tree->init($list);
                $list=$tree->getTreeArray(0);
                $list=$tree->getTreeArrayForReIndex($list);
            }
            $result['list'] =$list;
        }

        return $this->responseOk($result);
    }


    /**
     * 获取某个角色的权限菜单的url列表
     * @param $role_id
     * @return array
     * @throws Exception
     */
    public function getRoleMenusUrl($role_id){
        if(empty($role_id)){
            return $this->responseFail('','缺少参数');
        }

        $result = array(
            'list'  =>  [],
        );
        $rs_ids=$this->getRoleMenusId($role_id,0,'data');//获取id集合
        if(!empty($rs_ids['list'])){
            $list = $this->db->name('admin_mymenu')->where([
                ['id' ,'in' ,$rs_ids['list']],
                ['type' ,'in' ,[1,2]]
            ])->column('extra_urls','url');
        }
        if(empty($list)){
            $result['list'] = [];
        }else{
            $result['list'] =$list;
        }

        return $this->responseOk($result);
    }


    /**
     * 获取某个角色的权限菜单的url列表为格式话后的
     * @param $role_id
     * @return array
     * @throws Exception
     */
    public function getRoleMenusUrlAfterFormat($role_id){
        if(empty($role_id)){
            return $this->responseFail('','缺少参数');
        }

        $rs = $this->getRoleMenusUrl($role_id)['result'];
        $result = [];

        if(!empty($rs['list'])) {
            foreach ($rs['list'] as $key=>$item){
                $key = strtolower($key);
                array_push($result,$key);
                if(!empty($item)){//转换附加的权限url
                    $item = strtolower($item);
                    $item = explode(',',$item);
                    foreach ($item as $item2){
                        if(in_array($item2,$result)){
                            continue;
                        }
                        array_push($result,$item2);
                    }
                }
            }
        }
//        var_dump($result);
        return $this->responseOk($result);
    }


    /**
     * 角色菜单授权编辑 对应admin_myrole_auth_access表
     * @param int $role_id 对应角色id
     * @param array $data
     */
    public function editRoleMenus($role_id,$data){
        try{
            $role_id = intval($role_id);
            if(empty($role_id)){
                throw new Exception('参数错误');
            }
            if($role_id==-1){
                throw new Exception('该角色权限菜单不可编辑');
            }

            if(empty($data)){
                $data='';
            }else{
                if(!is_array($data)){
                    throw new Exception('参数不是数组格式');
                }
                $count1 = count($data);
                $data = implode($data,',');
                $count2=$this->db->name('admin_mymenu')->where([
                    ['id' ,'in' ,$data]
                ])->count();
                if($count1!=$count2||empty($count2)){//选项数据与数据库不一致
                    throw new Exception('请重新选择权限菜单');
                }

            }

            $has=$this->db->name('admin_myrole_auth_access')->where(['role_id' => $role_id])->value('id');
            if($has){
                $rs= $this->db->name('admin_myrole_auth_access')->where(['role_id'=>$role_id])->update(['mymenu_ids'=>$data]);
            }else{
                $rs=$this->db->name('admin_myrole_auth_access')->insertGetId([
                    'role_id' => $role_id,
                    'mymenu_ids' => $data
                ]);
            }

            if($rs){
                return $this->responseOk();
            }else{
                return $this->responseFail();
            }
        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

}
