<?php


namespace app\server\admin;


use app\common\base\ServerBase;
use think\db\concern\TimeFieldQuery;

class CityPriceLog extends ServerBase
{
    //价格变动列表
    public function list($search, $field, $type = 1, $pageSize = 50)
    {
        try {
            if (isset($search['city_no'])) {
                if (is_array($search['city_no'])) {
                    $where[] = ['c.city_no', $search['city_no'][0], $search['city_no'][1]];
                } else {
                    $where[] = ['c.city_no', '=', intval($search['city_no'])];
                }
            }
            if(isset($search['id'])){
                $where[] = ['c.id','=',$search['id']];
            }

            if (!empty($search['start_date'])) {
                $where[] = ['c.show_time', '>=', strtotime($search['start_date'])];
            }
            if (!empty($search['end_date'])) {
                $where[] = ['c.show_time', '<=', strtotime($search['end_date'] . ' +1 day')];
            }

            if ($type == 1) {
                $res = $this->db->name('city_price_statistics')->alias('c')
//                    ->join('site_city s', 's.id = c.city_no')
                    ->where($where)->field($field)->paginate($pageSize);

                if ($res->isEmpty()) {
                    $result['list'] = [];
                    $result['total'] = 0;
                    $result['last_page'] = 0;
                    $result['current_page'] = 0;
                } else {
                    $list = $res->toArray();


                    $result['total'] = $list['total'];
                    $result['last_page'] = $list['last_page'];
                    $result['current_page'] = $list['current_page'];
                    $result['list'] = $list['data'];
                }
            } else {
                $res = $this->db->name('city_price_statistics')->alias('c')
                    ->where($where)->field($field)->select();

                if ($res->isEmpty()) {
                    $result['list'] = [];

                } else {
                    $list = $res->toArray();
                    $result['list'] = $list;
                }
            }

            return $this->responseOk($result);
        } catch (\Exception $exception) {
            return $this->responseFail($exception->getMessage());
        }
    }

    /**
     * 编辑
     * @param $where
     * @param $data
     * @param $time
     * @return array
     */
    public function edit($where, $data,$time)
    {

        try {
//            $time = strtotime(date('Y-m', time()));
            $last_time = strtotime(date('Y-m', $time) . ' -1 month'); //前一个月

            //查找当前月数据
            $cityWhere = [
                'show_time' => $time,
                'city_no'   => $data['city_no']
            ];
            $info = $this->db->name('city_price_statistics')->where($cityWhere)->find();

            $price = $data['price'];

            $cityWhere1 = [
                'city_no'   => $data['city_no']
            ];
            $last_price = $this->db->name('city_price_statistics')->where($cityWhere1)->where('show_time','<=',$last_time)->find();
            if (!empty($last_price) && $last_price['price'] > 0) {
                $data['last_month_rate'] = bcdiv(($price-$last_price['price']), $last_price['price'], 2)*100;
            } else {
                $data['last_month_rate'] = 0;
            }
            $q_money       = json_decode($data['city_price'],true);
            $q_last_money  = json_decode($last_price['city_price'],true);
            foreach ($q_money as $k => $v){
               if(empty($q_last_money)){
                   $q_money[$k]['type'] =    2;
                   $q_money[$k]['num'] =    0;
               }else{
                   foreach ($q_last_money as $key=> $value){
                       if($v['id'] == $value['id']){
                           $rate = bcdiv(($v['price']-$value['price']), $value['price'], 3)*100;
                           $q_money[$k]['type'] = $rate >0 ? 1:0;
                           $q_money[$k]['num'] =    $rate;
                           break;
                       }
                   }  //同比
               }

            }
            $data['city_price'] = json_encode($q_money);
            if (empty($data['id'])) {//插入
                if (!empty($info)) {
                    return $this->responseFail('当月记录已经存在，不能再创建，可以修改');
                }

                $this->db->name('city_price_statistics')->insert($data);
            } else {
                $this->db->name('city_price_statistics')->where($where)->update($data);
            }
            return $this->responseOk();
        } catch (\Exception $exception) {
            return $this->responseFail($exception->getMessage());
        }
    }


    public function cityPriceLogDelete($where)
    {
        try {
            $info = $this->db->name('city_price_change_log')->find();
            if (!$info) {
                return $this->responseFail('查无此记录');
            }
            $this->db->where($where)->delete();
            return $this->responseOk();
        } catch (\Exception $exception) {
            return $this->responseFail($exception->getMessage());
        }
    }

    /**
     * 根据月份取数据
     * @param $data
     * @param $city_no
     */
    public function getInfoByMonth($data,$city_no)
    {
         $rs = $this->db->name('city_price_statistics')
                ->where('show_time', '<=', $data)
                ->where('city_no','=',$city_no)
                ->order('show_time desc')
                ->find() ?? [];

//         echo $this->db->getLastSql();
         if(empty($rs)){
             $city = (new City())->getSityCitysInfo([
                 'id' => $city_no
             ])['result'];
             if(!empty($city)){
                 $rs['city_no'] = $city['id'];
                 $rs['city_no_name'] = $city['cname'];
             }
         }
        return $rs;
    }

    /**
     *  获取时间段 数据
     */
    public function getListByMonth($date, $end_date,$city_no)
    {
        $list = $this->db->name('city_price_statistics')
            ->where('show_time', '<=', $date)
            ->where('show_time', '>=', $end_date)
            ->where('city_no','=',$city_no)
            ->column('*', 'show_time');
//          echo $this->db->getLastSql();
        if (empty($list)) {
            return [];
        }
        return $list;
    }

    /**
     * 获取去年平均值
     */
    public function getVagByCity($time,$city_no){
        $Y   = intval(date('Y',$time)) -1;
        $start = strtotime("$Y-01");
        $end   = strtotime("$Y-12");
        $avg   = $this->db->name('city_price_statistics')
//            ->where('show_time', '<=', $start)
//            ->where('show_time', '>=', $end)
            ->where('city_no','=',$city_no)
            ->avg('price');
        return $avg;

    }
}