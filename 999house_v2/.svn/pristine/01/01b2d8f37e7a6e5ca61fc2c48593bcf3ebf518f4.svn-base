<?php


namespace app\admin\controller;

use app\common\base\AdminBaseController;
use app\common\MyConst;
use app\server\marketing\Vote;


class VoteController extends AdminBaseController
{

    public function getList()
    {
        $data = $this->request->param();
        $where = [
            'name'   => $data['name'],
            'status' => $data['status'],
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

        $rs = (new Vote())->getList($where)['result'];
        if (empty($rs['list'])) {
            $rs = [];
        } else {
            //print_r($rs['list']);
            foreach ($rs['list'] as &$v) {
                $v['context_rule'] = htmlspecialchars_decode($v['context_rule']);
                $v['cover_url'] = !empty($v['cover_url']) ? $this->getFormatImgs($v['cover_url']) : [];
                $v['time_show_status'] = 0;
                if (!empty($v['start_time']) && !empty($v['end_time'])) {
                    $v['time_show_status'] = 1;
                }
                $v['more_set'] = json_decode($v['more_set'], true);

                $v['wx_h5'] = '/9house/pages/12/vote.html?active_id='.$v['id'].'&region_no='.$v['region_no'];
            }
        }
        $this->success($rs);
    }

    public function enable(){
        $id     = $this->request->post('id');
        $status = $this->request->post('status');

        if(empty($id)){
            $this->error('请选择药操作的活动');
        }

        $result = (new Vote())->enable($id,$status);

        if($result['code'] == 1){
            return $this->success();
        }else{
            return $this->success();
        }
    }

    public function edit()
    {
        $data = $this->request->param();
        $data['id'] = intval($data['id']);
        $data['name'] = trim_all($data['name']);
        $data['time_show_status'] = intval($data['time_show_status']);

        if (empty($data['name'])) {
            $this->error('请填写名称');
        }

        $data['region_no'] = intval($data['region_no']);
        if (empty($data['region_no'])) {
            $this->error('缺少区域参数');
        }
        $data['type'] = intval($data['type']);
        if ($data['type'] == 1) {//无底色设置
            $data['bgcolor'] = '';
        }
        if (empty($data['start_time']) || empty($data['end_time'])) {
            $this->error('请设置时间范围');
        }
        $data['start_time'] = strtotime($data['start_time']);
        $data['end_time'] = strtotime($data['end_time']);
        if ($data['start_time'] >= $data['end_time']) {
            $this->error('请设置正确的时间范围');
        }

        $indata = [
            'name'          => $data['name'],
            'status'        => intval($data["status"]),
            'page_title'    => $data["page_title"],
            'page_keywords' => $data["page_keywords"],
            'page_desc'     => $data["page_desc"],
            'region_no'     => $data['region_no'],
            'cover_url'     => !empty($data['cover_url'][0]) && !empty($data['cover_url'][0]['url']) ? $data['cover_url'][0]['url'] : "",
            'bgcolor'       => strval($data['bgcolor']),
            'fontbgcolor'   => strval($data['fontbgcolor']  ),
            'start_time'    => $data['start_time'],
            'end_time'      => $data['end_time'],
            'context_rule'  => $data['context_rule'],
            'more_set'      => json_encode($data['more_set'], JSON_UNESCAPED_UNICODE)
        ];

        if ($data['id']) {
            $rs = (new Vote())->edit($data['id'], $indata);
        } else {
            $rs = (new Vote())->add($indata);
        }
        if ($rs['code'] == 1) {
            $this->success();
        } else {
            $this->error();
        }
    }

    //删除
    public function del()
    {
        $data = $this->request->param();
        $data['id'] = intval($data['id']);

        if (empty($data['id'])) {
            $this->error('缺失参数');
        }
        $rs = (new Vote())->del($data['id']);
        $this->success($rs);
    }

    //============ 投票内容配置操作 start ===============//
    public function getConstModules()
    {
        $moduleList = MyConst::MODULE_LIST;

        $this->success([
            'banner'      => $moduleList['banner'],
            'estates_new' => $moduleList['estates_new']
        ]);
    }

    public function getDetails()
    {
        $data = $this->request->param();

        $rs = (new Vote())->getDetails([
            'vote_id' => $data['vote_id']
        ])['result'];
        foreach ($rs as $key => &$value){
            $value['cover_imgs'] = empty($value['img']) ? [] : $this->getFormatImgs($value['img']) ;
            $value['introduction'] = htmlspecialchars_decode($value['introduction']);
        }
        $this->success([
            'list' => $rs
        ]);
    }

    public function getBanner()
    {
        $data = $this->request->param();
        $data['vote_id'] = intval($data['vote_id']);
        if (empty($data['vote_id'])) {
            $this->error('缺失参数');
        }
        $vote = (new Vote())->getInfo(['id' => $data['vote_id']], 'banner')['result'];
        if (!empty($vote['banner'])) {
            $vote['banner'] = json_decode($vote['banner'], true);
            foreach ($vote['banner'] as &$v) {
                $v['cover_imgs'] = !empty($v['cover_imgs']) ? $this->getFormatImgs($v['cover_imgs']) : [];
            }
            unset($v);
        } else {
            $vote['banner'] = [];
        }

        $this->success([
            'list' => $vote['banner']
        ]);
    }

    public function editBanner()
    {
        $data = $this->request->param();
        $data['vote_id'] = intval($data['vote_id']);
        if (empty($data['vote_id'])) {
            $this->error('缺失参数');
        }
        $cover_imgs = $data['cover_imgs'] && $data['cover_imgs'][0] && $data['cover_imgs'][0]['url'] ? $data['cover_imgs'][0]['url'] : '';
        if (empty($cover_imgs)) {
            $this->error('请上传图片');
        }

        $vote = (new Vote())->getInfo(['id' => $data['vote_id']], 'region_no,banner')['result'];
        if (empty($vote['region_no'])) {
            $this->error('数据错误');
        }
        $this->checkCanCity($vote['region_no']);

        $vote['banner'] = !empty($vote['banner']) ? json_decode($vote['banner'], true) : [];
        $forids = array_unique(array_keys($vote['banner']));
        if (!empty($data['forid'])) {//修改，进行变更修改时去除旧的
            if (!in_array($data['forid'], $forids)) {
                $this->error('参数id错误');
            }
        } else {//添加
            $data['forid'] = intval(max($forids)) + 1;
        }

        $vote['banner'][$data['forid']] = [
            'module'     => $data['module'],
            'forid'      => $data['forid'],
            'forhref'    => $data['forhref'],//跳转地址
            'forsort'    => $data['forsort'],
            'cover_imgs' => $cover_imgs,
        ];

        $rs = (new Vote())->edit($data['vote_id'], [
            'banner' => json_encode($vote['banner'])
        ]);
        if ($rs['code'] == 1) {
            $this->success();
        } else {
            $this->error($rs['msg']);
        }
    }

    public function delBanner()
    {
        $data = $this->request->param();
        $data['vote_id'] = intval($data['vote_id']);
        $data['forid'] = intval($data['forid']);

        if (empty($data['vote_id'])) {
            $this->error('缺失参数');
        }
        if (empty($data['forid'])) {
            $this->error('缺失参数');
        }

        $vote = (new Vote())->getInfo(['id' => $data['vote_id']], 'region_no,banner')['result'];
        if (empty($vote['region_no'])) {
            $this->error('数据错误');
        }
        $this->checkCanCity($vote['region_no']);
        $vote['banner'] = !empty($vote['banner']) ? json_decode($vote['banner'], true) : [];
        unset($vote['banner'][$data['forid']]);
        if (empty($vote['banner'])) {
            $vote['banner'] = '';
        } else {
            $vote['banner'] = json_encode($vote['banner'], JSON_UNESCAPED_UNICODE);
        }

        $rs = (new Vote())->edit($data['vote_id'], [
            'banner' => $vote['banner']
        ]);
        if ($rs['code'] == 1) {
            $this->success();
        } else {
            $this->error($rs['msg']);
        }
    }

    public function editDetail()
    {
        $data = $this->request->param();
        $data['id'] = intval($data['id']);
        $data['vote_id'] = intval($data['vote_id']);
        $data['forid'] = intval($data['forid']);
        $data['vote_num'] = intval($data['vote_num']);

        if (empty($data['vote_id'])) {
            $this->error('缺失参数');
        }
//        if (empty($data['forid'])) {
//            $this->error('请选择数据');
//        }
        if (empty($data['module'])) {
            $this->error('缺失模块标识参数');
        }

        $cover_imgs = $data['cover_imgs'] && $data['cover_imgs'][0] && $data['cover_imgs'][0]['url'] ? $data['cover_imgs'][0]['url'] : '';
        if (empty($cover_imgs)) {
            $this->error('请上传图片');
        }

        $vote = (new Vote())->getInfo(['id' => $data['vote_id']], 'region_no')['result'];
        if (empty($vote['region_no'])) {
            $this->error('数据错误');
        }
        $this->checkCanCity($vote['region_no']);

        $indata = [
            'forid'        => $data['forid'],
            'vote_id'      => $data['vote_id'],
            'forname'      => $data['forname'],
            'module'       => $data['module'],
            'forsort'      => $data['forsort'],
            'vote_num'     => $data['vote_num'],
            'region_no'    => $data['region_no'],
            'introduction' => $data['introduction'],
            'share_desc'   => $data['share_desc'],
            'img'          => $cover_imgs,

        ];

        if (!empty($data['id'])) {
            $rs = (new Vote())->editDetail($data['id'], $indata);
        } else {
            $rs = (new Vote())->addDetail($indata);
        }

        if ($rs['code'] == 1) {
            $this->success();
        } else {
            $this->error($rs['msg']);
        }
    }

    public function delDetail()
    {
        $data = $this->request->param();
        $data['id'] = intval($data['id']);

        if (empty($data['id'])) {
            $this->error('缺失参数');
        }

        $rs = (new Vote())->delDetail($data['id']);
        if ($rs['code'] == 1) {
            $this->success();
        } else {
            $this->error($rs['msg']);
        }
    }

    public function changeDetailSort()
    {
        $data = $this->request->param();
        $data['id'] = intval($data['id']);
        $data['forsort'] = intval($data['forsort']);

        $rs = (new Vote())->editDetail($data['id'], [
            'forsort' => $data['forsort']
        ]);
        if ($rs['code'] == 1) {
            $this->success();
        } else {
            $this->error($rs['msg']);
        }
    }
    //============ 投票内容配置操作 end ===============//

    //查看投票记录
    public function getLogList()
    {
        $data = $this->request->param();
        $data['vote_detail_id'] = intval($data['vote_detail_id']);
        if (empty($data['vote_detail_id']) || empty($data['region_no'])) {
            return $this->success([]);
        }
        $where = [
            'vote_detail_id' => $data['vote_detail_id'],
            'startdate'      => $data['startdate'],
            'enddate'        => $data['enddate'],
        ];

        $rs = (new Vote())->getLogList($where)['result'];
        if (empty($rs['list'])) {
            $rs = [];
        }
        $this->success($rs);
    }
}