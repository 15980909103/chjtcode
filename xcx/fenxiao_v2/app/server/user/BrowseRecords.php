<?php


namespace app\server\user;


use app\common\base\ServerBase;
use app\common\MyConst;
use app\index\controller\EstatesController;
use app\server\estates\Tag as estatesTag;
use Exception;
use think\facade\Db;

class BrowseRecords extends ServerBase
{

    /**
     * 我的浏览记录
     * @param $userId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function browseRecords($userId)
    {
        try {
            $discountTime = time();
            $saleStatusArray = MyConst::ESTATESNEW_SALE_STATUS;
            $housePurposeArray = MyConst::HOUSE_PURPOSE;

            $table_name = $this->getTableName();
            $data = [];
            $where = [
                ['b.user_id', '=', $userId],
                ['e.status', '=', 1],
                ['e.is_delete', '=', 0],
            ];

            $list = $this->db->name($table_name)->alias('b')
                ->join('estates_new e', 'e.id = b.building_id')
                ->leftJoin('estates_has_tag eht', 'e.id=eht.estate_id and eht.type=1')
                ->where($where)
                ->order('b.browse_time', 'desc')
                ->group('e.id')
                ->field('b.id,
                 e.id as estates_id,
                b.browse_time,
                b.name,
                b.price,
                b.built_area,
                b.area,
                b.business_district,
                e.name,
                e.list_cover as list_cover,
                e.sale_status,
                e.house_purpose,
                e.discount,
                e.logo,
                e.detail_cover,
                e.status,
                e.is_delete,
                GROUP_CONCAT(eht.tag_id) as feature_tag
                ')
                ->select();

            if ($list->isEmpty()) {
                return $this->responseOk([]);
            }
            foreach ($list as $key => $value) {

                $value['sale_status_name'] = $saleStatusArray[$value['sale_status']];
                //处理住宅
                $house_purpose = explode(',', $value['house_purpose']);
//                $value['house_purpose_name'] = $housePurposeArray[$house_purpose[0]] ?? '';
                $value['house_purpose'] = $house_purpose[0];

                //特色标签处理
                if (empty($value['feature_tag'])) {
                    $value['feature_tag'] = [];
                } else {
                    $value['feature_tag'] = explode(',', $value['feature_tag']);
                }

                /**
                 * 卖点
                 */
                $sellingPoint = [];
                $discount = json_decode($value['discount'], TRUE);
                if (!empty($discount)) {
                    foreach ($discount as $dis) {// 找出时间范围内
                        $startTime = strtotime($dis['start_time']);
                        $endTime = strtotime($dis['end_time']);
                        if ($startTime <= $discountTime && $endTime >= $discountTime) {
                            $sellingPoint[] = ['title' => $dis['title'], 'type' => 'discount'];
                        }
                    }
                }

                $value['selling_point'] = $sellingPoint;

