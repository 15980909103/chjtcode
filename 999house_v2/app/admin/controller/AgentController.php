<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: pl125 <xskjs888@163.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\base\AdminBaseController;
use app\server\admin\Admin;
use app\server\admin\Agent;
use app\server\admin\CitySite;
use app\server\user\User;

class AgentController extends AdminBaseController
{
    
    /**
     * 列表
     */
    public function getList()
    {
        $param = $this->request->param();

        $data = [
            'where' => [],
            'page_size' => 20,
        ];

        if(!empty($param['search_word'])) {
            $data['where'][] = ['name|phone', 'like', "%{$param['search_word']}%"];
        }

        $res = (new Agent())->getList($data);
        if(empty($res['code']) || 1 != $res['code']) {
            $this->error($res);
        }
        $res = $res['result'];

        if(!empty($res['list'])) {
            foreach($res['list'] as &$v) {
                $v['city_no'] = !empty($v['city_no']) ? $v['city_no'] : '';
                $v['area_no'] = !empty($v['area_no']) ? $v['area_no'] : '';
                $v['head_img_url'] = !empty($v['head_img']) ? $this->getFormatImgs($v['head_img']) : [];
                $v['create_time'] = date('Y-m-d', $v['create_time']);
            }
        }

        $this->success($res);
    }

    /**
     * 添加/编辑
     */
    public function editAgent()
    {
        $param = $this->request->param();

        if(empty($param['name'])) {
            $this->error('请填写姓名');
        }
        if(empty($param['phone'])) {
            $this->error('请填写手机');
        }
        if(!preg_match('/^1\d{10}$/', $param['phone'])) {
            $this->error('手机格式错误');
        }
        if(!empty($params['is_default']) && !empty($param['city_no'])) {
            $this->error('未选地区不得设为默认');
        }

        if(empty($param['user_id'])) {
           return $this->error('请选择绑定微信账号');
        }
//        $this->db->startTrans();
        $data = [
            'name' => $param['name'],
            'phone' => $param['phone'],
            'head_img' => !empty($param['head_img_url'][0])&&!empty($param['head_img_url'][0]['url']) ? $param['head_img_url'][0]['url'] : "",
            'type' => $param['type'] ?? 1,
            'status' => $param['status'] ?? 1,
            'city_no' => $param['city_no'] ?? 0,
            'area_no' => $param['area_no'] ?? 0,
            'is_default' => !empty($param['is_default']) ? (int)$param['is_default'] : 0,
        ];
        //建立套房是账号
        $adminData =[
            'account'   => $param['name'],
            'mobile'    => $param['phone'],
            'name'      => $param['name'],
            'user_id'   => $param['user_id'],
            'head_img'  => $data['head_img'],
            'password'  => '123456',
            'role_id'   => 3,
            'status'    => 1,
        ];
        $city_info  = (new CitySite())->getCityInfo($param['city_no']);
        if($city_info['code'] ==0){
           return $this->error($city_info['msg']);
        }

        $city_info =  $city_info['result'];
        $adminData[ 'region_nos_info']= [
                [
                    'id'    => $city_info['id'],
                    'cname' => $city_info['cname'],
                    'pid'   => $city_info['pid'],
                    'pcname'=> $city_info['pcname'],
                ]
        ];


        $adminData['region_nos_info']= json_encode($adminData['region_nos_info'],JSON_UNESCAPED_UNICODE);
        $building = $param['building'] ?? [];


        $server = new Agent();
        $admin_server = new Admin();

        //检测用户是否绑定了其他套房师
        $admin_info_user = $admin_server->getUserInfo(['user_id' =>$param['user_id']])['result'];
        if(!empty($param['id'])) {
            if(!empty($admin_info_user) && $admin_info_user['estates_agent_id'] != $param['id']){
                return  $this->error('改用户已经绑定了套房师了不能绑定多个套房师哦！');
            }
            $res    = $server->edit(['id' => $param['id']], $data, $building);
            if($res['code'] == 0){
                $this->db->rollback();
                return  $this->error($res);
            }
            $admin_info      = $admin_server->getUserInfo(['estates_agent_id' =>$param['id']])['result'];
            //没有新增账号;
            if(empty($admin_info) ){
                $adminData['estates_agent_id'] = $param['id'];
                $res1   = $admin_server->userAdd($adminData);
                if($res1['code'] == 0){
                    $this->db->rollback();
                    return  $this->error($res1);
                }

            }else{
                //这边只更改 绑定的前台用户
                $res1   = $admin_server->userEdit($admin_info['id'],['user_id'=>$param['user_id'],'head_ico_path' => $data['head_img']] );

                if($res1['code'] == 0){
                    $this->db->rollback();
                    return  $this->error($res1);
                }
                //解除久的套师身份
                $res4 = (new User())->setUserType($admin_info['user_id'],1);
                if(!$res4){
                        $this->db->rollback();
                        return  $this->error();

                }

            }

            //将用户属性改为套房师
            $res3 = (new User())->setUserType($param['user_id'],2);
            if(!$res3){
                    $this->db->rollback();
                    return  $this->error();

            }

        } else {
            $this->db->startTrans();
            if(!empty($admin_info_user)){
                return  $this->error('改用户已经绑定了套房师了不能绑定多个套房师哦！');
            }
            $res    = $server->add($data, $building);
            if($res['code'] == 0){
                $this->db->rollback();
                return  $this->error($res);
            }

            $adminData['estates_agent_id'] = $res['result'];
            $res1   = $admin_server->userAdd($adminData);
            if($res1['code'] == 0){
                $this->db->rollback();
                return  $this->error($res1);
            }

            $res3 = (new User())->setUserType($data['user_id'],2);

            if(!$res3){

                $this->db->rollback();
                return  $this->error();

            }


        }
        $this->db->commit();
//        if(empty($res['code']) || 1 != $res['code']) {
//            $this->error($res);
//        }

        $this->success();
    }

