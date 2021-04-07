<?php


namespace app\admin\controller;

use app\common\base\AdminBaseController;
use app\common\MyConst;
use app\admin\validate\EstatesValidate;
use app\server\admin\Chat;
use app\server\admin\News;
use app\server\estates\BuildingPhotos;
use app\server\estates\Estatesnew;
use app\server\estates\EstatesnewBuilding;
use app\server\estates\EstatesnewHouse;
use app\server\estates\EstatesnewNews;
use app\server\estates\EstatesnewPrice;
use app\server\estates\EstatesnewTime;
use app\server\estates\Tag;
use app\server\user\Attention;
use Exception;

class EstatesController extends AdminBaseController
{
    //=============== 标签操作 ==================//
    public function getTagList()
    {
        $data = $this->request->param();
        $where = [
            'name'   => $data['name'],
            'status' => $data['status'],
            'type'   => $data['type']
        ];
        $rs = (new Tag())->getList($where)['result'];
        if (empty($rs['list'])) {
            $rs = [];
        } else {
            foreach ($rs['list'] as &$v) {
                //list($v['cover_id'], $v['cover_url']) = $this->getImgsIdAndUrl($v['cover']);
                $v['cover_url'] = !empty($v['cover']) ? $this->getFormatImgs($v['cover']) : [];
            }
        }
        $this->success($rs);
    }

    //修改状态
    public function enableTag()
    {
        $data = $this->request->param();

        $rs = (new Tag())->edit(intval($data['id']), ['status' => intval($data['status'])]);
        if ($rs['code'] == 1) {
            $this->success();
        } else {
            $this->error($rs['msg']);
        }
    }

    //删除
    public function delTag()
    {
        $data = $this->request->param();
        $rs = (new Tag())->del(intval($data['id']));
        $this->success($rs);
    }

    public function editTag()
    {
        $data = $this->request->param();
        $data['id'] = intval($data['id']);
        $data['name'] = trim_all($data['name']);

        if (empty($data['name'])) {
            $this->error('请填写标签名称');
        }

        $cover_imgs = $data['cover_url'] && $data['cover_url'][0] && $data['cover_url'][0]['url'] ? $data['cover_url'][0]['url'] : '';
        if (empty($cover_imgs)) {
            $this->error('请上传图片');
        }
        $indata = [
            'name'   => $data['name'],
            'type'   => $data['type'],
            'status' => $data['status'],
            'cover'  => $cover_imgs,
        ];

        if ($data['id']) {
            $rs = (new Tag())->edit($data['id'], $indata);
        } else {
            $rs = (new Tag())->add($indata);
        }

        if ($rs['code'] == 1) {
            $this->success();
        } else {
            $this->error($rs['msg']);
        }
    }

    // 标签批量操作 -删除-显示-隐藏
    public function tagBatchEdit(){
        $params = $this->request->param();
        $res = (new Tag())->tagBatchEdit($params);
        if ($res['code'] == 1) {
            $this->success();
        } else {
            $this->error($res['msg']);
        }
    }



    //=============== 标签操作 end==================//

    //=============== 通用常量操作 ==================//
    //返回楼盘的建筑用途列表
    public function getHousePurpose()
    {
        $rs = MyConst::HOUSE_PURPOSE;
        $this->success($rs);
    }

    //返回楼盘的相册类型列表
    public function getBuildingPhotosCategory()
    {
        $rs = MyConst::BUILDINGPHOTOS_CATEGORYS;
        $this->success($rs);
    }

    //返回朝向列表
    public function getOrientation()
    {
        $rs = MyConst::ORIENTATION;
        $this->success($rs);
    }

    //返回几居室列表
    public function getRooms()
    {
        $rs = MyConst::ROOMS;
        $this->success($rs);
    }

    //返回新房销售状态列表
    public function getEstatesNewSaleStatus()
    {
        $rs = MyConst::ESTATESNEW_SALE_STATUS;
        $this->success($rs);
    }
    //=============== 通用常量操作 end==================//


    //=============== 新房操作 ==================//
    /**
     * 楼盘列表
     */
    public function getEstatesnewList()
    {
        $data = $this->request->param();

        // 分页
        $pageSize = !empty($data['page_size']) ? $data['page_size'] : 20;
        if ($pageSize > 100) {
            $this->error('获取数量超出限制');
        }
        // 字段
        $fields = 'en.id, en.list_cover, en.name, en.status, en.city_str, en.num_read, en.num_collect, en.num_share, en.sort, en.recommend, en.num_read_real, en.num_collect_real, en.num_share_real, en.sale_status, num_read_search, num_read_search_real';

        // 条件
        $search = [
            'name'          => trim_all($data['name']),
            'status'        => $data['status'] ?? -1,
            'sale_status'   => $data['sale_status'] ?? -1,
            'recommend'     => $data['recommend'] ?? -1,
            'area'          => $data['area'] ?? "",
            'business_area' => $data['business_area'] ?? "",
            'street'        => $data['street'] ?? "",
            'sort'          => 'desc',
            'from'          => $data['from'] ?? 'normal',// 来源 normal-正常列表 innerTable-内嵌列表
        ];

        // 城市
        if (empty($data['region_no']) || -1 == $data['region_no']) {// 搜索当前全部城市
            $regionRes = $this->getMyCity();

            $cityIds = !empty($regionRes['data']) ? array_column($regionRes['data'], 'id') : [];

            $search['city'] = $cityIds;
        } else {
            $search['city'] = $data['region_no'];
        }

        $rs = (new Estatesnew())->getList($search, $fields, $pageSize);
        // var_dump($this->db->getLastSql());

        if (!empty($rs['code'])) {
            $res = $rs['result'];
            if (empty($res['list'])) {
                $res = [];
            } else {
                foreach ($res['list'] as &$v) {
                    $v['release_status'] = (int)$v['status'];
                    // $v['list_cover'] = $this->getImgsIdAndUrl($v['list_cover'])['1']['0']['url'] ?? '';
                    $cover = !empty($v['list_cover']) ? $this->getFormatImgs($v['list_cover']) : [];
                    $v['list_cover'] = $cover[0]['url'];
                }
            }
            $this->success($res);
        } else {
            $this->error($rs);
        }
    }

