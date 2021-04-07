<?php


namespace app\model;


use think\Model;

class Chat extends Model
{
    protected $table ='9h_chat';
    protected $autoWriteTimestamp='int';
    /**
     *
     *添加聊天记录
     */
    public function addMsg($data){

    }

    /**
     * 未读消息条数
     */
    public function getCountNotRead($user_id){
        $msg_count       = self::where('to_user_id','=',$user_id)->where('is_read','=',0)->count();
        $this->table= '9h_chat_stytem_msg_user';
        $stytem_count    =self::where('user_id','=',$user_id)->where('is_read','=',2)->count();
        return  [
            'msg_count'     => $msg_count,
            'stytem_count'  => $stytem_count,
            'total_count'   => $msg_count + $stytem_count,
        ];
    }

    public function setMsgRead($dialogue_id,$user_id){
        self::where('chat_dialogue_id','=',$dialogue_id)->where('to_user_id','=',$this->user_id)->save(['is_read'=>1]);
    }

}