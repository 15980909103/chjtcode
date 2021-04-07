<?php

namespace app\admin\controller;

use app\common\base\AdminBaseController;
use app\common\MyConst;
use app\server\admin\Admin;
use app\server\admin\City;
use app\server\admin\CityPriceLog;
use app\server\admin\CitySite;
use app\server\admin\CommentList;
use app\server\admin\ConsultingComments;
use app\server\admin\News;
use app\server\admin\Role;
use app\server\marketing\Subject;
use think\facade\Db;
use think\Validate;


class IndexController extends AdminBaseController
{
    public function index(){

        $data       = $this->request->post();
//        $actiontype = $data['actiontype'];
//        $page       = $data['page'];
//        $pageSize   = $data['pageSize'];
        if( empty($data['city_list']) ){
            return $this->error('请选选择城市');
        }
        $newsmodel  = new News();
        $where = [
          'cityl_list' => $data['city_no']  ,
          'status'     => 2
        ];
        $newsStatusBy2 = $newsmodel->getCountByStatus($where);//获取草稿数

        $subjectmodel = new Subject();
        $where = [
              'city_no' => $data['city_no']  ,
              'status'     => 2
        ];
        $subjectCount = $subjectmodel->getCountByTime($data);

        $commentmodel = new CommentList();
         $where = [
                'city_no' => $data['city_no']  ,
                'status'     => 2
         ];
        $comentCount  = $commentmodel->commentCountByStatus($data);
        //最新创造列表
        $where = [
            'cityl_list' => $data['city_no']  ,
            'status'     => 2
        ];
        $newslist['update'] = $newsmodel->getnewsListBywhere($where,'update_time desc');
        $ids = [];
        foreach ($newslist['update'] as $v){
            $ids[] = $v['id'];
        }
        if($ids){
            $comments  =  (new ConsultingComments())->getCountById($ids,$where['pid']);
//            var_dump($comments->toArray());
            if(!empty($comments)){
                foreach ($newslist['update'] as $ks => $vs){
                    $newslist['update'][$ks]['update_time'] = date('m-d',$vs['update_time']);
                    $newslist['update'][$ks]['index_ico'] = json_decode($vs['img_path'],true)[0]['url'];
                    foreach ($comments as $key => $val){
                        if($vs['id'] == $val['article_id']){
                            $newslist['update'][$ks]['commentNum'] = $val['count'];
                            continue;
                        }
                    }
                }

            }else{
                foreach ($newslist['update'] as $ks => $vs){
                        $newslist['update'][$ks]['update_time'] = date('m-d',$vs['update_time']);
                        $newslist['update'][$ks]['index_ico'] = json_decode($vs['img_path'],true)[0]['url'];
                       $newslist['update'][$ks]['commentNum']= 0;
                }

            }
        }
        $newslist['update'] =  array_chunk($newslist['update'],6);


        //获取热门视频
        $toplist['video'] = []; //todo 目前没有视频的榜单 有在加

        $result = [
            'newsStatusBy2' => $newsStatusBy2,
            'subjectCount' => $subjectCount,
            'comentCount'  => $comentCount,
            'htCount'      => 0,
            'newList'      => $newslist,
        ];

        $this->success($result);


     }
     public function getInstituteList(){
         $data = $this->request->post();
         $result = [];
         if(empty($data['city_no'])){
            return $this->error('请选择城市');
         }

         if(empty($data['date'])){
             return $this->error('请选择时间');
         }


             $where = [
                 'date'              => $data['date'],
                 'city'              => $data['city_no']
             ];

             $data   = $where;
             if(empty($data['date'])) {
                 $data['date'] = date('Y-m',time());
             }
             $date                 = date('Y-m',strtotime($data['date']));
             $date                 = strtotime($date);
             $model          = new CityPriceLog();
             $info           = $model->getInfoByMonth($date,$data['city']);
             if(!empty($info['price'])){ //价格没有为没数据
                 $list           = [
                     'city_no'           => $info['city_no'],
                     'city_no_name'      => $info['city_no_name'],
                     'price'             => round($info['price'],2),
//                     'city_price'        => json_decode($info['city_price'],true),
                     'show_time'         => date('m',$info['show_time']) != date('m',$date) ? date('m',$date) :date('m',$info['show_time']),  //月份
                     'recent_opening'    => intval($info['recent_opening']),
                     'on_sale'           => intval($info['on_sale']),
                     'deal'              => intval($info['deal']),
                     'last_month_rate'   => ($info['last_month_rate']) ?? 0 .'%',
                     'last_month_type'   => $info['last_month_rate'] > 0 ? 1:0,
                 ];
             }else{
                 $list           = [
                     'city_no'           => $info['city_no'],
                     'city_no_name'      => $info['city_no_name'],
                     'price'             => '暂无数据',
//                     'city_price'        => json_decode($info['city_price'],true),
                     'show_time'         => date('m',$info['show_time']) != date('m',$date) ? date('m',$date) :date('m',$info['show_time']),  //月份
                     'recent_opening'    => '暂无数据',
                     'on_sale'           => '暂无数据',
                     'deal'              => '暂无数据',
//                     'last_month_rate'   => ($info['last_month_rate']) ?? 0 .'%',
//                     'last_month_type'   => $info['last_month_rate'] > 0 ? 1:0,
                 ];
             }

            $result['xf'] = $list;
             //todo 二手房又数据
            $result['esf'] =  [
                'city_no'           => $info['city_no'],
                'city_no_name'      => $info['city_no_name'],
                'price'             => '暂无数据',
//                     'city_price'        => json_decode($info['city_price'],true),
                'show_time'         => date('m',$info['show_time']) != date('m',$date) ? date('m',$date) :date('m',$info['show_time']),  //月份
                'recent_opening'    => '暂无数据',
                'on_sale'           => '暂无数据',
                'deal'              => '暂无数据',
//                     'last_month_rate'   => ($info['last_month_rate']) ?? 0 .'%',
//                     'last_month_type'   => $info['last_month_rate'] > 0 ? 1:0,
            ];


            $this->success($result);


     }