    /**
     * 楼盘详情
     */
    public function getEstatesnewInfo()
    {
        $data = $this->request->param();

        if (empty($data['id'])) {
            $this->error('缺少必要参数');
        }
        $where = [
            ['id', '=', $data['id']],
        ];
        $rs = (new Estatesnew())->getInfo($where);

        if (!empty($rs['code'])) {
            $res = $rs['result'];
            if (empty($res)) {
                $res = [];
            }
            // 数据处理
            $res['house_purpose'] = !empty($res['house_purpose']) ? explode(',', $res['house_purpose']) : [];// 建筑用途
            $res['subways'] = !empty($res['subways']) ? explode(',', $res['subways']) : [];// 地铁
            // $res['subway_sites'] = !empty($res['subway_sites']) ? explode(',', $res['subway_sites']) : [];// 地铁站点
            $res['subway_sites'] = !empty($res['subway_sites']) ? $res['subway_sites'] : "";// 地铁站点
            $res['status'] = (string)$res['status'];
            $res['recommend'] = (string)$res['recommend'];
            $res['is_cheap'] = (string)$res['is_cheap'];
            $res['sale_status'] = (string)$res['sale_status'];
            // $res['delivery_time'] = !empty($res['delivery_time']) ?  date('Y-m-d', $res['delivery_time']) : "";// 交房时间
            // 销售许可证
            $salesLicense = json_decode($res['sales_license'], TRUE);
            if (empty($salesLicense) || !is_array($salesLicense)) {
                $salesLicense = [];
            }
            $res['sales_license'] = $salesLicense;
            // 优惠信息
            $discount = json_decode($res['discount'], TRUE);
            if (empty($discount) || !is_array($discount)) {
                $discount = [];
            }

            if (!empty($discount)) {
                foreach ($discount as &$v) {
                    $v['btn'] = !empty($v['btn']) ? $this->getFormatImgs($v['btn']) : [];
                }

            }
            $res['discount'] = $discount;
            // 沙盘图
            $res['sand_img_url'] = !empty($res['sand_table']) ? $this->getFormatImgs($res['sand_table']) : [];
            $res['activity_img'] = htmlspecialchars_decode($res['activity_img']);
            // logo
            $res['logo'] = !empty($res['logo']) ? $this->getFormatImgs($res['logo']) : [];
            // 列表图
            //list($res['list_cover_id'], $res['list_cover']) = $this->getImgsIdAndUrl($res['list_cover']);
            $res['list_cover'] = !empty($res['list_cover']) ? $this->getFormatImgs($res['list_cover']) : [];

            // 详情图
            //list($res['detail_cover_id'], $res['detail_cover']) = $this->getImgsIdAndUrl($res['detail_cover']);
            $res['detail_cover'] = !empty($res['detail_cover']) ? $this->getFormatImgs($res['detail_cover']) : [];
            // 特色标签
            $featureTag = $this->db->name('estates_has_tag')->where(['type' => 1, 'estate_id' => $data['id']])->column('tag_id');
            $res['feature_tag'] = !empty($featureTag) ? $featureTag : [];// 特色标签
            // 经纬度
            $res['coordinate'] = !empty($res['lng']) && !empty(['lat']) ? "{$res['lng']},{$res['lat']}" : "";

            $this->success($res);
        } else {
            $this->error($rs);
        }
    }


