<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: pl125 <xskjs888@163.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\validate\AccountValidate;
use app\common\base\AdminBaseController;
use app\common\MyConst;
use app\server\admin\Admin;
use app\server\admin\Chat;
use think\Validate;


class ChatController extends AdminBaseController
{
    /**
     * 获取对话列表
     */
  public function dialogueList(){
      $user = $this->user;
      $user_id = $user['user_id'];
      if(!$user_id){
          return $this->error('请选绑定账号');
      }
      $chatServer   = new Chat();
      $list         = $chatServer->dialogueList($user_id);
      return $this->success($list);
  }

    /**
     * 获取聊天纪录
     */
  public function getChatListByUser(){
      $user     = $this->user;
      $user_id  = $user['user_id'];
      $dialogue_id    = $this->request->post('dialogue_id');
      if(empty($dialogue_id)){
          return  $this->error('参数错误');
      }

      $chatServer   = new Chat();
      $list         = $chatServer->getChatListByUser($dialogue_id);

      return $this->success($list);


  }

  public function stytemMsg(){

  }

    /**
     * 发送系统消息
     */
  public function sendSystemMsg(){
     $data  = $this->request->post();
     $msg_data = [
        'id'                => $data['id'] ,
        'title'             => $data['title'],
        'context'           => $data['context'],
        'cover'             => $data['cover'],
        'status'            => $data['status'],
        'chat_type'          => $data['chat_type'],
        'sub_context'       => $data['sub_context'],
        'update_time'        => time(),
        'create_time'       => time(),
     ];

     $user_id_list = $data['user_ids'];
     $users = [];
     foreach ($user_id_list as $k=> $v){
         $users[$k]['id'] = $v;
     }
     $chatServer = new Chat();
     if(empty($data['id'])){
         $result     = $chatServer->addSyetemMsg($msg_data,$users);
     }else{
         $result     = $chatServer->editSyetemMsg($msg_data,$users);
      }



     if($result['code'] == 0){
        return $this->error($result['msg']);
     }

     $this->success();
  }

    /**
     * 获取系统消息类型
     */
  public function getSystemType(){
      $data  = MyConst::CHATSYSTEM;
      $new_data =[];
      if(!empty($data)){
          foreach ($data as $k=> $v){
              $new_data[] = [
                  'id'      =>  $k,
                  'name'    => $v
              ];
          }
      }
      $this->success($new_data);
  }

  public function getSystemList(){
      $data = $this->request->post();
      $serach  = [
          'title'       => $data['title'],
          'chat_type'   => $data['chat_type'],
          'pageSize'   => $data['pageSize'] ?? 16
      ];

      $list  = (new Chat())->getSystemList($serach);

      foreach ($list['result']['list'] as &$v){
          $v['context']  = htmlspecialchars_decode($v['contxt']);
          $v['cover']    = empty($v['cover'])    ?  '' : $v['cover'];
      }
//      var_dump($list);
      if($list['code'] ==0){
          return $this->error();
      }

      $this->success($list['result']);
  }

}