     public function getRank(){
         $type      = $this->request->post('type');
         $city_no   = $this->request->post('city_no');
         $newskey  = MyConst::NEWS_HOS_LIST;
         $redis =  $this->getReids();
         switch ($type){
             case 'newsHos' :$list =  $redis->hGet($newskey,$city_no);break;
             case 'voideHos':$list =  [];break;
             case 'htHos'   :$list =  [];break;
         }

         $list = json_decode($list,true);
         $ids = [];
         foreach ($list as $v){
             $ids[] = $v['id'];
         }
         if(empty($ids)){
             $list = [];
         }else{
             $list = (new News())->getListByIds($ids);
         }

         $this->success($list);
     }

    public function userInfo(){
        $rs = (new Admin())->getUserInfo(['userid'=>$this->getUserId()]);

        if(empty($rs['result']['region_nos_info'])){
            $region_nos_info = (new City())->getSiteCitys([],'id,cname,pid,pcname')['result'];
        }else{
            $region_nos_info = json_decode($rs['result']['region_nos_info'],true);
        }

        $this->success([
            'id' => $rs['result']['id'],
            'username' => $rs['result']['account'],
            'user_id'  => $rs['result']['user_id'], //绑定的前台账号用于聊天
            'region_nos_info' => $region_nos_info,
        ]);
    }

    public function editPassword(){
        $validate = new Validate([
            'oldpassword' => 'require|different:newpassword',
            'newpassword' => 'require|confirm:newpassword2|length:6,20',
        ]);
        $validate->message([
            'oldpassword.require' => '请输入密码',
            'oldpassword.different' => '新旧密码不能一致',
            'newpassword.require' => '请输入新密码',
            'newpassword.confirm' => '两次输入的新密码不一致',
            'newpassword.length'  => '新密码长度为6-20位'
        ]);

        $data = $this->request->param();
        if (!$validate->check($data)) {
            $this->error(['code'=>0,'msg'=>$validate->getError()]);
        }

        $rs = (new Admin())->userEditPwd($this->getUserId(),$data['oldpassword'],$data['newpassword']);
        if($rs['code']==1){
            $this->success('','操作成功');
        }else{
            $this->error(['code'=>0,'msg'=>$rs['msg']]);
        }
    }

    public function menu(){
        $rs = (new Role())->getRoleMenus($this->roleId,'getTree')['result'];
        $this->success($rs);
    }

}
