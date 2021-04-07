<?php
namespace app\common\traits;

use app\common\MyConst;
use think\Exception;
use think\facade\Db;


/**
 * 楼盘
 * Trait estates
 */
trait TraitEstates{

    protected $hotEstates = null;

    /**
     * 列表server条件构造
     */
    protected function buildWhere($params, &$data)
    {
        $time = time();

        // 分组
        $group = [];

        $where = [];
        if(!empty($params['cond'])) {// 混合条件处理
            if(is_array($params['cond'])) {
                foreach($params['cond'] as $c) {
                    $other[] = $this->dealCond($c);
                }
            } else {
                $other = $this->dealCond($params['cond']);
            }
            if(!empty($other)) {
                $params = array_merge($params, $other);
            }
        }
        if (!empty($params['city_no'])) {// 城市
            $where[] = ['en.city', 'in', $params['city_no']];
        }
        if (!empty($params['area_no'])) {// 区域
            $where[] = ['en.area', 'in', $params['area_no']];
        }
        if (!empty($params['business_no'])) {// 商圈
            $where[] = ['en.business_area', 'in', $params['business_no']];
        }
        if (!empty($params['street_no'])) {// 街道
            $where[] = ['en.street', '=', $params['street_no']];
        }
        if (!empty($params['subway'])) {// 地铁线
            // $where[] = ['', 'exp', Db::Raw("FIND_IN_SET({$params['subway']}, en.subways)")];
            $subwaySqlStr = $this->dealFindRaw('en.subways', $params['subway']);
            if(!empty($subwaySqlStr)) {
                $where[] = ['', 'exp', Db::Raw($subwaySqlStr)];
            }
        }
        if (!empty($params['sites'])) {// 地铁站点
            $where[] = ['subway_sites', 'in', $params['sites']];
        }
        if (!empty($params['price']) && !empty($params['price_type'])) {// 价格
            if ('total' == $params['price_type']) {// 总价
                $priceField = 'en.price_total';
                $mag = 0;
            } else {// 均价
                $priceField = 'en.price';
                // $mag = 10000;
                $mag = 0;
            }
            $priceSqlStr = $this->dealStrRaw($priceField, $params['price'], $mag);
            if(!empty($priceSqlStr)) {
                $where[] = ['', 'exp', Db::Raw($priceSqlStr)];
            }
        }
        if (!empty($params['rooms'])) {// 几居室
            if(empty($data['join_already']) || !in_array('estates_new_house', $data['join_already'])) {
                $data['join'][] = ['table' => 'estates_new_house eb', 'cond' => 'en.id=eb.estate_id', 'type' => 'left'];// 联表户型
                $group[] = 'en.id';
                $data['join_already'][] = 'estates_new_house';
            }
            $where[] = ['eb.rooms', 'in', $params['rooms']];
        }
        if (!empty($params['sale_status'])) {// 销售状态
            $where[] = ['en.sale_status', '=', $params['sale_status']];
        }
        if (!empty($params['opening_time'])) {// 本月开盘/近期开盘
            $date = date('Y-m', $time);
            switch ($params['opening_time']) {
                // 近期开盘
                case "near":
                    // 前后推一个月共三月
                    $startOpen = strtotime(date('Y-m', strtotime("-1 month")));// 上个月第一天
                    $endOpen = strtotime(date('Y-m', strtotime("+2 month")));// 下下个月第一天
                    break;
                // 本月开盘
                case "month":
                    $startOpen = strtotime($date);// 本月第一天
                    $endOpen = strtotime(date('Y-m', strtotime("+1 month")));// 下个月第一天
                    break;
                default:
                    break;
            }
            if (!empty($startOpen) && !empty($endOpen)) {
                $data['join'][] = ['table' => 'estates_new_time et', 'cond' => 'en.id=et.estate_id', 'type' => 'left'];// 联表开盘时间
                $group[] = 'en.id';
                $where[] = ['et.opening_time', '>=', $startOpen];
                $where[] = ['et.opening_time', '<', $endOpen];
            }
        }
        if(!empty($params['built_area'])) {// 面积
            if(empty($data['join_already']) || !in_array('estates_new_house', $data['join_already'])) {
                $data['join'][] = ['table' => 'estates_new_house eb', 'cond' => 'en.id=eb.estate_id', 'type' => 'left'];// 联表户型
                $group[] = 'en.id';
                $data['join_already'][] = 'estates_new_house';
            }
            $areaSqlStr = $this->dealStrRaw('eb.built_area', $params['built_area']);
            if(!empty($areaSqlStr)) {
                $where[] = ['', 'exp', Db::Raw($areaSqlStr)];
            }
        }
        if(!empty($params['is_cheap'])) {// 低价盘
            $where[] = ['en.is_cheap', '=', $params['is_cheap']];
        }
        if(!empty($params['tags']) && is_array($params['tags'])) {// 标签
            if(empty($data['join_already']) || !in_array('estates_has_tag', $data['join_already'])) {
                $data['join'][] = ['table' => 'estates_has_tag eht', 'cond' => '(en.id=eht.estate_id and eht.type=1)', 'type' => 'left'];// 联表楼盘所有标签
                $group[] = 'en.id';
                $data['join_already'][] = 'estates_has_tag';
            }
            $where[] = ['eht.tag_id', 'in', $params['tags']];
        }
        if(!empty($params['recommend'])) {// 推荐
            $where[] = ['recommend', '=', $params['recommend']];
        }
        if(!empty($params['video'])) {// 视频看房
            if(empty($data['join_already']) || !in_array('information_video', $data['join_already'])) {
                $data['join'][] = ['table' => 'information_video iv', 'cond' => '(en.id=iv.forid and iv.is_propert_news=1)', 'type' => 'inner'];// 联表视频表
                $group[] = 'en.id';
                $data['join_already'][] = 'information_video';
            }
        }
        if(!empty($params['estate_id'])) {// ID或ID集 底层会判断用等于还是in
            $where[] = ['en.id', 'in', $params['estate_id']];
        }
        if (!empty($params['house_purpose'])) {// 建筑用途
            // $where[] = ['en.house_purpose', 'in', $params['house_purpose']];
            $houseSqlStr = $this->dealFindRaw('en.house_purpose', $params['house_purpose']);
            if(!empty($houseSqlStr)) {
                $where[] = ['', 'exp', Db::Raw($houseSqlStr)];
            }
        }
        if (!empty($params['name'])) {// 楼盘名称/地址
            $where[] = ['en.name|en.address', 'like', "%{$params['name']}%"];
        }

        if (!empty($where)) {
            $data['where'] = $where;
        }

        // 分组
        if (!empty($group)) {
            $group = array_unique($group);// 去重
            $data['group'] = implode(',', $group);
        }
    }