    /**
     * 添加/编辑 楼盘
     */
    public function editEstate()
    {
        $param = $this->request->param();

        $id = !empty($param['id']) ? (int)$param['id'] : 0;

        $time = time();

        $other = [];

        /**
         * 楼盘数据
         */

        $data = [
            'name'                     => $param['name'] ?? "",
            'title'                     => $param['title'] ?? "",
            'price'                    => (float)$param['price'],
            'activity_img'             => $param['activity_img'],
            'activity_type'            => $param['activity_type'],
            'price_total'              => (float)$param['price_total'],
            'price_str'                => $param['price_str'] ?? "",
            'built_area'               => $param['built_area'], //拿掉了float
            'province'                 => $param['province'] ?? "",
            'city'                     => $param['city'] ?? "",
            'area'                     => $param['area'] ?? "",
            'business_area'            => $param['business_area'] ?? "",
            'province_str'             => $param['province_str'] ?? "",
            'city_str'                 => $param['city_str'] ?? "",
            'area_str'                 => $param['area_str'] ?? "",
            'business_area_str'        => $param['business_area_str'] ?? "",
            'street_str'               => $param['street_str'] ?? "",
            'street'                   => $param['street'],
            'address'                  => $param['address'] ?? '',
            'house_type'               => $param['house_type'] ?? '',
            // 'coordinate' => $param['coordinate'] ?? "",
            // 'logo' => $param['logo'] ?? '',
            // 'list_cover' => $param['list_cover'] ?? '',
            // 'detail_cover' => $param['detail_cover'] ?? '',
            'sale_status'              => (int)$param['sale_status'],
            'sizelayout'               => $param['sizelayout'] ?? "",
            'planning_number'          => (int)$param['planning_number'],
            'total_area'               => (float)$param['total_area'],
            'total_construction_area'  => (float)$param['total_construction_area'],
            'building_type'            => $param['building_type'] ?? "",
            'floor_condition'          => $param['floor_condition'] ?? "",
            'developers'               => $param['developers'] ?? "",
            'floor_height'             => $param['floor_height'] ?? "",
            'decoration'               => $param['decoration'] ?? "",
            'progress_project'         => $param['progress_project'] ?? "",
            'public_bear'              => $param['public_bear'] ?? "",
            'sales_telephone'          => $param['sales_telephone'] ?? "",
            'property_company'         => $param['property_company'] ?? "",
            'property_type'            => $param['property_type'] ?? "",
            'property_charges'         => $param['property_charges'] ?? "",
            'volume_rate'              => $param['volume_rate'] ?? "",
            'greening_rate'            => $param['greening_rate'] ?? "",
            'parking_space_number'     => (int)$param['parking_space_number'],
            'parking_space_proportion' => $param['parking_space_proportion'] ?? "",
            'sign_up'                  => (int)$param['sign_up'],
            'status'                   => (int)$param['status'],
            'recommend'                => (int)$param['recommend'],
            'num_collect'              => (int)$param['num_collect'],
            'num_share'                => (int)$param['num_share'],
            'num_read'                 => (int)$param['num_read'],
            'num_read_search'          => (int)$param['num_read_search'],
            'sort'                     => (int)$param['sort'],
            'subway_sites'             => (int)$param['subway_sites'],
            'share_desc'               => $param['share_desc'], //分享详情
            // 'delivery_time' => !empty($param['delivery_time']) ? strtotime($param['delivery_time']) : 0,
            'is_cheap'                 => (int)$param['is_cheap'],
            'vr'                       => '',
        ];
        // 数据处理
        $data['house_purpose'] = !empty($param['house_purpose']) ? implode(',', $param['house_purpose']) : "";// 建筑用途
        // $data['house_purpose'] = $param['house_purpose'] ?? "";// 建筑用途
        $data['subways'] = !empty($param['subways']) ? implode(',', array_unique($param['subways'])) : "";// 地铁
        // $data['subway_sites'] = !empty($param['subway_sites']) ? implode(',', $param['subway_sites']) : "";// 地铁站点
        $data['sales_license'] = !empty($param['sales_license']) ? json_encode($param['sales_license'], JSON_UNESCAPED_UNICODE) : "";// 销售许可证

        if (!empty($param['discount'])) {
            foreach ($param['discount'] as &$v) {

                if(!empty($v['title']) || !empty($v['start_time']) || !empty($v['end_time'])){
                    if(empty($v['title'])){
                        return $this->error('优惠信息标题不能为空');
                    }
                    if(empty($v['start_time']) || empty($v['end_time'])){
                        return $this->error('优惠信息开始和结束时间不能为空');
                    }
                    if(empty($v['content'])){
                        return $this->error('优惠信息详情内容不能为空');
                    }

                }



                $v['btn'] = !empty($v['btn'][0]) && !empty($v['btn'][0]['url']) ? $v['btn'][0]['url'] : "";// 封面图
            }

        }
        $data['discount'] = !empty($param['discount']) ? json_encode($param['discount'], JSON_UNESCAPED_UNICODE) : "";// 优惠信息

        $sand_img_urls = [];
        if (!empty($param['sand_img_url'])) {
            foreach ($param['sand_img_url'] as $item) {
                array_push($sand_img_urls, $item['url']);
            }
        }

        $data['sand_table'] = !empty($sand_img_urls) ? implode(',', $sand_img_urls) : "";// 沙盘图

        $data['logo'] = !empty($param['logo'][0]) && !empty($param['logo'][0]['url']) ? $param['logo'][0]['url'] : "";// logo
        $data['list_cover'] = !empty($param['list_cover'][0]) && !empty($param['list_cover'][0]['url']) ? $param['list_cover'][0]['url'] : "";// 封面图片


        $data['detail_cover'] = !empty($param['detail_cover'][0]) && !empty($param['detail_cover'][0]['url']) ? $param['detail_cover'][0]['url'] : "";// 详情图片
        // 经纬度
        $coordinate = !empty($param['coordinate']) ? explode(',', $param['coordinate']) : [];
        $data['lng'] = $coordinate['0'] ?? "";
        $data['lat'] = $coordinate['1'] ?? "";

        $featureTags = !empty($param['feature_tag']) ? $param['feature_tag'] : [];// 特色标签


        $server = new Estatesnew();

        // 信息校验
        $validate = new EstatesValidate();
        if (2 == $data['status']) {
            // 草稿发布的校验
            $scene = !empty($id) ? 'editDraft' : 'addDraft';
        } else {
            // 正式发布的校验
            $scene = !empty($id) ? 'edit' : 'add';
        }
        if (!$validate->scene($scene)->check($param)) {
            $this->error($validate->getError());
        }

        if (empty($id)) {
            /**
             * 价格变化初始数据
             */
            $other['priceData'] = [
                'old_price'   => 0,
                'new_price'   => (float)$param['price'],
                'admin_id'    => $this->userId,
                'create_time' => $time,
                'update_time' => $time,
                'month_time'  => strtotime(date('Y-m', $time)),
            ];

            /**
             * 标签
             */
            $other['newTags'] = $featureTags;

            $res = $server->add($data, $other);

            $action = 'add';
        } else {
            /**
             * 价格变化数据
             */
            // 获取历史
            $serverPrice = new EstatesnewPrice();
            $paramPrice = [
                'where' => ['estate_id' => $id],
                'field' => 'id, old_price, new_price, create_time',
            ];
            $resPrice = $serverPrice->getOne($paramPrice);
            if (empty($resPrice['code'])) {
                $this->error($resPrice);
            }
            $resPrice = $resPrice['result'];

            $priceId = 0;
            $oldPrice = 0;
            if (!empty($resPrice)) {
                // 对比记录时间
                $recordTime = strtotime(date('Y-m-d', $resPrice['create_time']));
                $today = strtotime(date('Y-m-d'));
                if ($recordTime == $today) {
                    $priceId = $resPrice['id'];
                    $oldPrice = $resPrice['old_price'] ?? 0;
                } else {
                    $oldPrice = $resPrice['new_price'] ?? 0;
                }
            }
            if ($oldPrice != (float)$param['price']) {
                $other['priceData'] = [
                    'id'        => $priceId,
                    'estate_id' => $id,
                    'admin_id'  => $this->userId,
                    'old_price' => $oldPrice,
                    'new_price' => (float)$param['price'],
                ];
            }
            /**
             * 标签
             */
            list($other['newTags'], $other['delTags']) = $this->dealTag($id, $featureTags);
            $info = $server->getInfo([['id','=',$id]])['result'];
            //价格有变化触发消息推送
            $chatServer = new Chat();
            if(!empty($info) && ($info['price_total'] != $data['price_total']  ||  $info['price'] != $data['price'] ) ) {
                //todo

                $msg_data = [
                    'title'           => '您关注的楼盘价格有变动，赶快去查看吧!',
                    'contxt'          => '',
                    'status'          => '1',
                    'cover'          =>  $data['list_cover'] ?? 'upload/images/admin/admin/lp_list.png',
                    'chat_type'       => 4,
                    'estate_id'       => $id,
                    'sub_context'     => "您关注的楼盘{$data['name']}价格有变动，赶快去查看吧!",
                    'name'            => $data['name'] ?? '',
                    'update_time'     => time(),
                    'create_time'     => time(),
                ];
                //获取楼盘关注列表
                $follow_list  = (new Attention())->getFollowUserListByEstatesId($id);
                if(!empty($follow_list)){
                    $chatServer->addSyetemMsg($msg_data,$follow_list);
                }

            }
            $res  = $server->edit($id, $data, $other);

            $action = 'edit';
            $data['id'] = $id;
        }
        if(!empty($info) && (
                    $info['built_area'] != $data['built_area']
                ||  $info['house_type'] != $data['house_type']
                ||  $info['sale_status'] != $data['sale_status']
                ||  $info['sale_status'] != $data['sale_status']
                ||  $info['bind_building'] != $data['bind_building']
                ||  $info['total_area'] != $data['total_area']
                ||  $info['floor_height'] != $data['floor_height']
                ||  $info['progress_project'] != $data['progress_project']
                ||  $info['public_bear'] != $data['public_bear']
                ||  $info['discount'] != $data['discount']
                ||  $info['opening_time'] != $data['opening_time']
                ||  $info['delivery_time'] != $data['delivery_time']
            ) ) {
            //todo

            $msg_data = [
                'title'           => '您关注的楼盘信息有变更，赶快去查看吧',
                'contxt'          => '',
                'status'          => '1',
                'cover'          =>  $data['list_cover'] ?? 'upload/images/admin/admin/lp_list.png',
                'chat_type'       => 4,
                'estate_id'       => $id,
                'sub_context'     => "您关注的楼盘{$data['name']}信息有变更，赶快去查看吧",
                'name'              => $data['name'] ?? '',
                'update_time'     => time(),
                'create_time'     => time(),
            ];

            //获取楼盘关注列表
            $follow_list  = (new Attention())->getFollowUserListByEstatesId($id);
            if(!empty($follow_list)){
                $chatServer->addSyetemMsg($msg_data,$follow_list);
            }

        }
        if (!empty($res['code'])) {
            /**
             * 异步调用旧库接口
             */
            $this->sendOldSync(['type' => 'estate', 'action' => $action, 'data' => $data]);

            $this->success();
        } else {
            $this->error($res);
        }
    }

