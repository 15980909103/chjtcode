<?php

namespace app\server\admin;

use Exception;
use app\common\base\ServerBase;


/*
 * 公用城市操作
 * */
class City extends ServerBase
{
    /**
     * 获取通用基础的城市列表
     * @param array $search
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList($search = []){
        $where = [];
        if(!empty($search['canme'])){
            $where[]=  ['canme','like', '%'.$search['canme'].'%'];
        }
        if(!empty($search['id'])){
            $where[]=  ['id','=', intval($search['id'])];
        }
        if(isset($search['pid'])){
            $where[]=  ['pid','=', intval($search['pid'])];
        }
        $result = array(
            'list'  =>  [],
            'total' =>  0,
            'last_page' =>  0,
            'current_page'  =>  0
        );
        $list = $this->db->name('sys_city')
            ->where($where)->select()->toArray();

        if(empty($list)){
            $list = [];
        }

        return $this->responseOk($list);
    }

    //=============== 站点市级操作 ==============//
    /**
     * 获取站点的城市列表
     */
    public function getSiteCitys($search=[],$fields='*'){
        $where = [];
        if(!empty($search['canme'])){
            $where[]=  ['canme','like', '%'.$search['canme'].'%'];
        }
        if(isset($search['pid'])){
            $where[]=  ['pid','=', intval($search['pid'])];
        }
        if(in_array($search['status'],['0','1'])){
            $where[]=  ['status','=', $search['status']];
        }
        if(in_array($search['is_hot'],['0','1'])){
            $where[]=  ['is_hot','=', $search['is_hot']];
        }

        $result = array(
            'list'  =>  [],
            'total' =>  0,
            'last_page' =>  0,
            'current_page'  =>  0
        );
        $list = $this->db->name('site_city')
            ->field($fields)
            ->where($where)->select()->toArray();

        if(empty($list)){
            $list = [];
        }

        return $this->responseOk($list);
    }

    public function getSityCitysInfo($search=[], $fields='*'){
        $rs = $this->db->name('site_city')
            ->field($fields)
            ->where($search)->find();
        return $this->responseOk($rs);
    }