                $data[$value['browse_time']][] = $value;
            }

            //获取公共标签数据
            $lable = $this->lable();


            foreach ($data as $key => $v) {
                foreach ($v as $k => &$vi) {

                    $lableData = [$vi['sale_status_name']];
                    if (!empty($vi['house_purpose']) && isset($lable['house_purpose'][$vi['house_purpose']])) {
                        array_push($lableData, $lable['house_purpose'][$vi['house_purpose']]);
                    }
                    if (!empty($vi['feature_tag'][0]) && isset($lable['feature_tag'][$vi['feature_tag'][0]])) {
                        array_push($lableData, $lable['feature_tag'][$vi['feature_tag'][0]]);
                    }
                    if (!empty($vi['feature_tag'][1]) && isset($lable['feature_tag'][$vi['feature_tag'][1]])) {
                        array_push($lableData, $lable['feature_tag'][$vi['feature_tag'][1]]);
                    }

                    $sellingData = [];
                    if (!empty($vi['selling_point'])) {
                        foreach ($vi['selling_point'] as $sv) {
                            $sellingData[] = [
                                'type' => 0,
                                'name' => $sv['title'],
                                'img'  => '/9house/static/logo.png'
                            ];
                        }
                    }


                    $vi['id'] = $vi['estates_id'];
                    $vi['type'] = 8;
                    $vi['info'] = [
                        'name'  => $vi['name'],
                        "tip"   => $lableData,
                        'price' => $vi['price'],
                        'site'  => $vi['area'],
                        'area'  => $vi['built_area'],
                        'lab'   => $sellingData
                    ];
                    if(!empty($vi['logo']) && !empty($vi['detail_cover'])){
                        $vi['cover'] = 1;
                    }else{
                        $vi['cover'] = 0;
                    }
                    if($vi['status'] == 1 && $vi['is_delete'] ==0){
                        $vi['status_delete'] = 0;
                    }else{
                        $vi['status_delete'] = 1;//被下架或者删除了
                    }
                    $vi['img'] = [$vi['list_cover']];
                }

                $res[] = [
                    'time' => date('Y-m-d', $key),
                    'list' => $v
                ];
            }
            return $this->responseOk($res);

        } catch (Exception $exception) {
            return $this->responseFail($exception->getMessage());
        }
    }

    /**
     * 我的浏览记录表创建
     * @return bool
     */
    public function createBrowseRecord($table_name)
    {
        try {
            $table_name = '9h_'.$table_name;
            $this->db->startTrans();
            $sql = "CREATE TABLE `$table_name` (
                      `id` int NOT NULL AUTO_INCREMENT,
                      `building_id` int NOT NULL COMMENT '楼盘id',
                      `user_id` int NOT NULL DEFAULT '0' COMMENT '用户id',
                      `city_no` int DEFAULT '0' COMMENT '城市编号',
                      `browse_time` int NOT NULL DEFAULT '0' COMMENT '浏览时间',
                      `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '楼盘名称',
                      `built_area` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '面积',
                      `price` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '价格',
                      `business_district` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '商圈',
                      `area` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '区 如思明区等',
                      `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1普通的浏览 2活动浏览',
                      `create_time` int NOT NULL DEFAULT '0',
                      `update_time` int NOT NULL DEFAULT '0',
                      PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='楼盘浏览记录表'";
            $this->db->query($sql);
            $this->db->commit();
            return true;
        } catch (Exception $exception) {
            $this->db->rollback();
            return false;
        }
    }

    /**
     * 获取简单信息
     */
    public function getSimpleList($where = [], $fields = '*')
    {
        try {
            $month = date('Ym', time());
            $table_name = 'estates_new_browse_' . $month;

            //判断数据表是否存在
            $result = $this->db->query("SHOW TABLES LIKE '" . "9h_" . $table_name . "'");
            if (empty($result)) {
                return $this->responseOk([]);
            }

            $myDB = $this->db->name($table_name);
            if (!empty($where)) {
                $myDB->where($where);
            }
            $myDB->field($fields);
            $record = $myDB->select()->toArray();
            if (empty($record)) {
                $record = [];
            }
            return $this->responseOk($record);
        } catch (Exception $e) {
            return $this->responseFail($e->getMessage());
        }
    }

    public function lable()
    {
        $rs = [
            'house_purpose'            => MyConst::HOUSE_PURPOSE, //楼盘的建筑用途列表
            'buildingphotos_categorys' => MyConst::BUILDINGPHOTOS_CATEGORYS, //楼盘的相册类型列表
            'orientation'              => MyConst::ORIENTATION, //朝向列表
            'rooms'                    => MyConst::ROOMS, //几居室列表
            'estatesnew_sale_status'   => MyConst::ESTATESNEW_SALE_STATUS, //新房销售状态列表
            'feature_tag'              => (new estatesTag())->getTagList(), //特色标签
        ];
        return $rs;
    }

    //获取配置返回表名
    public function getTableName()
    {
        try {
            $table_name = '';
            $info = $this->db->name('sysset')->where('key', 'estates_browse')->field('val')->find();
            if (!$info) {
                return false;
            }
            $val = json_decode($info['val'], true);

            $year = date('Y', time());
            $month = date('m', time());
            $day = date('d', time());
            $getTableTime = explode('_', $val['table_name'])[3];
            $oldYear = date('m', strtotime($getTableTime));
            $oldMonth = date('m', strtotime($getTableTime));
            $oldDay = date('d', strtotime($getTableTime));

            switch ($val['type']) {
                case 'month':
                    //判断当前配置是否过期
                    //年相同时候
                    if($year == $oldYear){
                        if ($month - $oldMonth < $val['number']) {
                            return $val['table_name'];
                        }
                    }

                    //以当前时间节点建立新的表
                    $table_name = 'estates_new_browse_' . $year .'-'. $month;
                    break;
                case 'day' :
                    //判断当前配置是否过期
                    if($month == $oldMonth){
                        if ($day - $oldDay < $val['number']) {
                            return $val['table_name'];
                        }
                    }
                    //以当前时间节点建立新的表
                    $table_name = 'estates_new_browse_' . $year .'-'. $month .'-'. $day;
                    break;
            }
            //判断数据表是否存在
            $result = $this->db->query("SHOW TABLES LIKE '" . "9h_" . $table_name . "'");
            if (empty($result)) {
                $this->createBrowseRecord($table_name);
                //缓存新表数据
                $this->db->name('sysset')->where('key', 'estates_browse')->update([
                    'val' => json_encode([
                        'type'       => $val['type'],
                        'number'     => $val['number'],
                        'table_name' => $table_name //更新表名
                    ])
                ]);
            }
            return $table_name;
        } catch (Exception $exception) {
            return $this->responseFail($exception->getMessage());
        }
    }

    /**
     * 更新浏览记录
     * @param $id
     * @param $userId
     * @param $from
     * @param $estates
     * @return bool
     */
    public function updateEstaes($id,$userId,$from,$estates){
        try {
            // 浏览记录表处理
            $time = time();
            $isBrowse = TRUE;
            $timeMonth = date('Ym', time());
            //获取数据表
            $table_name = (new BrowseRecords)->getTableName();
            if($isBrowse) {
                $this->db->startTrans();
                // 楼盘浏览量累加
                $updateEstate = [
                    'num_read' => Db::raw('num_read+1'),
                    'num_read_real' => Db::raw('num_read_real+1'),
                ];
                if('search' == $from) {// 热搜点击
                    $updateEstate['num_read_search'] = Db::raw('num_read_search+1');
                    $updateEstate['num_read_search_real'] = Db::raw('num_read_search_real+1');
                }
                $resEstate = $this->db->name('estates_new')->where(['id' => $id])->update($updateEstate);
                // 楼盘浏览记录日志
                $insertBrowse = [
                    'building_id' => $id,
                    'user_id' => $userId,
                    'city_no' => $estates['city'],
                    'browse_time' => strtotime(date('Y-m-d')),
                    'name' => $estates['name'],
                    'built_area' => $estates['built_area'],
                    'price' => $estates['price'],
                    'business_district' => $estates['business_area_str'],
                    'area' => $estates['area_str'],
                    'create_time' => $time,
                    'update_time' => $time,
                    'type'  => empty($params['type']) ? 1 : 2 //类型
                ];
                $resBrowse = $this->db->name($table_name)->insert($insertBrowse);
                // 提交
                if($resEstate && $resBrowse) {
                    $this->db->commit();
                } else {
                    $this->db->rollback();
                }

        }
            return true;
        }catch (Exception $exception){
            return false;
        }
    }
}