<?php

/**
 * Created by PhpStorm.
 * User: USER022
 * Date: 2019/1/3
 * Time: 14:48
 */
include 'AdminController.php';
class User extends AdminController
{
    public function user_list(){
        return $this->render('user_list',$data);
    }
    public function set_where($select,$Db){
        foreach($select as $k=>$v){
            if($k=='nickName')
                $Db->where_like($k,'%'.$v.'%');
            else
                $Db->where_equalTo($k,$v);
        }
        return $Db;
    }
    public function user_page(){
        $curr=Context::Post('curr');
        $limit=Context::Post('limit');
        $select['nickName']=trim(Context::Post('nickName'));
        $select=array_filter($select,function($val){$tmp=$val ===  ''; return !$tmp;});
        if(!empty($select)){
            $userDb=$this->db->Name('xcx_user');
            $userDb=$this->set_where($select,$userDb);
            $data = $userDb->select()->page($curr,$limit)->orderBy('create_time','desc')->execute();
            $userDb=$this->set_where($select,$userDb);
            $count = $userDb->select('count(*)')->firstColumn();
        }else{
            $data = $this->db->Name('xcx_user')->select()->page($curr,$limit)->orderBy('create_time','desc')->execute();
            $count = $this->db->Name('xcx_user')->select('count(*)')->firstColumn();
        }
        if(!empty($data)){
            foreach($data as &$val){
                $city=(empty($val['country'])?'':$val['country']).' '.(empty($val['province'])?'':$val['province']).' '.(empty($val['city'])?'':$val['city']);
                $val['gender']=empty($val['gender'])?'未知':($val['gender']=='1'?'男':'女');
                $val['city']=trim($city);
                $val['phone']=empty($val['phone'])?'':$val['phone'];
                $val['language']=empty($val['language'])?'':$val['language'];
                $val['create_time']=date('Y-m-d H:i:s',$val['create_time']);
            }
            echo json_encode(['success'=>true,'data'=>$data,'count'=>$count]);
        }else{
            echo json_encode(['success'=>false,'curr'=>$curr]);
        }
    }

    public function user_edit(){
        $id=Context::Get('id');
        $data['data']=$this->db->Name('xcx_user')->select()->where_equalTo('id', $id)->firstRow();;
        return $this->render('user_edit',$data);
    }
    public function user_doedit(){
        $id=Context::Post('id');
        $data['nickName']=Context::Post('nickname');
        $data['gender']=Context::Post('gender');
        $data['country']=Context::Post('country');
        $data['province']=Context::Post('province');
        $data['city']=Context::Post('city');
        $data['phone']=Context::Post('phone');
        $data['update_time']=time();
        $res=$this->db->Name('xcx_user')->update($data)->where_equalTo('id',$id)->execute();
        if($res)
            echo json_encode(['success'=>true]);
        else
            echo json_encode(['success'=>false,'message'=>'保存失败']);
    }
    public function user_del(){
        $id=Context::Post('id');
        $res=$this->db->Name('xcx_user')->delete()->where_equalTo('id',$id)->execute();
        if($res)
            echo json_encode(['success'=>true]);
        else
            echo json_encode(['success'=>false,'message'=>'删除失败']);
    }
}