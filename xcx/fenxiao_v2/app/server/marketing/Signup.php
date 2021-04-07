<?php

namespace app\server\marketing;

use app\common\base\ServerBase;
use app\server\user\ShortMessage;
use think\Exception;

class Signup extends ServerBase
{
    //显示所有
    public function getList($search = [], $field = 's.*,es.name as forname', $pageSize = 50)
    {
        $where = [];

        if (!empty($search['name'])) {
            $where[] = ['s.name', 'like', '%' . $search['name'] . '%'];
        }
        $order = ['s.id' => 'desc'];

        if (!empty($search['start_time'])) {
            $where[] = ['s.start_time', '>=', $search['start_time']];
        }
        if (!empty($search['end_time'])) {
            $where[] = ['s.end_time', '<=', $search['end_time']];
        }

        if (!empty($search['region_no'])) {
            if (is_array($search['region_no'])) {
                $where[] = ['s.region_no', 'in', $search['region_no']];
            } else {
                $where[] = ['s.region_no', '=', $search['region_no']];
            }
        }

        $list = $this->db->name("signup")->alias('s')->join('estates_new es', 'es.id=s.forid')->field($field)->where($where)->order($order)->paginate($pageSize);
        if ($list->isEmpty()) {
            $result['list'] = [];
        } else {
            $result['total'] = $list->total();
            $result['last_page'] = $list->lastPage();
            $result['current_page'] = $list->currentPage();
            $result['list'] = $list->items();
        }
        return $this->responseOk($result);
    }

    //添加操作
    public function add($data)
    {
        try {

            $id = $this->db->name("signup")->insertGetId([
                'name'        => $data['name'],
                'subname'     => $data['subname'],
                'desc'        => $data["desc"],
                'forid'       => intval($data["forid"]),
                'start_time'  => $data["start_time"],
                'end_time'    => $data["end_time"],
                'region_no'   => $data['region_no'],
                "share_title" => $data["share_title"],
                "share_desc"  => $data["share_desc"],
                'share_img'   => $data["share_img"],
                'join_num'    => 0,
                'update_time' => 0,
                'create_time' => time()
            ]);   //将数据存入并返回自增 ID
            if (empty($id)) {
                throw new Exception('操作失败');
            }
            return $this->responseOk($id);
        } catch (Exception $e) {
            return $this->responseFail(['code' => 0, 'msg' => $e->getMessage()]);
        }
    }

    //修改状态
    public function edit($id, $data)
    {
        try {
            $id = intval($id);
            if (empty($id)) {
                throw new Exception('缺少设置参数');
            }
            unset($data['id']);//不可变更id

            $data['update_time'] = time();
            $rs = $this->db->name('signup')->where(['id' => $id])->update($data);
            if (empty($rs)) {
                throw new Exception('操作失败');
            }

            return $this->responseOk();
        } catch (Exception $e) {
            return $this->responseFail(['code' => 0, 'msg' => $e->getMessage()]);
        }
    }


    public function del($id)
    {
        try {
            $res = $this->db->name("signup")->where("id", $id)->delete();
            if ($res) {

                return $this->responseOk($res);
            } else {
                throw new Exception('操作失败');
            }

        } catch (Exception $e) {
            return $this->responseFail(['code' => 0, 'msg' => $e->getMessage()]);
        }
    }


