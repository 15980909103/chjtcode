<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: pl125 <xskjs888@163.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\base\AdminBaseController;
use app\server\admin\Sys;


class SysController extends AdminBaseController
{
    public function sysInfo(){
        $data = $this->request->param();

        if(is_array($data['key'])){
            $data['key'] = ['in',$data['key']];
        }
        $rs = (new Sys())->sysInfo([
            'key'=> $data['key']
        ])['result'];

        $this->success($rs);
    }
    public function sysEdit(){
        $data = $this->request->param();
        if(!is_array($data['key'])){
            $data['key'] = [$data['key']];
        }
       $key= $data['key'];


       foreach ($key as $k=>$val){
           if(is_array($val)){
               $val = json_encode($val);
           }
           if($k == 'kfmobilecode'){
               $val = json_decode($val,true);
               $val = !empty($val) ? implode(',', array_column($val, 'url')) : '';
           }
           $rs = (new Sys())->sysEdit([
               'key'=> $k,
               'val'=> $val
           ])['result'];
       }

        $this->success($rs,'操作成功');
    }

    
}
