<?php

namespace app\server\marketing;

use app\common\base\ServerBase;
use think\Exception;
use think\facade\Db;

class Subject extends ServerBase
{
    //显示所有
    public function getList($search = [])
    {
        $where = [];
        if (!in_array($search['status'], ['0', '1', '2', '3'])) {
            unset($search['status']);
        }
        if (isset($search['status'])) {//状态
            $where[] = ['status', '=', $search['status']];
        }

        if (!empty($search['region_no'])) {
            if (is_array($search['region_no'])) {
                $where[] = ['region_no', 'in', $search['region_no']];
            } else {
                $where[] = ['region_no', '=', $search['region_no']];
            }
        }
        if(!empty($search['activity_type']) ){
            $where[] = ['activity_type', '=', $search['activity_type']];
        }else{
            $where[] = ['activity_type', '=', 0];
        }

        if (!empty($search['show_on_list'])) {
            if (in_array($search['show_on_list'], ['estates_new', 'estates_good'])) {
                $where[] = ['', 'exp', Db::raw("FIND_IN_SET('{$search['show_on_list']}', show_on_list)")];
            }
        }


        $order = ['id' => 'desc'];

        $list = $this->db->name("subject")->where($where)->order($order)->select()->toArray();
        if (empty($list)) {
            $result['list'] = [];
        } else {
//            foreach ($list as &$value) {
//                $value['banner'] = getRealStaticPath($value["banner"]);
//            }
//            unset($value);
            $result['list'] = $list;
        }
        return $this->responseOk($result);
    }

    public function getInfo($search = [], $field = '*')
    {
        $where = [];
        if (!in_array($search['status'], ['0', '1', '2', '3'])) {
            unset($search['status']);
        }
        if (isset($search['status'])) {//状态
            $where[] = ['status', '=', $search['status']];
        }
        $where[] = ['id', '=', $search['id']];

        $info = $this->db->name("subject")->field($field)->where($where)->find();
        if (!empty($info['config'])) {
            $info['config'] = json_decode($info['config'], true);
        }else{
            $info['config'] = '';
        }
        return $this->responseOk($info);
    }

    //添加操作
    public function add($data)
    {
        try {
            $id = $this->db->name("subject")->insertGetId([
                'name'          => $data['name'],
                'status'        => intval($data["status"]),
                'page_title'    => $data["page_title"],
                'page_keywords' => $data["page_keywords"],
                'page_desc'     => $data["page_desc"],
                'region_no'     => $data['region_no'],
                'config'        => empty($data['config']) ? '' : $data['config'],
                'type'          => intval($data['type']),
//                'banner'=> $data['banner'],
                'bgcolor'       => strval($data['bgcolor']),
                'cover_url'       => strval($data['cover_url']),
                'update_time'   => 0,
                'create_time'   => time(),
                'start_time'    => $data['start_time'],
                'end_time'      => $data['end_time'],
                'bg_img'        => $data['bg_img'],
                'sign_up'       => $data['sign_up'],
                'phone'         => $data['phone'],
                'context_rule'  => $data['context_rule'],
                'activity_type' => $data['activity_type']?? 3,
                'kf_qrcode'     => $data['kf_qrcode']?? '',
                'gzh_qrcode'    => $data['gzh_qrcode']?? '',

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

            //区域发生变化时候配置重置
            $has = $this->db->name('subject')->field('id,region_no')->where(['id' => $id])->find();
            if (isset($data['region_no']) && !empty($has['id']) && $has['region_no'] != $data['region_no']) {
                $data['config'] = '';
            }
            if (!empty($data['bgcolor'])) {
                $data['bgcolor'] = strval($data['bgcolor']);
            }

            $data['update_time'] = time();
            $rs = $this->db->name('subject')->where(['id' => $id])->update($data);
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

            $res = $this->db->name("subject")->where("id", $id)->delete();
            if ($res) {

                return $this->responseOk($res);
            } else {
                throw new Exception('操作失败');
            }

        } catch (Exception $e) {
            return $this->responseFail(['code' => 0, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * 通过时间获取数量
     */
    public function getCountByTime($data){
        if($data['city_no']){
            return 0;
        }
        $count = $this->db->name('subject')
            ->where('status','=',1)
            ->where('start_time','>',time())
            ->where('region_no','in', $data['city_no'])
            ->where('end_time','<',time())
            ->whereOr('start_time','=',0)
            ->count();

        return $count;
    }


}