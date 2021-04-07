<?php


namespace app\admin\controller;
use app\common\base\AdminBaseController;
use app\server\admin\Banner;

class BannerController extends AdminBaseController
{
    /**
     * banner图位置列表
     */
    public function getPlaceList(){
        $data = $this->request->param();
        $where = [
            'type' => $data['type']
        ];
        $rs = (new Banner())->getPlaceList($where)['result'];

        $this->success($rs);
    }
    public function placeEdit(){
        $data = $this->request->param();
        $data['id'] = intval($data['id']);
        $data['place'] = str_replace(' ', '', $data['place']);

        if(empty($data['place'])){
            $this->error('请填写图片应用位置');
        }
        $indata = [
            'type'=> $data['type'],
            'desc'=> $data['desc'],
            'place'=> $data['place'],
        ];

        if($data['id']){
            $rs = (new Banner())->placeEdit($data['id'],$indata);
        }else{
            $rs = (new Banner())->placeAdd($indata);
        }
        if($rs['code']==1){
            $this->success();
        }else{
            $this->error($rs['msg']);
        }
    }
    //删除
    public function placeDel()
    {
        $data = $this->request->param();
        $rs = (new Banner())->placeDel(intval($data['id']));
        $this->success($rs);
    }

    /**
     * Banner图列表
     */
    public function getBannerList(){
        $data = $this->request->param();
        $where = [
            'status'    => $data['status'],
            'start_time'=> strtotime($data['startdate']),
            'end_time'  => strtotime($data['enddate']),
            'place'     => $data['place'],
            'place_id'     => $data['place_id']
        ];

        $rs = (new Banner())->getList($where)['result'];
        if(empty($rs['list'])){
            $rs = [];
        }

        foreach ($rs['list'] as $k=> $v) {
            $rs['list'][$k]['img_ids']  =   explode(',',$v['cover']) ?? [];
            $rs['list'][$k]['is_propert_news']  =  $v['is_propert_news'] == 1 ? true:false;
            $rs['list'][$k]['urls']    =  $v['type'] !=1 ? (json_decode($v['cover_path'],true) ?? [] ): ($this->getVoidePath($v['cover'])) ;
        }
//        var_dump($rs);return false;
        $this->success($rs);
    }
    //修改状态
    public function bannerEnable(){
        $data = $this->request->param();

        $rs = (new Banner())->edit(intval($data['id']),['status'=>intval($data['status'])]);
        if($rs['code'] == 1){
            $this->success();
        }else{
            $this->error($rs['msg']);
        }
    }
    //删除
    public function bannerDel()
    {
        $data = $this->request->param();
        $rs = (new Banner())->del(intval($data['id']));
        $this->success($rs);
    }

    public function bannerEdit(){
        $data = $this->request->param();
        $data['id'] = intval($data['id']);
        $data['place_id'] = intval($data['place_id']);

        if(empty($data['place_id'])){
            $this->error('缺失图片应用位置参数');
        }
        $place = $this->db->name('banner_img_place')->where(['id'=>$data['place_id']])->value('place');
        if(empty($place)){
            $this->error('缺失图片应用位置参数');
        }
        $data["start_time"] = strtotime($data["start_time"]) ?? 0;
        $data["end_time"] = strtotime($data["end_time"]) ?? 0;
        if($data['type'] == 1){
            if(empty($data["start_time"])||empty($data["end_time"])){
                $this->error('请设置该广告的有效时间范围');
            }
            if($data["start_time"]>=$data["end_time"]){
                $this->error('开始时间超过结束时间');
            }
        }

        $data["type"] = intval($data["type"]);
        if($data["type"]=='1'){//视频类型时单张排列
            $data["align"] = 0;
        }

        $indata = [
            'status' => intval($data["status"]),
            'put_type' => intval($data['put_type']),
            'click_on_upper' => intval($data['click_on_upper']), //点击量
            "read_num_upper"=> intval($data["read_num_upper"]),//浏览量
            'sort' => intval($data["sort"]),
            'place' => $place,
            'place_id'=> $data['place_id'],
            'href' => $data["href"],
            'start_time'=> $data["start_time"],
            'end_time'=> $data["end_time"],
            'region_no'=> $data["region_no"],
            'forid'=> $data["forid"],
            'forname'=> $data["forname"],
            'article_id'=> $data["article_id"],
            'article_name'=> $data["article_name"],
//            'is_propert_news'=> $data["is_propert_news"] ==='true' ? 1:0,
            'title'=> $data["title"],
            'sub_title'=> $data["sub_title"],

            'type' => $data["type"],
            'align' => intval($data["align"]),
            'update_time' => time(),
        ];
        if($data['type'] != 1 ){
            if($data['type'] == 2){  //type 为2的时候是
                $indata['is_propert_news'] = 1;
            }else{
                $indata['is_propert_news'] = 0;
            }
            $indata['cover']        = implode($data['img_ids'],',');
            $indata['cover_path']   = json_encode($data['urls']);
        }else{
            $indata['cover']   = $data['cover'];
            $indata['cover_path']   = json_encode($data['fileUrl']);
        }
//        var_dump($indata);
        if( empty($indata['cover']) ){
            return $this->error('图片或视频不能为空');
        }
        if($data['id']){
            $rs = (new Banner())->edit($data['id'],$indata);
        }else{
            $rs = (new Banner())->add($indata);
        }
        if($rs['code']==1){
            $this->success();
        }else{
            $this->error();
        }
    }

