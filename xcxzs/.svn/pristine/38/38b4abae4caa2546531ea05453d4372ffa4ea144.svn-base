<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdminController
 *
 * @author admin
 */
defined("LOKI") || die("you no have right to access here");

class AdminController extends Controller
{
    protected $db = null;
    protected $gid = null;
    protected $city = null;
    protected $province = null;

    public function permission()
    {
        $this->db = new Query();
        $this->db2 = new Query(new DataBase2());
        $this->gid = $_SESSION['gid'];
        $this->province = $_SESSION['province'];
        $this->city = $_SESSION['city'];
        if (!isset($_SESSION['username']) || !isset($_SESSION['password']) || empty($_SESSION['username']) || empty($_SESSION['password'])) {
            Context::Redirect('/xiamenyyhoutai/login/index');
        } else {
            $this->projectCheck(Context::$controller, Context::$action);// 项目负责人检测
            $this->sourceCheck(Context::$controller, Context::$action);// 渠道部检测
            if ($_SESSION['gid'] != 0) {
                $controller = Context::$controller;
                $action = Context::$action;
                $neglectController = ['login', 'main'];
                if (!in_array($controller, $neglectController)) {
                    $tblGroup = Load::Model('group');
                    $rowGroup = $tblGroup->get($_SESSION['gid']);
                    $auths = $rowGroup['auths'];
                    $auths = explode(',', $auths);
                    $tblAuth = Load::Model('auth');
                    $rowAuth = $tblAuth->get2($controller, $action, $auths);
                    if (empty($rowAuth)) {
                        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                            echo json_encode(['ajax_error' => true]);
                            exit;
                        } else {
                            Context::Redirect('/xiamenyyhoutai/login/error404');
                        }
                    }
                }
            }
        }
        return true;
    }

    // 指定请求要检测项目负责人
    protected function projectCheck($controller, $action)
    {
        $request = strtolower("{$controller}/{$action}");
//        var_dump($request);

        $list = [
            'xcxbuilding/building_doadd',
            'xcxbuilding/building_doedit',
            'xcxbuilding/building_del',
            'xcxbuilding/floor_status',
            'xcxbuilding/floor_doadd',
            'xcxbuilding/floor_doedit',
            'xcxbuilding/floor_del',
            'xcxbuilding/map_doadd',
            'xcxbuilding/map_doedit',
            'xcxbuilding/unit_doadd',
            'xcxbuilding/unit_doedit',
            'xcxbuilding/unit_del',
            'xcxbuilding/door_doadd',
            'xcxbuilding/door_doedit',
            'xcxbuilding/door_status',
            'xcxbuilding/door_is_hot',
            'xcxbuilding/door_del',
            'xcxbuilding/doorimg_doadd',
            'xcxbuilding/doorimg_doedit',
            'xcxbuilding/doorimg_status',
            'xcxbuilding/doorimg_del',
            'xcxbuilding/shuffle_doadd',
            'xcxbuilding/shuffle_doedit',
            'xcxbuilding/shuffle_del',
            'xcxbuilding/shuffle_status',
        ];

        if (in_array($request, $list)) {
            if (7 != $this->gid) {
//                var_dump($this->gid);
                echo json_encode(['success' => false, 'message' => '非项目负责人不可操作']);
                exit();
            }
        }
    }

    // 指定请求要检测是否渠道组
    protected function sourceCheck($controller, $action)
    {
        $request = strtolower("{$controller}/{$action}");
//        var_dump($request);

        $list = [
            'xcxstore/store_doadd',
            'xcxstore/store_doedit',
        ];

        if (in_array($request, $list)) {
            if (5 != $this->gid) {
//                var_dump(1111);
                echo json_encode(['success' => false, 'message' => '非渠道部不可操作']);
                exit();
            }
            if (5 == $this->gid) {
                $info = $this->db->Name('admin')->select("a.channel_id, sa.type", "a")
                    ->where_equalTo('a.id', $_SESSION['aid'])
                    ->leftJoin('xcx_store_agent', 'sa', 'sa.said = a.channel_id')->firstRow();
                if($info['type'] == 6){
                    echo json_encode(['success' => false, 'message' => '非渠道组员不可操作']);
                    exit();
                }
            }
        }
    }
}