    /**
     * 处理标签
     */
    protected function dealTag($id, $tags)
    {
        try {
            $delTags = [];// 需要删除的标签的记录ID
            $newTags = [];// 需要增加的标签的ID
            $tmp = [];
            $where = [
                ['estate_id', '=', $id],
                ['type', '=', 1],
            ];
            $res = $this->db->name('estates_has_tag')->where($where)->field('id, tag_id')->select()->toArray();

            if (!empty($res)) {
                foreach ($res as $v) {
                    $tmp[] = $v['tag_id'];
                    if (!in_array($v['tag_id'], $tags)) {// 不在本次选中标签内的,删除
                        $delTags[] = $v['id'];
                    }
                }
                // 新增的标签
                $newTags = array_diff($tags, $tmp);// 在本次选中的标签内，但不在原有标签内
            } else {
                $newTags = $tags;
            }
            return [$newTags, $delTags];
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     *修改楼盘状态/推荐
     */
    public function setEstatesStatus()
    {
        $param = $this->request->param();

        $id = !empty($param['id']) ? (int)$param['id'] : 0;

        if (empty($id) || empty($param['type'])) {
            $this->error('缺少必要参数');
        }

        $data = [];

        $status = (int)$param['status'];

        $where[] = ['id', '=', $id];

        switch ($param['type']) {
            case 'status':
                $data['status'] = $status;
                $where[] = ['status', '<>', 2];
                break;
            case 'recommend':
                $data['recommend'] = $status;
                break;
            default:
                $this->error('非法类型');
                break;
        }

        $server = new Estatesnew();
        $res = $server->editByWhere($where, $data);

        if (!empty($res['code'])) {

            if('status' == $param['type']) {
                /**
                 * 异步调用旧库接口
                 */
                $this->sendOldSync(['type' => 'estate', 'action' => 'status', 'data' => ['id' => $id, 'status' => $status]]);
            }

            $this->success();
        } else {
            $this->error($res);
        }
    }

    /**
     * 删除楼盘-软删除
     */
    public function delEstates()
    {
        $param = $this->request->param();

        $id = !empty($param['id']) ? (int)$param['id'] : 0;

        $data = [
            'is_delete' => 1,
        ];

        if (empty($id)) {
            $this->error('缺少必要参数');
        }

        $server = new Estatesnew();
        $res = $server->edit($id, $data);

        if (!empty($res['code'])) {
            $info = $this->db->name('estates_new')->where('id', $param['id'])->field('city, area')->find();

            $popular = MyConst::ESTATES_LIST_POPULAR;
            $search = MyConst::ESTATES_LIST_SEARCH;
            if(!empty($info['city'])) {
                $cityCode = $info['city'];
                $this->delRank($popular, $cityCode, $param['id']);
                $this->delRank($search, $cityCode, $param['id']);
            }
            if(!empty($info['area'])) {
                $areaCode = $info['area'];
                $this->delRank($popular, $areaCode, $param['id']);
                $this->delRank($search, $areaCode, $param['id']);
            }

            /**
             * 异步调用旧库接口
             */
            $this->sendOldSync(['type' => 'estate', 'action' => 'delete', 'data' => ['id' => $id]]);

            $this->success();
        } else {
            $this->error($res);
        }
    }

    /**
     * 同步修改排行
     */
    protected function delRank($key, $code, $id)
    {
        $redis = $this->getReids();

        $rankIds = $redis->hGet($key, $code);
        $rankIds = !empty($rankIds) ? explode(',', $rankIds) : [];
        if(!empty($rankIds)) {
            $index = array_search($id, $rankIds);
            if(false !== $index) {
                unset($rankIds[$index]);
            }
            $rank = !empty($rankIds) ? implode(',', $rankIds) : '';
            $redis->hSet($key, $code, $rank);
        }
    }

    /**
     *修改楼盘排序
     */
    public function changeEstateSort()
    {
        $param = $this->request->param();

        $id = !empty($param['id']) ? (int)$param['id'] : 0;

        if (empty($id)) {
            $this->error('缺少必要参数');
        }

        $data = [
            'sort' => (int)$param['sort'],
        ];

        $server = new Estatesnew();
        $res = $server->edit($id, $data);

        if (!empty($res['code'])) {
            $this->success();
        } else {
            $this->error($res);
        }
    }

    /**
     * 获取图片路径
     */
    protected function getImgPath($id)
    {
        if (empty($id)) {
            return '';
        }
        if (!is_array($id) && !is_string($id)) {
            return '';
        }
        if (is_string($id)) {
            $info = $this->db->name('upload_file')->where('file_id', '=', $id)->value('file_path');
        } else {
            $info = $this->db->name('upload_file')->where('file_id', 'in', $id)->column('file_path as url,file_hash as name');
        }

        return $info;
    }

    //=============== 新房操作 end==================//

    //=============== 新房楼栋操作 start==================//

    /**
     * 楼栋列表
     */
    public function getEstatesnewBuildingList()
    {
        $data = $this->request->param();

        // 分页
        $pageSize = !empty($data['page_size']) ? $data['page_size'] : 20;
        if ($pageSize > 100) {
            $this->error('获取数量超出限制');
        }
        // 字段
        $fields = 'enb.*';
        // 条件
        $search = [
            'name'      => trim_all($data['name']),
            'estate_id' => $data['estate_id'] ?? 0,
        ];

        $rs = (new EstatesnewBuilding())->getList($search, $fields, $pageSize);

        if (!empty($rs['code'])) {
            $res = $rs['result'];
            if (empty($res['list'])) {
                $res = [];
            }
            $this->success($res);
        } else {
            $this->error($rs);
        }
    }

    /**
     * 楼栋列表-不分页
     */
    public function getBuildingList()
    {
        $data = $this->request->param();

        // 字段
        $fields = 'enb.*';
        // 条件
        $search = [
            'name'      => trim_all($data['name']),
            'estate_id' => $data['estate_id'] ?? 0,
        ];

        $rs = (new EstatesnewBuilding())->getList($search, $fields);

        if (!empty($rs['code'])) {
            $res = $rs['result'];
            if (empty($res['list'])) {
                $res = [];
            }
            $this->success($res);
        } else {
            $this->error($rs);
        }
    }

    /**
     * 楼栋添加/修改
     */
    public function editEstatesnewBuilding()
    {
        $param = $this->request->param();

        $id = !empty($param['id']) ? (int)$param['id'] : 0;

        $estateId = !empty($param['estate_id']) ? (int)$param['estate_id'] : 0;

        // 楼栋信息
        $data = [
            'estate_id'     => $estateId,
            'name'          => !empty($param['name']) ? trim_all($param['name']) : "",
            'sale_status'   => (int)$param['sale_status'],
            'unit'          => (int)$param['unit'],
            'house_number'  => (int)$param['house_number'],
            'floor_number'  => (int)$param['floor_number'],
            'delivery_time' => !empty($param['delivery_time']) ? strtotime($param['delivery_time']) : 0,
            'building_type' => (string)$param['building_type'],
        ];

        // 许可证信息
        $where = [
            ['id', '=', $estateId],
        ];
        $rs = (new Estatesnew())->getSalesLicense($where);
        if (empty($rs['code'])) {
            $this->error($rs);
        }
        if (empty($rs['result'])) {
            $salesLicense = [];
        } else {
            $salesLicense = $rs['result'];
        }

        $select = $param['sales_license'] ?? [];
        foreach ($salesLicense as &$v) {
            if (in_array($v['license'], $select)) {
                $v['select'] = 1;
            }
            $v['building'] = !empty($v['building']) ? explode(',', $v['building']) : [];
        }


        $server = new EstatesnewBuilding();
        if ($id) {
            $res = $server->edit($id, $data, $salesLicense);
        } else {
            $res = $server->add($data, $salesLicense);
        }

        if (!empty($res['code'])) {
            $this->success($res['result']);
        } else {
            $this->error($res);
        }
    }

    /**
     * 删除楼栋-软删除
     */
    public function delEstateBuilding()
    {
        $param = $this->request->param();

        $id = !empty($param['id']) ? (int)$param['id'] : 0;

        $data = [
            'is_delete' => 1,
        ];

        if (empty($id)) {
            $this->error('缺少必要参数');
        }

        $server = new EstatesnewBuilding();
        $res = $server->edit($id, $data);

        if (!empty($res['code'])) {
            $this->success();
        } else {
            $this->error($res);
        }
    }

    /**
     * 获取楼盘的许可证及楼栋绑定的许可证
     */
    public function getSalesLicense()
    {
        $param = $this->request->param();

        $estateId = !empty($param['estate_id']) ? (int)$param['estate_id'] : 0;
        // $buildingId = !empty($param['building_id']) ? (int)$param['building_id'] : 0;

        $where = [
            ['id', '=', $estateId],
        ];
        $rs = (new Estatesnew())->getSalesLicense($where);

        if (empty($rs['code'])) {
            $this->error($rs);
        }

        if (empty($rs['result'])) {
            $salesLicense = [];
        } else {
            $salesLicense = $rs['result'];
        }

        // 哪些许可证内绑定了楼栋
        $select = [];
        if (!empty($salesLicense)) {
            foreach ($salesLicense as &$v) {
                $v['building'] = !empty($v['building']) ? explode(',', $v['building']) : [];
                // if(in_array($buildingId, $v['building'])) {
                //     $select[] = $v['license'];
                // }
            }
        }

        $result = [
            'sales_license' => $salesLicense,
            // 'select' => $select,
        ];

        $this->success($result);
    }

    //=============== 新房楼栋操作 end==================//

    //=============== 新房户型操作 start==================//

    /**
     * 户型列表
     */
    public function getEstatesnewHouseList()
    {
        $data = $this->request->param();

        // 分页
        $pageSize = !empty($data['page_size']) ? $data['page_size'] : 20;
        if ($pageSize > 100) {
            $this->error('获取数量超出限制');
        }
        // 字段
        $fields = 'enh.*';
        // 条件
        $search = [
            'name'        => trim_all($data['name']),
            'building_id' => $data['building_id'] ?? 0,
            'estate_id'   => $data['estate_id'] ?? 0,
        ];

        $rs = (new EstatesnewHouse())->getList($search, $fields, $pageSize);

        if (!empty($rs['code'])) {
            $res = $rs['result'];
            if (empty($res['list'])) {
                $res = [];
            } else {
                foreach ($res['list'] as &$v) {
                    $v['building_id'] = !empty($v['building_id']) ? $v['building_id'] : '';

                    $v['house_purpose'] = !empty($v['house_purpose']) ? explode(',', $v['house_purpose']) : [];

                    $v['cover_url'] = !empty($v['img']) ? $this->getFormatImgs($v['img']) : [];
                }
            }
            $this->success($res);
        } else {
            $this->error($rs);
        }
    }

    /**
     * 户型添加/修改
     */
    public function editEstatesnewHouse()
    {
        $param = $this->request->param();

        $id = !empty($param['id']) ? (int)$param['id'] : 0;

        $estateId = !empty($param['estate_id']) ? (int)$param['estate_id'] : 0;
        $buildingId = !empty($param['building_id']) ? (int)$param['building_id'] : 0;

        $cover_imgs = $param['cover_url'] && $param['cover_url'][0] && $param['cover_url'][0]['url'] ? $param['cover_url'][0]['url'] : '';
        if (empty($cover_imgs)) {
            $this->error('请上传图片');
        }

        $time = time();
        $data = [
            'estate_id'     => $estateId,
            'building_id'   => $buildingId,
            'name'          => !empty($param['name']) ? trim_all($param['name']) : "",
            'sale_status'   => (int)$param['sale_status'],
            'price'         => (float)$param['price'],
            'price_total'   => (float)$param['price_total'],
            'price_str'     => $param['price_str'] ?? "",
            'built_area'    => (float)$param['built_area'],
            'rooms'         => (int)$param['rooms'],
            'rooms_str'     => $param['rooms_str'],
            'orientation'   => (int)$param['orientation'],
            'house_purpose' => !empty($param['house_purpose']) ? implode(',', $param['house_purpose']) : '',
            'img'           => $cover_imgs,
        ];

        $server = new EstatesnewHouse();
        if ($id) {
            $res = $server->edit($id, $data);
        } else {
            $res = $server->add($data);

            $id = $res['result'] ?? 0;
        }

        if (!empty($res['code'])) {
            /**
             * 异步调用旧库接口
             */
            $this->sendOldSync(['type' => 'house', 'action' => 'add', 'data' => ['id' => $id]]);

            $this->success($res['result']);
        } else {
            $this->error($res);
        }
    }

    /**
     * 户型删除
     */
    public function delEstateHouse()
    {
        $param = $this->request->param();

        $id = !empty($param['id']) ? (int)$param['id'] : 0;

        if (empty($id)) {
            $this->error('缺少必要参数');
        }

        $server = new EstatesnewHouse();
        $res = $server->delete($id);

        if (!empty($res['code'])) {
            /**
             * 异步调用旧库接口
             */
            $this->sendOldSync(['type' => 'house', 'action' => 'delete', 'data' => ['id' => $id]]);

            $this->success();
        } else {
            $this->error($res);
        }
    }

    //=============== 新房户型操作 end==================//

    //=============== 新房相册操作 start==================//
    public function getBuildingPhotosList()
    {
        $data = $this->request->param();
        $data['estate_id'] = intval($data['estate_id']);
        if (empty($data['estate_id'])) {
            return $this->success([]);
        }

        $where = [
            'estate_id'   => intval($data['estate_id']),
            'category_id' => intval($data['category_id']),
        ];
        $rs = (new BuildingPhotos())->getList($where)['result'];
        if (empty($rs['list'])) {
            $rs = [];
        } else {
            foreach ($rs['list'] as &$v) {
                $v['cover_url'] = !empty($v['cover']) ? $this->getFormatImgs($v['cover']) : [];
            }
            unset($v);
        }
        $this->success($rs);
    }

    //删除
    public function delBuildingPhotos()
    {
        $data = $this->request->param();
        $rs = (new BuildingPhotos())->del(intval($data['id']));

        /**
         * 异步调用旧库接口
         */
        $this->sendOldSync(['type' => 'img', 'action' => 'delete', 'data' => ['id' => $data['id']]]);

        $this->success($rs);
    }

    public function editBuildingPhotos()
    {
        $data = $this->request->param();
        $data['id'] = intval($data['id']);
        $data['category_id'] = intval($data['category_id']);

        if (empty($data['estate_id'])) {
            $this->error('缺失楼盘参数');
        }
        if (empty($data['category_id'])) {
            $this->error('请选择类别');
        }
        if (empty($data['cover_id'])) {
            $this->error('请上传图片');
        }

        $indata = [
            'estate_id'   => $data['estate_id'],
            'category_id' => $data['category_id'],
            'cover'       => !empty($data['cover_url'][0]) && !empty($data['cover_url'][0]['url']) ? $data['cover_url'][0]['url'] : "",
        ];

        if ($data['id']) {
            $rs = (new BuildingPhotos())->edit($data['id'], $indata);
        } else {
            $rs = (new BuildingPhotos())->add($indata);

            $id = $rs['result'];
        }

        if ($rs['code'] == 1) {
            /**
             * 异步调用旧库接口
             */
            $this->sendOldSync(['type' => 'img', 'action' => 'add', 'data' => ['id' => $id]]);

            $this->success();
        } else {
            $this->error($rs['msg']);
        }
    }

    /**
     * 相册列表分组
     */
    public function getImgsListGroup()
    {
        $data = $this->request->param();
        $data['estate_id'] = intval($data['estate_id']);
        if (empty($data['estate_id'])) {
            return $this->success([]);
        }

        $where = [
            'estate_id' => $data['estate_id'],
        ];
        $rs = (new BuildingPhotos())->getList($where)['result'];
        if (empty($rs['list'])) {
            $this->success([]);
        } else {
            $arr = [];//以类别分组
            foreach ($rs['list'] as $v) {
                //list($v['cover_id'], $v['cover_url']) = $this->getImgsIdAndUrl($v['cover']);
                $v['cover_url'] = $this->getFormatImgs($v['cover']);
                $catgory = MyConst::BUILDINGPHOTOS_CATEGORYS[$v['category_id']] ?? "ID:" . $v['category_id'];
                $arr[$catgory][] = $v;
            }
            unset($rs);
            $this->success($arr);
        }
    }
    //=============== 新房相册操作 end==================//

    //=============== 楼盘开盘时间操作 start==================//

    /**
     * 开盘时间列表
     */
    public function getEstatesnewTime()
    {
        $data = $this->request->param();

        // 分页
        $pageSize = !empty($data['page_size']) ? $data['page_size'] : 20;
        if ($pageSize > 100) {
            $this->error('获取数量超出限制');
        }
        // 字段
        $fields = '*';
        // 条件
        $search = [
            'estate_id'   => $data['estate_id'] ?? 0,
        ];

        $rs = (new EstatesnewTime())->getList($search, $fields, $pageSize);

        if (!empty($rs['code'])) {
            $res = $rs['result'];
            if (empty($res['list'])) {
                $res = [];
            } else {
                foreach ($res['list'] as &$v) {
                    $v['opening_time'] = !empty($v['opening_time']) ? date('Y-m-d', $v['opening_time']) : '';
                    $v['building'] = !empty($v['building']) ? json_decode($v['building'], TRUE) : [];
                }
            }
            $this->success($res);
        } else {
            $this->error($rs);
        }
    }

    /**
     * 开盘时间添加/修改
     */
    public function editEstatesnewTime()
    {
        $param = $this->request->param();

        $id = !empty($param['id']) ? (int)$param['id'] : 0;

        // $estateId = !empty($param['estate_id']) ? (int)$param['estate_id'] : 0;
        // $buildingId = !empty($param['building_id']) ? (int)$param['building_id'] : 0;

        if(empty($param['estate_id'])) {
            $this->error('楼盘ID缺失');
        }
        $estateId = $param['estate_id'];
        if (empty($param['opening_time'])) {
            $this->error('缺少开通时间');
        }
        if(!empty($param['building'])) {
            $buildingIds = array_column($param['building'], 'id');
            if(sizeof($buildingIds) != sizeof(array_unique($buildingIds))) {
                // 存在重复值
                $this->error('重复绑定楼盘');
            }
        }


        $time = time();
        $openTime = strtotime($param['opening_time']);
        if(!empty($param['building'])) {
            foreach($param['building'] as $k => $v) {
                if(empty($v['id'])) {
                    unset($param['building'][$k]);
                }
            }
            $buildings = json_encode($param['building'], JSON_UNESCAPED_UNICODE);
        } else {
            $buildings = '';
        }
        $data = [
            'estate_id'    => $estateId,
            'opening_time' => $openTime,
            'building'     => $buildings,
        ];

        $server = new EstatesnewTime();

        $paramOpenTime = [
            'where' => [
                ['estate_id', '=', $estateId],
                ['opening_time', '=', $openTime],
            ],
            'fileds' => 'id',
        ];
        if($id) {
            $paramOpenTime['where'][] = ['id', '<>', $id];
        }
        $isExist = $server->getOne($paramOpenTime);
        // var_dump($this->db->getLastSql());
        if(!empty($isExist['result'])) {
            $this->error('已存在相同开盘时间');
        }
        
        if ($id) {
            $res = $server->edit($id, $data);
        } else {
            $res = $server->add($data);
        }

        if (!empty($res['code'])) {
            $this->success($res['result']);
        } else {
            $this->error($res);
        }
    }

    /**
     * 开盘时间删除
     */
    public function delEstateTime()
    {
        $param = $this->request->param();

        $id = !empty($param['id']) ? (int)$param['id'] : 0;

        if (empty($id)) {
            $this->error('缺少必要参数');
        }

        $server = new EstatesnewTime();
        $res = $server->delete($id);

        if (!empty($res['code'])) {
            $this->success();
        } else {
            $this->error($res);
        }
    }

    //=============== 楼盘开盘时间操作 end==================//

    //=============== 楼盘动态资讯 start==================//
    public function getEstatesnewNews()
    {
        $data = $this->request->param();
        $data['estate_id'] = intval($data['estate_id']);
        if (empty($data['estate_id'])) {
            return $this->success([]);
        }

        $where = [
            'estate_id' => intval($data['estate_id']),
        ];

        $where = [
            'region_no'  => empty($data['region_no']) ? '' : $data['region_no'],
            'forid'      => empty($data['forid']) ? '' : $data['forid'],
        ];

        $rs = (new News())->getList($where)['result'];
        $this->success($rs);


//        $rs = (new EstatesnewNews())->getList($where)['result'];
//        if (empty($rs['list'])) {
//            $rs['list'] = [];
//        } else {
//            foreach ($rs['list'] as &$v) {
//                //list($v['img_ids'], $v['img_url']) = $this->getImgsIdAndUrl($v['cover']);
//                $v['img_ids'] = !empty($v['cover']) ? explode(',', $v['cover']) : [];
//                $v['img_url'] = !empty($v['cover']) ? $this->getFormatImgs($v['cover']) : [];
//            }
//        }
//        $this->success($rs);
    }

    //删除
    public function delEstatesnewNews()
    {
        $data = $this->request->param();
        $rs = (new EstatesnewNews())->del(intval($data['id']));
        $this->success($rs);
    }

    public function editEstatesnewNews()
    {
        $data = $this->request->param();
        $data['id'] = intval($data['id']);
        $data['estate_id'] = intval($data['estate_id']);

        if (empty($data['estate_id'])) {
            $this->error('缺失楼盘参数');
        }
        if (empty($data['img_url']) || empty($data['img_ids'])) {
            $this->error('请上传图片');
        }
        $data['create_time'] = strtotime($data['create_time']);
        if (empty($data['create_time'])) {
            $this->error('请选择发布时间');
        }
        if (empty($data['title'])) {
            $this->error('请填写资讯标题');
        }
        if (empty($data['content'])) {
            $this->error('请选填写资讯内容');
        }
        if (count($data['img_url']) > 3) {
            $this->error('最多上传3张图');
        }


        $img_urls = [];
        if (!empty($data['img_url'])) {
            foreach ($data['img_url'] as $item) {
                array_push($img_urls, $item['url']);
            }
        }
        $indata = [
            'estate_id'   => $data['estate_id'],
            'cover'       => !empty($img_urls) ? implode(',', $img_urls) : '',
            'title'       => $data['title'] ?? '',
            'describe'    => $data['describe'] ?? '',
            'content'     => !empty($data['content']) ? htmlspecialchars_decode($data['content']) : '',
            'create_time' => $data['create_time'],
        ];

        if ($data['id']) {
            $rs = (new EstatesnewNews())->edit($data['id'], $indata);
        } else {
            $rs = (new EstatesnewNews())->add($indata);
        }

        if ($rs['code'] == 1) {
            $this->success();
        } else {
            $this->error($rs['msg']);
        }
    }
    //=============== 楼盘动态资讯 end==================//

    // ============== 榜单管理 start ==================//

    /**
     * 获取城市
     */
    public function getRankCity()
    {
        $regionRes = $this->getMyCity();
        if (!empty($regionRes['code']) && 1 == $regionRes['code']) {
            if (!empty($regionRes['data'])) {
                $cityIds = array_column($regionRes['data'], 'id');
                if (!empty($cityIds)) {
                    $whereArea = [
                        ['pid', 'in', $cityIds],
                        ['status', '=', 1],
                    ];
                    $fieldsArea = "id, cname, pid";
                    $areaList = $this->db->name('site_city_area')->where($whereArea)->field($fieldsArea)->select()->toArray();
                    if (!empty($areaList)) {
                        foreach ($regionRes['data'] as &$v) {
                            foreach ($areaList as $a) {
                                if ($v['id'] == $a['pid']) {
                                    $v['children'][] = ['id' => $a['id'], 'cname' => $a['cname'], 'type' => 'area'];
                                    $v['type'] = 'city';
                                }
                            }
                        }
                    } else {
                        foreach ($regionRes['data'] as &$v) {
                            $v['children'] = [];
                            $v['type'] = 'city';
                        }
                    }
                }
            }
            $this->success($regionRes['data']);
        } else {
            $this->error($regionRes);
        }
    }

    /**
     * 获取楼盘排行
     */
    public function getRank()
    {
        $params = $this->request->param();

        if (empty($params['region_id'])) {
            $this->error('缺少必要参数');
        }

        $regionCode = $params['region_id'];

        $type = $params['type'] ?? 1;

        switch ($type) {
            // 人气榜
            case 1:
                $key = MyConst::ESTATES_LIST_POPULAR;
                break;
            // 热搜榜
            case 2:
                $key = MyConst::ESTATES_LIST_SEARCH;
                break;
            default:
                $this->error('类型有误');
                break;
        }

        $redis = $this->getReids();

        $regionIds = [];
        $result = [];
        $rankIds = $redis->hGet($key, $regionCode);
        if (!empty($rankIds)) {
            $regionIds = explode(',', $rankIds);
        }

        if (!empty($regionIds) && is_array($regionIds)) {
            $data['where'] = [
                ['id', 'in', $regionIds],
            ];
            $data['fields'] = 'id, name, city_str, area_str';
            $estates = (new Estatesnew())->getListByParams($data);
            // var_dump($estates);
            if (empty($estates['code']) || 1 != $estates['code']) {
                $this->error($estates);
            }
            $estates = $estates['result'];
            if (!empty($estates)) {
                $rank = 1;
                foreach ($regionIds as $id) {
                    foreach ($estates as $e) {
                        if ($id == $e['id']) {
                            $result[] = [
                                'rank'      => $rank,
                                'estate_id' => $id,
                                'estate'    => $e['name'],
                                'region'    => "{$e['city_str']}{$e['area_str']}",
                            ];
                            $rank++;
                            break;
                        }
                    }
                }
            }

        }

        $this->success($result);
    }

    /**
     * 新增排名
     */
    public function editRank()
    {
        $params = $this->request->param();

        if (empty($params['forid']) || empty($params['region_id']) || empty($params['deal'])) {
            $this->error('缺少必要参数');
        }

        $regionCode = $params['region_id'];
        $deal = $params['deal'];
        $rank = $params['rank'] ?? 0;
        $estateId = $params['forid'];

        if ('del' != $deal) {
            if (!$rank) {
                $this->error('缺少排名');
            }
        }

        $type = $params['type'] ?? 1;

        switch ($type) {
            // 人气榜
            case 1:
                $key = MyConst::ESTATES_LIST_POPULAR;
                break;
            // 热搜榜
            case 2:
                $key = MyConst::ESTATES_LIST_SEARCH;
                break;
            default:
                $this->error('类型有误');
                break;
        }

        $redis = $this->getReids();

        $regionIds = [];
        $rankIds = $redis->hGet($key, $regionCode);
        if (!empty($rankIds)) {
            $regionIds = explode(',', $rankIds);
        }

        switch ($deal) {
            // 新增
            case 'add':
                if ($rank > sizeof($regionIds)+1 && 1 != $rank) {
                    $this->error('前几位排名还没添加，请按顺序添加排名');
                }
                if (in_array($estateId, $regionIds)) {
                    $this->error('已在榜单中，请修改');
                }
                $rank -= 1;
                array_splice($regionIds, $rank, 0, $estateId);
                break;
            // 编辑
            case 'edit':
                if ($rank > sizeof($regionIds)+1 && 1 != $rank) {
                    $this->error('前几位排名还没添加，请按顺序添加排名');
                }
                $rank -= 1;
                // if(in_array($estateId, $regionIds)) {// 原先在榜单内，先移除
                //     $k = 0;
                //     foreach($regionIds as $rIds) {
                //         if($rIds == $estateId) {
                //             array_splice($regionIds, $k, 1);
                //         }
                //         $k++;
                //     }
                // }
                // array_splice($regionIds, $rank, 0, $estateId);// 将新排名加入榜单内

                if (in_array($estateId, $regionIds)) {// 原先在榜单内，提示
                    $this->error('该楼盘已在榜单内');
                }
                $regionIds[$rank] = $estateId;
                break;
            // 删除
            case 'del':
                if (in_array($estateId, $regionIds)) {// 是否在榜单内
                    $k = 0;
                    foreach ($regionIds as $rIds) {
                        if ($rIds == $estateId) {
                            array_splice($regionIds, $k, 1);
                        }
                        $k++;
                    }
                }
                break;
            default:
                $this->error('非法处理方式');
                break;
        }

        $regionIds = implode(',', $regionIds);

        $redis->hSet($key, $regionCode, $regionIds);

        $this->success();
    }

    // ============== 榜单管理 end ====================//
}