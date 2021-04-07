<?php
declare (strict_types = 1);

namespace app\task;


use app\common\pool\RedisPool;
use app\server\admin\Chat;
use app\server\index\BoMessageLog;
use app\server\user\User;
use app\websocket\Gateway;
use Swoole\IDEHelper\StubGenerators\Swoole;
use Swoole\Runtime;
use think\cache\driver\Redis;
use think\Exception;
use function Co\run;

class SystemMsg
{
    public function run($msg_data= null){
        //todo 整理系统消息发送
        //分批次获取用户推送消息c
        var_dump($msg_data);
        $type       = $msg_data['type'];
        $user_list  = $msg_data['user_list'];
        $pageSize  = 20;
        $page = 1;
        $user_server = new User();
        if($type==1){
            $data  = $user_server->getListBypage($pageSize,$page);
        }else{
            $data = $user_list;
        }
        while (!empty($data)){
            foreach ($data as $v){
                $ChatServer  = new Chat();
                try{
                    if(Gateway::isUidOnline($v['id'])){
                        //在线发送sorket 数据给我给前端
                        $send_data = [
                            'code'          => 1,
                            'msg'           => '系统消息',
                            'type'          =>  'systemmsg',
                            'returndata'    => [
                                'title'           => $msg_data['title'] ?? '',
                                'context'         => empty($msg_data['contxt']) ? '' :htmlspecialchars_decode($msg_data['contxt']),
                                'status'          => $msg_data['status'],
                                'cover'           => $msg_data['cover'] ??'',
                                'estate_id'       => $msg_data['estate_id'] ??'',
                                'chat_type'       => $msg_data['chat_type'],
                                'sub_context'     => $msg_data['sub_context'],
                                'name'            => $msg_data['name'] ??'',
                                'id'              => $msg_data['id'],
                                'is_cover'        => 1,
                            ],
                        ];
//                    var_dump($send_data);
                        Gateway::sendToUid($v['id'],json_encode($send_data));
                        $ChatServer->addSyetemMsgByUser($msg_data,$v['id']);

                    }else{
                        $ChatServer->addSyetemMsgByUser($msg_data,$v['id']);
                    }

                }catch (\Throwable $e){
                    echo  $e->getMessage();
                }

            }
            if($type ==1){
                $page++;
                $data = $user_server->getListBypage($pageSize,$page);
            }else{
                $data =[];
            }

        }


    }
}
