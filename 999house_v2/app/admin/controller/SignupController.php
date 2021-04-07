<?php


namespace app\admin\controller;

use app\common\base\AdminBaseController;
use app\server\marketing\Signup;


class SignupController extends AdminBaseController
{

    public function getList()
    {
        $data = $this->request->param();
        $where = [
            'name'       => $data['name'],
            'start_time' => !empty($data['startdate']) ? strtotime($data['startdate']) : '',
            'end_time'   => !empty($data['enddate']) ? strtotime($data['enddate'] . ' +1 day') : ''
        ];

        // 城市
        if (!empty($data['region_no'])) {
            if (-1 == $data['region_no']) {// 搜索当前全部城市
                $regionRes = $this->getMyCity();

                $cityIds = !empty($regionRes['data']) ? array_column($regionRes['data'], 'id') : [];

                $where['region_no'] = $cityIds;
            } else {
                $where['region_no'] = $data['region_no'];
            }
        }

        $rs = (new Signup())->getList($where)['result'];
        if (empty($rs['list'])) {
            $rs = [];
        } else {
            foreach ($rs['list'] as &$item) {
                $item['cover_url'] = !empty($item['share_img']) ? $this->getFormatImgs($item['share_img']) : [];
            }
            unset($item);
        }

        $this->success($rs);
    }

    //删除
    public function del()
    {
        $data = $this->request->param();
        $rs = (new Signup())->del(intval($data['id']));
        $this->success($rs);
    }

    public function edit()
    {
        $data = $this->request->param();
        $data['id'] = intval($data['id']);
        $data['name'] = trim_all($data['name']);
        $data['forid'] = intval($data['forid']);

        if (empty($data['name'])) {
            $this->error('请填写标题');
        }
        if (empty($data['forid'])) {
            $this->error('请选择数据');
        }
        $data["start_time"] = strtotime($data["start_time"]);
        $data["end_time"] = strtotime($data["end_time"]);
        if (empty($data["start_time"]) || empty($data["end_time"])) {
            $this->error('请设置有效的时间范围');
        }
        if ($data["start_time"] >= $data["end_time"]) {
            $this->error('开始时间超过结束时间');
        }

        $data['region_no'] = intval($data['region_no']);
        if (empty($data['region_no'])) {
            $this->error('缺少区域参数');
        }

        $indata = [
            'name'        => $data['name'],
            'subname'     => $data['subname'],
            'desc'        => $data["desc"],
            'forid'       => $data["forid"],
            'start_time'  => $data["start_time"],
            'end_time'    => $data["end_time"],//has_num
            'region_no'   => $data['region_no'],
            "share_title" => $data["share_title"],
            "share_desc"  => $data["share_desc"],
            'share_img'   => !empty($data['cover_url'][0]['url']) ? $data['cover_url'][0]['url'] : "",
        ];

        if ($data['id']) {
            $rs = (new Signup())->edit($data['id'], $indata);
        } else {
            $rs = (new Signup())->add($indata);
        }
        if ($rs['code'] == 1) {
            $this->success();
        } else {
            $this->error();
        }
    }


    public function getLogList()
    {
        $data = $this->request->param();
        $data['signup_id'] = intval($data['signup_id']);
        if (empty($data['signup_id']) || empty($data['region_no'])) {
            return $this->success([]);
        }
        $where = [
            'signup_id' => $data['signup_id'],
            'startdate' => $data['startdate'],
            'enddate'   => $data['enddate'],
            'nickname'  => $data ['nickname'],
            'type'      => $data ['type'],
            'do_export' => empty($data['do_export']) ? 0 : $data['do_export']
        ];

        $rs = (new Signup())->getLogList($where)['result'];
        if (empty($rs['list'])) {
            $rs = [];
        }
        $this->success($rs);
    }
}