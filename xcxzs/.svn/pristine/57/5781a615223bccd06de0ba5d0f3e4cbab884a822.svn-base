<?php

namespace app\server\index;

use Co\WaitGroup;
use Exception;
use app\common\base\ServerBase;


/*
 * 公用城市操作
 * */
class ArtColumn extends ServerBase
{
    public function delColumnByArtId($id){
        if(empty($id)){

            return false;
        }

       $result  =  $this->db->name('article_cloumn')->where('article_id','=',$id)->delete();
       return  $result === false ?  false:true;

    }
}