    //todo 旧版
    public function bannerEdit1(){
        $data = $this->request->param();
        $data['id'] = intval($data['id']);
        $data['place_id'] = intval($data['place_id']);

        if(empty($data['place_id'])){
            $this->error('缺失图片应用位置参数');
        }
        $place = $this->db->name('banner_img_place')->where(['id'=>$data['place_id']])->value('place');
        if(empty($place)){
            $this->error('缺失图片应用位置参数');
        }
        $data["start_time"] = strtotime($data["start_time"]);
        $data["end_time"] = strtotime($data["end_time"]);
        if(empty($data["start_time"])||empty($data["end_time"])){
            $this->error('请设置该广告的有效时间范围');
        }
        if($data["start_time"]>=$data["end_time"]){
            $this->error('开始时间超过结束时间');
        }
        $data["type"] = intval($data["type"]);
        if($data["type"]=='1'){//视频类型时单张排列
            $data["align"] = 0;
        }

        $indata = [
            'status' => intval($data["status"]),
            'put_type' => intval($data['put_type']), //浏览量
            'click_on' => intval($data['click_on']), //点击量
            'sort' => intval($data["sort"]),
            'place' => $place,
            'place_id'=> $data['place_id'],
            'href' => $data["href"],
            'start_time'=> $data["start_time"],
            'end_time'=> $data["end_time"],
            'region_no'=> $data["region_no"],
            'forid'=> $data["forid"],
            'forname'=> $data["forname"],
            'is_propert_news'=> $data["is_propert_news"] ==='true' ? 1:0,
            'title'=> $data["title"],
            "read_num"=> intval($data["read_num"]),
            'type' => $data["type"],
            'align' => intval($data["align"]),
            'update_time' => time(),
        ];
        if($data['type'] == 0 ){
            $indata['cover']        = implode($data['img_ids'],',');
            $indata['cover_path']   = json_encode($data['urls']);
        }else{
            $indata['cover']   = $data['cover'];
            $indata['cover_path']   = json_encode($data['fileUrl']);
        }
//        var_dump($indata);
        if( empty($indata['cover']) ){
            return $this->error('图片或视频不能为空');
        }
        if($data['id']){
            $rs = (new Banner())->edit($data['id'],$indata);
        }else{
            $rs = (new Banner())->add($indata);
        }
        if($rs['code']==1){
            $this->success();
        }else{
            $this->error();
        }
    }


    public function bannerChangeSort(){
        $data = $this->request->param();
        $rs = (new Banner())->edit(intval($data['id']),['sort'=>$data['sort']]);
        if($rs['code'] == 1){
            $this->success();
        }else{
            $this->error($rs['msg']);
        }
    }

    //下拉标签
    public function getPlace(){
        $res = $this->db->name('banner_img_place')->field('id,desc')->select();
        if(!$res){
            return [];
        }
        return $res;
    }



}