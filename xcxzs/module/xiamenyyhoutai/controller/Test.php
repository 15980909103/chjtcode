<?php

/**
 * Created by PhpStorm.
 * User: USER022
 * Date: 2019/1/3
 * Time: 14:48
 */
include 'AdminController.php';
class Test extends AdminController
{
    public function index(){
        $data=[];
        $data['aaa']='你好';
        return $this->render('index', $data);
    }
    public function db(){
        $data=[];
        $tmpData=$this->db->Name('logo')->select()->where_equalTo('id',1)->execute();
        $data['post']=Context::Post('aa');
        $data['data']=$tmpData;
        echo json_encode(['success'=>true,'data'=>$data]);
    }
}