<?php


namespace app\model;


use think\Model;


class ChatDialogue extends Model
{
    protected $table ='9h_chat_dialogue';
    protected $autoWriteTimestamp='int';
    protected $pk ='id';
    /**
     *
     *添加聊天记录
     */
    public function addDialogueList($data){

        if(empty($data)){
           return false;
        }
        $group_id  = $data['group_id'] ?? 0;
        $where  = [
            'to_id'   =>$data['to_user_id'],
            'user_id' => $data['send_user_id'],
        ];
        if(empty($group_id)){ //群聊天
            $info  = $this->whereRaw('to_id=:to_id and user_id=:user_id',$where)
                ->whereOrRaw('to_id=:user_id and user_id=:to_id',$where)
                ->where('to_type' ,'=',1)
                ->find();
            $list  = [
                'user_id'      => $data['send_user_id'],
                'to_id'        => $data['to_user_id'],
                'to_type'      => 1,
            ];

            //如果纯在跟新聊天时间
            if(!empty($info)){
                $info->update_time = time();
                $result = $info->save();
                $id = $info['id'];
            }else{ //不存在创建对话信息
                $this->setAttrs($list);
                $result =  $this->insert($list,true);
                $id = $result;
                $this->table='9h_chat_friend';
                //查看是否存在好友关系，如果存在跟新会话信息
                $friendinfo = $this->where('user_id','=',$data['send_user_id'])
                                   ->where('friend_user_id','=',$data['to_user_id'])->find();

                if(!empty($friendinfo) && $friendinfo['dialogue_id']==0){ //存在好友关系并且会话id为0时更新会话id
                    $this->where('id','=',$friendinfo['id'])->save(['dialogue_id'=>$id]);
                }
            }


        }else{
            $info  = $this->where('to_id','=',$group_id)
                          ->where('to_type' ,'=',2)
                          ->find();

            $list  = [
                'user_id'      => $data['send_user_id'],
                'to_id'        => $data['group_id'],
                'to_type'      => 2,
            ];

            if(empty($info)){
                $this->setAttrs($list);
                $result = $this->save();
                $id = $this->getLastInsID();
            }else{ //群聊 直接刷新群聊时间
                $result = self::update(['update_time'=>time()],[['to_id','=',$group_id],['to_type','=',2]]);
                $id = $info['id'];
            }
        }

        return  $result === false ? false : $id;

    }
}