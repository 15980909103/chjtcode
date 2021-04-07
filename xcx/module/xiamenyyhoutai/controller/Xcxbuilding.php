<?php

/**
 * Created by PhpStorm.
 * User: USER022
 * Date: 2019/1/3
 * Time: 14:48
 */
include 'AdminController.php';
include System . DS . 'Upload.php';
include System . DS . 'Encryption.php';

class Xcxbuilding extends AdminController
{
    /*============================================= 经纪人楼盘管理 =====================================================*/
    public function agent_building_index()
    {
        return $this->render('agent_building_index');
    }

    /**
     * 佣金变化页面
     * @return false|string
     */
    public function commission_change_index()
    {
        return $this->render('commission_change_index');
    }

    /**
     * 经纪人分享记录页面
     * @return false|string
     */
    public function agent_share_record_index()
    {
        $agentArr = $this->db->Name('xcx_agent_share AS xas')
            ->leftJoin('xcx_agent_user', 'au', 'au.id=xas.agent_id')
            ->select('DISTINCT au.id,au.nickname,au.name')
            ->execute();
        foreach ($agentArr as &$val) {
            $val['nickname'] = $val['name'] ? $val['name'] : $val['nickname'];
        }
        $data['agentArr'] = $agentArr;
        return $this->render('agent_share_record_index', $data);
    }

