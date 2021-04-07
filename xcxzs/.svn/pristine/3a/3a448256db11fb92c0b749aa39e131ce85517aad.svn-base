<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of main
 *
 * @author ADMIN
 */
include 'AdminController.php';
include System . DS . 'Upload.php';

class admin extends AdminController
{
    public function index()
    {
        $pageRow = Context::Get('row'); //显示几条
        $pageRow = empty($pageRow) ? 20 : intval($pageRow);
        $page = Context::Get('page'); //页码
        $page = empty($page) ? 1 : intval($page);
        $arr = array();
        $arr['name'] = Context::Get('name');
        $arr['username'] = Context::Get('username');
        $arr['gid'] = Context::Get('gid');
        $arr['status'] = Context::Get('status');

        $tblAdmin = Load::Model('admin');
        /* @var $model ResultModel */
        $data = array();
        $data = $arr;
        $total = $tblAdmin->total($arr);

        $totalPage = ceil($total / $pageRow);
        $totalPage = $totalPage == 0 ? 1 : $totalPage;
        $page = $page > $totalPage ? $totalPage : $page;

        $data['rows'] = $tblAdmin->find($pageRow, $page, $arr);
        //获取所有角色组
        $groupDict = [];
        $groupDara = $this->db->Name('group')->select()->execute();
        if (!empty($groupDara)) {
            foreach ($groupDara as $v) {
                $groupDict[$v['id']] = $v['name'];
            }
        }
        if (!empty($data['rows'])) {
            foreach ($data['rows'] as &$val) {
                $val['group_name'] = empty($val['gid']) ? '超级管理员' : $groupDict[$val['gid']];
            }
        }

        $pagination = new Pagination($page, $total);
        $pagination->setPageRow($pageRow);

        $data['total'] = $total;
        $data['pageRow'] = $pageRow;
        $data['pagination'] = $pagination->Render();
        $tblGroup = Load::Model('group');
        $rowGroup = $tblGroup->getall();
        $data['groups'] = $rowGroup;
        return $this->render('list', $data);
    }

    function add()
    {
        $data = array();
        $tblGroup = Load::Model('group');
        $rowGroup = $tblGroup->getall();
        $data['groups'] = $rowGroup;
        return $this->render('add', $data);
    }

