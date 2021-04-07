<?php

namespace app\index\controller;

use app\common\base\UserBaseController;
use app\common\traits\TraitEstates;
use app\server\estates\Estatesnew;
use app\server\marketing\Liveroom;
use app\server\marketing\Subject;


class SubjectController extends UserBaseController
{
    use TraitEstates;


    public function index()
    {
        $params = $this->request->param();

        $time = time();
        
        if(empty($params['id'])) {
            $this->error('缺少必要参数');
        }
        $id = $params['id'];

        $serachSubject = [
            'status' => 1,
            'id' => $id,
        ];
        $subject = (new Subject())->getInfo($serachSubject);
        $subject = $subject['result'];

        if(!empty($subject)) {
            // 时间判断 为0时不做判断
            if(!empty($subject['start_time']) && $subject['start_time'] > $time) {
                $this->error('专题未开始');
            }
            if(!empty($subject['end_time']) && $subject['end_time'] < $time) {
                $this->error('专题已结束');
            }

            $subject['estates'] = $this->getEstatesList($subject);
            $subject['label'] = $this->getLabelList($subject);

            if(!$subject['type']) {// 版面类型 0-UI品质好房(有直播)
                $subject['live_room'] = $this->getLiveRoomList($subject);
            } else {
                $subject['live_room'] = [];
            }
            if(!empty($subject['config']['banner'])){
                $subject['banner'] = $subject['config']['banner'];
                $sort = array_column($subject['banner'], 'forsort');
                array_multisort($sort, SORT_DESC, $subject['banner']);
            }

            unset($subject['config']);
        } else {
            $subject = [];
        }

        $this->success($subject);
    }


    /**
     * 楼盘列表
     */
    public function getEstatesList($subject)
    {
        $time = time();

        $estatesData = [];
        if(!empty($subject['config']['estates_new'])) {
            $estates = $subject['config']['estates_new'];
            $tmp = $estates;// 数组以ID为键名，排序后会被重新索引
            // 排序
            $sort = array_column($estates, 'forsort');
            $labelArray = array_column($tmp,'label_id','forid');

            array_multisort($sort, SORT_DESC, $estates);
            // 数据
            $ids = array_column($estates, 'forid');// 专题关联的楼盘ID
            if(!empty($ids)) {
                $params['estate_id'] = $ids;
                $data = [
                    // 字段
                    'fields' => "en.id, en.name, en.title, en.list_cover, en.logo, en.price, en.price_total, en.city_str, en.area_str, en.business_area_str, en.sale_status, en.built_area, en.house_purpose, en.recommend, en.is_cheap, GROUP_CONCAT(tag_id) as feature_tag",
                    // 排序
                    'order' => [
                        'en.sort' => 'desc',
                        'en.id'   => 'desc',
                    ],
                    // 联表
                    'join' => [
                        ['table' => 'estates_has_tag eht', 'cond' => '(en.id=eht.estate_id and eht.type=1)', 'type' => 'left'],// 标签
                    ],
                    // 已联表
                    'join_already' => ['estates_has_tag'],
                    // 分组
                    'group' => ['en.id'],
                ];
                
                // 根据版面不同连不同的表获取相应信息
                if(!$subject['type']) {// 版面类型 0-UI品质好房(有视频) 1-UI优惠活动(有报名)
                    // 联表视频
                    $data['join'][] = ['table' => 'information_video iv', 'cond' => '(en.id=iv.forid and iv.is_propert_news=1 and iv.status=1 and iv.resource_type=1)', 'type' => 'left'];
                    $data['join_already'][] = 'information_video';
                    $otherFields = ', iv.video_url';
                } else {
                    // 联表报名
                    $data['join'][] = ['table' => 'signup su', 'cond' => "(en.id=su.forid and su.start_time<={$time} and su.end_time>{$time})", 'type' => 'left'];
                    $data['join_already'][] = 'signup';
                    $otherFields = ', su.id as sign_id, su.name as sign_name, su.subname as sign_subname, join_num as sign_num';
                }
                $data['fields'] = $data['fields'] . $otherFields;

                // 条件/联表/分组
                $this->buildWhere($params, $data);
                $estatesData = (new Estatesnew())->getListByParams($data);

                if(empty($estatesData['code']) || 1 != $estatesData['code']) {
                    $this->error($estatesData);
                }
                $estatesData = $estatesData['result'];
                if(!empty($estatesData)) {
                    foreach($estatesData as &$ev) {

                        //标签
                        if(empty($labelArray[$ev['id']])){
                            $ev['label_id'] = 0;
                        }else{
                            $ev['label_id'] = $labelArray[$ev['id']];
                        }

                        // 建筑用途
                        $housePurpose = !empty($ev['house_purpose']) ? explode(',', $ev['house_purpose']) : [];
                        $ev['house_purpose'] = array_slice($housePurpose, 0, 1)[0] ?? 1;// 取出第一个
                        // 价格
                        $ev['price'] = (int)$ev['price'];
                        // 标签
                        $featureTag = !empty($ev['feature_tag']) ? explode(',', $ev['feature_tag']) : [];
                        $ev['feature_tag'] = array_slice($featureTag, 0, 2);// 取出前两个
                        //是否显示封面
                        $ev['has_cover'] = !empty($ev['list_cover'])&&!empty($ev['logo'])? 1 : 0;
                        // 多图
                        if(!empty($tmp[$ev['id']]['imgs'])) {
                            $eimgs = explode(',', $tmp[$ev['id']]['imgs']);
                            $ev['imgs'] = $eimgs;
                        } else {
                            $ev['imgs'] = [];
                        }
                        // 联表结果默认值
                        if(!$subject['type']) {
                            $ev['video_url'] = $ev['video_url'] ?? '';
                        } else {
                            $ev['sign_id'] = $ev['sign_id'] ?? 0;
                            $ev['sign_name'] = $ev['sign_name'] ?? "";
                            $ev['sign_subname'] = $ev['sign_subname'] ?? "";
                            $ev['sign_num'] = $ev['sign_num'] ?? 0;
                        }
                    }
                } else {
                    $estatesData = [];
                }
            }
        }

        return $estatesData;
    }

