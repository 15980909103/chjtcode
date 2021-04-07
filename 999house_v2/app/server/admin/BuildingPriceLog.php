<?php


namespace app\server\admin;


use app\common\base\ServerBase;

class BuildingPriceLog extends ServerBase
{
    //价格变动列表
    public function list($where, $field, $pageSize)
    {
        try {
            $res = $this->db->name('price_change_log')->where($where)->field($field)->paginate($pageSize);

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

            return $this->responseOk($result);
        } catch (\Exception $exception) {
            return $this->responseFail($exception->getMessage());
        }
    }


    public function edit($where, $data, $time)
    {
        try {
            if (empty($data['id'])) {

                // $time = time();
                $start = strtotime(date('Y-m-d',$time));
                $end = strtotime(date('Y-m-d',$time).' 23:59:59');

                $whereInfo[] = ['month_time','>=',$start];
                $whereInfo[] = ['month_time','<=',$end];
                $whereInfo[] = ['estate_id', '=', $data['estate_id']];

                //判断当天是否已经有价格，有就提示只能修改，没有就创建
                $info = $this->db->name('price_change_log')->where($whereInfo)->find();
                // var_dump($this->db->getLastSql());

                if($info){
                    return $this->responseFail('当天历史价格已经存在，请进行修改即可');
                }

                $data['create_time'] = time();
                $data['update_time'] = time();

                $res = $this->db->name('price_change_log')->insertGetId($data);
            } else {
                $data['update_time'] = time();

                $this->db->name('price_change_log')->where($where)->update($data);
                $res = $data['id'];
            }
            return $this->responseOk($res);
        } catch (\Exception $exception) {
            return $this->responseFail($exception->getMessage());
        }
    }
}