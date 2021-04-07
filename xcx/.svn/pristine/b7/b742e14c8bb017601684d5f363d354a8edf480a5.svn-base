<?php


namespace app\server\admin;


use app\common\base\ServerBase;

class Notification extends ServerBase
{
    public function bannerMessage($params , $pageSize){
        try {
            $where = [

            ];
            if(!empty($params['status'])){
                $where[] = [
                    'status','=',$params['status']
                ];
            }

            if(!empty($params['type'])){
                $where[] = [
                    'type','=',$params['type']
                ];
            }

            $res = $this->db->name('notification')->where($where)->paginate($pageSize);


            if ($res->isEmpty()) {

                $result['list'] = [];
                $result['total'] = 0;
                $result['last_page'] = 0;
                $result['current_page'] = 0;
            } else {
                $list = $res->toArray();

                foreach ($list['data'] as $key => &$value){
                    $value['type'] = $value['type'] == 1 ? '广告':'其他';
                }
                $result['total'] = $list['total'];
                $result['last_page'] = $list['last_page'];
                $result['current_page'] = $list['current_page'];
                $result['list'] = $list['data'];
            }


            return $this->responseOk($result);
        }catch (\Exception $exception){
            return $this->responseFail($exception->getMessage());
        }
    }
}