    /**
     * 拼接原生-价格/面积字符串
     */
    protected function dealStrRaw($field, $data, $mag = 0)
    {
        $str = '';
        if(!empty($data)) {
            if(is_string($data)) {
                $data = [$data];
            }
            foreach($data as $v) {
                $arr = explode('-', $v);
                if (sizeof($arr) < 2) {// 只有一个数
                    $cond = (int)$arr['0'];
                    if($mag) {// 是否有倍率
                        $cond = bcmul($cond, $mag, 2);
                    }
                    if (strstr($arr['0'], "以上")) {
                        $part[] = "{$field}>={$cond}";
                    }
                    if (strstr($arr['0'], "以下")) {
                        $part[] = "{$field}<={$cond}";
                    }
                } else {// 两个数组成的区间
                    $start = (int)$arr['0'];
                    $end = (int)$arr['1'];
                    if($mag) {// 是否有倍率
                        $start = bcmul($start, $mag, 2);
                        $end = bcmul($end, $mag, 2);
                    }
                    $part[] = "({$field}>={$start} and {$field}<={$end})";
                }
            }
        }
        if(!empty($part)) {
            $part = implode(' or ', $part);
            // $str = "({$part})";
            $str = $part;
        }
        return $str;
    }

    /**
     * 拼接原生-find_in_set
     */
    protected function dealFindRaw($field, $data)
    {
        $str = '';
        if(!empty($data)) {
            if(is_string($data)) {
                $data = [$data];
            }
            foreach($data as $v) {
                $part[] = " FIND_IN_SET({$v}, {$field})";
            }
            $part = implode('or', $part);
            $str = $part;
        }
        return $str;
    }

    /**
     * 拆解混合条件
     */
    protected function dealCond($param)
    {
        $res = [];
        if(empty($param) || is_array($param)) {
            return $res;
        }

        switch($param) {
            // 在售
            case 1:
                $res = [
                    'sale_status' => 2,
                ];
                break;
            // 本月开盘
            case 2:
                $res = [
                    'opening_time' => 'month',
                ];
                break;
            // 近期开盘
            case 3:
                $res = [
                    'opening_time' => 'near',
                ];
                break;
            // 视频看房
            case 4:
                $res = [
                    'video' => 1,
                ];
                break;
        }

        return $res;
    }

    /**
     * 处理卖点信息
     */
    protected function dealSellingPoint($data)
    {
        $time = time();

        $sellingPoint = [];

        // 优惠信息
        $discount = json_decode($data['discount'], TRUE);
        if (!empty($discount)) {
            foreach ($discount as $dis) {// 找出时间范围内
                $startTime = strtotime($dis['start_time']);
                $endTime = strtotime($dis['end_time']);
                if ($startTime <= $time && $endTime >= $time) {
                    $sellingPoint[] = ['title' => $dis['title'], 'type' => 'discount'];
                }
            }
        }

        // 人气榜
        $redis = $this->getReids();
        if(!isset($this->hotEstates)) {// 未获取过
            $keyHot = MyConst::ESTATES_LIST_POPULAR;
            $areaList = $redis->hGet($keyHot, $data['area']);
            if(!empty($areaList)) {
                $areaList = explode(',', $areaList);
                if(!empty($areaList)) {
                    $this->hotEstates = $areaList;
                }
            }
        }
        if(!empty($this->hotEstates) && is_array($this->hotEstates)) {
            if(in_array($data['id'], $this->hotEstates)) {
                $sellingPoint[] = ['title' => "入围{$data['area_str']}人气楼盘榜", 'type' => 'hot'];
            }
        }

        return $sellingPoint;
    }
}
