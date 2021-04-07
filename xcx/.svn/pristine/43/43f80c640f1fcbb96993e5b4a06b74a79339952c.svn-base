<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: pl125 <xskjs888@163.com>
// +----------------------------------------------------------------------

namespace app\server\admin;

use app\common\traits\TraitInstance;
use app\common\base\ServerBase;
use think\Db;
use think\Exception;

/*
 *
 * */
class Log extends ServerBase
{
    //use TraitInstance;

    public function sendpayLog($search = [], $pagesize = 50,$field='*'){
        $result = array(
            'list'  =>  [],
            'total' =>  0,
            'last_page' =>  0,
            'current_page'  =>  0
        );
        $where = [];
        if(!empty($search['out_trade_no'])){
            $where[] = ['out_trade_no','like','%'.$search['out_trade_no'].'%'];
        }
        if(!empty($search['startdate'])){
            $time = strtotime($search['startdate']);
            $where[]=  ['create_time','>=', $time];
        }
        if(!empty($search['enddate'])){
            $time = strtotime($search['enddate']." +1 day");
            $where[]=  ['create_time','<', $time];
        }
        $list = $this->db->name('subsidy_wxsend_log')->field($field)
            ->where($where)->paginate($pagesize);

        if($list->isEmpty()){
            $result['list'] = [];
        }else{
            $list=$list->toArray();
            $result['total'] = $list['total'];
            $result['last_page'] = $list['last_page'];
            $result['current_page'] = $list['current_page'];
            $result['list'] =$list['data'];
        }
        return $this->responseOk($result);
    }

}