    //==================== 报名记录操作 start ====================//
    public function getLogList($search = [], $field = 'sl.*,u.nickname,u.headimgurl', $pageSize = 50)
    {
        $where = [];
        if($search['type'] == 1){
            if (!empty($search['signup_id'])) {
                $where[] = ['sl.signup_id', '=', $search['signup_id']];
            }
        }else{
            if (!empty($search['signup_id'])) {
                $where[] = ['sl.active_id', '=', $search['signup_id']];
            }
        }

        if (!empty($search['user_id'])) {
            $where[] = ['sl.user_id', '=', $search['user_id']];
        }
        if (!empty($search['region_no'])) {
            $where[] = ['sl.region_no', '=', $search['region_no']];
        }

        if (!empty($search['startdate'])) {
            $where[] = ['sl.create_time', '>=', strtotime($search['startdate'])];
        }
        if (!empty($search['enddate'])) {
            $where[] = ['sl.create_time', '<', strtotime($search['enddate'] . ' +1 day')];
        }

        if (!empty($search['nickname'])) {
            $where[] = ['u.nickname', 'like', '%' . $search['nickname'] . '%'];
        }
//        if (!empty($search['type'])) {
//            $where[] = ['sl.type', '=', $search['type']];
//        }

        $order = ['sl.id' => 'desc'];

        $result = array(
            'list'         => [],
            'total'        => 0,
            'last_page'    => 0,
            'current_page' => 0
        );

        if(!empty($search['do_export'])){
            $list = $this->db->name("signup_log")
                ->alias('sl')
                ->field('sl.id,sl.forname,u.nickname,sl.phone,sl.create_time')
                ->join('user u', 'u.id=sl.user_id', 'LEFT')
                ->where($where)
                ->order($order)
                ->select();
        }else{
            $list = $this->db->name("signup_log")
                ->alias('sl')->field($field)
                ->join('user u', 'u.id=sl.user_id', 'LEFT')
                ->where($where)
                ->order($order)
                ->paginate($pageSize);
        }


        if ($list->isEmpty()) {
            $result['list'] = [];
        } else {
            $list = $list->toArray();
            if(!empty($search['do_export'])){
                foreach ($list as $key => &$value){
                    $value['create_time'] = date('Y-m-d H:i',$value['create_time']);
                }

                $result['list'] = $list;
            }else{
                $result['total'] = $list['total'];
                $result['last_page'] = $list['last_page'];
                $result['current_page'] = $list['current_page'];

                $result['list'] = $list['data'];
            }

        }

        return $this->responseOk($result);
    }

    //用户添加报名记录
    public function addLog($data)
    {
        $data['signup_id'] = intval($data['signup_id']);
        if (empty($data['signup_id'])) {
            return $this->responseFail('参数错误');
        }
        // 报名设置信息是否正确
        $where = [
            ['s.id', '=', $data['signup_id']],
        ];
        $field = 's.forid, s.region_no, s.is_check_code, en.name as forname';
        $signUp = $this->db->name('signup')->alias('s')
            ->join('estates_new en', 's.forid=en.id')
            ->field($field)->where($where)->find();

        if (empty($signUp)) {
            return $this->responseFail('抱歉，无效报名活动');
        }

        $hasData = $this->db->name("signup_log")->alias('s')->where(
            [
                ['s.phone', '=', $data['phone']]
            ]
        )->join('signup_log s2', 's2.phone=s.phone AND s2.signup_id=' . $data['signup_id'], 'LEFT')
//            ->limit(0, 2)
            ->order('s.type','asc')
            ->field('s.phone,s.signup_id,s.type')->select()->toArray();

        // 此次报名记录已有手机号数据
        if (!empty($hasData)) {
            $has = 0;
            if ($hasData[0]['type'] == 1 || $hasData[1]['type'] == 1) {
                $has = 1;
            }
            $hasData = array_column($hasData, 'phone', 'signup_id');
            // 已报名过
            $signup_ids = array_keys($hasData);

            if (in_array($data['signup_id'], $signup_ids) && $has == 1) {
//                return $this->responseFail('抱歉，您已经报名过');
                return $this->responseFail('报名成功', [], 3);
            }
            unset($signup_ids);
        }

        if ($data['browser_cache'] == 1) {//前端标识是否是以默认浏览器缓存来的号码,1为浏览器缓存不进行验证码校验
            $mobiles = array_values($hasData);
            if (empty($mobiles) || !in_array($data['phone'], $mobiles)) {
                return $this->responseFail('抱歉，手机验证有误请重新验证', [], 2);
            }
        } else {
            if (!empty($signUp['is_check_code'])) {// 手机号之前没有数据 // 后台是否开启验证码校验且非前端浏览器缓存
                //进行验证码校验
                $msgServer = new ShortMessage();
                if (!$msgServer->checkCode([
                    'mobile' => $data['phone'],
                    'code'   => $data['code'],
                    'sence'  => 'sign_up'
                ])) {
                    return $this->responseFail('请输入正确的验证码');
                }
            }
        }

        try {
            $now_time = time();
            $this->db->startTrans();
            $id = $this->db->name("signup_log")->insertGetId([
                'active_id'   => $data['active_id'],
                'signup_id'   => $data['signup_id'],
                'forid'       => $signUp['forid'],
                'forname'     => $signUp['forname'],
                'module'      => $data['module'],
                'region_no'   => $signUp['region_no'],
                'user_id'     => $data['user_id'],
                'create_time' => $now_time,
                'name'        => $data['name'],
                'phone'       => $data['phone'],
                'source'      => $data['source'] == 'wx_h5' ? 'wx_h5' : 'douyin'
            ]);   //将数据存入并返回自增 ID
            if (empty($id)) {
                $this->db->name('log')->insert([
                    'content' => $this->db->getLastSql(),
                    'source'  => 'signup_1',
                    'created_at' => time()
                ]);
                throw new Exception('操作失败');
            }

            //参与人数 +1
            $rs = $this->db->name('signup')->where(['id' => $data['signup_id']])->inc('join_num')->update();
            if (empty($rs)) {
                $this->db->name('log')->insert([
                    'content' => $this->db->getLastSql(),
                    'source'  => 'signup_2',
                    'created_at' => time()
                ]);
                throw new Exception('操作失败');
            }

            //防止短时间内多点
            $count = $this->db->name("signup_log")->where([
                'phone'     => $data['phone'],
                'signup_id' => $data['signup_id'],
                'type'      => 1
            ])->count();
            if ($count > 1) {
                $this->db->name('log')->insert([
                    'content' => $this->db->getLastSql(),
                    'source'  => 'signup_3',
                    'created_at' => time()
                ]);
                throw new Exception('操作失败');
            }

            $this->db->commit();
            return $this->responseOk($id);
        } catch (Exception $e) {
            $this->db->rollback();
            $this->db->name('log')->insert([
                'content' => $e->getMessage(),
                'source'  => 'signup_7',
                'created_at' => time()
            ]);
            return $this->responseFail(['code' => 0, 'msg' => $e->getMessage()]);
        }
    }
    //==================== 报名记录操作 end ====================//