    //添加操作
    public function siteCitysAdd($data)
    {
        try{
            $has = $this->db->name("site_city")->where([
                'id'=> $data['id']
            ])->value('id');
            if(!empty($has)){
                throw new Exception('该城市已经存在');
            }

            $id = $this->db->name("site_city")->insertGetId([
                'id'=> $data['id'],
                'cname'=> $data['cname'],
                'status'=> $data['status'],
                'is_hot'=> $data['is_hot'],
                'sort'=> $data['sort'],
                'pid'=> $data['pid'],
                'pcname'=> $data['pcname'],
                'lng'=> $data['lng'],
                'lat'=> $data['lat'],
                'update_time'=> 0
            ]);   //将数据存入并返回自增 ID
            if(empty($id)){
                throw new Exception('操作失败');
            }
            return $this->responseOk($id);
        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    //修改状态
    public function siteCitysEdit($id,$data){
        try{
            $id = intval($id);
            if(empty($id)){
                throw new Exception('缺少设置参数');
            }
            $has = $this->db->name("site_city")->where([
                'id'=> $id
            ])->value('id');
            if(empty($has)){
                throw new Exception('查无此数据');
            }

            unset($data['id']);//不可变更id
            unset($data['cname']);//不可变更
            $data['update_time'] = time();
            $rs = $this->db->name('site_city')->where(['id'=>$id])->update($data);
            if(empty($rs)){
                throw new Exception('操作失败');
            }
        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }


    public function siteCitysDel($id)
    {
        try{
            $res = $this->db->name("site_city")->where("id",$id)->delete();
            if($res){
                return $this->responseOk($res);
            }else{
                throw new Exception('操作失败');
            }

        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }
    //=============== 站点市级操作 end==============//

    //=============== 站点区级操作 ==============//
    public function getSiteAreas($search=[]){
        $where = [];
        if(!empty($search['canme'])){
            $where[]=  ['canme','like', '%'.$search['canme'].'%'];
        }
        if(isset($search['pid'])){
            if(is_array($search['pid'])) {
                $where[]=  ['pid', 'in', $search['pid']]; 
            } else {
                $where[]=  ['pid','=', intval($search['pid'])]; 
            }
        }
        if(isset($search['status'])&&in_array($search['status'],['0','1'])){
            $where[]=  ['status','=', $search['status']];
        }
        if(isset($search['is_custom'])&&in_array($search['is_custom'],['0','1'])){
            $where[]=  ['is_custom','=', $search['is_custom']];
        }

        $result = array(
            'list'  =>  [],
            'total' =>  0,
            'last_page' =>  0,
            'current_page'  =>  0
        );
        $list = $this->db->name('site_city_area')
            ->where($where)->select()->toArray();

        if(empty($list)){
            $list = [];
        }

        return $this->responseOk($list);
    }

    public function siteAreasAdd($data)
    {
        try{
            $has = $this->db->name("site_city_area")->where([
                'id'=> $data['id']
            ])->value('id');
            if(!empty($has)){
                throw new Exception('该区级编码已经存在');
            }

            //是否和默认城市编码冲突
            $has2 = $this->db->name("sys_city")->where([
                'id'=> $data['id']
            ])->value('id');
            if(!empty($has2)){
                throw new Exception('该区级编码不可设置');
            }

            $id = $this->db->name("site_city_area")->insertGetId([
                'id'=> $data['id'],
                'cname'=> $data['cname'],
                'status'=> $data['status'],
                'pid'=> $data['pid'],
                'is_custom'=> intval($data['is_custom']),
                'update_time'=> 0
            ]);   //将数据存入并返回自增 ID
            if(empty($id)){
                throw new Exception('操作失败');
            }
            return $this->responseOk($id);
        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }
    public function siteAreasEdit($id,$data){
        try{
            $id = intval($id);
            if(empty($id)){
                throw new Exception('缺少设置参数');
            }
            $has = $this->db->name("site_city_area")->where([
                'id'=> $data['id']
            ])->value('id');

            if(!empty($has)&&$id!=$has){
                throw new Exception('该区级编码已经存在');
            }

            //是否和默认城市编码冲突
            $has2 = $this->db->name("sys_city")->where([
                'id'=> $data['id']
            ])->value('id');
            if(!empty($has2)){
                throw new Exception('该区级编码不可设置');
            }

            $data['update_time'] = time();
            $rs = $this->db->name('site_city_area')->where(['id'=>$id])->update($data);
            if(empty($rs)){
                throw new Exception('操作失败');
            }

        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }
    public function siteAreasDel($id)
    {
        try{
            $res = $this->db->name("site_city_area")->where("id",$id)->delete();
            if($res){
                return $this->responseOk($res);
            }else{
                throw new Exception('操作失败');
            }

        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }
    //=============== 站点区级操作 end==============//


    //=============== 站点商圈操作 ==============//
    public function getSiteBusinessAreas($search=[]){
        $where = [];
        if(!empty($search['canme'])){
            $where[]=  ['canme','like', '%'.$search['canme'].'%'];
        }
        if(isset($search['city_no'])){
            if(is_array($search['city_no'])) {
                $where[]=  ['city_no', 'in', $search['city_no']];
            } else {
                $where[]=  ['city_no','=', intval($search['city_no'])];
            }
        }
        if(isset($search['area_no'])){
            if(is_array($search['area_no'])) {
                $where[]=  ['area_no','in', $search['area_no']]; 
            } else {
                $where[]=  ['area_no','=', $search['area_no']]; 
            }
        }
        if(isset($search['id'])) {
            $where[]=  ['id','in', $search['id']];
        }

        if(isset($search['status'])&&in_array($search['status'],['0','1'])){
            $where[]=  ['status','=', $search['status']];
        }

        $result = array(
            'list'  =>  [],
            'total' =>  0,
            'last_page' =>  0,
            'current_page'  =>  0
        );
        $list = $this->db->name('site_city_business_area')
            ->where($where)->select()->toArray();

        if(empty($list)){
            $list = [];
        }

        return $this->responseOk($list);
    }

    public function siteBusinessAreasAdd($data)
    {
        try{
            $has = $this->db->name("site_city_business_area")->where([
                'cname'=> $data['cname'],
                'area_no'=> $data['area_no'],
            ])->value('id');
            if(!empty($has)){
                throw new Exception('该商圈已经存在');
            }

            $id = $this->db->name("site_city_business_area")->insertGetId([
                'cname'=> $data['cname'],
                'status'=> $data['status'],
                'area_no'=> $data['area_no'],
                'city_no'=> $data['city_no'],
                'update_time'=> 0
            ]);   //将数据存入并返回自增 ID
            if(empty($id)){
                throw new Exception('操作失败');
            }
            return $this->responseOk($id);
        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }
    public function siteBusinessAreasEdit($search=[],$data){
        try{
            $id = intval($search['id']);
            if(empty($id)){
                throw new Exception('缺少设置参数');
            }
            if(!empty($data['cname'])){
                $has = $this->db->name("site_city_business_area")->where([
                    'cname'=> $data['cname'],
                    'area_no'=> $search['area_no'],
                ])->value('id');
                if(!empty($has)&&$id!=$has){
                    throw new Exception('该商圈已经存在');
                }
            }

            $data['update_time'] = time();
            $rs = $this->db->name('site_city_business_area')->where(['id'=>$id])->update($data);
            if(empty($rs)){
                throw new Exception('操作失败');
            }

        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }
    public function siteBusinessAreasDel($id)
    {
        try{
            $res = $this->db->name("site_city_business_area")->where("id",$id)->delete();
            if($res){
                return $this->responseOk($res);
            }else{
                throw new Exception('操作失败');
            }

        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }
    //=============== 站点商圈操作 end==============//

    //=============== 站点街道操作 ==============//
    public function getsiteStreet($search=[]){
        $where = [];
        if(!empty($search['canme'])){
            $where[]=  ['canme','like', '%'.$search['canme'].'%'];
        }
        if(isset($search['city_no'])){
            if(is_array($search['city_no'])){
                $where[]=  ['city_no',$search['city_no'][0], $search['city_no'][1]];
            }else{
                $where[]=  ['city_no','=', intval($search['city_no'])];
            }
        }

        if(isset($search['area_no'])){
            $where[]=  ['area_no','=', $search['area_no']];
        }

        if(isset($search['status'])&&in_array($search['status'],['0','1'])){
            $where[]=  ['status','=', $search['status']];
        }

        $result = array(
            'list'  =>  [],
            'total' =>  0,
            'last_page' =>  0,
            'current_page'  =>  0
        );
        $list = $this->db->name('site_city_street')
            ->where($where)->select()->toArray();

        if(empty($list)){
            $list = [];
        }

        return $this->responseOk($list);
    }

    public function siteStreetAdd($data)
    {
        try{
            $has = $this->db->name("site_city_street")->where([
                'cname'=> $data['cname'],
                'area_no'=> $data['area_no'],
            ])->value('id');
            if(!empty($has)){
                throw new Exception('该商圈已经存在');
            }

            $id = $this->db->name("site_city_street")->insertGetId([
                'cname'=> $data['cname'],
                'status'=> $data['status'],
                'area_no'=> $data['area_no'],
                'city_no'=> $data['city_no'],
                'update_time'=> 0
            ]);   //将数据存入并返回自增 ID
            if(empty($id)){
                throw new Exception('操作失败');
            }
            return $this->responseOk($id);
        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }
    public function siteStreetEdit($search=[],$data){
        try{
            $id = intval($search['id']);
            if(empty($id)){
                throw new Exception('缺少设置参数');
            }
            if(!empty($data['cname'])){
                $has = $this->db->name("site_city_street")->where([
                    'cname'=> $data['cname'],
                    'area_no'=> $search['area_no'],
                ])->value('id');
                if(!empty($has)&&$id!=$has){
                    throw new Exception('该商圈已经存在');
                }
            }

            $data['update_time'] = time();
            $rs = $this->db->name('site_city_street')->where(['id'=>$id])->update($data);
            if(empty($rs)){
                throw new Exception('操作失败');
            }

        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }
    public function siteStreetDel($id)
    {
        try{
            $res = $this->db->name("site_city_street")->where("id",$id)->delete();
            if($res){
                return $this->responseOk($res);
            }else{
                throw new Exception('操作失败');
            }

        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }
    //=============== 站点街道操作 end==============//

}