    /**
     * 经纪人分享记录数据渲染
     */
    public function agent_share_record_page()
    {
        $curr = Context::Post('curr');
        $limit = Context::Post('limit');
        $where['agent_id'] = Context::Post('agent_id');
        $where['share_type'] = Context::Post('share_type');
        $where['client_type'] = Context::Post('client_type');
        $select = 'xas.id,au.nickname AS aunickname,au.name AS auname,au.headimgurl,aa.cover,aa.title,bb.name AS bbname,
                   bb.pic,xas.share_type,xas.client_type,xas.create_time,xas.user_id';
        $where = array_filter($where, function ($val) {
            $tmp = $val === '';
            return !$tmp;
        });
        if (empty($where)) {
            $record_arr = $this->agent_share_record_db()->select($select)->page($curr, $limit)->execute();
            $count = $this->agent_share_record_db()->select('COUNT(*)')->firstColumn();
        } else {
            $record_arr = $this->set_where($where, $this->agent_share_record_db())->select($select)
                ->page($curr, $limit)->execute();
            $count = $this->set_where($where, $this->agent_share_record_db())->select('COUNT(*)')->firstColumn();
        }

        if (!empty($record_arr)) {
            $user_id = [];
            $agent_id = [];
            foreach ($record_arr as $val) {
                //判断小程序用户端
                if ($val['client_type'] == 1) {
                    $user_id[] = $val['user_id'];
                }
                //判断公众号经纪人端
                if ($val['client_type'] == 2) {
                    $agent_id[] = $val['user_id'];
                }
            }
            $user_id = array_unique($user_id);
            $agent_id = array_unique($agent_id);
            //通过ID获取小程序用户
            $userDataArr = [];
            $agentDataArr = [];
            if (!empty($user_id)) {
                $userDataArr = $this->db->Name('xcx_user')
                    ->where_in('id', $user_id)
                    ->select('id,nickName')->execute();
            }
            //通过ID获取公众号经纪人
            if (!empty($agent_id)) {
                $agentDataArr = $this->db->Name('xcx_agent_user')
                    ->where_in('id', $agent_id)
                    ->select('id,nickname,name')->execute();
            }
            //将小程序用户昵称添加到分享数组
            foreach ($userDataArr as $value1) {
                foreach ($record_arr as &$value2) {
                    if ($value1['id'] == $value2['user_id'] && $value2['client_type'] == 1) {
                        $value2['username'] = $value1['nickName'];
                    }
                }
            }
            //将公众号经纪人姓名昵称添加到分享数组
            foreach ($agentDataArr as $value1) {
                foreach ($record_arr as &$value3) {
                    if ($value1['id'] == $value3['user_id'] && $value3['client_type'] == 2) {
                        $value3['username'] = $value1['name'] ? $value1['name'] : $value1['nickname'];
                    }
                }
            }
            foreach ($record_arr as &$val) {
                $val['auname'] = $val['auname'] ? $val['auname'] : $val['aunickname'];
                $val['share_type'] = $val['share_type'] == 1 ? "名片" : ($val['share_type'] == 2 ? "文章" : "楼盘");
                $val['client_type'] = $val['client_type'] == 1 ? "小程序客户端" : "公众号经纪人端";
                $val['create_time'] = date('Y-m-s H:i:s', $val['create_time']);
                $val['title'] = $val['title'] ? $val['title'] : "";
                $val['bbname'] = $val['bbname'] ? $val['bbname'] : "";
            }
            echo json_encode(['success' => true, 'data' => $record_arr, 'count' => $count]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    /**
     * 经纪人分享记录 DB对象
     * @return mixed
     */
    private function agent_share_record_db()
    {
        $db = $this->db->Name('xcx_agent_share AS xas')->leftJoin('xcx_agent_user', 'au', 'au.id=xas.agent_id')
            ->leftJoin('xcx_article_article', 'aa', 'aa.id=xas.article_id')
            ->leftJoin('xcx_building_building', 'bb', 'bb.id=xas.building_id')
            ->orderBy('xas.id', 'DESC');
        return $db;
    }

    /**
     * 经纪人楼盘管理数据渲染
     */
    public function agent_building_page()
    {
        $curr = Context::Post('curr');
        $limit = Context::Post('limit');
        $select['au.name'] = trim(Context::Post('agentName'));
        $select['bb.name'] = trim(Context::Post('buildingName'));

        $select = array_filter($select, function ($val) {
            $tmp = $val === '';
            return !$tmp;
        });

        // 非超级管理员需对区域做判断
//        if (!empty($this->gid)) {
//            if (empty($this->city)) {
//                echo json_encode(['success' => false, 'message' => '区域权限有误']);
//                exit();
//            }
//            $select['bb.city'] = $this->city;
//        }
        $db = $this->db->Name('xcx_agent_building AS ab');
        //条件+分页
        if (!empty($select)) {
            $db->leftJoin('xcx_agent_user', 'au', 'au.id = ab.agent_id')
                ->leftJoin('xcx_building_building', 'bb', 'bb.id=ab.building_id')
                ->select('ab.id,ab.agent_id,ab.building_id,ab.is_focus,
                                 ab.status,ab.create_time,ab.update_time,au.nickname,
                                 au.headimgurl,au.name AS username,bb.name,bb.sales_status')
                ->orderBy('ab.create_time', 'DESC')
                ->page($curr, $limit);
            $db = $this->set_dict_where($select, $db);
            $data = $db->execute();
            //记录数
            $db->leftJoin('xcx_agent_user', 'au', 'au.id = ab.agent_id')
                ->leftJoin('xcx_building_building', 'bb', 'bb.id=ab.building_id')
                ->select('COUNT(*)');
            $db = $this->set_dict_where($select, $db);
            $count = $db->firstColumn();
        } else {//分页
            $data = $db->leftJoin('xcx_agent_user', 'au', 'au.id = ab.agent_id')
                ->leftJoin('xcx_building_building', 'bb', 'bb.id=ab.building_id')
                ->select('ab.id,ab.agent_id,ab.building_id,ab.is_focus,ab.status,
                                 ab.create_time,ab.update_time,au.nickname,au.headimgurl,
                                 au.name AS username,bb.name,bb.sales_status')
                ->orderBy('ab.create_time', 'DESC')
                ->page($curr, $limit)
                ->execute();
            //总记录
            $count = $this->db->Name('xcx_agent_building')->select('COUNT(*)')->firstColumn();
        }
        if (!empty($data)) {
            foreach ($data as &$val) {
                $val['create_time'] = date('Y-m-d H:i:s', $val['create_time']);
                $val['update_time'] = date('Y-m-d H:i:s', $val['update_time']);
                $val['username'] = empty($val['username']) ? $val['nickname'] : $val['username'];
                $val['is_focus'] = $val['is_focus'] ? "是" : "否";
            }
            echo json_encode(['success' => true, 'data' => $data, 'count' => $count]);
        } else {
            echo json_encode(['success' => false, 'curr' => $curr]);
        }
    }

    /**
     * 经纪人楼盘管理状态修改
     */
    public function agent_building_status()
    {
        $id = Context::Post('id');
        $status = Context::Post('status');

//        if (!empty($this->gid)) {
//            if (empty($this->city)) {
//                echo json_encode(['success' => false, 'message' => '无该城市权限']);
//                exit();
//            }
//            // 查询楼盘城市是否匹配
//            $abRes = $this->db->Name('xcx_agent_building')
//                ->select('ab.id', 'ab')
//                ->leftJoin('xcx_building_building', 'bb', 'ab.building_id=bb.id')
//                ->where_equalTo('ab.id', $id)
//                ->where_like('bb.city', "%{$this->city}%")
//                ->firstRow();
//            if (empty($abRes)) {
//                echo json_encode(['success' => false, 'message' => '您无该记录权限']);
//                exit();
//            }
//        }

        $res = $this->db->Name('xcx_agent_building')->update(['status' => $status])
            ->where_equalTo('id', $id)->execute();
        if ($res)
            echo json_encode(['success' => true]);
        else
            echo json_encode(['success' => false]);
    }

    /**
     * 经纪人楼盘管理删除
     */
    public function agent_building_del()
    {
        $id = Context::Post('id');

//        if (!empty($this->gid)) {
//            if (empty($this->city)) {
//                echo json_encode(['success' => false, 'message' => '无该城市权限']);
//                exit();
//            }
//            // 查询楼盘城市是否匹配
//            $abRes = $this->db->Name('xcx_agent_building')
//                ->select('ab.id', 'ab')
//                ->leftJoin('xcx_building_building', 'bb', 'ab.building_id=bb.id')
//                ->where_equalTo('ab.id', $id)
//                ->where_like('bb.city', "%{$this->city}%")
//                ->firstRow();
//            if (empty($abRes)) {
//                echo json_encode(['success' => false, 'message' => '您无该记录权限']);
//                exit();
//            }
//        }

        $db = $this->db->Name('xcx_agent_building');
        $result = $db->delete()->where_equalTo('id', $id)->execute();
        if ($result) {
            echo json_encode(['success' => true]);
            return;
        }
        echo json_encode(['success' => false, 'message' => '删除失败']);
    }

    /**
     * 经纪人楼盘添加页面
     * @return false|string
     */
    public function agent_building_add()
    {
        $agentUserArr = $this->db->Name('xcx_agent_user')->select('id,nickname,name')->execute();
        $buildingDb = $this->db->Name('xcx_building_building')->select('id,name');

        // 非超级管理员要核对区域
//        if (!empty($this->gid)) {
//            if (empty($this->city)) {
//                $buildingDb->where_equalTo('id', 0);// 没有所属区域，权限不足，查空数据
//            } else {
//                $buildingDb->where_like('city', "%{$this->city}%");
//            }
//        }

        $buildingArr = $buildingDb->execute();
        foreach ($agentUserArr as &$data) {
            $data['username'] = empty($data['name']) ? $data['nickname'] : $data['name'];
        }
        $arr = ['agentUserArr' => $agentUserArr, 'buildingArr' => $buildingArr];
        return $this->render('agent_building_add', $arr);
    }

    /**
     * 经纪人楼盘添加
     */
    public function agent_building_doadd()
    {
        $data['agent_id'] = Context::Post('agent_id');
        $data['building_id'] = Context::Post('building_id');
        $data['create_time'] = time();
        $data['update_time'] = time();

        // 经纪人是否已开通楼盘
        $abRes = $this->db->Name('xcx_agent_building')
            ->select('id')
            ->where_equalTo('agent_id', $data['agent_id'])
            ->where_equalTo('building_id', $data['building_id'])
            ->firstRow();
        if (!empty($abRes)) {
            echo json_encode(['success' => false, 'message' => '经纪人已开通该楼盘']);
            exit();
        }

        // 非超级管理员是否有该城市权限
//        if (!empty($this->gid)) {
//            $bbDb = $this->db->Name('xcx_building_building')->select('id')->where_equalTo('id', $data['building_id']);
//            if (empty($this->city)) {
//                $bbDb->where_equalTo('id', 0);// 没有城市权限，用该条件使得结果为空
//            } else {
//                $bbDb->where_like('city', "%{$this->city}%");
//            }
//        }
//        $bbRes = $bbDb->firstRow();
//        if (empty($bbRes)) {
//            echo json_encode(['success' => false, 'message' => '无该城市权限']);
//            exit();
//        }

        $result = $this->db->Name('xcx_agent_building')->insert($data)->execute();
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => '保存失败']);
        }
    }

    /**
     * 经纪人楼盘报备页面
     */
    public function building_report_index()
    {
        //渠道部门
        if($this->gid == 5 || $this->gid == 8){
            $channeid = $this->db->Name('admin')->where_equalTo('id',$_SESSION['aid'])->select('channel_id')->firstRow();
            $type = $this->db->Name('xcx_store_agent')->where_equalTo('said',$channeid['channel_id'])->select('type,mgid')->firstRow();
            if($type['type'] == 5){
                $aid = $_SESSION['aid'];
            }elseif($type['type'] == 6){
                $mgid = explode(',',$type['mgid']);
                $saidArray = $this->db->Name('xcx_store_agent')->where_in('mgid',$mgid)->where_equalTo('type',5)->select('said')->execute();
                $channelId = array_column($saidArray,'said');
                $adminId = $this->db->Name('admin')->where_in('channel_id',$channelId)->select('id')->execute();
                $aid = array_column($adminId,'id');
            }

            if($this->gid == 5){
                if(empty($aid)){
                    echo json_encode(['success'=>true,'data'=>[],'count'=>0]);
                    exit();
                }
            }
            $userName = $this->reported_db()
                ->leftJoin('xcx_store_agent', 'sa', 'sa.agent_id=au.id')
                ->leftJoin('xcx_store_store', 'ss', 'sa.store_id=ss.id');
            if(is_array($aid)){
                $userName = $userName->where_in('ss.aid',$aid);
            }else{
                $userName = $userName->where_equalTo('ss.aid',$aid);
            }
            $userName = $userName->select('DISTINCT br.user_name,br.id')->execute();

            $agen = $this->reported_db()
                ->leftJoin('xcx_store_agent', 'sa', 'sa.agent_id=au.id')
                ->leftJoin('xcx_store_store', 'ss', 'sa.store_id=ss.id');
            if(is_array($aid)){
                $agen = $agen->where_in('ss.aid',$aid);
            }else{
                $agen = $agen->where_equalTo('ss.aid',$aid);
            }

            $agen = $agen->select('DISTINCT br.user_name,br.id')->execute();

            $building = $this->reported_db()
                ->leftJoin('xcx_store_agent', 'sa', 'sa.agent_id=au.id')
                ->leftJoin('xcx_store_store', 'ss', 'sa.store_id=ss.id');

            if(is_array($aid)){
                $building = $building->where_in('ss.aid',$aid);
            }else{
                $building = $building->where_equalTo('ss.aid',$aid);
            }
            $building = $building->select('DISTINCT br.user_name,br.id')->execute();

        }elseif ($this->gid == 7){
            $userName = $this->reported_db()->where_equalTo('bb.aid',$_SESSION['aid'])->select('DISTINCT br.user_name,br.id')->execute();
            $agen = $this->reported_db()->where_equalTo('bb.aid',$_SESSION['aid'])->select('DISTINCT au.nickname,au.name AS auname,au.id')->execute();
            $building = $this->reported_db()->where_equalTo('bb.aid',$_SESSION['aid'])->select('DISTINCT bb.name AS bbname,bb.id')->execute();
        }
        else{
            $userName = $this->reported_db()->select('DISTINCT br.user_name,br.id')->execute();
            $agen = $this->reported_db()->select('DISTINCT au.nickname,au.name AS auname,au.id')->execute();
            $building = $this->reported_db()->select('DISTINCT bb.name AS bbname,bb.id')->execute();
        }


        foreach ($agen as &$val) {
            $val['auname'] = $val['auname'] ? $val['auname'] : $val['nickname'];
        }
        $data['agen'] = $agen;
        $data['username'] = $userName;
        $data['building'] = $building;
        return $this->render('building_report_index', $data);
    }

    /**
     * 经纪人楼盘结佣页面
     */
    public function building_settlement_index()
    {
        $userName = $this->reported_db()->select('DISTINCT br.user_name,br.id')->execute();
        $agen = $this->reported_db()->select('DISTINCT au.nickname,au.name AS auname,au.id')->execute();
        $building = $this->reported_db()->select('DISTINCT bb.name AS bbname,bb.id')->execute();
        foreach ($agen as &$val) {
            $val['auname'] = $val['auname'] ? $val['auname'] : $val['nickname'];
        }
        $data['agen'] = $agen;
        $data['username'] = $userName;
        $data['building'] = $building;
        return $this->render('building_settlement_index', $data);
    }

    /**
     * 经纪人楼盘结佣页面数据渲染
     */
    public function building_settlement_page()
    {
        //$where['br.status_type']     = Context::Post('status_type');
        $where['br.status_type'] = 6;
        $where['br.building_id'] = Context::Post('building_id');
        $where['br.agent_id'] = Context::Post('agent_id');
        $where['br.user_gender'] = Context::Post('user_gender');
        $where['br.user_name'] = Context::Post('user_name');
        $where['brc.admin_is_read'] = Context::Post('admin_is_read');
        //判断是否传入读取状态
        if ($where['brc.admin_is_read'] == '0' || $where['brc.admin_is_read'] == '1') {
            //经纪人发给后台
            $where['brc.send_from'] = '1';
        }
        $limit = Context::Post('limit');
        $curr = Context::Post('curr');
        $where = array_filter($where, function ($val) {
            $tmp = $val === '';
            return !$tmp;
        });
        $select = 'DISTINCT br.id,br.user_name,br.user_phone,au.nickname AS aunickname,au.name AS auname,br.user_gender,
                   bb.name AS bbname,bb.pic,br.take_time,br.status_type,br.examine_type,br.commission,br.describe,br.create_time,br.update_time';
        //判断该管理员所能查看的报备楼盘信息
        $adminRow = $this->db->Name('admin')->select()->where_equalTo('id', $_SESSION['aid'])->firstRow();
        if ($_SESSION['gid'] == '0') {  //超级管理员
            if (empty($where)) {
                $managementArr = $this->reported_db()
                    ->select($select)
                    ->orderBy('br.id', 'DESC')
                    ->page($curr, $limit)
                    ->execute();
                $count = $this->reported_db()
                    ->select('COUNT(DISTINCT br.id)')
                    ->firstColumn();
            } else {
                $managementArr = $this->set_where($where, $this->reported_db())
                    ->select($select)
                    ->page($curr, $limit)
                    ->orderBy('br.id', 'DESC')
                    ->execute();
                $count = $this->set_where($where, $this->reported_db())
                    ->select('COUNT(DISTINCT br.id)')
                    ->firstColumn();
            }
        } else {
            $reported_building_ids = $adminRow['reported_building_ids'];
            $buildingIds = [];
            if (empty($reported_building_ids) && $reported_building_ids !== '0') {
//                $buildingIds[]=0;
            } else {
                if ($reported_building_ids === '0') {
                    $buildingIds = [];
                } else {
                    $reported_building_ids = explode(',', $reported_building_ids);
                    $buildingIds = $reported_building_ids;
                }
            }
            if (empty($where)) {
                if (empty($buildingIds)) {
                    $managementArr = $this->reported_db()
                        ->select($select)
                        ->orderBy('br.id', 'DESC')
                        ->page($curr, $limit)
                        ->execute();
                    $count = $this->reported_db()
                        ->select('COUNT(DISTINCT br.id)')
                        ->firstColumn();
                } else {
                    $managementArr = $this->reported_db()
                        ->where_in('bb.id', $buildingIds)
                        ->select($select)
                        ->orderBy('br.id', 'DESC')
                        ->page($curr, $limit)
                        ->execute();
                    $count = $this->reported_db()
                        ->where_in('bb.id', $buildingIds)
                        ->select('COUNT(DISTINCT br.id)')
                        ->firstColumn();
                }
            } else {
                if (empty($buildingIds)) {
                    $managementArr = $this->set_where($where, $this->reported_db())
                        ->select($select)
                        ->page($curr, $limit)
                        ->orderBy('br.id', 'DESC')
                        ->execute();
                    $count = $this->set_where($where, $this->reported_db())
                        ->select('COUNT(DISTINCT br.id)')
                        ->firstColumn();
                } else {
                    $managementArr = $this->set_where($where, $this->reported_db())
                        ->where_in('bb.id', $buildingIds)
                        ->select($select)
                        ->page($curr, $limit)
                        ->orderBy('br.id', 'DESC')
                        ->execute();
                    $count = $this->set_where($where, $this->reported_db())
                        ->where_in('bb.id', $buildingIds)
                        ->select('COUNT(DISTINCT br.id)')
                        ->firstColumn();
                }
            }
        }
        foreach ($managementArr as &$val) {
            $val['auname'] = $val['auname'] ? $val['auname'] : $val['aunickname'];
            $val['describe'] = $val['describe'] ? $val['describe'] : "";
            $val['commission'] = $val['commission'] ? $val['commission'] : "";
            $val['take_time'] = date('Y-m-d H:i:s', $val['take_time']);
            $val['create_time'] = date('Y-m-d H:i:s', $val['create_time']);
            $val['update_time'] = date('Y-m-d H:i:s', $val['update_time']);
            $val['user_gender'] = $val['user_gender'] == 1 ? '男' : '女';
            $val['status_type_name'] = $this->examine_type_toString($val['examine_type']);
        }
        if (empty($managementArr)) {
            echo json_encode(['success' => false]);
        } else {
            echo json_encode(['success' => true, 'data' => $managementArr, 'count' => $count]);
        }
    }

    public function settlement_edit()
    {
        $id = Context::Get('id');
        $data['data'] = $this->db->Name('xcx_building_reported')->select('id,commission')->where_equalTo('id', $id)->firstRow();
        return $this->render('settlement_edit', $data);
    }

    /**
     * 经纪人结佣
     */
    public function set_settlement()
    {
        $pdo = new DataBase();
        $pdo->beginTransaction(); // 开启一个事务
        try {

            $id = Context::Post('id');
            $examine_type = Context::Post('examine_type');

            $row = $this->db->Name('xcx_building_reported')->select()->where_equalTo('id', $id)->firstRow();
            $inform_content = trim(Context::Post('inform_content'));

            $json_data = $row['json_data'];
            $json_data_arr = json_decode($json_data, true);
            $json_data_arr[] = ['time' => time(), 'examine_type' => $examine_type, 'content' => $inform_content];
            $json_data = json_encode($json_data_arr);

            // 非超级管理员检查区域
//            if (!empty($this->gid)) {
//                $bbDb = $this->db->Name('xcx_building_building')->select()->where_equalTo('id', $row['building_id']);
//                if (empty($this->city)) {
//                    $bbDb->where_equalTo('id', 0);
//                } else {
//                    $bbDb->where_like('city', "%{$this->city}%");
//                }
//                $bbRes = $bbDb->firstRow();
//                if (empty($bbRes)) {
//                    echo json_encode(['success' => false, 'message' => '保存失败']);
//                    exit();
//                }
//            }

            $result = $this->db->Name('xcx_building_reported')->update(['examine_type' => $examine_type, 'update_time' => time(), 'is_admin' => 1, 'admin_id' => $_SESSION['aid'], 'json_data' => $json_data])
                ->where_equalTo('id', $id)->execute();
            if (empty($result)) {
                throw new PDOException('修改报备状态失败请重试');
            }

            // 插入报备日志
            $logInsert = [
                'said'         => $row['said'],
                'agent_id'     => $row['agent_id'],
                'examine_said' => $row['examine_said'],
                'examine_aid'  => $row['examine_aid'],
                'report_id'    => $row['id'],
                'admin_id'     => $_SESSION['aid'],
                'is_admin'     => 1,
                'agent_type'   => 2,
                'status_type'  => 6,
                'examine_type' => $examine_type,
                'content'      => $inform_content,
                'created_at'   => time(),
                'updated_at'   => time(),
            ];
            $resLog = $this->db->Name('xcx_reported_log')->insert($logInsert)->execute();
            if (empty($resLog)) {
                throw new PDOException('插入报备日志失败请重试');
            }

            $data['admin_id'] = $_SESSION['aid'];
            $data['real_commission'] = Context::Post('commission_change');
            $data['update_at'] = time();
            $data['status'] = $examine_type;
            $res = $this->db->Name('xcx_reported_settlement')->update($data)->where_equalTo('report_id', $id)->execute();
            if (empty($res)) {
                throw new PDOException('修改佣金日志失败请重试');
            }

            $pdo->commit();
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            $pdo->rollback();
            echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }

    }

    /**
     * 经纪人楼盘报备页面数据渲染
     */
    public function building_report_page()
    {
        $where['br.status_type'] = Context::Post('status_type');
        $where['br.building_id'] = Context::Post('building_id');
        $where['br.agent_id'] = Context::Post('agent_id');
        $where['br.user_gender'] = Context::Post('user_gender');
        $where['br.user_name'] = Context::Post('user_name');
        $where['brc.admin_is_read'] = Context::Post('admin_is_read');
        //判断是否传入读取状态
        if ($where['brc.admin_is_read'] == '0' || $where['brc.admin_is_read'] == '1') {
            //经纪人发给后台
            $where['brc.send_from'] = '1';
        }
        $limit = Context::Post('limit');
        $curr = Context::Post('curr');
        $where = array_filter($where, function ($val) {
            $tmp = $val === '';
            return !$tmp;
        });
        $select = 'DISTINCT br.id,br.user_name,br.user_phone,au.nickname AS aunickname,au.name AS auname,br.user_gender,
                   bb.name AS bbname,bb.pic,br.take_time,br.status_type,br.describe,br.create_time,br.update_time';
        //判断该管理员所能查看的报备楼盘信息
        $adminRow = $this->db->Name('admin')->select()->where_equalTo('id', $_SESSION['aid'])->firstRow();

        if ($this->gid == '0') {  //超级管理员 $_SESSION['gid']
            if (empty($where)) {
                $managementArr = $this->reported_db()
                    ->select($select)
                    ->orderBy('br.id', 'DESC')
                    ->page($curr, $limit)
                    ->execute();
                $count = $this->reported_db()
                    ->select('COUNT(DISTINCT br.id)')
                    ->firstColumn();
            } else {
                $managementArr = $this->set_where($where, $this->reported_db())
                    ->select($select)
                    ->page($curr, $limit)
                    ->orderBy('br.id', 'DESC')
                    ->execute();
                $count = $this->set_where($where, $this->reported_db())
                    ->select('COUNT(DISTINCT br.id)')
                    ->firstColumn();
            }
        } else {
            $reported_building_ids = $adminRow['reported_building_ids'];
            $buildingIds = [];
            if (empty($reported_building_ids) && $reported_building_ids !== '0') {
//                $buildingIds[]=0;
            } else {
                if ($reported_building_ids === '0') {
                    $buildingIds = [];
                } else {
                    $reported_building_ids = explode(',', $reported_building_ids);
                    $buildingIds = $reported_building_ids;
                }
            }

            if (empty($where)) {
                if (empty($buildingIds)) {
                    // $buildingIds 为空时候
                    if($this->gid == 5 || $this->gid == 8){

                        $channeid = $this->db->Name('admin')->where_equalTo('id',$_SESSION['aid'])->select('channel_id')->firstRow();
                        $type = $this->db->Name('xcx_store_agent')->where_equalTo('said',$channeid['channel_id'])->select('type,mgid')->firstRow();
                        if($type['type'] == 5){
                            $aid = $_SESSION['aid'];
                        }elseif($type['type'] == 6){
                            $mgid = explode(',',$type['mgid']);
                            $saidArray = $this->db->Name('xcx_store_agent')->where_in('mgid',$mgid)->where_equalTo('type',5)->select('said')->execute();
                            $channelId = array_column($saidArray,'said');
                            $adminId = $this->db->Name('admin')->where_in('channel_id',$channelId)->select('id')->execute();
                            $aid = array_column($adminId,'id');
                        }

                        if($this->gid == 5 || $this->gid == 8){
                            if(empty($aid)){
                                echo json_encode(['success'=>true,'data'=>[],'count'=>0]);
                                exit();
                            }
                        }
                        $managementArr = $this->reported_db();
                        $managementArr->leftJoin('xcx_store_agent', 'sa', 'sa.agent_id=au.id')
                            ->leftJoin('xcx_store_store', 'ss', 'sa.store_id=ss.id');

                        if(is_array($aid)){
                            $managementArr = $managementArr->where_in('ss.aid',$aid);
                        }else{
                            $managementArr = $managementArr->where_equalTo('ss.aid',$aid);
                        }

                        $managementArr = $managementArr
                            ->select($select)
                            ->orderBy('br.id', 'DESC')
                            ->page($curr, $limit)
                            ->execute();

                        $count = $this->reported_db();

                        $count->leftJoin('xcx_store_agent', 'sa', 'sa.agent_id=au.id')
                            ->leftJoin('xcx_store_store', 'ss', 'sa.store_id=ss.id');
                        if(is_array($aid)){
                            $count = $count->where_in('ss.aid',$aid);
                        }else{
                            $count = $count->where_equalTo('ss.aid',$aid);
                        }
                        $count = $count
                            ->select('COUNT(DISTINCT br.id)')
                            ->firstColumn();


                    }elseif ($this->gid == 7){

                        $managementArr = $this->reported_db()
                            ->where_equalTo('bb.aid',$_SESSION['aid'])
                            ->select($select)
                            ->orderBy('br.id', 'DESC')
                            ->page($curr, $limit)
                            ->execute();
                        $count = $this->reported_db()
                            ->where_equalTo('bb.aid',$_SESSION['aid'])
                            ->select('COUNT(DISTINCT br.id)')
                            ->firstColumn();

                    }else{
                        $managementArr = $this->reported_db()
                            ->select($select)
                            ->orderBy('br.id', 'DESC')
                            ->page($curr, $limit)
                            ->execute();
                        $count = $this->reported_db()
                            ->select('COUNT(DISTINCT br.id)')
                            ->firstColumn();
                    }



                } else {

                    if($this->gid == 5 || $this->gid == 8){
                        $channeid = $this->db->Name('admin')->where_equalTo('id',$_SESSION['aid'])->select('channel_id')->firstRow();
                        $type = $this->db->Name('xcx_store_agent')->where_equalTo('said',$channeid['channel_id'])->select('type,mgid')->firstRow();
                        if($type['type'] == 5){
                            $aid = $_SESSION['aid'];
                        }elseif($type['type'] == 6){
                            $mgid = explode(',',$type['mgid']);
                            $saidArray = $this->db->Name('xcx_store_agent')->where_in('mgid',$mgid)->where_equalTo('type',5)->select('said')->execute();
                            $channelId = array_column($saidArray,'said');
                            $adminId = $this->db->Name('admin')->where_in('channel_id',$channelId)->select('id')->execute();
                            $aid = array_column($adminId,'id');
                        }

                        if($this->gid == 5 || $this->gid == 8){
                            if(empty($aid)){
                                echo json_encode(['success'=>true,'data'=>[],'count'=>0]);
                                exit();
                            }
                        }
                        $managementArr = $this->reported_db();
                        $managementArr->leftJoin('xcx_store_agent', 'sa', 'sa.agent_id=au.id')
                            ->leftJoin('xcx_store_store', 'ss', 'sa.store_id=ss.id');

                        if(is_array($aid)){
                            $managementArr = $managementArr->where_in('ss.aid',$aid);
                        }else{
                            $managementArr = $managementArr->where_equalTo('ss.aid',$aid);
                        }

                        $managementArr = $managementArr
                            ->where_in('bb.id', $buildingIds)
                            ->select($select)
                            ->orderBy('br.id', 'DESC')
                            ->page($curr, $limit)
                            ->execute();

                        $count = $this->reported_db();

                        $count->leftJoin('xcx_store_agent', 'sa', 'sa.agent_id=au.id')
                            ->leftJoin('xcx_store_store', 'ss', 'sa.store_id=ss.id');
                        if(is_array($aid)){
                            $count = $count->where_in('ss.aid',$aid);
                        }else{
                            $count = $count->where_equalTo('ss.aid',$aid);
                        }
                        $count = $count
                            ->where_in('bb.id', $buildingIds)
                            ->select('COUNT(DISTINCT br.id)')
                            ->firstColumn();


                    }elseif($this->gid == 7){
                        $managementArr = $this->reported_db()
                            ->where_equalTo('bb.aid',$_SESSION['aid'])
                            ->where_in('bb.id', $buildingIds)
                            ->select($select)
                            ->orderBy('br.id', 'DESC')
                            ->page($curr, $limit)
                            ->execute();
                        $count = $this->reported_db()
                            ->where_equalTo('bb.aid',$_SESSION['aid'])
                            ->where_in('bb.id', $buildingIds)
                            ->select('COUNT(DISTINCT br.id)')
                            ->firstColumn();
                    }else{
                        $managementArr = $this->reported_db()
                            ->where_in('bb.id', $buildingIds)
                            ->select($select)
                            ->orderBy('br.id', 'DESC')
                            ->page($curr, $limit)
                            ->execute();
                        $count = $this->reported_db()
                            ->where_in('bb.id', $buildingIds)
                            ->select('COUNT(DISTINCT br.id)')
                            ->firstColumn();
                    }



                }
            } else { //条件不为空时候
                if (empty($buildingIds)) {
                    if($this->gid == 5 || $this->gid == 8){
                        $channeid = $this->db->Name('admin')->where_equalTo('id',$_SESSION['aid'])->select('channel_id')->firstRow();
                        $type = $this->db->Name('xcx_store_agent')->where_equalTo('said',$channeid['channel_id'])->select('type,mgid')->firstRow();
                        if($type['type'] == 5){
                            $aid = $_SESSION['aid'];
                        }elseif($type['type'] == 6){
                            $mgid = explode(',',$type['mgid']);
                            $saidArray = $this->db->Name('xcx_store_agent')->where_in('mgid',$mgid)->where_equalTo('type',5)->select('said')->execute();
                            $channelId = array_column($saidArray,'said');
                            $adminId = $this->db->Name('admin')->where_in('channel_id',$channelId)->select('id')->execute();
                            $aid = array_column($adminId,'id');
                        }

                        if($this->gid == 5 || $this->gid == 8){
                            if(empty($aid)){
                                echo json_encode(['success'=>true,'data'=>[],'count'=>0]);
                                exit();
                            }
                        }
                        $managementArr = $this->set_where($where, $this->reported_db());
                        $managementArr->leftJoin('xcx_store_agent', 'sa', 'sa.agent_id=au.id')
                            ->leftJoin('xcx_store_store', 'ss', 'sa.store_id=ss.id');

                        if(is_array($aid)){
                            $managementArr = $managementArr->where_in('ss.aid',$aid);
                        }else{
                            $managementArr = $managementArr->where_equalTo('ss.aid',$aid);
                        }

                        $managementArr = $managementArr
                            ->select($select)
                            ->page($curr, $limit)
                            ->orderBy('br.id', 'DESC')
                            ->execute();

                        $count = $this->set_where($where, $this->reported_db());

                        $count->leftJoin('xcx_store_agent', 'sa', 'sa.agent_id=au.id')
                            ->leftJoin('xcx_store_store', 'ss', 'sa.store_id=ss.id');
                        if(is_array($aid)){
                            $count = $count->where_in('ss.aid',$aid);
                        }else{
                            $count = $count->where_equalTo('ss.aid',$aid);
                        }
                        $count = $count
                            ->select('COUNT(DISTINCT br.id)')
                            ->firstColumn();


                    }elseif($this->gid == 7){
                        $managementArr = $this->set_where($where, $this->reported_db())
                            ->where_equalTo('bb.aid',$_SESSION['aid'])
                            ->select($select)
                            ->page($curr, $limit)
                            ->orderBy('br.id', 'DESC')
                            ->execute();
                        $count = $this->set_where($where, $this->reported_db())
                            ->where_equalTo('bb.aid',$_SESSION['aid'])
                            ->select('COUNT(DISTINCT br.id)')
                            ->firstColumn();
                    }else{
                        $managementArr = $this->set_where($where, $this->reported_db())
                            ->select($select)
                            ->page($curr, $limit)
                            ->orderBy('br.id', 'DESC')
                            ->execute();
                        $count = $this->set_where($where, $this->reported_db())
                            ->select('COUNT(DISTINCT br.id)')
                            ->firstColumn();
                    }


                } else {

                    if($this->gid == 5 || $this->gid == 8){
                        $channeid = $this->db->Name('admin')->where_equalTo('id',$_SESSION['aid'])->select('channel_id')->firstRow();
                        $type = $this->db->Name('xcx_store_agent')->where_equalTo('said',$channeid['channel_id'])->select('type,mgid')->firstRow();
                        if($type['type'] == 5){
                            $aid = $_SESSION['aid'];
                        }elseif($type['type'] == 6){
                            $mgid = explode(',',$type['mgid']);
                            $saidArray = $this->db->Name('xcx_store_agent')->where_in('mgid',$mgid)->where_equalTo('type',5)->select('said')->execute();
                            $channelId = array_column($saidArray,'said');
                            $adminId = $this->db->Name('admin')->where_in('channel_id',$channelId)->select('id')->execute();
                            $aid = array_column($adminId,'id');
                        }

                        if($this->gid == 5 || $this->gid == 8){
                            if(empty($aid)){
                                echo json_encode(['success'=>true,'data'=>[],'count'=>0]);
                                exit();
                            }
                        }
                        $managementArr = $this->set_where($where, $this->reported_db());
                        $managementArr->leftJoin('xcx_store_agent', 'sa', 'sa.agent_id=au.id')
                            ->leftJoin('xcx_store_store', 'ss', 'sa.store_id=ss.id');

                        if(is_array($aid)){
                            $managementArr = $managementArr->where_in('ss.aid',$aid);
                        }else{
                            $managementArr = $managementArr->where_equalTo('ss.aid',$aid);
                        }

                        $managementArr = $managementArr
                            ->where_in('bb.id', $buildingIds)
                            ->select($select)
                            ->page($curr, $limit)
                            ->orderBy('br.id', 'DESC')
                            ->execute();

                        $count = $this->set_where($where, $this->reported_db());

                        $count->leftJoin('xcx_store_agent', 'sa', 'sa.agent_id=au.id')
                            ->leftJoin('xcx_store_store', 'ss', 'sa.store_id=ss.id');
                        if(is_array($aid)){
                            $count = $count->where_in('ss.aid',$aid);
                        }else{
                            $count = $count->where_equalTo('ss.aid',$aid);
                        }
                        $count = $count
                            ->where_in('bb.id', $buildingIds)
                            ->select('COUNT(DISTINCT br.id)')
                            ->firstColumn();


                    }elseif ($this->gid == 7){
                        $managementArr = $this->set_where($where, $this->reported_db())
                            ->where_equalTo('bb.aid',$_SESSION['aid'])
                            ->where_in('bb.id', $buildingIds)
                            ->select($select)
                            ->page($curr, $limit)
                            ->orderBy('br.id', 'DESC')
                            ->execute();
                        $count = $this->set_where($where, $this->reported_db())
                            ->where_equalTo('bb.aid',$_SESSION['aid'])
                            ->where_in('bb.id', $buildingIds)
                            ->select('COUNT(DISTINCT br.id)')
                            ->firstColumn();
                    }
                    else{
                        $managementArr = $this->set_where($where, $this->reported_db())
                            ->where_in('bb.id', $buildingIds)
                            ->select($select)
                            ->page($curr, $limit)
                            ->orderBy('br.id', 'DESC')
                            ->execute();
                        $count = $this->set_where($where, $this->reported_db())
                            ->where_in('bb.id', $buildingIds)
                            ->select('COUNT(DISTINCT br.id)')
                            ->firstColumn();
                    }

                }
            }
        }
        foreach ($managementArr as &$val) {
            $val['auname'] = $val['auname'] ? $val['auname'] : $val['aunickname'];
            $val['describe'] = $val['describe'] ? $val['describe'] : "";
            $val['take_time'] = date('Y-m-d H:i:s', $val['take_time']);
            $val['create_time'] = date('Y-m-d H:i:s', $val['create_time']);
            $val['update_time'] = date('Y-m-d H:i:s', $val['update_time']);
            $val['user_gender'] = $val['user_gender'] == 1 ? '男' : '女';
            $val['status_type_name'] = $this->status_type_toString($val['status_type']);
        }
        if (empty($managementArr)) {
            echo json_encode(['success' => false]);
        } else {
            echo json_encode(['success' => true, 'data' => $managementArr, 'count' => $count]);
        }
    }

    /**
     * 查看经纪人报备信息
     * @return false|string
     */
    public function building_report_info_old()
    {
        $id = Context::Get('id');
        $building_report_info = $this->db->Name('xcx_building_reported')
            ->select('json_data')
            ->where_equalTo('id', $id)->firstRow();
        if (!empty($building_report_info)) {
            foreach ($building_report_info as &$val) {
                $val = json_decode($val, true);
                foreach ($val as &$value) {
                    $value['time'] = date('Y-m-d', $value['time']);
                    $value['status_type'] = $this->status_type_toString($value['status_type']);
                    $value['day'] = substr($value['time'], -2);
                    $value['year_month'] = substr($value['time'], 0, 7);
                }
            }
            $data['reportedData'] = $building_report_info['json_data'];
            return $this->render('building_report_info', $data);
        }
    }

    /**
     * 查看经纪人报备信息 新
     * @return false|string
     */
    public function building_report_info()
    {
        $id = Context::Get('id');
        $building_report_info = $this->db->Name('xcx_reported_log')
            ->select('rl.status_type, rl.examine_type, rl.content, rl.updated_at, sa.agent_name', 'rl')
            ->leftJoin('xcx_store_agent', 'sa', 'sa.said=rl.examine_said')
            ->where_equalTo('report_id', $id)
            ->orderBy('rl.updated_at', 'asc')
            ->execute();
        $res = [];
        if (!empty($building_report_info)) {
            foreach ($building_report_info as $key => $val) {
                $keyStatus = "{$val['status_type']}|{$val['examine_type']}";
                $statusStr = $this->getReportStatus()[$keyStatus];
                if (1 == $val['status_type'] && 1 == $val['examine_type']) {
                    $res[$key]['status_type'] = $statusStr;
                } else {
                    if (!empty($val['status_type'])) {
                        $typeStr = "审核";
                    } else {
                        $typeStr = "备注";
                    }
                    $res[$key]['status_type'] = $statusStr . "({$val['agent_name']}{$typeStr})";
                }
                $res[$key]['day'] = date("d", $val['updated_at']);
                $res[$key]['year_month'] = date("Y.m", $val['updated_at']);
                $res[$key]['time'] = date("Y-m-d", $val['updated_at']);
                $res[$key]['content'] = $val['content'];
            }
            $data['reportedData'] = $res;
            return $this->render('building_report_info', $data);
        }
    }

    // 报备状态
    protected function getReportStatus()
    {
        return [
            '1|-2' => '报备失效',
            '1|-1' => '报备驳回',
            '1|1'  => '报备中',
            '1|2'  => '报备完成',
            '2|-2' => '带看失效',
            '2|-1' => '带看驳回',
            '2|1'  => '带看中',
            '2|2'  => '带看完成',
            '3|-2' => '成交失效',
            '3|-1' => '成交驳回',
            '3|1'  => '成交中',
            '3|2'  => '成交完成',
            '4|-2' => '确认业绩失效',
            '4|-1' => '确认业绩驳回',
            '4|1'  => '确认业绩中',
            '4|2'  => '确认业绩完成',
            '5|-2' => '开票失效',
            '5|-1' => '开票驳回',
            '5|1'  => '开票中',
            '5|2'  => '开票完成',
            '6|-2' => '结佣失效',
            '6|-1' => '结佣驳回',
            '6|1'  => '结佣中',
            '6|2'  => '结佣完成',
        ];
    }

    /**
     * 经纪人报备业务
     */
    public function set_reported()
    {
        //DataBase::log(__FILE__.__LINE__,$_POST);
        $id = Context::Post('id');
        $row = $this->db->Name('xcx_building_reported')->select('building_id,json_data')->where_equalTo('id', $id)->firstRow();
        if (!empty($_SESSION['gid'])) {
            $adminRow = $this->db->Name('admin')->select()->where_equalTo('id', $_SESSION['aid'])->firstRow();
            $reported_building_ids = explode(",", $adminRow['reported_building_ids']);
            $reported_status = explode(",", $adminRow['reported_status']);
            if (!in_array(Context::Post('status_type'), $reported_status) || !((isset($adminRow['reported_building_ids']) && $adminRow['reported_building_ids'] == 0) || in_array($row['building_id'], $reported_building_ids))) {
                echo json_encode(['success' => false, 'message' => '您没有修改该状态的权限']);
                exit;
            }
        }
        //获取报备状态并且加1为下一状态
        $flag = Context::Post('flag');
        if (!empty($flag)) {
            $status_type = 99;
        } else {
            $status_type = Context::Post('status_type') + 1;
        }
        $text = trim(Context::Post('text'));

        $json_data = $row['json_data'];
        $json_data_arr = json_decode($json_data, true);
        $json_data_arr[] = ['time' => time(), 'status_type' => $status_type, 'content' => $text];
        $json_data = json_encode($json_data_arr);

        $result = $this->db->Name('xcx_building_reported')->update(['status_type' => $status_type, 'update_time' => time(), 'json_data' => $json_data])
            ->where_equalTo('id', $id)->execute();
        if ($result)
            echo json_encode(['success' => true]);
        else
            echo json_encode(['success' => false, 'message' => '修改失败']);
    }

    /**
     * 楼盘报备DB对象
     * @return mixed
     */
    private function reported_db()
    {

        $db = $this->db->Name('xcx_building_reported AS br')
            ->leftJoin('xcx_user', 'xu', 'xu.id=br.user_id')
            ->leftJoin('xcx_building_building', 'bb', 'bb.id=br.building_id')
            ->leftJoin('xcx_agent_user', 'au', 'au.id=br.agent_id')
            ->leftJoin('xcx_building_reported_comments', 'brc', 'brc.reported_id=br.id');

        if (!empty($this->gid)) {
//            $db->where_like("bb.city", "%{$this->city}%");
        }

        return $db;
    }

    /**
     * 获取评论并打开页面
     */
    public function get_comment()
    {
        $reported_id = Context::Get('id');
        //查询是否有未读
        $comment = $this->db->Name('xcx_building_reported_comments')
            ->select('id')
            ->where_equalTo('reported_id', $reported_id)
            ->where_equalTo('send_from', '1')
            ->where_equalTo('admin_is_read', '0')
            ->execute();
        //有未读则修改读取状态
        if (!empty($comment)) {
            $PDO = new DataBase();
            $PDO::beginTransaction();//开启事务
            //修改管理员读取状态
            $comment_id = $this->db->Name('xcx_building_reported_comments')
                ->update(['admin_is_read' => '1'])
                ->where_equalTo('reported_id', $reported_id)
                ->where_equalTo('send_from', '1')
                ->execute();
            if (!$comment_id) {
                $PDO::rollBack();
                echo json_encode(['success' => false, 'message' => '修改读取状态失败']);
                return;
            }
            $PDO::commit();
        }
        //查询出该报备的所有评论
        $list = $this->reported_comment()
            ->select('brc.id,brc.reported_id,brc.admin_is_read,brc.content,brc.send_from,brc.create_time,brc.update_time,
                               au.nickname,au.headimgurl auheadimgurl,a.name,a.headimgurl aheadimgurl')
            ->where_equalTo('brc.reported_id', $reported_id)
            ->execute();
        foreach ($list as &$val) {
            $val['create_time'] = date('Y-m-d H:i:s', $val['create_time']);
        }
        $data['list'] = $list;
        $data['reported_id'] = $reported_id;
        return $this->render('get_comment', $data);
    }

    /**
     * 后台管理员评论提交
     */
    public function admin_submit_comment()
    {
        $reported_id = Context::Post('reported_id');
        $comment_content = trim(Context::Post('comment_content'));
        if ($comment_content == '') {
            return;
        }
        $admin_id = $_SESSION['aid'];
        $my_time = time();
        $data = ['reported_id' => $reported_id, 'agent_id' => 0, 'admin_id' => $admin_id, 'send_from' => '2',
                 'content'     => $comment_content, 'admin_is_read' => '0', 'create_time' => $my_time, 'update_time' => 0];
        $PDO = new DataBase();
        $PDO::beginTransaction();//开启事务
        //插入管理员回复的评论
        $comment_id = $this->db->Name('xcx_building_reported_comments')
            ->insert($data)
            ->execute();
        //判断管理员评论是否插入
        if (!$comment_id) {
            $PDO::rollBack();
            echo json_encode(['success' => false, 'message' => '管理员评论保存失败']);
            return;
        }
        $PDO::commit();
        //查询出管理员评论以及相关信息 追加到评论界面
        $list = $this->reported_comment()
            ->select('brc.id,brc.reported_id,brc.admin_is_read,brc.content,brc.send_from,brc.create_time,brc.update_time,
                               au.nickname,au.headimgurl auheadimgurl,a.name,a.headimgurl aheadimgurl')
            ->where_equalTo('brc.id', $comment_id)
            ->execute();
        foreach ($list as &$val) {
            $val['create_time'] = date('Y-m-d H:i:s', $val['create_time']);
        }
        echo json_encode(['success' => true, 'data' => $list]);
    }

    /**
     * 报备评论DB对象
     */
    private function reported_comment()
    {
        $db = $this->db->Name('xcx_building_reported_comments AS brc')
            ->leftJoin('xcx_building_reported', 'br', 'br.id=brc.reported_id')
            ->leftJoin('xcx_agent_user', 'au', 'au.id=brc.agent_id')
            ->leftJoin('admin', 'a', 'a.id=brc.admin_id');
        return $db;
    }

    /**
     * 报备状态字符替换
     * @param $status_type
     * @return string
     */
    private function status_type_toString($status_type)
    {
        switch ($status_type) {
            case 0:
                $status_type_toString = '审核';
                break;
            case 1:
                $status_type_toString = '报备';
                break;
            case 2:
                $status_type_toString = '带看';
                break;
            case 3:
                $status_type_toString = '成交';
                break;
            case 4:
                $status_type_toString = '确认业绩';
                break;
            case 5:
                $status_type_toString = '开票';
                break;
            case 6:
                $status_type_toString = '结佣(完成)';
                break;
            case 99:
                $status_type_toString = '审核不通过';
                break;
        }
        return $status_type_toString;
    }

    /**
     * 结佣状态字符替换
     * @param $status_type
     * @return string
     */
    private function examine_type_toString($status_type)
    {
        switch ($status_type) {
            case -1:
                $examine_type_toString = '结佣(驳回)';
                break;
            case -2:
                $examine_type_toString = '结佣(失效)';
                break;
            case 1:
                $examine_type_toString = '结佣(进行中)';
                break;
            case 2:
                $examine_type_toString = '结佣(完成)';
                break;
        }
        return $examine_type_toString;
    }

    /**
     * 打开楼盘通知页面
     * @return false|string
     */
    public function building_circularize_index()
    {
        return $this->render('building_circularize_index');
    }

    /**
     * 搜索框渲染
     */
    public function building_circularize_search()
    {
        $selectXU = 'DISTINCT xu.id,xu.nickName xunickname';
        $selectAU = 'DISTINCT au.id,au.nickname aunickname';
        $selectBB = 'DISTINCT bb.id,bb.name bbname';
        //小程序昵称
        $userName = $this->building_circularize_Db()
            ->select($selectXU)
            ->where_greatThan('bc.user_id', 0)
            ->execute();
        //经纪人昵称
        $agentName = $this->building_circularize_Db()
            ->select($selectAU)
            ->execute();
        //楼盘名称
        $buildingName = $this->building_circularize_Db()
            ->select($selectBB)
            ->execute();
        $ajax = ['success' => true, 'userName' => $userName, 'agentName' => $agentName, 'buildingName' => $buildingName];
        if (empty($userName) && empty($agentName) && empty($buildingName)) {
            $ajax = ['success' => false, 'message' => '搜索框名称查询失败'];
        }
        echo json_encode($ajax);
    }

    /**
     * 楼盘通知页面数据渲染
     */
    public function building_circularize_page()
    {
        $curr = Context::Post('curr');
        $limit = Context::Post('limit');

        $where['user_id'] = Context::Post('user_id');
        $where['agent_user_id'] = Context::Post('agent_user_id');
        $where['building_building_id'] = Context::Post('building_building_id');

        $where = array_filter($where, function ($val) {
            $tmp = $val === "";
            return !$tmp;
        });
        $select = 'bc.id,bc.kaipan_notice,bc.jianjia_notice,xu.nickName xunickname,xu.avatarUrl,
                   au.name auname,au.nickname aunickname,au.headimgurl,bb.name bbname,bb.pic';
        //无查询条件
        if (empty($where)) {
            $building_circularize_array = $this->building_circularize_Db()
                ->select($select)
                ->page($curr, $limit)
                ->execute();
            $building_circularize_count = $this->building_circularize_Db()
                ->select('COUNT(*)')
                ->firstColumn();
        } else {//有查询条件
            $building_circularize_array = $this->set_where($where, $this->building_circularize_Db())
                ->select($select)
                ->page($curr, $limit)
                ->execute();
            $building_circularize_count = $this->set_where($where, $this->building_circularize_Db())
                ->select('COUNT(*)')
                ->firstColumn();
        }

        if (empty($building_circularize_array) && empty($building_circularize_count)) {
            $ajax = ['success' => false];
        } else {
            foreach ($building_circularize_array as &$val) {
                $val['xunickname'] = empty($val['xunickname']) ? "" : $val['xunickname'];
                $val['avatarUrl'] = empty($val['avatarUrl']) ? "" : $val['avatarUrl'];
            }
            $ajax = ['success' => true, 'data' => $building_circularize_array, 'count' => $building_circularize_count];
        }
        echo json_encode($ajax);
    }

    /**
     * 用户楼盘管理 开启/关闭降价通知
     */
    public function building_jianjia_notice()
    {
        $id = Context::Post('id');
        $jianjia_notice = Context::Post('jianjia_notice');
        $res = $this->db->Name('xcx_building_circularize')->update(['jianjia_notice' => $jianjia_notice])
            ->where_equalTo('id', $id)->execute();
        if ($res)
            echo json_encode(['success' => true]);
        else
            echo json_encode(['success' => false]);
    }

    /**
     * 用户楼盘管理 开启/关闭开盘通知
     */
    public function building_kaipan_notice()
    {
        $id = Context::Post('id');
        $kaipan_notice = Context::Post('kaipan_notice');

        $res = $this->db->Name('xcx_building_circularize')->update(['kaipan_notice' => $kaipan_notice])
            ->where_equalTo('id', $id)->execute();
        if ($res)
            echo json_encode(['success' => true]);
        else
            echo json_encode(['success' => false]);
    }

    /**
     * 楼盘通知对象
     */
    private function building_circularize_Db()
    {
        return $this->db->Name('xcx_building_circularize bc')
            ->leftJoin('xcx_user', 'xu', 'xu.id=bc.user_id')
            ->leftJoin('xcx_agent_user', 'au', 'au.id=bc.agent_user_id')
            ->leftJoin('xcx_building_building', 'bb', 'bb.id=bc.building_building_id');
    }

    /**
     * 多个条件查询  (等于)
     * @param $where 要查询的条件
     * @param $Db
     * @return mixed
     */
    public function set_where($where, $Db)
    {
        foreach ($where as $k => $v) {
            $Db->where_equalTo($k, $v);
        }
        return $Db;
    }

    /*============================================= 楼盘字典管理 =======================================================*/
    public function dict_index()
    {
        return $this->render('dict_index');
    }

    public function set_dict_where($select, $Db)
    {
        foreach ($select as $k => $v) {
            $Db->where_like($k, '%' . $v . '%');
        }
        return $Db;
    }

    public function dict_page()
    {
        $curr = Context::Post('curr');
        $limit = Context::Post('limit');
        $select['name'] = Context::Post('name');
        $select['tbl_name'] = Context::Post('tbl_name');
        $select = array_filter($select, function ($val) {
            $tmp = $val === '';
            return !$tmp;
        });
        if (!empty($select)) {
            $userDb = $this->db->Name('xcx_building_dict');
            $userDb = $this->set_dict_where($select, $userDb);
            $data = $userDb->select()->page($curr, $limit)->orderBy('tbl_name')->orderBy('orders')->execute();
            $userDb = $this->set_dict_where($select, $userDb);
            $count = $userDb->select('count(*)')->firstColumn();
        } else {
            $data = $this->db->Name('xcx_building_dict')->select()->page($curr, $limit)
                ->orderBy('tbl_name')->orderBy('orders')->execute();
            $count = $this->db->Name('xcx_building_dict')->select('count(*)')->firstColumn();
        }
        if (!empty($data)) {
            foreach ($data as &$val) {
                $val['create_time'] = date('Y-m-d H:i:s', $val['create_time']);
                $val['update_time'] = date('Y-m-d H:i:s', $val['update_time']);
            }
            echo json_encode(['success' => true, 'data' => $data, 'count' => $count]);
        } else {
            echo json_encode(['success' => false, 'curr' => $curr]);
        }
    }

    public function dict_add()
    {
        return $this->render('dict_add');
    }

    public function dict_doadd()
    {
        $data['`code`'] = Context::Post('code');
        $data['name'] = Context::Post('name');
        $data['tbl_name'] = Context::Post('tbl_name');
        $data['`describe`'] = Context::Post('describe');
        $data['orders'] = Context::Post('orders');

        $data['create_time'] = time();
        $data['update_time'] = time();
        $res = $this->db->Name('xcx_building_dict')->insert($data)->execute();
        if ($res)
            echo json_encode(['success' => true]);
        else
            echo json_encode(['success' => false, 'message' => "保存失败"]);
    }

    public function dict_edit()
    {
        $id = Context::Get('id');
        $data['data'] = $this->db->Name('xcx_building_dict')->select()->where_equalTo('id', $id)->firstRow();
        return $this->render('dict_edit', $data);
    }

    public function dict_doedit()
    {
        $id = Context::Post('id');
        $data['`code`'] = Context::Post('code');
        $data['name'] = Context::Post('name');
        $data['tbl_name'] = Context::Post('tbl_name');
        $data['`describe`'] = Context::Post('describe');
        $data['orders'] = Context::Post('orders');
        $data['update_time'] = time();
        $res = $this->db->Name('xcx_building_dict')->update($data)->where_equalTo('id', $id)->execute();
        if ($res) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => '保存失败']);
        }
    }

    public function dict_status()
    {
        $id = Context::Post('id');
        $if_show = Context::Post('if_show');
        $res = $this->db->Name('xcx_building_dict')->update(['if_show' => $if_show])->where_equalTo('id', $id)->execute();
        if ($res)
            echo json_encode(['success' => true]);
        else
            echo json_encode(['success' => false]);
    }

    public function dict_del()
    {
        $id = Context::Post('id');
        $res = $this->db->Name('xcx_building_dict')->delete()->where_equalTo('id', $id)->execute();
        if ($res) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => '删除失败']);
        }
    }

    /*============================================= 楼盘管理 ========================================================*/
    public function building_index()
    {
        return $this->render('building_index');
    }

    public function set_building_where($select, $Db)
    {
        foreach ($select as $k => $v) {
            if ($k == 'name' || $k == 'sales_telephone' || 'city' == $k)
                $Db->where_like($k, '%' . $v . '%');
            else
                $Db->where_equalTo($k, $v);
        }
        return $Db;
    }

    public function building_page()
    {
        $curr = Context::Post('curr');
        $limit = Context::Post('limit');
        $select['status'] = Context::Post('status');
        $select['is_hot'] = Context::Post('is_hot');
        $select['name'] = Context::Post('name');
        $select['sales_telephone'] = Context::Post('sales_telephone');
        $select = array_filter($select, function ($val) {
            $tmp = $val === '';
            return !$tmp;
        });
        // 非超级管理员只能查看自己所属区域
//        if (!empty($this->gid)) {
//            if (empty($this->city)) {
//                echo json_encode(['success' => false, 'message' => '没有所属城市，无信息可查看']);
//                exit();
//            }
//            $select['city'] = $this->city;
//        }

        if (!empty($select)) {
            $userDb = $this->db->Name('xcx_building_building');
            $userDb = $this->set_building_where($select, $userDb);
            //条件
            if($this->gid == 7){
                $userDb =  $userDb->where_equalTo('aid',$_SESSION['aid']);
            }
            $data = $userDb->select()->where_equalTo('is_delete', 0)->page($curr, $limit)->orderBy('create_time', 'desc')->execute();
            $userDb = $this->set_building_where($select, $userDb);
            if($this->gid == 7){
                $userDb =  $userDb->where_equalTo('aid',$_SESSION['aid']);
            }
            $count = $userDb->select('count(*)')->where_equalTo('is_delete', 0)->firstColumn();
        } else {
            $data = $this->db->Name('xcx_building_building');
            $count = $this->db->Name('xcx_building_building');
            if($this->gid == 7){
                $data =  $data->where_equalTo('aid',$_SESSION['aid']);
            }
            $data = $data->select()->where_equalTo('is_delete', 0)->page($curr, $limit)->orderBy('create_time', 'desc')->execute();
            if($this->gid == 7){
                $count =  $count->where_equalTo('aid',$_SESSION['aid']);
            }
            $count = $count->select('count(*)')->where_equalTo('is_delete', 0)->firstColumn();
        }
        if (!empty($data)) {
            foreach ($data as &$val) {
                $val['views_number'] = $val['views_number'] >= 10000 ? sprintf("%.1f", $val['views_number'] / 10000) . '万' : $val['views_number'];
                $val['create_time'] = date('Y-m-d H:i:s', $val['create_time']);
                $val['update_time'] = date('Y-m-d H:i:s', $val['update_time']);
            }
            echo json_encode(['success' => true, 'data' => $data, 'count' => $count]);
        } else {
            echo json_encode(['success' => false, 'curr' => $curr]);
        }
    }

    //地址经纬度搜索
    public function building_search()
    {
        $keyword = Context::Post('keyword');
        $res = $this->sendPost("https://apis.map.qq.com/ws/geocoder/v1/", ['address' => $keyword, 'key' => '7VABZ-GKERX-R5K4U-ZNGQ6-6Z5B7-BZFC7']);
        echo $res;
    }

    public function building_add()
    {
        //获取楼盘字典
        $dict = [];
        $dictData = $this->db->Name('xcx_building_dict')->select()->where_notEqualTo('orders', 0)->where_equalTo('if_show', 1)->orderBy('orders')->execute();
        if (!empty($dictData)) {
            foreach ($dictData as $val) {
                $dict[$val['tbl_name']][] = $val['name'];
            }
        }
        return $this->render('building_add', $dict);
    }

    public function building_doadd()
    {
        $cs = json_decode(Context::Post('parame'), 1);

        // 非超级管理员不能添加自己所属城市外的楼盘
//        if (!empty($this->gid)) {
//            if (empty($this->city)) {
//                echo json_encode(['success' => false, 'message' => '没有所属城市，不可修改']);
//                exit();
//            }
//            if ($cs['city'] != $this->city) {
//                echo json_encode(['success' => false, 'message' => '您没有该城市权限']);
//                exit();
//            }
//        }

        $aid = $_SESSION['aid'];

        $data['coordinate'] = $cs['coordinate'];    //楼盘坐标
        $data['aid'] = $aid;    // 添加账号的ID
        $data['name'] = $cs['name'];    //楼盘名称
        $data['sales_status'] = $cs['sales_status'];    //销售状态
        $data['fold'] = $cs['fold'];    //参考价格
        $data['fold_detail'] = $cs['fold_detail'];    // 价格详情
        $data['commission_type'] = (int)$cs['commission_type'];    //佣金类型
        $data['commission'] = $cs['commission'];    //佣金
        $data['store_manager_commission'] = 0;    //店长佣金
        $data['team_member_commission'] = 0;    //组员佣金
        $data['flag'] = trim($cs['flag'], ',');    //楼盘标记
        $data['province'] = $cs['province'];    //省份
        $data['city'] = $cs['city'];    //城市
        $data['area'] = $cs['area'];    //区域
        $data['address'] = $cs['address'];    //地址
        $data['house_type'] = trim($cs['house_type'], ',');    //楼盘类型
        $data['louchen'] = $cs['louchen'];    //楼层
        $data['floor_height'] = $cs['floor_height'];    //楼层高度
        $data['sort'] = (int)$cs['sort'];    //排序
        $data['developers'] = $cs['developers'];    //开发商
        $data['sales_telephone'] = $cs['sales_telephone'];    //售楼电话
        $data['kaipang_time'] = strtotime($cs['kaipang_time']);    //开盘时间
        $data['jiaofang_time'] = strtotime($cs['jiaofang_time']);    //交房时间
        $data['sales_license'] = $cs['sales_license'];    //预售许可证
        $data['license_time'] = strtotime($cs['license_time']);    //许可证发证时间
        $data['bind_building'] = $cs['bind_building'];    //绑定楼栋
        $data['sizelayout'] = $cs['sizelayout'];    //大小户型
        $data['planning_number'] = $cs['planning_number'];    //规划户数
        $data['project_type'] = $cs['project_type'];    //项目类型
        $data['building_type'] = $cs['building_type'];    //建筑类型
        $data['total_area'] = $cs['total_area'];    //占地总面积
        $data['total_construction_area'] = $cs['total_construction_area'];    //建筑总面积
        $data['floor_condition'] = $cs['floor_condition'];    //楼层状况
        $data['progress_project'] = $cs['progress_project'];    //项目进度
        $data['pool'] = $cs['pool'];    //公摊
        $data['decoration'] = $cs['decoration'];    //装修情况
        $data['property_company'] = $cs['property_company'];    //物业公司
        $data['property_type'] = $cs['property_type'];    //物业类型
        $data['property_charges'] = $cs['property_charges'];    //物业费
        $data['volume_rate'] = $cs['volume_rate'];    //容积率
        $data['greening_rate'] = $cs['greening_rate'];    //绿化率
        $data['parking_space_number'] = $cs['parking_space_number'];    //车位数
        $data['parking_space_proportion'] = $cs['parking_space_proportion'];    //车位比
        $data['traffic_complete'] = $cs['traffic_complete'];    //交通配套
        $data['education_resources'] = $cs['education_resources'];    //教育资源
        $data['medical_health'] = $cs['medical_health'];    //医疗健康
        $data['shopping_mall'] = $cs['shopping_mall'];    //商城购物
        $data['live_entertainment'] = $cs['live_entertainment'];    //生活娱乐
        $data['supporting_information'] = $cs['supporting_information'];    //配型信息
        $data['create_time'] = time();
        $data['update_time'] = time();

        $data['early_hours'] = intval($cs['early_hours']);
        $data['protect_set']['status1_hours'] = intval($cs['protect_set[status1_hours]']);
        $data['protect_set']['status2_hours'] = intval($cs['protect_set[status2_hours]']);
        $data['protect_set']['status3_hours'] = intval($cs['protect_set[status3_hours]']);
        if(empty($data['protect_set']['status1_hours']) || empty($data['protect_set']['status2_hours'])) {
            echo json_encode(['success' => false, 'message' => "报备保护期和带看保护期不能为0"]);
            exit();
        }
        $data['protect_set'] = json_encode($data['protect_set'], JSON_UNESCAPED_UNICODE);

        $upfile = new UploadFiles(array('filepath' => BasePath . DS . 'upload' . DS . 'building'));
        if ($upfile->uploadeFile('file')) {
            $arrfile = $upfile->getnewFile();
            $data['pic'] = '/upload/building/' . $arrfile;
            $res = $this->db->Name('xcx_building_building')->insert($data)->execute();
            if ($res) {
                $this->set_building_map($data['coordinate'], $res);
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => "保存失败"]);
            }
        } else {
            $err = $upfile->gteerror();
            echo json_encode(['success' => false, 'message' => $err]);
            exit;
        }
    }

    /**
     * 求两个已知经纬度之间的距离,单位为米
     *
     * @param lng1 $ ,lng2 经度
     * @param lat1 $ ,lat2 纬度
     * @return float 距离，单位米
     * @author www.Alixixi.com
     */
    function getdistance($lng1, $lat1, $lng2, $lat2)
    {
        // 将角度转为狐度
        $radLat1 = deg2rad($lat1); //deg2rad()函数将角度转换为弧度
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000;
        return $s;
    }

    //生成对应的周边信息
    public function set_building_map($location = "", $id)
    {
        $location = explode(',', $location);
        $keyword = ["公交", "学校", "医院", "购物", "美食"];
        $boundary = "nearby(" . $location[0] . "," . $location[1] . ",1000)";
        $key = "7VABZ-GKERX-R5K4U-ZNGQ6-6Z5B7-BZFC7";
        foreach ($keyword as $value) {
            $res = $this->sendGet("https://apis.map.qq.com/ws/place/v1/search?keyword=" . urlencode($value) . "&boundary=" . $boundary . "&key=" . $key);
            $res = json_decode($res, true);
            if (empty($res['status'])) {
                $dataInfo = $res['data'];
                if (!empty($dataInfo)) {
                    foreach ($dataInfo as $val) {
                        $data = [];
                        $data['building_id'] = $id;
                        $data['keyword'] = $value;
                        $data['title'] = $val['title'];
                        $data['address'] = $val['address'];
                        $data['tel'] = $val['tel'];
                        $data['category'] = $val['category'];
                        $data['lat'] = $val['location']['lat'];
                        $data['lng'] = $val['location']['lng'];
                        $data['province'] = $val['ad_info']['province'];
                        $data['city'] = $val['ad_info']['city'];
                        $data['district'] = $val['ad_info']['district'];
                        $data['distance'] = $this->getdistance($location[1], $location[0], $data['lng'], $data['lat']);
                        $data['create_time'] = time();
                        $data['update_time'] = time();
                        $this->db->Name('xcx_building_map')->insert($data)->execute();
                    }
                }
            }
        }
        return true;
    }

    public function basic_index()
    {  //基础信息
        $id = Context::Get('id');
        //获取楼盘字典
        $dict = [];
        $dictData = $this->db->Name('xcx_building_dict')->select()->where_notEqualTo('orders', 0)->where_equalTo('if_show', 1)->orderBy('orders')->execute();
        if (!empty($dictData)) {
            foreach ($dictData as $val) {
                $dict[$val['tbl_name']][] = $val['name'];
            }
        }
        //获取楼盘信息
        $data['data'] = $this->db->Name('xcx_building_building')->select()->where_equalTo('id', $id)->firstRow();
        $data['data']['kaipang_time'] = date('Y-m-d', $data['data']['kaipang_time']);
        $data['data']['jiaofang_time'] = date('Y-m-d', $data['data']['jiaofang_time']);
        $data['data']['license_time'] = date('Y-m-d', $data['data']['license_time']);
        $data['data']['early_hours'] = intval($data['data']['early_hours']);
        $data['data']['protect_set'] = $data['data']['protect_set'] ? json_decode($data['data']['protect_set'], 1) : [];
        $data['data']['protect_set']['status1_hours'] = intval($data['data']['protect_set']['status1_hours']);
        $data['data']['protect_set']['status2_hours'] = intval($data['data']['protect_set']['status2_hours']);
        $data['data']['protect_set']['status3_hours'] = intval($data['data']['protect_set']['status3_hours']);
        // 楼盘标记处理
        $data['data']['flag'] = explode(',', $data['data']['flag']);

        $data = array_merge($data, $dict);
        return $this->render('basic_index', $data);
    }

    public function shuffle_index()
    {      //轮播图
        $data['data']['id'] = Context::Get('id');
        return $this->render('shuffle_index', $data);
    }

    public function map_index()
    {    //周边地图
        $data['data']['id'] = Context::Get('id');
        return $this->render('map_index', $data);
    }

    public function floor_index()
    {    //楼栋信息
        $id = Context::Get('id');
        $data['data'] = $this->db->Name('xcx_building_building')->select()->where_equalTo('id', $id)->firstRow();
        $data['data']['floor_img'] = empty($data['data']['floor_img']) ? '/upload/static/empty.png' : $data['data']['floor_img'];
        return $this->render('floor_index', $data);
    }

    public function report_index()
    {//报备规则
        $id = Context::Get('id');
        $data['data'] = $this->db->Name('xcx_building_building')->select()->where_equalTo('id', $id)->firstRow();
        $data['data']['id'] = $id;
        $data['data']['early_hours'] = intval($data['data']['early_hours']);
        $data['data']['protect_set'] = $data['data']['protect_set'] ? json_decode($data['data']['protect_set'], 1) : [];
        $data['data']['protect_set']['status1_hours'] = intval($data['data']['protect_set']['status1_hours']);
        $data['data']['protect_set']['status2_hours'] = intval($data['data']['protect_set']['status2_hours']);
        $data['data']['protect_set']['status3_hours'] = intval($data['data']['protect_set']['status3_hours']);

        $date = $this->db->Name('xcx_building_report')->select()->where_equalTo('unit_id', $id)->firstRow();
        if ($date) {
            $data['data']['online_rules'] = $date['online_rules'];
            $data['data']['commission_rules'] = $date['commission_rules'];
            $data['data']['report_rules'] = $date['report_rules'];
            $data['data']['look_rules'] = $date['look_rules'];
            $data['data']['servant_rules'] = $date['servant_rules'];
            $data['data']['target_rules'] = $date['target_rules'];
        }

        return $this->render('report_index', $data);
    }

    public function building_edit()
    {
        $data['id'] = Context::Get('id');
        return $this->render('building_edit', $data);
    }

    public function report_doedit()
    {

        $cs = Context::Post();
        if (isset($cs['parame'])) {
            $cs = json_decode($cs['parame'], TREU);
        }
        $unit_id = $cs['id'];
        $data['unit_id'] = $unit_id;

        $data['online_rules'] = $cs['online_rules'];
        $data['commission_rules'] = $cs['commission_rules'];
        $data['report_rules'] = $cs['report_rules'];
        $data['look_rules'] = $cs['look_rules'];
        $data['servant_rules'] = $cs['servant_rules'];
        $data['target_rules'] = $cs['target_rules'];
        $data['status'] = 1;
        $data['create_time'] = time();

        $info = (new Query())->Name('xcx_building_report')->select('id,unit_id')->where_equalTo('unit_id', $unit_id)->firstRow();
        if ($info) {
            $data['update_time'] = time();
            $res = $this->db->Name('xcx_building_report')->update($data)->where_equalTo('unit_id', $unit_id)->execute();
        } else {
            $res = $this->db->Name('xcx_building_report')->insert($data)->execute();
        }

        $infodata = (new Query())->Name('xcx_building_building')->select()->where_equalTo('id', $unit_id)->firstRow();
        if ($infodata) {
            $datas['early_hours'] = intval($cs['early_hours']);
            $datas['protect_set']['status1_hours'] = intval($cs['protect_set']['status1_hours']);
            $datas['protect_set']['status2_hours'] = intval($cs['protect_set']['status2_hours']);
            $datas['protect_set']['status3_hours'] = intval($cs['protect_set']['status3_hours']);
            $datas['protect_set'] = json_encode($datas['protect_set'], JSON_UNESCAPED_UNICODE);
            $datas['update_time'] = time();
            $resdate = $this->db->Name('xcx_building_building')->update($datas)->where_equalTo('id', $unit_id)->execute();
        }

        if ($res) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => '保存失败']);
        }


    }

    /**
     * 提交
     */
    public function building_doedit()
    {
        $cs = Context::Post();

        if (isset($cs['parame'])) {
            $cs = json_decode($cs['parame'], TRUE);
            $isFile = TRUE;// 有文件上传
        }

        // 非超级管理员不能添加自己所属城市外的楼盘
//        if (!empty($this->gid)) {
//            if (empty($this->city)) {
//                echo json_encode(['success' => false, 'message' => '没有所属城市，不可修改']);
//                exit();
//            }
//            if ($cs['city'] != $this->city) {
//                echo json_encode(['success' => false, 'message' => '您没有该城市权限']);
//                exit();
//            }
//        }

        $id = $cs['id'];
        $type = $cs['type'];
        $data['coordinate'] = $cs['coordinate'];    //楼盘坐标
        $data['name'] = $cs['name'];    //楼盘名称
        $data['sales_status'] = $cs['sales_status'];    //销售状态
        $data['fold'] = $cs['fold'];    //参考价格
        $data['fold_detail'] = $cs['fold_detail'];    //价格详情
        $data['commission_type'] = (int)$cs['commission_type'];
        $data['commission'] = $cs['commission'];    //店员佣金
        $data['store_manager_commission'] = 0;    //店长佣金
        $data['team_member_commission'] = 0;    //组员佣金
        $data['flag'] = trim($cs['flag'], ',');    //楼盘标记
        $data['province'] = $cs['province'];    //省份
        $data['city'] = $cs['city'];    //城市
        $data['area'] = $cs['area'];    //区域
        $data['address'] = $cs['address'];    //地址
        $data['house_type'] = trim($cs['house_type'], ',');    //楼盘类型
        $data['louchen'] = $cs['louchen'];    //楼层
        $data['floor_height'] = $cs['floor_height'];    //楼层高度
        $data['sort'] = (int)$cs['sort'];    //排序
        $data['developers'] = $cs['developers'];    //开发商
        $data['sales_telephone'] = $cs['sales_telephone'];    //售楼电话
        $data['kaipang_time'] = strtotime($cs['kaipang_time']);    //开盘时间
        $data['jiaofang_time'] = strtotime($cs['jiaofang_time']);    //交房时间
        $data['sales_license'] = $cs['sales_license'];    //预售许可证
        $data['license_time'] = strtotime($cs['license_time']);    //许可证发证时间
        $data['bind_building'] = $cs['bind_building'];    //绑定楼栋
        $data['sizelayout'] = $cs['sizelayout'];    //大小户型
        $data['planning_number'] = $cs['planning_number'];    //规划户数
        $data['project_type'] = $cs['project_type'];    //项目类型
        $data['building_type'] = $cs['building_type'];    //建筑类型
        $data['total_area'] = $cs['total_area'];    //占地总面积
        $data['total_construction_area'] = $cs['total_construction_area'];    //建筑总面积
        $data['floor_condition'] = $cs['floor_condition'];    //楼层状况
        $data['progress_project'] = $cs['progress_project'];    //项目进度
        $data['pool'] = $cs['pool'];    //公摊
        $data['decoration'] = $cs['decoration'];    //装修情况
        $data['property_company'] = $cs['property_company'];    //物业公司
        $data['property_type'] = $cs['property_type'];    //物业类型
        $data['property_charges'] = $cs['property_charges'];    //物业费
        $data['views_number'] = $cs['views_number'];    //浏览量
        $data['volume_rate'] = $cs['volume_rate'];    //容积率
        $data['greening_rate'] = $cs['greening_rate'];    //绿化率
        $data['parking_space_number'] = $cs['parking_space_number'];    //车位数
        $data['parking_space_proportion'] = $cs['parking_space_proportion'];    //车位比
        $data['traffic_complete'] = $cs['traffic_complete'];    //交通配套
        $data['education_resources'] = $cs['education_resources'];    //教育资源
        $data['medical_health'] = $cs['medical_health'];    //医疗健康
        $data['shopping_mall'] = $cs['shopping_mall'];    //商城购物
        $data['live_entertainment'] = $cs['live_entertainment'];    //生活娱乐
        $data['supporting_information'] = $cs['supporting_information'];    //配型信息
        $data['update_time'] = time();
        $data['early_hours'] = intval($cs['early_hours']);
        if(!empty($isFile)) {
            $data['protect_set'] = [
                'status1_hours' => $cs['protect_set[status1_hours]'],
                'status2_hours' => $cs['protect_set[status2_hours]'],
                'status3_hours' => $cs['protect_set[status3_hours]'],
            ];
        } else {
            $data['protect_set'] = $cs['protect_set'];
        }
        $data['protect_set']['status1_hours'] = intval($data['protect_set']['status1_hours']);
        $data['protect_set']['status2_hours'] = intval($data['protect_set']['status2_hours']);
        $data['protect_set']['status3_hours'] = intval($data['protect_set']['status3_hours']);
        if(empty($data['protect_set']['status1_hours']) || empty($data['protect_set']['status2_hours'])) {
            echo json_encode(['success' => false, 'message' => "报备保护期和带看保护期不能为0"]);
            exit();
        }
        $data['protect_set'] = json_encode($data['protect_set'], JSON_UNESCAPED_UNICODE);

        $info = (new Query())->Name('xcx_building_building')
            ->select('id,commission,store_manager_commission,team_member_commission,fold,name,pic')
            ->where_equalTo('id', $id)->firstRow();
        if ($type != 'empty') {
            $upfile = new UploadFiles(array('filepath' => BasePath . DS . 'upload' . DS . 'building'));
            if ($upfile->uploadeFile('file')) {
                $arrfile = $upfile->getnewFile();
                if (!empty($info)) {
                    $data['pic'] = '/upload/building/' . $arrfile;
                } else {
                    echo json_encode(['success' => false, 'message' => '保存失败']);
                    exit;
                }
            } else {
                $err = $upfile->gteerror();
                echo json_encode(['success' => false, 'message' => $err]);
                exit;
            }
        }
        $res = $this->db->Name('xcx_building_building')->update($data)->where_equalTo('id', $id)->execute();
        if ($res) {
            if (isset($data['fold']) || isset($data['commission']) || isset($data['store_manager_commission']) || isset($data['team_member_commission'])) {
                $new_price = $info['fold'];
                $save_log = 0;
                $changeType = 0;// 变化类型 1-全部 2-价格 3-佣金
                $changeFold = 0;// 价格是否变化
                $changeCommission = 0;// 佣金是否变化
                if ($info['fold'] != $data['fold']) {
                    $new_price = $data['fold'];
                    $save_log = 1;
                    $changeFold = 1;
                }
                //店员佣金变化
                $new_commission = $info['commission'];
                if ($info['commission'] != $data['commission']) {
                    $new_commission = $data['commission'];
                    $save_log = 1;
                    $changeCommission = 1;
                }
                //店长佣金变化
                $new_store_manager_commission = $info['store_manager_commission'];
                if ($info['store_manager_commission'] != $data['store_manager_commission']) {
                    $new_store_manager_commission = $data['store_manager_commission'];
                    $save_log = 1;
                    $changeCommission = 1;
                }
                //店员佣金变化
                $new_team_member_commission = $info['team_member_commission'];
                if ($info['team_member_commission'] != $data['team_member_commission']) {
                    $new_team_member_commission = $data['team_member_commission'];
                    $save_log = 1;
                    $changeCommission = 1;
                }

                if ($changeFold && $changeCommission) {
                    $changeType = 1;
                } else {
                    if ($changeFold && !$changeCommission) {
                        $changeType = 2;
                    }
                    if (!$changeFold && $changeCommission) {
                        $changeType = 3;
                    }
                }
                if ($save_log == 1) {
                    $this->db->Name('xcx_building_building_changelog')->insert([
                        'building_id'                  => $id,
                        'building_name'                => $info['name'],
                        'building_cover'               => $info['pic'],
                        'admin_id'                     => $_SESSION['aid'],
                        'old_price'                    => $info['fold'],
                        'old_commission'               => $info['commission'],
                        'price'                        => $new_price,
                        'commission'                   => $new_commission,
                        'type'                         => $changeType,
                        'create_time'                  => time(),
                        /**
                         * zengxiaokai 2020.6.22
                         */
                        'old_store_manager_commission' => $info['store_manager_commission'],//变更前店长佣金
                        'old_team_member_commission'   => $info['team_member_commission'],//变更钱组员佣金
                        'store_manager_commission'     => $new_store_manager_commission,//变更后的店长佣金
                        'team_member_commission'       => $new_team_member_commission, //变更后的组员佣金
                    ])->execute();
                }
            }

            if ($type != 'empty') {
                if (file_exists(BasePath . $info['pic'])) {
                    unlink(BasePath . $info['pic']);
                }
            }
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => '保存失败']);
        }
    }

    public function building_status()
    {
        $id = Context::Post('id');
        $status = Context::Post('status');
        $bbDb = $this->db->Name('xcx_building_building')->update(['status' => $status])->where_equalTo('id', $id);
//        if (!empty($this->gid)) {
//            if (empty($this->city)) {
//                $bbDb->where_equalTo('id', 0);// 无城市权限，用该条件使记录不被修改
//            } else {
//                $bbDb->where_like('city', "%{$this->city}%");
//            }
//        }
        $res = $bbDb->execute();
        if ($res)
            echo json_encode(['success' => true]);
        else
            echo json_encode(['success' => false]);
    }

    public function building_is_hot()
    {
        $id = Context::Post('id');
        $is_hot = Context::Post('is_hot');
        $bbDb = $this->db->Name('xcx_building_building')->update(['is_hot' => $is_hot])->where_equalTo('id', $id);
//        if (!empty($this->gid)) {
//            if (empty($this->city)) {
//                $bbDb->where_equalTo('id', 0);// 无城市权限，用该条件使记录不被修改
//            } else {
//                $bbDb->where_like('city', "%{$this->city}%");
//            }
//        }
        $res = $bbDb->execute();
        if ($res)
            echo json_encode(['success' => true]);
        else
            echo json_encode(['success' => false]);
    }

    public function building_is_open_project()
    {
        $id = Context::Post('id');
        $is_open_project = Context::Post('is_open_project');
        $bbDb = $this->db->Name('xcx_building_building')->update(['is_open_project' => $is_open_project])->where_equalTo('id', $id);
//        if (!empty($this->gid)) {
//            if (empty($this->city)) {
//                $bbDb->where_equalTo('id', 0);// 无城市权限，用该条件使记录不被修改
//            } else {
//                $bbDb->where_like('city', "%{$this->city}%");
//            }
//        }
        $res = $bbDb->execute();
        if ($res)
            echo json_encode(['success' => true]);
        else
            echo json_encode(['success' => false]);
    }

    public function building_del()
    {
        $id = Context::Post('id');
        $bdDb = $this->db->Name('xcx_building_building')->update([
            'update_time' => time(),
            'is_delete'   => 1,
        ])->where_equalTo('id', $id);

//        if (!empty($this->gid)) {
//            if (empty($this->city)) {
//                $bdDb->where_equalTo('id', 0);// 无城市权限，用该条件使记录不被修改
//            } else {
//                $bdDb->where_like('city', "%{$this->city}%");
//            }
//        }

        $res = $bdDb->execute();
        if ($res) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => '删除失败']);
        }