    /**
     * 删除
     */
    public function delete()
    {
        $param = $this->request->param();

        if(empty($param['id'])) {
            $this->error('缺少必要参数');
        }

        $res = (new Agent())->delete($param['id']);

        if(empty($res['code']) || 1 != $res['code']) {
            $this->error($res);
        }

        $this->success();
    }

    /**
     * 状态修改
     */
    public function setStatus()
    {
        $param = $this->request->param();

        if(empty($param['id'])) {
            $this->error('缺少必要参数');
        }

        $data = [
            'status' => $param['status'] ?? 0,
        ];

        $res = (new Agent())->edit(['id' => $param['id']], $data);

        if(empty($res['code']) || 1 != $res['code']) {
            $this->error($res);
        }

        $this->success();
    }


    //==============淘房师-楼盘 关联 start===================//

    /**
     * 列表
     */
    public function getEstatesList()
    {
        $param = $this->request->param();

        if(empty($param['agent_id'])) {
            $this->error('缺少必要参数');
        }
        $agentId = $param['agent_id'];
        
        $params = [
            'where' => [
                ['agent_id', '=', $agentId],
            ],
            'join' => [['table' => 'estates_new en', 'cond' => "en.id=ae.estate_id", 'type' => 'inner']],
            'fields' => 'ae.id, ae.estate_id as forid, en.name as forname, en.city as region_no',
            'page_size' => 20,
        ];
        $res = (new Agent())->getListByRelation($params);

        if(empty($res['code']) || 1 != $res['code']) {
            $this->error($res);
        }

        $result = $res['result'];

        $this->success($result);
    }

    /**
     * 增加
     */
    public function addEstate()
    {
        $param = $this->request->param();

        if(empty($param['agent_id']) || empty($param['forid'])) {
            $this->error('缺少必要参数');
        }

        $data = [
            'agent_id' => $param['agent_id'],
            'estate_id' => $param['forid'],
        ];

        $res = (new Agent())->addRelation($data);

        if(empty($res['code']) || 1 != $res['code']) {
            $this->error($res);
        }

        $result = $res['result'];

        $this->success($result);
    }

    /**
     * 删除
     */
    public function delEstate()
    {
        $param = $this->request->param();

        if(empty($param['id'])) {
            $this->error('缺少必要参数');
        }

        $res = (new Agent())->delRelation($param['id']);

        if(empty($res['code']) || 1 != $res['code']) {
            $this->error($res);
        }

        $result = $res['result'];

        $this->success($result);
    }

    //==============淘房师-楼盘 关联 end===================//

}