    function personnel() {
        $type = Context::Post('type');
        if($type == 5){
            $type = 5;
        }elseif($type == 7){
            $type = 7;
        }elseif($type = 8){
            $type = 6;
        }
        $data = $this->db->Name('xcx_store_agent')
            ->where_equalTo('type', $type)
            ->where_notEqualTo('agent_id', 0)
            ->select('said, name, agent_name, agent_img')->execute();
        echo json_encode(['success' => $data]);
    }

function addsave()
{
    $data = array();
    $arr = $_POST;

    $tblAdmin = Load::Model('admin');
    if (!empty($arr['password'])) {
        $arr['password'] = md5($arr['password']);
    } else {
        $arr['password'] = md5("123456");
    }
    $arr['atime'] = time();
    $personnel = $arr['personnel'];
    unset($arr['id']);
    unset($arr['image']);
    unset($arr['personnel']);

    if(empty($arr['username'])) {
        echo json_encode(['success'=>false,'message'=>'用户名不能为空']);
        exit();
    }
    $is_exit = $this->db->Name('admin')->where_equalTo('username',$arr['username'])
        ->select('id')->firstRow();
    if(!empty($is_exit)){
        echo json_encode(['success'=>false,'message'=>'用户名已存在']);
        exit();
    }

    //判断一下是否被绑定了
    if(!empty($personnel)) {
        if($arr['gid'] == 5 || $arr['gid'] == 8){
            $arr['channel_id'] = $personnel;
            $is_exit = $this->db->Name('admin')->where_equalTo('channel_id',$personnel)
                ->select('id')->firstRow();

            if(!empty($is_exit)){
                echo json_encode(['success'=>false,'message'=>'所属人员id已经被绑定']);
                exit();
            }
        }
        if($arr['gid'] == 7){
            $arr['charge_id'] = $personnel;
            $is_exit = $this->db->Name('admin')->where_equalTo('charge_id', $personnel)
                ->select('id')->firstRow();

            if(!empty($is_exit)){
                echo json_encode(['success'=>false,'message'=>'所属人员id已经被绑定']);
                exit();
            }
        }
    }


    try {
        $tblAdmin->add($arr);
        echo json_encode(['success' => true]);
    } catch (Exception $ex) {
        //print_r($ex);
        DataBase::log(__FILE__ . __LINE__, $ex);
        echo json_encode(['success' => false,'message'=>$ex->getMessage()]);
    }
    //Context::Redirect('/admin/admin/index');
}

function edit()
{
    $data = array();
    $id = Context::$para[0];
    $tblAdmin = Load::Model('admin');
    $rowAdmin = $tblAdmin->get($id);
    $data['data'] = $rowAdmin;
    $tblGroup = Load::Model('group');
    $rowGroup = $tblGroup->getall();
    $data['groups'] = $rowGroup;

    if($data['data']['gid'] == 5){
        $list = $this->db->Name('xcx_store_agent')
            ->where_equalTo('type', 5)
            ->where_notEqualTo('agent_id', 0)
            ->select('said, name, agent_name, agent_img')->execute();
        $data['list'] = empty($list) ? [] : $list;
    }elseif($data['data']['gid'] == 7){
        $list = $this->db->Name('xcx_store_agent')
            ->where_equalTo('type', 7)
            ->where_notEqualTo('agent_id', 0)
            ->select('said, name, agent_name, agent_img')->execute();
        $data['data']['channel_id'] = $data['data']['charge_id'];
        $data['list'] = empty($list) ? [] : $list;
    }elseif($data['data']['gid'] == 8){
        $list = $this->db->Name('xcx_store_agent')
            ->where_equalTo('type', 6)
            ->where_notEqualTo('agent_id', 0)
            ->select('said, name, agent_name, agent_img')->execute();
        $data['list'] = empty($list) ? [] : $list;
    }

    if(!empty($data['list'])) {
        foreach ($data['list'] as &$v) {
            $v['select_name'] = $v['name'];
            if(!empty($v['agent_name'])) {
                $v['select_name'] .= " {$v['agent_name']} ";
            }
        }
    }


//    var_dump($data['groups']);
    return $this->render('edit', $data);
}

function editsave()
{
    $data = array();
    $arr = $_POST;
    $id = $arr['id'];

    $tblAdmin = Load::Model('admin');
    if (!empty($arr['password'])) {
        $arr['password'] = md5($arr['password']);
    } else {
        unset($arr['password']);
    }
    $arr['atime'] = time();

    //判断一下是否被绑定了
    if(!empty($arr['channel_id'])) {
        if($arr['gid'] == 5 || $arr['gid'] == 8){
            $is_exit = $this->db->Name('admin')->where_equalTo('channel_id', $arr['channel_id'])
                ->select('id,channel_id')->execute();
            if(!empty($is_exit)){
                foreach ($is_exit as $key => $value){
                    if($value['channel_id'] == $arr['channel_id'] && $id != $value['id']){
                        echo json_encode(['success'=>false,'message'=>'所属人员id已经被绑定']);
                        exit();
                    }
                }
            }
        }

        if($arr['gid'] == 7){
            $arr['charge_id'] = $arr['channel_id'];
            $is_exit = $this->db->Name('admin')->where_equalTo('charge_id', $arr['channel_id'])
                ->select('id,charge_id')->execute();
            if(!empty($is_exit)){
                foreach ($is_exit as $key => $value){
                    if($value['charge_id'] == $arr['channel_id'] && $id != $value['id']){
                        echo json_encode(['success'=>false,'message'=>'所属人员id已经被绑定']);
                        exit();
                    }
                }
            }
            unset($arr['channel_id']);
        }
    } else {
        if($arr['gid'] == 7) {
            $arr['charge_id'] = $arr['channel_id'];
            unset($arr['channel_id']);
        }
    }


    unset($arr['id']);
    unset($arr['image']);
    unset($arr['file']);
    try {
        $tblAdmin->update($id, $arr);
        echo json_encode(['success' => true]);
    } catch (Exception $ex) {
        //print_r($ex);
        echo json_encode(['success' => false]);
    }
    //Context::Redirect('/admin/admin/index');
}

function del()
{
    $data = array();
    $id = Context::$para[0];
    $tblAdmin = Load::Model('admin');
    try {
        $tblAdmin->delete($id);
        echo json_encode(['success' => true]);
    } catch (Exception $ex) {
        //print_r($ex);
        echo json_encode(['success' => false]);
    }
}

public
function group_list()
{
    $pageRow = Context::Get('row'); //显示几条
    $pageRow = empty($pageRow) ? 20 : intval($pageRow);
    $page = Context::Get('page'); //页码
    $page = empty($page) ? 1 : intval($page);
    $arr = array();
    $arr['name'] = Context::Get('name');
    $arr['status'] = Context::Get('status');

    $tblGroup = Load::Model('group');
    $data = array();
    $data = $arr;
    $total = $tblGroup->total($arr);

    $totalPage = ceil($total / $pageRow);
    $totalPage = $totalPage == 0 ? 1 : $totalPage;
    $page = $page > $totalPage ? $totalPage : $page;

    $data['rows'] = $tblGroup->find($pageRow, $page, $arr);

    $pagination = new Pagination($page, $total);
    $pagination->setPageRow($pageRow);

    $data['total'] = $total;
    $data['pageRow'] = $pageRow;
    $data['pagination'] = $pagination->Render();
    return $this->render('group_list', $data);
}

function group_add()
{
    $data = array();
    $tblAuth = Load::Model('auth');
    $rowAuth = $tblAuth->getall2();
    $data['auths'] = $rowAuth;
    return $this->render('group_add', $data);
}

function group_addsave()
{
    $data = array();
    $arr = $_POST;

    $tblGroup = Load::Model('group');
    $arr['atime'] = time();
    if (empty($arr['auths'])) {
        $arr['auths'] = '';
    } else {
        $arr['auths'] = implode(',', $arr['auths']);
    }
    unset($arr['id']);
    unset($arr['image']);
    try {
        $tblGroup->add($arr);
        echo json_encode(['success' => true]);
    } catch (Exception $ex) {
        //print_r($ex);
        echo json_encode(['success' => false]);
    }
}

function group_edit()
{
    $data = array();
    $id = Context::$para[0];
    $tblGroup = Load::Model('group');
    $rowGroup = $tblGroup->get($id);
    $data['data'] = $rowGroup;
    if (!empty($rowGroup['auths'])) {
        $data['auth_arr'] = explode(',', $rowGroup['auths']);
    } else {
        $data['auth_arr'] = [];
    }
    $tblAuth = Load::Model('auth');
    $rowAuth = $tblAuth->getall2();
    $data['auths'] = $rowAuth;
    return $this->render('group_edit', $data);
}

function group_editsave()
{
    $data = array();
    $arr = $_POST;
    $id = $arr['id'];

    $tblGroup = Load::Model('group');
    $arr['atime'] = time();
    if (empty($arr['auths'])) {
        $arr['auths'] = '';
    } else {
        $arr['auths'] = implode(',', $arr['auths']);
    }
    unset($arr['id']);
    unset($arr['image']);
    try {
        $tblGroup->update($id, $arr);
        echo json_encode(['success' => true]);
    } catch (Exception $ex) {
        //print_r($ex);
        echo json_encode(['success' => false]);
    }
}

function group_del()
{
    $data = array();
    $id = Context::$para[0];
    $tblGroup = Load::Model('group');
    try {
        $tblGroup->delete($id);
        echo json_encode(['success' => true]);
    } catch (Exception $ex) {
        //print_r($ex);
        echo json_encode(['success' => false]);
    }
}

public
function auth_list()
{
    $pageRow = Context::Get('row'); //显示几条
    $pageRow = empty($pageRow) ? 20 : intval($pageRow);
    $page = Context::Get('page'); //页码
    $page = empty($page) ? 1 : intval($page);
    $arr = array();
    $arr['controller'] = Context::Get('controller');
    $arr['controller_name'] = Context::Get('controller_name');
    $arr['action'] = Context::Get('action');
    $arr['action_name'] = Context::Get('action_name');
    $arr['status'] = Context::Get('status');

    $tblAuth = Load::Model('auth');
    $data = array();
    $data = $arr;
    $total = $tblAuth->total($arr);

    $totalPage = ceil($total / $pageRow);
    $totalPage = $totalPage == 0 ? 1 : $totalPage;
    $page = $page > $totalPage ? $totalPage : $page;

    $joinArr[] = [
        'type'  => 'left',
        'table' => 'menu',
        'alias' => 'm',
        'on'    => "a.menu_id=m.id",
    ];
    $fileds = 'a.*, m.name as menu_name';
    $data['rows'] = $tblAuth->joinFind($pageRow, $page, $arr, $fileds, $joinArr);

    $pagination = new Pagination($page, $total);
    $pagination->setPageRow($pageRow);

    $data['total'] = $total;
    $data['pageRow'] = $pageRow;
    $data['pagination'] = $pagination->Render();
    return $this->render('auth_list', $data);
}

function auth_add()
{
    $data = array();
    // 菜单
    $menu = (new Query())->Name('menu')->select('id, name')->where_equalTo('status', 1)->execute();
    $default = [
        ['id' => 0, 'name' => '无所属菜单']
    ];
    if (!empty($menu)) {
        $menu = array_merge($default, $menu);
        $data['menu'] = $menu;
    } else {
        $data['menu'] = $default;
    }
    return $this->render('auth_add', $data);
}

function auth_addsave()
{
    $data = array();
    $arr = $_POST;

    $tblAuth = Load::Model('auth');
    $arr['atime'] = time();
    unset($arr['id']);
    unset($arr['image']);
    try {
        $tblAuth->add($arr);
        echo json_encode(['success' => true]);
    } catch (Exception $ex) {
        //print_r($ex);
        echo json_encode(['success' => false]);
    }
}

function auth_edit()
{
    $data = array();
    $id = Context::$para[0];
    $tblAuth = Load::Model('auth');
    $rowAuth = $tblAuth->get($id);
    $data['data'] = $rowAuth;
    // 菜单
    $menu = (new Query())->Name('menu')->select('id, name')->where_equalTo('status', 1)->execute();
    $default = [
        ['id' => 0, 'name' => '无所属菜单']
    ];
    if (!empty($menu)) {
        $menu = array_merge($default, $menu);
        $data['menu'] = $menu;
    } else {
        $data['menu'] = $default;
    }

    return $this->render('auth_edit', $data);
}

function auth_editsave()
{
    $data = array();
    $arr = $_POST;
    $id = $arr['id'];

    $tblAuth = Load::Model('auth');
    $arr['atime'] = time();
    unset($arr['id']);
    unset($arr['image']);
    try {
        $tblAuth->update($id, $arr);
        echo json_encode(['success' => true]);
    } catch (Exception $ex) {
        //print_r($ex);
        echo json_encode(['success' => false]);
    }
}

function auth_del()
{
    $data = array();
    $id = Context::$para[0];
    $tblAuth = Load::Model('auth');
    try {
        $tblAuth->delete($id);
        echo json_encode(['success' => true]);
    } catch (Exception $ex) {
        //print_r($ex);
        echo json_encode(['success' => false]);
    }
}

//头像上传
function uploadimg()
{
    $id = Context::Post('id');
    $upfile = new UploadFiles(array('filepath' => BasePath . DS . 'upload' . DS . 'admin'));
    if ($upfile->uploadeFile('file')) {
        $arrfile = $upfile->getnewFile();
        $info = (new Query())->Name('admin')->select()->where_equalTo('id', $id)->firstRow();
        if (!empty($info)) {
            $old = $info['img'];
            $res = $this->db->Name('admin')->update(['img' => '/upload/admin/' . $arrfile])->where_equalTo('id', $id)->execute();
            if ($res) {
                if (file_exists(BasePath . $old)) {
                    unlink(BasePath . $old);
                }
                echo json_encode(['success' => true]);
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => '保存失败']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => '保存失败']);
            exit;
        }
    } else {
        $err = $upfile->gteerror();
        echo json_encode(['success' => false, 'msg' => $err]);
        exit;
    }
}

