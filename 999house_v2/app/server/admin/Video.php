<?php

namespace app\server\admin;

use app\common\traits\TraitInstance;
use app\common\base\ServerBase;
use think\Db;
use think\Exception;

/*
 *
 * */
class Video extends ServerBase
{
    
    /**
     * 简单列表
     */
    public function getSimpleVideo($where = [], $fields = "*", $order = [], $pageSize = 0)
    {
        try {
            $myDB = $this->db->name('information_video');
            if(!empty($where)) {
                $myDB->where($where);
            }
            $myDB->field($fields);
            if(!empty($order)) {
                $myDB->order($order);
            }
            if(!empty($pageSize)) {
                $myDB->limit($pageSize);
            }
            $res = $myDB->select()->toArray();
            if(empty($res)) {
                $res = [];
            }
            return $this->responseOk($res);
        } catch(Exception $e) {
            return $this->responseFail($e->getMessage());
        }
    }
}