    //优惠活动报名
    public function discountRegistration($data)
    {
        $data['signup_id'] = intval($data['signup_id']);
        if (empty($data['signup_id'])) {
            return $this->responseFail('参数错误');
        }
        // 报名设置信息是否正确
        $where = [
            ['id', '=', $data['signup_id']],
        ];
        $signUp = $this->db->name('subject')->where($where)->field('sign_up,region_no')->find();

        if (empty($signUp)) {
            return $this->responseFail('抱歉，无效报名活动');
        }
        if ($signUp['sign_up'] != 1) {
            return $this->responseFail('抱歉，活动报名未开启');
        }

        $hasData = $this->db->name('signup_log')->where(
            [
                ['signup_id', '=', $data['signup_id']],
                ['phone', '=', $data['phone']],
                ['type', '=', 2]
            ]
        )->field('phone,signup_id')->find();


        // 此次报名记录已有手机号数据
        if (!empty($hasData)) {
//            return $this->responseFail('抱歉，您已经报名过');
            return $this->responseFail('报名成功', [], 3);
        }

        if ($data['browser_cache'] != 1) {//前端标识是否是以默认浏览器缓存来的号码,1为浏览器缓存不进行验证码校验
            //进行验证码校验
            $msgServer = new ShortMessage();
            if (!$msgServer->checkCode([
                'mobile' => $data['phone'],
                'code'   => $data['code'],
                'sence'  => 'sign_up'
            ])) {
                return $this->responseFail('请输入正确的验证码');
            }
        }

        try {
            $now_time = time();
            $this->db->startTrans();
            $id = $this->db->name("signup_log")->insertGetId([
                'signup_id'   => $data['signup_id'],
                'active_id'   => $data['active_id'],
                'module'      => $data['module'],
                'region_no'   => $signUp['region_no'],
                'user_id'     => $data['user_id'],
                'create_time' => $now_time,
                'name'        => $data['name'], //真实姓名
                'phone'       => $data['phone'], //手机号码
                'type'        => 2,
                'source'      => $data['source'] == 'wx_h5' ? 'wx_h5' : 'douyin'
            ]);   //将数据存入并返回自增 ID
            if (empty($id)) {
                $this->db->name('log')->insert([
                    'content' => $this->db->getLastSql(),
                    'source'  => 'signup_4',
                ]);
                throw new Exception('操作失败');
            }

            //防止短时间内多点
            $count = $this->db->name("signup_log")->where([
                'phone'     => $data['phone'],
                'signup_id' => $data['signup_id'],
                'type'      => 2
            ])->count();


            if ($count > 1) {
                $this->db->name('log')->insert([
                    'content' => $this->db->getLastSql(),
                    'source'  => 'signup_5',
                ]);
                throw new Exception('操作失败');
            }
            $this->db->commit();
            return $this->responseOk($id);
        } catch (Exception $e) {
            $this->db->rollback();
            $this->db->name('log')->insert([
                'content' => $this->db->getLastSql(),
                'source'  => 'signup_6',
            ]);
            return $this->responseFail(['code' => 0, 'msg' => $e->getMessage()]);
        }
    }


}