<?php


namespace app\admin\controller;

use app\common\base\AdminBaseController;
use app\common\MyConst;
use app\server\marketing\Subject;
use app\server\user\BrowseRecords;


class SubjectController extends AdminBaseController
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

        $rs = (new Subject())->getList($where)['result'];
        if (empty($rs['list'])) {
            $rs = [];
        } else {
            foreach ($rs['list'] as &$v) {
                $v['context_rule'] = htmlspecialchars_decode($v['context_rule']);
                //list($v['cover_id'], $v['cover_url']) = $this->getImgsIdAndUrl($v['banner']);
                $v['cover_url'] = !empty($v['cover_url']) ? $this->getFormatImgs($v['cover_url']) : [];
                $v['bg_img'] = !empty($v['bg_img']) ? $this->getFormatImgs($v['bg_img']) : [];
                $v['time_show_status'] = 0;
                if (!empty($v['start_time']) && !empty($v['end_time'])) {
                    $v['time_show_status'] = 1;//是否显示活动的开始结束时间控件
                }
                $v['wx_h5'] = '/9house/pages/12/apply.html?active_id=' . $v['id'] . '&source=wx_h5';// . '&region_no=' . $v['region_no'];
                $v['douyin'] = '/9house/pages/12/apply.html?active_id=' . $v['id'] . '&source=douyin';// . '&region_no=' . $v['region_no'];
            }
        }
        $this->success($rs);
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

        $indata = [
            'name'          => $data['name'],
            'phone'         => $data['phone'],
            'status'        => intval($data["status"]),
            'sign_up'       => intval($data["sign_up"]),
            'page_title'    => $data["page_title"],
            'page_keywords' => $data["page_keywords"],
            'page_desc'     => $data["page_desc"],
            'region_no'     => $data['region_no'],
            'context_rule'  => $data['context_rule'],
            'cover_url'     => !empty($data['cover_url']) ? implode(',', array_column($data['cover_url'], 'url')) : "",
            'bg_img'        => !empty($data['bg_img']) ? implode(',', array_column($data['bg_img'], 'url')) : "",
            'bgcolor'       => strval($data['bgcolor']),
            'type'          => intval($data['type'])
        ];

        if (!empty($data['time_show_status'])) {
            if (empty($data['start_time']) || empty($data['end_time'])) {
                $this->error('请设置时间范围');
            }
            $indata['start_time'] = strtotime($data['start_time']);
            $indata['end_time'] = strtotime($data['end_time']);

            if ($indata['start_time'] >= $indata['end_time']) {
                $this->error('请设置正确的时间范围');
            }
        } else {
            $indata['start_time'] = 0;
            $indata['end_time'] = 0;
        }

        if ($data['id']) {
            $rs = (new Subject())->edit($data['id'], $indata);
        } else {
            $rs = (new Subject())->add($indata);
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
        $rs = (new Subject())->del(intval($data['id']));
        $this->success($rs);
    }

    //============ 主题专题配置操作 ===============//
    public function getConfigList()
    {
        $data = $this->request->param();
        $moduleList = MyConst::MODULE_LIST;

        $moduleList2 = [];
        //获取可操作的模块
        switch ($data['subject_type']) {
            case '0'://页面版面1
                $moduleList2 = [
                    'banner'      => $moduleList['banner'],
                    'estates_new' => $moduleList['estates_new'],
                    'label'       => $moduleList['label']
                ];
                break;
            case '1'://页面版面2
                $moduleList2 = [
                    'banner'      => $moduleList['banner'],
                    'estates_new' => $moduleList['estates_new'],
                    'label'       => $moduleList['label']
                ];
                break;
            case '2' ://页面 3
                $moduleList2 = [
                    'banner'      => $moduleList['banner'],
                    'estates_new' => $moduleList['estates_new'],
                    'label'       => $moduleList['label']
                ];
                break;
        }
        $rs = (new Subject())->getInfo([
            'id' => $data['id']
        ], 'config,region_no')['result'];

        //获取浏览量 -start
        $idArray = array_keys($rs['config']['estates_new']);

        $brWhere = [
            ['building_id', 'in', $idArray],
            ['type', '=', 2]
        ];
        $data = [];
        if (!empty($idArray)) {
            $table_name = (new BrowseRecords())->getTableName();
            $data = $this->db->name($table_name)->where($brWhere)->field('building_id,count(building_id) as number')->group('building_id')->select()->toArray();
        }
        //获取浏览量 -end

        $data = array_column($data, 'number', 'building_id');
        if (!empty($rs['config']['estates_new'])) {

            foreach ($rs['config']['estates_new'] as &$v) {
                if (empty($data[$v['forid']])) {
                    $v['look_number'] = 0;
                } else {
                    $v['look_number'] = $data[$v['forid']];
                }
                $v['cover_imgs'] = !empty($v['cover_imgs']) ? explode(',', $v['cover_imgs']) : [];
            }
            unset($v);
        }
        if (!empty($rs['config']['banner'])) {
            foreach ($rs['config']['banner'] as &$v2) {
                $v2['cover_imgs'] = !empty($v2['cover_imgs']) ? $this->getFormatImgs($v2['cover_imgs']) : [];
            }
            unset($v2);
        }


        $this->success([
            'moduleList' => $moduleList2,
            'list'       => $rs['config'] ?? [],
            'region_no'  => $rs['region_no'],
        ]);
    }

    public function editConfig()
    {
        $data = $this->request->param();
        $data['id'] = intval($data['id']);
        if (empty($data['id'])) {
            $this->error('缺失参数');
        }
        if (empty($data['module'])) {
            $this->error('缺失模块标识参数');
        }
        $data['doforid'] = intval($data['doforid']);//是否有值时标识添加或者修改，为要修改的id

        if ($data['module'] == 'banner') {//banner模块操作
            //找出当前banner里面图
            $list = (new Subject())->getInfo([
                'id' => $data['id']
            ], 'config')['result'];
            $list = $list['config'] ?? [];
            if (empty($list[$data['module']])) {
                $list[$data['module']] = [];
            }

            $forids = array_unique(array_keys($list[$data['module']]));
            if (!empty($data['doforid'])) {//修改，进行变更修改时去除旧的
                $data['forid'] = $data['doforid'];
                if (!in_array($data['forid'], $forids)) {
                    $this->error('参数id错误');
                }
            } else {//添加
                $data['forid'] = intval(max($forids)) + 1;
            }
            $cover_imgs = $data['cover_imgs'] && $data['cover_imgs'][0] && $data['cover_imgs'][0]['url'] ? $data['cover_imgs'][0]['url'] : '';
            if (empty($cover_imgs)) {
                $this->error('请上传图片');
            }

            $list[$data['module']][$data['forid']] = [
                'forid'      => $data['forid'],
                'forhref'    => $data['forhref'],//跳转地址
                'forsort'    => $data['forsort'],
                'cover_imgs' => $cover_imgs,
            ];
        } elseif ($data['module'] == 'label') {

            //找出当前banner里面图
            $list = (new Subject())->getInfo([
                'id' => $data['id']
            ], 'config')['result'];

            $list = empty($list['config']) ? [] : $list['config'];

            if (empty($list[$data['module']])) {
                $list[$data['module']] = [];
            }

            $forids = array_unique(array_keys($list[$data['module']]));
            if (!empty($data['doforid'])) {//修改，进行变更修改时去除旧的
                $data['forid'] = $data['doforid'];
                if (!in_array($data['forid'], $forids)) {
                    $this->error('参数id错误');
                }
            } else {//添加
                if(empty($forids)){
                    $data['forid'] = 1;
                }else{
                    $data['forid'] = intval(max($forids)) + 1;
                }

            }

            $list[$data['module']][$data['forid']] = [
                'forid'   => $data['forid'],
                'forname' => $data['forname'],
                'status'  => $data['status'],
            ];

        } else {//非banner模块操作
            $data['forid'] = intval($data['forid']);//新的id
            if (empty($data['forid'])) {
                $this->error('请选择数据');
            }

            $list = (new Subject())->getInfo([
                'id' => $data['id']
            ], 'config')['result'];
            $list = !empty($list['config']) ? $list['config'] : [];
            if (!empty($list[$data['module']][$data['forid']])) {
                if (empty($data['doforid']) || (!empty($data['doforid']) && $data['doforid'] != $data['forid'])) {//添加或者修改当前记录时
                    $this->error('该记录已存在');
                }
            }

            if (!empty($data['doforid']) && $data['doforid'] != $data['forid']) {//进行变更修改时去除旧的
                unset($list[$data['module']][$data['doforid']]);
            }
            $list[$data['module']][$data['forid']] = [
                'forname'    => $data['forname'],
                'forid'      => $data['forid'],
                'forsort'    => $data['forsort'],
                'label_id'   => $data['label_id'],
                'cover_imgs' => !empty($data['cover_imgs']) ? implode(',', $data['cover_imgs']) : "",
            ];
        }

        $indata = [
            'config' => json_encode($list, JSON_UNESCAPED_UNICODE)
        ];

        $rs = (new Subject())->edit($data['id'], $indata);
        if ($rs['code'] == 1) {
            $this->success();
        } else {
            $this->error($rs['msg']);
        }
    }

    public function delConfig()
    {
        $data = $this->request->param();
        $data['id'] = intval($data['id']);
        $data['forid'] = intval($data['forid']);
        if (empty($data['id'])) {
            $this->error('缺失参数');
        }
        if (empty($data['forid'])) {
            $this->error('请选择数据');
        }

        $list = (new Subject())->getInfo([
            'id' => $data['id']
        ], 'config')['result'];
        $list = !empty($list['config']) ? $list['config'] : [];
        if (empty($list) || empty($list[$data['module']][$data['forid']])) {
            $this->error('操作失败，无该记录数据');
        }

        unset($list[$data['module']][$data['forid']]);
        if (empty($list[$data['module']])) {
            unset($list[$data['module']]);
        }
        $indata = [
            'config' => !empty($list) ? json_encode($list, JSON_UNESCAPED_UNICODE) : ''
        ];

        $rs = (new Subject())->edit($data['id'], $indata);
        if ($rs['code'] == 1) {
            $this->success();
        } else {
            $this->error($rs['msg']);
        }
    }

    public function changeConfigSort()
    {
        $data = $this->request->param();
        $data['id'] = intval($data['id']);
        $data['forid'] = intval($data['forid']);
        if (empty($data['id'])) {
            $this->error('缺失参数');
        }
        if (empty($data['forid'])) {
            $this->error('请选择数据');
        }

        $list = (new Subject())->getInfo([
            'id' => $data['id']
        ], 'config')['result'];
        $list = !empty($list['config']) ? $list['config'] : [];
        if (empty($list) || empty($list[$data['module']][$data['forid']])) {
            $this->error('操作失败，无该记录数据');
        }
        $info = $list[$data['module']][$data['forid']];

        if (empty($data['ftype'])) {
            $list[$data['module']][$data['forid']] = [
                'forname' => $info['forname'],
                'forid'   => $info['forid'],
                'forsort' => $data['forsort'],
            ];
        } else {
            $list[$data['module']][$data['forid']] = [
                'forname' => $info['forname'],
                'forid'   => $info['forid'],
                'status'  => $data['forsort'],
            ];
        }


        $indata = [
            'config' => !empty($list) ? json_encode($list, JSON_UNESCAPED_UNICODE) : ''
        ];

        $rs = (new Subject())->edit($data['id'], $indata);
        if ($rs['code'] == 1) {
            $this->success();
        } else {
            $this->error($rs['msg']);
        }
    }

    public function labelData()
    {
        $param = $this->request->param();
        $rs = (new Subject())->getInfo([
            'id' => $param['id']
        ], 'config,region_no')['result'];

        if (empty($rs['config']['label'])) {
            return $this->success();
        }

        $rs = $rs['config']['label'];
        $data = [];
        foreach ($rs as $key => $value) {
            if ($value['status'] != 2) {
                $data[$key]['id'] = $value['forid'];
                $data[$key]['name'] = $value['forname'];
            }
        }
        $this->success($data);
    }

}