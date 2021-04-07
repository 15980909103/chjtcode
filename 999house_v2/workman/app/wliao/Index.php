<?php


namespace app\wliao;


use app\model\Chat;
use app\model\ChatDialogue;
use app\model\ChatGroup;
use app\model\User;
use GatewayWorker\Lib\Gateway;
use think\Db;

class Index extends BaseWork
{
    public function index($data){

    }
    //**用户到用户聊天
    public function usertouser($data){
        $touser_id  = $data['to_user_id'];
        $msg_type   = $data['msg_type']??'';
        if($touser_id == $this->user_id){
            return ;
        }
        if(empty($msg_type)){
            Gateway::sendToUid($this->user_id,$this->error('小心类型不能为空',[],'errmsg'));
            return;
        }

        if($msg_type ==2 &&  empty($data['msg_url'])){
            Gateway::sendToUid($this->user_id,$this->error('图片地址不能为空',[],'errmsg'));
            return;
        }
        $model = new Chat();
        $user        = new User();
        $to_user_info              = $user->find($touser_id);
        if(empty($to_user_info)){
            Gateway::sendToUid($this->user_id,$this->error('接收用户不存在',[],'errmsg'));
            return;
        }
        $return_data['user'] =[
            'id'            => $to_user_info['id'],
            'user_name'     => empty($to_user_info['user_name']) ? $to_user_info['nickname']:$to_user_info['user_name'],
            'user_avatar'   => empty($to_user_info['user_avatar']) ? $to_user_info['headimgurl'] :$to_user_info['user_avatar'],
        ];

        $data  =[
            'send_user_id'      => $this->user_id,
            'to_user_id'        => $touser_id,
            'send_time'         => time(),
            'msg_type'          => $msg_type,
            'msg_url'           => $data['msg_url'] ?? '',
            'msg'               => $data['send_msg'] ?? '',
            'group_id'          => 0,
        ];
        //纪录对话信息
        $server           = new ChatDialogue();
        $chat_dialogue_id = $server->addDialogueList($data);

        $data['chat_dialogue_id'] = $chat_dialogue_id;
        $data['is_read']    = 0;
        dump($data);
        $return_data['msg']                     = $data['msg'];
        $return_data['type']                    = $msg_type;
        $return_data['msg_url']                 = $data['msg_url'];
        $return_data['chat_dialogue_id']        = $chat_dialogue_id; //下发会话id用于消息置顶
        $model->setAttrs($data);
        $model->save();
        Gateway::sendToUid($this->user_id,$this->success($return_data,'发送成功','say'));
        Gateway::sendToUid($touser_id,$this->success($return_data,'发送成功','say'));

    }
    //添加聊天消息
    public function addfriend(){

    }

    /**
     * 群聊
     * @param $data
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function usertogroup($data){
        $touser_id  = $data['group_id']; //群id
        $model       = new Chat();
        $groupModel  = new ChatGroup();
        $groupInfo   = $groupModel->find($touser_id);
        if(empty($groupInfo)){
            Gateway::sendToUid($this->user_id,$this->error('群信息不存在',[],'say'));
            return;
        }

        $return_data['user'] =[
            'id'            => $groupInfo['id'],
            'user_name'     => $groupInfo['group_name'],
            'user_avatar'   => $groupInfo['groop_ico'],
        ];

        $data  =[
            'send_user_id'      => $this->user_id,
            'to_user_id'        => 0,
            'send_time'         => time(),
            'msg_type'          => 1,
            'msg'               => $data['send_msg'] ?? '',
            'group_id'          => $touser_id,
        ];
        //纪录对话信息
        $server           = new ChatDialogue();
        $chat_dialogue_id = $server->addDialogueList($data);

        $data['chat_dialogue_id'] = $chat_dialogue_id;

        $return_data['msg']                     = $data['msg'];
        $return_data['chat_dialogue_id']        = $chat_dialogue_id; //下发会话id用于消息置顶
        $model->setAttrs($data);
        $model->save();

        Gateway::sendToGroup($touser_id,$this->success($return_data,'发送成功','say'));
        Gateway::sendToGroup($this->user_id,$this->success($return_data,'发送成功','say'));
    }

    /**
     * @param $data
     * 修改消息状态
     */
    public function setMsgStatus($data){
        dump($data);
        $dialogue_id = $data['dialogue_id'] ?? null;
        if(empty($dialogue_id) ){
            Gateway::sendToUid($this->user_id,$this->error('会话id不能为空',[],'errmsg'));
            return;
        }
        //将未读会话状态弄成已读
        $model = new Chat();
        $model->setMsgRead($dialogue_id,$this->user_id);
        Gateway::sendToGroup($this->user_id,$this->success([],'操作成功','succmsg'));
    }


}