//报备权限管理
public
function reported_manage()
{
    $id = Context::Get('id');
    $adminRow = $this->db->Name('admin')->select()->where_equalTo('id', $id)->firstRow();
    if (empty($adminRow['reported_building_ids']) && $adminRow['reported_building_ids'] === '0') {
        $adminRow['is_all'] = '1';
        $adminRow['resList'] = json_encode([]);
    } else {
        $temp = [];
        $adminRow['is_all'] = '0';
        if (empty($adminRow['reported_building_ids'])) {
            $adminRow['resList'] = json_encode([]);
        } else {
            $building_ids = explode(',', $adminRow['reported_building_ids']);
            $buildingData = $this->db->Name('xcx_building_building')->select('id,name')->where_in('id', $building_ids)->execute();
            foreach ($buildingData as $val) {
                $temp[] = ['id' => $val['id'], 'name' => $val['name']];
            }
            $adminRow['resList'] = json_encode($temp, JSON_UNESCAPED_UNICODE);
        }
    }
    $data['data'] = $adminRow;
    return $this->render('reported_manage', $data);
}

//报备权限管理修改事件
public
function reported_manage_doedit()
{
    $id = Context::Post('id');
    if (!empty(Context::Post('building_ids'))) {
        $building_ids = trim(Context::Post('building_ids'), ',');
        $building_ids = explode(',', $building_ids);
        $building_ids = array_unique($building_ids);
        $building_ids = implode(',', $building_ids);
    } else {
        $building_ids = Context::Post('building_ids');
    }
    $data['reported_building_ids'] = $building_ids;
    $data['reported_status'] = trim(Context::Post('reported_status'), ',');
    $res = $this->db->Name('admin')->update($data)->where_equalTo('id', $id)->execute();
    if ($res) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => "保存失败"]);
    }
}
}