    /**
     * 直播列表
     */
    protected function getLiveRoomList($subject)
    {
        $time = time();

        $liveRoomData = [];
        if(!empty($subject['config']['liveRoom'])) {
            $liveRoom = $subject['config']['liveRoom'];
            // 排序
            $sort = array_column($liveRoom, 'forsort');
            array_multisort($sort, SORT_DESC, $liveRoom);
            // 数据
            $ids = array_column($liveRoom, 'forid');// 专题关联的楼盘
            if(!empty($ids)) {
                $searchLiveRoom = [
                    'status' => [1, 2], // 直播中或未开始
                    'ids' => $ids,
                    'sort' => 'desc',
                ];
                $liveRoomRes = (new Liveroom())->getList($searchLiveRoom);
                if(empty($liveRoomRes['code']) || 1 != $liveRoomRes['code']) {
                    $this->error($liveRoomRes);
                }
                $liveRoomRes = $liveRoomRes['result']['list'] ?? [];

                if(!empty($liveRoomRes)) {
                    $li = 1;
                    foreach($liveRoomRes as &$lv) {
                        if($lv['end_time'] <= $time) {// 已结束活动不显示
                            continue;
                        }
                        if($li > 6) {// 只取两条
                            break;
                        }
                        $liveRoomData[] = [
                            'id' => $lv['id'],
                            'room_name' => $lv['room_name'],
                            'cover' => $lv['cover'],
                            'status' => $lv['status'],
                            'room_url' => $lv['room_url'],
                            'start_time' => date('Y-m-d H:i:s', $lv['start_time']),
                            'end_time' => date('Y-m-d H:i:s', $lv['end_time']),
                        ];
                        $li++;
                    }
                }
            }
        }
        return $liveRoomData;
    }

    public function getLabelList($data){
        if (empty($data['config']['label'])) {
            return [];
        }

        $data = $data['config']['label'];
        $result = [];
        foreach ($data as $key => $value) {
            if ($value['status'] != 2) {
                $result[$key]['id'] = $value['forid'];
                $result[$key]['name'] = $value['forname'];
            }
        }
        return $result ;
    }

}