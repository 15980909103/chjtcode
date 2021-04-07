<?php


namespace app\admin\controller;


use app\common\base\AdminBaseController;
use app\server\admin\City;
use app\server\admin\CityPriceLog;

class CityPriceLogController extends AdminBaseController
{
    //城市历史价格表
    public function cityPriceList()
    {
        $region_no = $this->request->get('city');
        $start = $this->request->get('startdate');
        $end = $this->request->get('enddate');

        $where = [];
        if (empty($region_no) || $region_no == -1) {
            $regionRes = $this->getMyCity();
            $cityIds = !empty($regionRes['data']) ? array_column($regionRes['data'], 'id') : [];
            $where['city_no'] = ['in', $cityIds];
        } else {
            $where['city_no'] = intval($region_no);
        }

        if (!empty($start)) {
            $where['start_date'] = $start;
        }
        if (!empty($end)) {
            $where['end_date'] = $end;
        }

        $field = 'c.id,
        c.city_no,
        c.price as new_price,
        c.city_no_name as cname,
        c.city_price as new_city_price,
        c.recent_opening,
        c.deal,
        c.show_time as change_time,
        c.on_sale';
        $res = (new CityPriceLog())->list($where, $field);

        if ($res['code'] == 0) {
            $this->error($res['msg']);
        }
        foreach ($res['result']['list'] as $key => &$value) {
            $value['new_city_price'] = json_decode($value['new_city_price'], true);
            $value['change_time'] = date('Y-m', $value['change_time']);
            $value['city_no'] = (string)$value['city_no'];
        }

        return $this->success($res['result']);
    }

    //城市历史价格编辑
    public function cityPriceLogEdit()
    {
        $param = $this->request->param();

        if(empty($param['city_no'])){
            return $this->error('请选择城市区域!');
        }

        $where = [
            ['id', '=', $param['id']]
        ];

        //去掉id为0的城市
        foreach ($param['new_city_price'] as $k => $v){
           if(empty($v['id'])){
               unset($param['new_city_price'][$k]);
           }
        }

        if(!empty($param['change_time'])) {
            $time = strtotime(date('Y-m',strtotime($param['change_time']))) ;
        } else {
            $time = strtotime(date('Y-m', time()));
        }

        $data = [
            'id'             => $param['id'],
            'show_time'      => $time,
            'price'          => $param['new_price'],
            'city_price'     => json_encode($param['new_city_price']),
            'city_no'        => $param['city_no'],
            'recent_opening' => $param['recent_opening'], //开盘
            'on_sale'        => $param['on_sale'], //在售
            'deal'           => $param['deal'],   //成交
            'city_no_name'   => $param['cname']   //城市名字
        ];

        $res = (new CityPriceLog())->edit($where, $data,$time);

        if ($res['code'] == 0) {
            $this->error($res['msg']);
        }
        return $this->success();
    }


    public function cityPriceLogDelete()
    {
        $param = $this->request->param();
        $where = [
            ['id', '=', $param['id']]
        ];

        $res = (new CityPriceLog())->cityPriceLogDelete($where);
        if ($res['code'] == 0) {
            $this->error($res['msg']);
        }
        return $this->success();

    }

    //城市区域历史价格表
    public function cityAreaPriceList()
    {
        $region_no = $this->request->param('region_no');
        $start = $this->request->param('start_time');
        $end = $this->request->param('end_time');
        $cityId = $this->request->param('city_id');

        $where = [];
        if (empty($region_no) || $region_no == -1) {
            $regionRes = $this->getMyCity();
            $cityIds = !empty($regionRes['data']) ? array_column($regionRes['data'], 'id') : [];
            $where['city_no'] = ['in', $cityIds];
        } else {
            $where['city_no'] = intval($region_no);
        }

        if (!empty($start)) {
            $where['start_date'] = $start;
        }
        if (!empty($end)) {
            $where['end_date'] = $end;
        }

        $where['id'] = $cityId;


        $field = 'c.id,c.city_price as new_city_price,c.show_time';
        $res = (new CityPriceLog())->list($where, $field, 2);

        if ($res['code'] == 0) {
            $this->error($res['msg']);
        }
        foreach ($res['result']['list'] as $key => $value) {
            $value['new_city_price'] = json_decode($value['new_city_price'], true);

            foreach ($value['new_city_price'] as $k => $v) {

                $resData[] = [
                    'id' => $v['id'],
                    'cname'       => $v['name'],
                    'new_price'   => $v['price'],
                    'change_time' => date('Y-m', $value['show_time'])
                ];
            }

        }
//        var_dump($res);
        $result['list'] = $resData;
        return $this->success($result);

    }

    //所属区域
    public function getAreaInfo($region_no)
    {
        $where['pid'] = intval($region_no);
        $res = (new City())->getSiteAreas($where)['result'];
        $res = array_column($res, 'cname', 'id');
        return $res;
    }


}