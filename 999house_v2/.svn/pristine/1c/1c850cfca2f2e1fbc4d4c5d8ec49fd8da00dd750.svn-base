<?php
namespace app\index\controller;

use app\common\base\UserBaseController;
use app\common\lib\wxapi\WxServe;
use app\server\index\Activity;
use app\server\index\ActivityBox;
use app\server\index\BoMessageLog;
use app\server\index\ShortMessage;
use app\server\merchant\Activities;
use think\App;
use think\Exception;

class IndexController extends UserBaseController
{

    public function index()
    {
        $mch_id         = $this->merchantId;
        $activity_id    = $this->activitiesId;
//        var_dump($mch_id.'werwr');
        $model     = new Activity();
//        $model->getBoxBYActivity($mch_id,$activity_id,1,6);
        $res       = $model->getActivityById($mch_id,$activity_id);
        if($res['code'] == 0){
            return $this->error($res['msg']);
        }
        return     $this->success($res);

    }
    public function getActivityBox(){
        $mch_id         = $this->merchantId;
        $activity_id    = $this->activitiesId;
        $user_id        = $this->userId;
        $page           = $this->request->param('page',1);
        $pageSeiz       = $this->request->param('pageSeiz',6);
        $model          = new Activity();
//        $res            = $model->getBoxBYActivity($mch_id,$activity_id,$user_id,$page,$pageSeiz);
        $res            = $model->getBoxBYActivityNew($mch_id,$activity_id,$user_id); //按抽奖次数排序 ,
//        var_dump($res);return false;
        if($res['code'] == 0){
            return $this->error($res['msg']);
        }
            $result=[];
            $result['data']['total']        = $res['result']['total'];
            $result['data']['last_page']    = $res['result']['last_page'];
            $result['data']['current_page'] = $res['result']['current_page'];
            $result['data']['list']         = $res['result'];
            $result['data']['index_box']    = $res['result']['index_box'];
            $result['data']['room_order']   = $res['result']['room_order'];
            unset($result['data']['list']['index_box']);
            unset($result['data']['list']['room_order']);

//        var_dump($result);
        return     $this->success($result);



    }

    /*
     * 获取房间信息
     */
    public function getBoxInfo(){
        $mch_id         =  $this->merchantId;
        $activity_id    =  $this->activitiesId;
        $user_id        =  $this->userId;
        $box_id         =  $this->request->get('box_id','');
        $model          =  new ActivityBox();
        $res            =  $model->getBoxInfoByid($activity_id,$user_id,$mch_id,$box_id);
        if($res['code'] == 0){
            return $this->error($res['msg']);
        }
        return     $this->success($res);

    }



    public function GetMsgListByPage(){
        $data         = $this->request->param();
        $merchant_id  = $this->merchantId;
        $act_id       = $this->activitiesId;
        $order        = $data['order_id'];
        $pageSize     = $data['pageSize'];
        $model        = new BoMessageLog();
        $lsit         = $model->getOldMsgList($act_id,$order,$merchant_id,$pageSize);
        $data = [];
        foreach ($lsit as $k => $v){
            $data[$k]  =[
                'user_name'     =>$v['user_name'],
                'headimgurl'    =>$v['headimgurl'] ?? '',
                'time'          =>date('H:i',$v['update_time']),
                'event'         =>'SendMsg',
                'type'          => 1,
                'text'          => $v['content'],
                'id'            => $v['order']
            ] ;

        }
        return $this->success($data);


    }

    /**
     * 获取pk分享描述
     */
   public function getpkShareDescribe(){
       $merchant_id  = $this->merchantId;
       $act_id       = $this->activitiesId;
       $user_id      = $this->userId;
       $model        = new Activity();
       $res          = $model->getPknum([
           'mch_id'     => $merchant_id,
           'user_id'    => $user_id,
           'act_id'     => $act_id,
           ]);
//       var_dump($res);
       $user_pk_num  =  $res['pk_number']-$res['user_pk_num'];
       $pk_point_arr =  json_decode($res['pk_point'],true);
       $text         = "分享链接，邀请他进行pk，一盘订输赢，赢得一方积分加{$pk_point_arr['win']}分，
                        输得一方pk减{$pk_point_arr['lose']}分，平局加{$pk_point_arr['level']}分";

       $res          = [
           'user_pk_num'  => $user_pk_num,
           'text'         => $text,
       ];

       return $this->success($res);

   }

    public function valid()
    {
        try {
            $data = json_encode($this->request->param());
//            $this->db->name('log')->insert(['content' => $data, 'created_at' => time()]);

            $param['signature'] = $this->request->param()['signature'];
            $param['echostr'] = $this->request->param()['echostr'];
            $param['timestamp'] = $this->request->param()['timestamp'];
            $param['nonce'] = $this->request->param()['nonce'];

            //获取token

            $server = new Activities();
            $xwConfig = $server->wxConfigurationInfo($this->merchantId);
            if(!$xwConfig){
                throw new Exception('配置有误');
            }
            $param['token'] = $xwConfig['h5']['token'];

//            echo $param['echostr'];
            if(!empty($this->request->param()['echostr'])) {
                $wxServer = new WxServe();
                $resValid = $wxServer->valid($param);
//                $this->db->name('log')->insert(['content' => $resValid, 'created_at' => time()]);
//                header("Content-type: text/html; charset=utf-8");
//                ob_clean();
                echo $resValid;
            }
        }catch (\Throwable $throwable) {
            $this->db->name('log')->insert(['content' => $throwable->getMessage(), 'created_at' => time()]);
//            var_dump($throwable->getMessage());
        }

    }


}
