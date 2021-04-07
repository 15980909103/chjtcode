<?php

namespace app\server\admin;

use Exception;
use app\common\base\ServerBase;


/*
 * 公用城市操作
 * */
class Upload extends ServerBase
{
    /**
     * 添加
     */
    public function add($data){
        if(!$data){
            return false;
        }
        $result = $this->db->name('upload_file')->insert($data,true);

        return  $result === false ? false:$result;
    }
}