//        $info=(new Query())->Name('xcx_building_building')->select()->where_equalTo('id',$id)->firstRow();
//        $res=$this->db->Name('xcx_building_building')->delete()->where_equalTo('id',$id)->execute();
//        if($res){
//            if(file_exists(BasePath .$info['pic'])){
//                unlink(BasePath .$info['pic']);
//            }
//            $this->db->Name('xcx_building_map')->delete()->where_equalTo('building_id',$id)->execute(); //删除对应的周边信息
//            echo json_encode(['success'=>true]);
//        }else{
//            echo json_encode(['success'=>false,'message'=>'删除失败']);
//        }
    }

    /*============================================= 楼盘周边地图 ========================================================*/
    public function set_map_where($select, $Db)
    {
        foreach ($select as $k => $v) {
            $Db->where_equalTo($k, $v);
        }
        return $Db;
    }

    public function map_page()
    {
        $id = Context::Post('id');
        $curr = Context::Post('curr');
        $limit = Context::Post('limit');
        $select['keyword'] = Context::Post('keyword');
        $select = array_filter($select, function ($val) {
            $tmp = $val === '';
            return !$tmp;
        });
        if (!empty($select)) {
            $userDb = $this->db->Name('xcx_building_map');
            $userDb = $this->set_map_where($select, $userDb);
            $data = $userDb->select()->where_equalTo('building_id', $id)->page($curr, $limit)->orderBy('create_time', 'desc')->execute();
            $userDb = $this->set_map_where($select, $userDb);
            $count = $userDb->select('count(*)')->where_equalTo('building_id', $id)->firstColumn();
        } else {
            $data = $this->db->Name('xcx_building_map')->select()->where_equalTo('building_id', $id)->page($curr, $limit)->orderBy('keyword')->execute();
            $count = $this->db->Name('xcx_building_map')->where_equalTo('building_id', $id)->select('count(*)')->firstColumn();
        }
        if (!empty($data)) {
            foreach ($data as &$val) {
                $val['create_time'] = date('Y-m-d H:i:s', $val['create_time']);
                $val['update_time'] = date('Y-m-d H:i:s', $val['update_time']);
            }
            echo json_encode(['success' => true, 'data' => $data, 'count' => $count]);
        } else {
            echo json_encode(['success' => false, 'curr' => $curr]);
        }
    }

    public function map_add()
    {
        $data['id'] = Context::Get('id');
        return $this->render('map_add', $data);
    }

    public function map_doadd()
    {
        $data['building_id'] = Context::Post('building_id');
        $data['keyword'] = Context::Post('keyword');
        $data['title'] = Context::Post('title');
        $data['address'] = Context::Post('address');
        $data['tel'] = Context::Post('tel');
        $data['category'] = Context::Post('category');
        $data['lat'] = Context::Post('lat');
        $data['lng'] = Context::Post('lng');
        $data['province'] = Context::Post('province');
        $data['city'] = Context::Post('city');
        $data['district'] = Context::Post('area');
        $data['distance'] = Context::Post('distance');
        $data['create_time'] = time();
        $data['update_time'] = time();
        $res = $this->db->Name('xcx_building_map')->insert($data)->execute();
        if ($res)
            echo json_encode(['success' => true]);
        else
            echo json_encode(['success' => false, 'message' => "保存失败"]);
    }

    public function map_edit()
    {
        $id = Context::Get('id');
        $data['data'] = $this->db->Name('xcx_building_map')->select()->where_equalTo('id', $id)->firstRow();
        return $this->render('map_edit', $data);
    }

    public function map_doedit()
    {
        $id = Context::Post('id');
        $data['keyword'] = Context::Post('keyword');
        $data['title'] = Context::Post('title');
        $data['address'] = Context::Post('address');
        $data['tel'] = Context::Post('tel');
        $data['category'] = Context::Post('category');
        $data['lat'] = Context::Post('lat');
        $data['lng'] = Context::Post('lng');
        $data['province'] = Context::Post('province');
        $data['city'] = Context::Post('city');
        $data['district'] = Context::Post('area');
        $data['distance'] = Context::Post('distance');
        $data['update_time'] = time();
        $res = $this->db->Name('xcx_building_map')->update($data)->where_equalTo('id', $id)->execute();
        if ($res) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => '保存失败']);
        }
    }

    public function map_del()
    {
        $id = Context::Post('id');
        $res = $this->db->Name('xcx_building_map')->delete()->where_equalTo('id', $id)->execute();
        if ($res) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => '删除失败']);
        }
    }

    /*============================================= 楼盘轮播图 ========================================================*/
    public function set_shuffle_where($select, $Db)
    {
        foreach ($select as $k => $v) {
            $Db->where_like($k, '%' . $v . '%');
        }
        return $Db;
    }

    public function shuffle_page()
    {
        $id = Context::Post('id');
        $curr = Context::Post('curr');
        $limit = Context::Post('limit');
        $select['title'] = Context::Post('title');
        $select = array_filter($select, function ($val) {
            $tmp = $val === '';
            return !$tmp;
        });
        if (!empty($select)) {
            $userDb = $this->db->Name('xcx_building_shuffle');
            $userDb = $this->set_shuffle_where($select, $userDb);
            $data = $userDb->select()->where_equalTo('building_id', $id)->page($curr, $limit)->orderBy('sort')->execute();
            $userDb = $this->set_shuffle_where($select, $userDb);
            $count = $userDb->select('count(*)')->where_equalTo('building_id', $id)->firstColumn();
        } else {
            $data = $this->db->Name('xcx_building_shuffle')->select()->where_equalTo('building_id', $id)->page($curr, $limit)->orderBy('sort')->execute();
            $count = $this->db->Name('xcx_building_shuffle')->where_equalTo('building_id', $id)->select('count(*)')->firstColumn();
        }
        if (!empty($data)) {
            foreach ($data as &$val) {
                $val['create_time'] = date('Y-m-d H:i:s', $val['create_time']);
                $val['update_time'] = date('Y-m-d H:i:s', $val['update_time']);
            }
            echo json_encode(['success' => true, 'data' => $data, 'count' => $count]);
        } else {
            echo json_encode(['success' => false, 'curr' => $curr]);
        }
    }

    public function shuffle_status()
    {
        $id = Context::Post('id');
        $status = Context::Post('status');
        $res = $this->db->Name('xcx_building_shuffle')->update(['status' => $status])->where_equalTo('id', $id)->execute();
        if ($res)
            echo json_encode(['success' => true]);
        else
            echo json_encode(['success' => false]);
    }

    public function shuffle_del()
    {
        $id = Context::Post('id');
        $info = (new Query())->Name('xcx_building_shuffle')->select()->where_equalTo('id', $id)->firstRow();
        $res = $this->db->Name('xcx_building_shuffle')->delete()->where_equalTo('id', $id)->execute();
        if ($res) {
            if (file_exists(BasePath . $info['img'])) {
                unlink(BasePath . $info['img']);
            }
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => '删除失败']);
        }
    }

    public function shuffle_add()
    {
        $data['id'] = Context::Get('id');
        return $this->render('shuffle_add', $data);
    }

    public function shuffle_doadd()
    {
        $cs = json_decode(Context::Post('parame'), true);
        $data['building_id'] = $cs['building_id'];
        $data['title'] = $cs['title'];
        $data['url'] = $cs['url'];
        $data['sort'] = $cs['sort'];
        $data['create_time'] = time();
        $data['update_time'] = time();
        $upfile = new UploadFiles(array('filepath' => BasePath . DS . 'upload' . DS . 'building_shuffle'));
        if ($upfile->uploadeFile('file')) {
            $arrfile = $upfile->getnewFile();
            $data['img'] = '/upload/building_shuffle/' . $arrfile;
            $res = $this->db->Name('xcx_building_shuffle')->insert($data)->execute();
            if ($res)
                echo json_encode(['success' => true]);
            else
                echo json_encode(['success' => false, 'message' => "保存失败"]);
        } else {
            $err = $upfile->gteerror();
            echo json_encode(['success' => false, 'message' => $err]);
            exit;
        }
    }

    public function shuffle_edit()
    {
        $id = Context::Get('id');
        $data['data'] = $this->db->Name('xcx_building_shuffle')->select()->where_equalTo('id', $id)->firstRow();
        return $this->render('shuffle_edit', $data);
    }

    public function shuffle_doedit()
    {
        $cs = json_decode(Context::Post('parame'), true);
        $id = $cs['id'];
        $type = $cs['type'];
        $data['title'] = $cs['title'];
        $data['url'] = $cs['url'];
        $data['sort'] = $cs['sort'];
        $data['update_time'] = time();
        if ($type != 'empty') {
            $upfile = new UploadFiles(array('filepath' => BasePath . DS . 'upload' . DS . 'building_shuffle'));
            if ($upfile->uploadeFile('file')) {
                $arrfile = $upfile->getnewFile();
                $info = (new Query())->Name('xcx_building_shuffle')->select()->where_equalTo('id', $id)->firstRow();
                if (!empty($info)) {
                    $data['img'] = '/upload/building_shuffle/' . $arrfile;
                } else {
                    echo json_encode(['success' => false, 'message' => '保存失败']);
                    exit;
                }
            } else {
                $err = $upfile->gteerror();
                echo json_encode(['success' => false, 'message' => $err]);
                exit;
            }
        }
        $res = $this->db->Name('xcx_building_shuffle')->update($data)->where_equalTo('id', $id)->execute();
        if ($res) {
            if ($type != 'empty') {
                if (file_exists(BasePath . $info['img'])) {
                    unlink(BasePath . $info['img']);
                }
            }
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => '保存失败']);
        }
    }

    /*============================================= 楼栋信息 ========================================================*/
    public function floor_img()
    {
        $id = Context::Post('id');
        $data['update_time'] = time();
        $upfile = new UploadFiles(array('filepath' => BasePath . DS . 'upload' . DS . 'building'));
        if ($upfile->uploadeFile('file')) {
            $arrfile = $upfile->getnewFile();
            $info = (new Query())->Name('xcx_building_building')->select()->where_equalTo('id', $id)->firstRow();
            if (!empty($info)) {
                $data['floor_img'] = '/upload/building/' . $arrfile;
            } else {
                echo json_encode(['success' => false, 'message' => '保存失败']);
                exit;
            }
        } else {
            $err = $upfile->gteerror();
            echo json_encode(['success' => false, 'message' => $err]);
            exit;
        }
        $res = $this->db->Name('xcx_building_building')->update($data)->where_equalTo('id', $id)->execute();
        if ($res) {
            if (!empty($info['floor_img']) && file_exists(BasePath . $info['floor_img'])) {
                unlink(BasePath . $info['floor_img']);
            }
            echo json_encode(['success' => true, 'floor_img' => $data['floor_img']]);
        } else {
            echo json_encode(['success' => false, 'message' => '保存失败']);
        }
    }

    //修改坐标
    public function floor_setcoordinates()
    {
        $cs = json_decode(Context::Post('parame'), true);
        if (is_array($cs)) {
            //事务处理
            $pdo = new DataBase();
            try {
                $pdo->beginTransaction();
                foreach ($cs as $key => $val) {
                    $data = [];
                    $data['f_top'] = $val['f_top'];
                    $data['f_left'] = $val['f_left'];
                    $data['update_time'] = time();
                    $res[] = $this->db->Name('xcx_building_floor')->update($data)->where_equalTo('id', $key)->execute();
                }
                $res = array_unique($res);
                if ($res[0] && count($res) == 1) {
                    $pdo->commit();
                    echo json_encode(['success' => true]);
                } else {
                    $pdo->rollBack();
                    echo json_encode(['success' => false, 'message' => '保存失败！']);
                }
            } catch (PDOException $e) {
                $pdo->rollback();
                echo json_encode(['success' => false, 'message' => '保存失败！']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => '参数有误！']);
        }
    }

    //初始化所有楼栋
    public function floor_init()
    {
        $id = Context::Post('id');
        $data = $this->db->Name('xcx_building_floor')->select()->where_equalTo('building_id', $id)->where_equalTo('status', 1)->execute();
        if (empty($data)) {
            echo json_encode(['success' => true, 'data' => []]);
        } else {
            echo json_encode(['success' => true, 'data' => $data]);
        }
    }

    public function floor_page()
    {
        $id = Context::Post('id');
        $curr = Context::Post('curr');
        $limit = Context::Post('limit');
        $data = $this->db->Name('xcx_building_floor')->select()->where_equalTo('building_id', $id)->page($curr, $limit)->execute();
        $count = $this->db->Name('xcx_building_floor')->where_equalTo('building_id', $id)->select('count(*)')->firstColumn();
        if (!empty($data)) {
            foreach ($data as &$val) {
                $val['kaipan_time'] = date('Y-m-d', $val['kaipan_time']);
                $val['jiaofan_time'] = date('Y-m-d', $val['jiaofan_time']);
                $val['create_time'] = date('Y-m-d H:i:s', $val['create_time']);
            }
            echo json_encode(['success' => true, 'data' => $data, 'count' => $count]);
        } else {
            echo json_encode(['success' => false, 'curr' => $curr]);
        }
    }

    public function floor_status()
    {
        $id = Context::Post('id');
        $status = Context::Post('status');
        $res = $this->db->Name('xcx_building_floor')->update(['status' => $status])->where_equalTo('id', $id)->execute();
        if ($res)
            echo json_encode(['success' => true]);
        else
            echo json_encode(['success' => false]);
    }

    public function floor_del()
    {
        $id = Context::Post('id');
        $res = $this->db->Name('xcx_building_floor')->delete()->where_equalTo('id', $id)->execute();
        if ($res) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => '删除失败']);
        }
    }

    public function floor_add()
    {
        $data['id'] = Context::Get('id');
        //获取楼盘字典
        $dict = [];
        $dictData = $this->db->Name('xcx_building_dict')->select()->where_notEqualTo('orders', 0)->where_equalTo('if_show', 1)->orderBy('orders')->execute();
        if (!empty($dictData)) {
            foreach ($dictData as $val) {
                $dict[$val['tbl_name']][] = $val['name'];
            }
        }
        $data = array_merge($data, $dict);
        return $this->render('floor_add', $data);
    }

    public function floor_doadd()
    {
        $cs = json_decode(Context::Post('parame'), true);
        $data['building_id'] = $cs['building_id'];
        $data['title'] = $cs['title'];
        $data['sales_status'] = $cs['sales_status'];
        $data['floor_number'] = $cs['floor_number'];
        $data['house_number'] = $cs['house_number'];
        $data['year_number'] = $cs['year_number'];
        $data['kaipan_time'] = strtotime($cs['kaipan_time']);
        $data['jiaofan_time'] = strtotime($cs['jiaofan_time']);
        $data['create_time'] = time();
        $data['update_time'] = time();
        $upfile = new UploadFiles(array('filepath' => BasePath . DS . 'upload' . DS . 'building_floor'));
        if ($upfile->uploadeFile('file')) {
            $arrfile = $upfile->getnewFile();
            $data['pic'] = '/upload/building_floor/' . $arrfile;
            $res = $this->db->Name('xcx_building_floor')->insert($data)->execute();
            if ($res)
                echo json_encode(['success' => true]);
            else
                echo json_encode(['success' => false, 'message' => "保存失败"]);
        } else {
            $err = $upfile->gteerror();
            echo json_encode(['success' => false, 'message' => $err]);
            exit;
        }
    }

    public function floor_edit()
    {
        $id = Context::Get('id');
        //获取楼盘字典
        $dict = [];
        $dictData = $this->db->Name('xcx_building_dict')->select()->where_notEqualTo('orders', 0)->where_equalTo('if_show', 1)->orderBy('orders')->execute();
        if (!empty($dictData)) {
            foreach ($dictData as $val) {
                $dict[$val['tbl_name']][] = $val['name'];
            }
        }
        $data['data'] = $this->db->Name('xcx_building_floor')->select()->where_equalTo('id', $id)->firstRow();
        $data['data']['kaipan_time'] = date('Y-m-d', $data['data']['kaipan_time']);
        $data['data']['jiaofan_time'] = date('Y-m-d', $data['data']['jiaofan_time']);
        $data = array_merge($data, $dict);
        return $this->render('floor_edit', $data);
    }

    public function floor_doedit()
    {
        $cs = json_decode(Context::Post('parame'), true);
        $id = $cs['id'];
        $data['title'] = $cs['title'];
        $data['sales_status'] = $cs['sales_status'];
        $data['floor_number'] = $cs['floor_number'];
        $data['house_number'] = $cs['house_number'];
        $data['year_number'] = $cs['year_number'];
        $data['kaipan_time'] = strtotime($cs['kaipan_time']);
        $data['jiaofan_time'] = strtotime($cs['jiaofan_time']);
        $data['update_time'] = time();
        if ($cs['type'] != 'empty') {
            $upfile = new UploadFiles(array('filepath' => BasePath . DS . 'upload' . DS . 'building_floor'));
            if ($upfile->uploadeFile('file')) {
                $arrfile = $upfile->getnewFile();
                $info = (new Query())->Name('xcx_building_floor')->select()->where_equalTo('id', $id)->firstRow();
                if (!empty($info)) {
                    $data['pic'] = '/upload/building_floor/' . $arrfile;
                } else {
                    echo json_encode(['success' => false, 'message' => '保存失败']);
                    exit;
                }
            } else {
                $err = $upfile->gteerror();
                echo json_encode(['success' => false, 'message' => $err]);
                exit;
            }
        }
        $res = $this->db->Name('xcx_building_floor')->update($data)->where_equalTo('id', $id)->execute();
        if ($res) {
            if ($cs['type'] != 'empty') {
                if (file_exists(BasePath . $info['pic'])) {
                    unlink(BasePath . $info['pic']);
                }
            }
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => '保存失败']);
        }
    }

    /*============================================= 单元信息 ========================================================*/
    public function unit_index()
    {
        $data['data']['id'] = Context::Get('id');
        return $this->render('unit_index', $data);
    }

    public function unit_page()
    {
        $id = Context::Post('id');
        $curr = Context::Post('curr');
        $limit = Context::Post('limit');
        $data = $this->db->Name('xcx_building_unit')->select()->where_equalTo('floor_id', $id)->page($curr, $limit)->orderBy('sort')->execute();
        $count = $this->db->Name('xcx_building_unit')->where_equalTo('floor_id', $id)->select('count(*)')->firstColumn();
        if (!empty($data)) {
            foreach ($data as &$val) {
                $val['create_time'] = date('Y-m-d H:i:s', $val['create_time']);
                $val['update_time'] = date('Y-m-d H:i:s', $val['update_time']);
            }
            echo json_encode(['success' => true, 'data' => $data, 'count' => $count]);
        } else {
            echo json_encode(['success' => false, 'curr' => $curr]);
        }
    }

    public function unit_del()
    {
        $id = Context::Post('id');
        $res = $this->db->Name('xcx_building_unit')->delete()->where_equalTo('id', $id)->execute();
        if ($res) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => '删除失败']);
        }
    }

    public function unit_add()
    {
        $data['id'] = Context::Get('id');
        return $this->render('unit_add', $data);
    }

    public function unit_doadd()
    {
        $data['floor_id'] = Context::Post('floor_id');
        $data['title'] = Context::Post('title');
        $data['sort'] = Context::Post('sort');
        $data['floor_number'] = Context::Post('floor_number');
        $data['stairs_number'] = Context::Post('stairs_number');
        $data['create_time'] = time();
        $data['update_time'] = time();
        $res = $this->db->Name('xcx_building_unit')->insert($data)->execute();
        if ($res)
            echo json_encode(['success' => true]);
        else
            echo json_encode(['success' => false, 'message' => "保存失败"]);
    }

    public function unit_edit()
    {
        $id = Context::Get('id');
        $data['data'] = $this->db->Name('xcx_building_unit')->select()->where_equalTo('id', $id)->firstRow();
        return $this->render('unit_edit', $data);
    }

    public function unit_doedit()
    {
        $id = Context::Post('id');
        $data['title'] = Context::Post('title');
        $data['sort'] = Context::Post('sort');
        $data['floor_number'] = Context::Post('floor_number');
        $data['stairs_number'] = Context::Post('stairs_number');
        $data['update_time'] = time();
        $res = $this->db->Name('xcx_building_unit')->update($data)->where_equalTo('id', $id)->execute();
        if ($res) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => '保存失败']);
        }
    }

    /*============================================= 户型信息 ========================================================*/
    public function door_index()
    {
        $data['data']['id'] = Context::Get('id');
        return $this->render('door_index', $data);
    }

    public function door_page()
    {
        $id = Context::Post('id');
        $curr = Context::Post('curr');
        $limit = Context::Post('limit');
        $data = $this->db->Name('xcx_building_door')->select()->where_equalTo('unit_id', $id)->page($curr, $limit)->orderBy('sort')->execute();
        $count = $this->db->Name('xcx_building_door')->where_equalTo('unit_id', $id)->select('count(*)')->firstColumn();
        if (!empty($data)) {
            foreach ($data as &$val) {
                $val['create_time'] = date('Y-m-d H:i:s', $val['create_time']);
                $val['update_time'] = date('Y-m-d H:i:s', $val['update_time']);
            }
            echo json_encode(['success' => true, 'data' => $data, 'count' => $count]);
        } else {
            echo json_encode(['success' => false, 'curr' => $curr]);
        }
    }

    public function door_status()
    {
        $id = Context::Post('id');
        $status = Context::Post('status');
        $res = $this->db->Name('xcx_building_door')->update(['status' => $status])->where_equalTo('id', $id)->execute();
        if ($res)
            echo json_encode(['success' => true]);
        else
            echo json_encode(['success' => false]);
    }

    public function door_is_hot()
    {
        $id = Context::Post('id');
        $is_hot = Context::Post('is_hot');
        $res = $this->db->Name('xcx_building_door')->update(['is_hot' => $is_hot])->where_equalTo('id', $id)->execute();
        if ($res)
            echo json_encode(['success' => true]);
        else
            echo json_encode(['success' => false]);
    }

    public function door_del()
    {
        $id = Context::Post('id');
        $info = (new Query())->Name('xcx_building_door')->select()->where_equalTo('id', $id)->firstRow();
        $res = $this->db->Name('xcx_building_door')->delete()->where_equalTo('id', $id)->execute();
        if ($res) {
            if (file_exists(BasePath . $info['pic'])) {
                unlink(BasePath . $info['pic']);
            }
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => '删除失败']);
        }
    }

    public function door_add()
    {
        $data['id'] = Context::Get('id');
        //获取楼盘字典
        $dict = [];
        $dictData = $this->db->Name('xcx_building_dict')->select()->where_notEqualTo('orders', 0)->where_equalTo('if_show', 1)->orderBy('orders')->execute();
        if (!empty($dictData)) {
            foreach ($dictData as $val) {
                $dict[$val['tbl_name']][] = $val['name'];
            }
        }
        $data = array_merge($data, $dict);
        return $this->render('door_add', $data);
    }

    public function door_doadd()
    {
        $cs = json_decode(Context::Post('parame'), true);
        $data['unit_id'] = $cs['unit_id'];
        $data['title'] = $cs['title'];
        $data['sales_status'] = $cs['sales_status'];
        $data['house_type'] = $cs['house_type'];
        $data['family_structure'] = $cs['family_structure'];
        $data['orientation'] = $cs['orientation'];
        $data['construction_area'] = $cs['construction_area'];
        $data['price_total'] = $cs['price_total'];
        $data['spatial_information'] = $cs['spatial_information'];
        $data['spatial_information'] = str_replace('"/upload/ueditor/image', "\"" . WX_HOST . '/upload/ueditor/image', $data['spatial_information']);
        $data['sort'] = $cs['sort'];
        $data['create_time'] = time();
        $data['update_time'] = time();
        $upfile = new UploadFiles(array('filepath' => BasePath . DS . 'upload' . DS . 'building_door'));
        if ($upfile->uploadeFile('file')) {
            $arrfile = $upfile->getnewFile();
            $data['pic'] = '/upload/building_door/' . $arrfile;
            $res = $this->db->Name('xcx_building_door')->insert($data)->execute();
            if ($res)
                echo json_encode(['success' => true]);
            else
                echo json_encode(['success' => false, 'message' => "保存失败"]);
        } else {
            $err = $upfile->gteerror();
            echo json_encode(['success' => false, 'message' => $err]);
            exit;
        }
    }

    public function door_edit()
    {
        $id = Context::Get('id');
        //获取楼盘字典
        $dict = [];
        $dictData = $this->db->Name('xcx_building_dict')->select()->where_notEqualTo('orders', 0)->where_equalTo('if_show', 1)->orderBy('orders')->execute();
        if (!empty($dictData)) {
            foreach ($dictData as $val) {
                $dict[$val['tbl_name']][] = $val['name'];
            }
        }
        $data['data'] = $this->db->Name('xcx_building_door')->select()->where_equalTo('id', $id)->firstRow();
        $data = array_merge($data, $dict);
        return $this->render('door_edit', $data);
    }

    public function door_doedit()
    {
        $cs = json_decode(Context::Post('parame'), true);
        $id = $cs['id'];
        $type = $cs['type'];
        $data['title'] = $cs['title'];
        $data['sales_status'] = $cs['sales_status'];
        $data['house_type'] = $cs['house_type'];
        $data['family_structure'] = $cs['family_structure'];
        $data['orientation'] = $cs['orientation'];
        $data['construction_area'] = $cs['construction_area'];
        $data['price_total'] = $cs['price_total'];
        $data['spatial_information'] = $cs['spatial_information'];
        $data['spatial_information'] = str_replace('"/upload/ueditor/image', "\"" . WX_HOST . '/upload/ueditor/image', $data['spatial_information']);
        $data['sort'] = $cs['sort'];
        $data['update_time'] = time();
        if ($type != 'empty') {
            $upfile = new UploadFiles(array('filepath' => BasePath . DS . 'upload' . DS . 'building_door'));
            if ($upfile->uploadeFile('file')) {
                $arrfile = $upfile->getnewFile();
                $info = (new Query())->Name('xcx_building_door')->select()->where_equalTo('id', $id)->firstRow();
                if (!empty($info)) {
                    $data['pic'] = '/upload/building_door/' . $arrfile;
                } else {
                    echo json_encode(['success' => false, 'message' => '保存失败']);
                    exit;
                }
            } else {
                $err = $upfile->gteerror();
                echo json_encode(['success' => false, 'message' => $err]);
                exit;
            }
        }
        $res = $this->db->Name('xcx_building_door')->update($data)->where_equalTo('id', $id)->execute();
        if ($res) {
            if ($type != 'empty') {
                if (file_exists(BasePath . $info['pic'])) {
                    unlink(BasePath . $info['pic']);
                }
            }
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => '保存失败']);
        }
    }

    /*============================================= 户型轮播图信息 ========================================================*/
    public function doorimg_index()
    {
        $data['data']['id'] = Context::Get('id');
        return $this->render('doorimg_index', $data);
    }

    public function doorimg_page()
    {
        $id = Context::Post('id');
        $curr = Context::Post('curr');
        $limit = Context::Post('limit');
        $data = $this->db->Name('xcx_building_doorimg')->select()->where_equalTo('door_id', $id)->page($curr, $limit)->orderBy('sort')->execute();
        $count = $this->db->Name('xcx_building_doorimg')->where_equalTo('door_id', $id)->select('count(*)')->firstColumn();
        if (!empty($data)) {
            foreach ($data as &$val) {
                $val['create_time'] = date('Y-m-d H:i:s', $val['create_time']);
                $val['update_time'] = date('Y-m-d H:i:s', $val['update_time']);
            }
            echo json_encode(['success' => true, 'data' => $data, 'count' => $count]);
        } else {
            echo json_encode(['success' => false, 'curr' => $curr]);
        }
    }

    public function doorimg_status()
    {
        $id = Context::Post('id');
        $status = Context::Post('status');
        $res = $this->db->Name('xcx_building_doorimg')->update(['status' => $status])->where_equalTo('id', $id)->execute();
        if ($res)
            echo json_encode(['success' => true]);
        else
            echo json_encode(['success' => false]);
    }

    public function doorimg_del()
    {
        $id = Context::Post('id');
        $info = (new Query())->Name('xcx_building_doorimg')->select()->where_equalTo('id', $id)->firstRow();
        $res = $this->db->Name('xcx_building_doorimg')->delete()->where_equalTo('id', $id)->execute();
        if ($res) {
            if (file_exists(BasePath . $info['img'])) {
                unlink(BasePath . $info['img']);
            }
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => '删除失败']);
        }
    }

    public function doorimg_add()
    {
        $data['id'] = Context::Get('id');
        return $this->render('doorimg_add', $data);
    }

    public function doorimg_doadd()
    {
        $cs = json_decode(Context::Post('parame'), true);
        $data['door_id'] = $cs['door_id'];
        $data['title'] = $cs['title'];
        $data['url'] = $cs['url'];
        $data['sort'] = $cs['sort'];
        $data['create_time'] = time();
        $data['update_time'] = time();
        $upfile = new UploadFiles(array('filepath' => BasePath . DS . 'upload' . DS . 'building_door'));
        if ($upfile->uploadeFile('file')) {
            $arrfile = $upfile->getnewFile();
            $data['img'] = '/upload/building_door/' . $arrfile;
            $res = $this->db->Name('xcx_building_doorimg')->insert($data)->execute();
            if ($res)
                echo json_encode(['success' => true]);
            else
                echo json_encode(['success' => false, 'message' => "保存失败"]);
        } else {
            $err = $upfile->gteerror();
            echo json_encode(['success' => false, 'message' => $err]);
            exit;
        }
    }

    public function doorimg_edit()
    {
        $id = Context::Get('id');
        $data['data'] = $this->db->Name('xcx_building_doorimg')->select()->where_equalTo('id', $id)->firstRow();
        return $this->render('doorimg_edit', $data);
    }

    public function doorimg_doedit()
    {
        $cs = json_decode(Context::Post('parame'), true);
        $id = $cs['id'];
        $type = $cs['type'];
        $data['title'] = $cs['title'];
        $data['url'] = $cs['url'];
        $data['sort'] = $cs['sort'];
        $data['update_time'] = time();
        if ($type != 'empty') {
            $upfile = new UploadFiles(array('filepath' => BasePath . DS . 'upload' . DS . 'building_door'));
            if ($upfile->uploadeFile('file')) {
                $arrfile = $upfile->getnewFile();
                $info = (new Query())->Name('xcx_building_doorimg')->select()->where_equalTo('id', $id)->firstRow();
                if (!empty($info)) {
                    $data['img'] = '/upload/building_door/' . $arrfile;
                } else {
                    echo json_encode(['success' => false, 'message' => '保存失败']);
                    exit;
                }
            } else {
                $err = $upfile->gteerror();
                echo json_encode(['success' => false, 'message' => $err]);
                exit;
            }
        }
        $res = $this->db->Name('xcx_building_doorimg')->update($data)->where_equalTo('id', $id)->execute();
        if ($res) {
            if ($type != 'empty') {
                if (file_exists(BasePath . $info['img'])) {
                    unlink(BasePath . $info['img']);
                }
            }
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => '保存失败']);
        }
    }


    function getJson($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    protected function sendGet($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    protected function sendPost($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    // 佣金变化记录
    public function getCommissionChange()
    {
        try {
            $curr = Context::Post('curr');
            $limit = Context::Post('limit');

            $count = 0;

            $bbcDb = $this->db->Name('xcx_building_building_changelog')
                ->select('bbc.old_commission, bbc.commission,bbc.old_store_manager_commission,bbc.store_manager_commission,bbc.old_team_member_commission ,bbc.team_member_commission, bbc.create_time, bb.name as building_name, bb.pic as cover, a.name, a.username', 'bbc')
                ->innerJoin('xcx_building_building', 'bb', 'bbc.building_id=bb.id')
                ->leftJoin('admin', 'a', 'a.id=bbc.admin_id')
                ->where_equalTo('type', 3);

//            if (!empty($this->gid)) {
//                if (empty($this->city)) {
//                    $bbcDb->where_equalTo('bbc.id', 0);
//                } else {
//                    $bbcDb->where_like('bb.city', "%{$this->city}%");
//                    $isCity = TRUE;
//                }
//            }

            $res = $bbcDb->page($curr, $limit)->execute();

//            $count = 0;
            if (!empty($res)) {
                foreach ($res as &$v) {
                    $v['change_num'] = bcsub($v['commission'], $v['old_commission'], 2);
                    $v['store_manager_change_num'] = bcsub($v['store_manager_commission'], $v['old_store_manager_commission'], 2);
                    $v['team_member_change_num'] = bcsub($v['team_member_commission'], $v['old_team_member_commission'], 2);
                    $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
//                    $count++;
                }
                // 总数
                $countDb = $this->db->Name('xcx_building_building_changelog')
                    ->select('count(*) as count', 'bbc');

                if (!empty($isCity)) {
                    $countDb->leftJoin('xcx_building_building', 'bb', 'bbc.building_id=bb.id')
                        ->where_like('bb.city', "%{$this->city}%");
                }
                $countData = $countDb->where_equalTo('type', 3)
                    ->firstRow();
                if (!empty($countData['count'])) {
                    $count = $countData['count'];
                }
            }
            echo json_encode(['success' => true, 'data' => $res, 'count' => $count]);
        } catch (\ErrorException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // 直辖市
    protected function getDirect()
    {
        $directCityCode = [
            '110000' => [
                'name' => '北京市',
                'code' => ['110100', '110200'],
            ], // 北京
            '120000' => [
                'name' => '天津市',
                'code' => ['120100', '120200'],
            ], // 天津
            '310000' => [
                'name' => '上海市',
                'code' => ['310100', '310200'],
            ], // 上海
            '500000' => [
                'name' => '重庆市',
                'code' => ['500100', '500200', '500300'],
            ],// 重庆
        ];

        return $directCityCode;
    }

    // 楼盘城市管理
    public function building_city_index()
    {
        return $this->render('city_index');
    }

    // 楼盘城市列表
    public function building_city_list()
    {
        try {
            $curr = !empty(Context::Post('curr')) ? Context::Post('curr') : 1;
            $limit = !empty(Context::Post('limit')) ? Context::Post('limit') : 10;

            $name = !empty(Context::Post('title')) ? Context::Post('title') : '';

            $dbSQL = $this->db->Name('xcx_city')->select('id, city_no, city_name, status, is_common, province_no');

            if (!empty($this->gid)) {
                if (empty($this->city)) {
                    $dbSQL->where_equalTo('id', 0);// 无任何城市权限，以该条件使结果为空
                } else {
                    $name = $this->city;
                }
            }

            if (!empty($name)) {
                $isDirect = FALSE;
                $direct = $this->getDirect();
                foreach ($direct as $v) {
                    if (strstr($v['name'], $name)) {
                        $isDirect = TRUE;
                        $cityCode = $v['code'];
                        break;
                    }
                }

                if ($isDirect) {
                    $dbSQL->where_in('city_no', $cityCode);
                } else {
                    $dbSQL->where_like('city_name', "%{$name}%");
                }
            }

            $city = $dbSQL->page($curr, $limit)->execute();

            $count = 0;
            if (!empty($city)) {
                $db2 = $this->db->Name('xcx_city')->select('count(*)');
                if (!empty($name)) {
                    $db2->where_like('city_name', "%{$name}%");
                }
                $count = $db2->firstColumn();

                $directCityCode = $this->getDirect();
                foreach ($city as &$v) {
                    // 直辖市转换
                    if (array_key_exists($v['province_no'], $directCityCode)) {
                        $v['city_name'] = $directCityCode[$v['province_no']]['name'] . $v['city_name'];
                    }
                }
            }
            echo json_encode(['success' => true, 'data' => $city, 'count' => $count]);
        } catch (\ErrorException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // 楼盘城市状态修改
    public function building_city_status()
    {
        try {
            $id = !empty(Context::Post('id')) ? Context::Post('id') : 0;
            $status = !empty(Context::Post('status')) ? Context::Post('status') : 0;

            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => '参数缺失']);
                exit();
            }

            $res = $this->db->Name('xcx_city')->update(['status' => $status])->where_equalTo('id', $id)->execute();

            if ($res) {
                echo json_encode(['success' => true, 'message' => '修改成功']);
            } else {
                echo json_encode(['success' => false, 'message' => '修改失败']);
            }
        } catch (\ErrorException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // 楼盘城市常用城市
    public function building_city_common()
    {
        try {
            $id = !empty(Context::Post('id')) ? Context::Post('id') : 0;
            $common = !empty(Context::Post('common')) ? Context::Post('common') : 0;

            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => '参数缺失']);
                exit();
            }

            $res = $this->db->Name('xcx_city')->update(['is_common' => $common])->where_equalTo('id', $id)->execute();

            if ($res) {
                echo json_encode(['success' => true, 'message' => '修改成功']);
            } else {
                echo json_encode(['success' => false, 'message' => '修改失败']);
            }
        } catch (\ErrorException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // 楼盘城市区域管理
    public function city_area_index()
    {
        $data['code'] = Context::Get('id');
        return $this->render('city_area_index', $data);
    }

    // 楼盘城市区域列表
    public function city_area_list()
    {
        try {
            $curr = !empty(Context::Post('curr')) ? Context::Post('curr') : 1;
            $limit = !empty(Context::Post('limit')) ? Context::Post('limit') : 10;

            $code = !empty(Context::Post('code')) ? Context::Post('code') : '';

            if (empty($code)) {
                echo json_encode(['success' => false, 'message' => '参数缺失']);
            }

            $name = !empty(Context::Post('area_name')) ? Context::Post('area_name') : '';

            $dbSQL = $this->db->Name('xcx_area')->select('id, area_no, area_name, status');

            if (!empty($name)) {
                $dbSQL->where_like('area_name', "%{$name}%");
            }

            $area = $dbSQL->where_equalTo('city_no', $code)->page($curr, $limit)->execute();

            $count = 0;
            if (!empty($area)) {
                $db2 = $this->db->Name('xcx_area')->select('count(*)');
                if (!empty($name)) {
                    $db2->where_like('area_name', "%{$name}%");
                }
                $count = $db2->where_equalTo('city_no', $code)->firstColumn();
            }
            echo json_encode(['success' => true, 'data' => $area, 'count' => $count]);
        } catch (\ErrorException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // 楼盘城市区域状态修改
    public function city_area_status()
    {
        try {
            $id = !empty(Context::Post('id')) ? Context::Post('id') : 0;
            $status = !empty(Context::Post('status')) ? Context::Post('status') : 0;

            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => '参数缺失']);
                exit();
            }

            $res = $this->db->Name('xcx_area')->update(['status' => $status])->where_equalTo('id', $id)->execute();

            if ($res) {
                echo json_encode(['success' => true, 'message' => '修改成功']);
            } else {
                echo json_encode(['success' => false, 'message' => '修改失败']);
            }
        } catch (\ErrorException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // 前端添加楼盘页面定时请求，以防session过期
    public function timingTrigger()
    {
        echo json_encode(['success' => true, 'message' => 'timingTrigger']);
    }
}