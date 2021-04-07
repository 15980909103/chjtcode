<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace app\websorcket;

use app\common\MyConst;
use app\model\Chat;
use app\model\User;
use GatewayWorker\Lib\Gateway;
use think\cache\driver\Redis;
use think\Exception;
use think\exception\Handle;
use think\facade\Cache;
use think\facade\Config;
use think\worker\Application;
use Workerman\Worker;

/**
 * Worker 命令行服务类
 */
class Events
{
    public static $redis;
    /**
     * onWorkerStart 事件回调
     * 当businessWorker进程启动时触发。每个进程生命周期内都只会触发一次
     *
     * @access public
     * @param  \Workerman\Worker    $businessWorker
     * @return void
     */
    public static function onWorkerStart(Worker $businessWorker)
    {
        $app = new Application();
        $app->initialize();
        self::$redis = (new Redis(Config::get('cache.stores.redis')))->handler();

    }

    /**
     * onConnect 事件回调
     * 当客户端连接上gateway进程时(TCP三次握手完毕时)触发
     *
     * @access public
     * @param  int       $client_id
     * @return void
     */
    public static function onConnect($client_id)
    {

    }

    /**
     * onWebSocketConnect 事件回调
     * 当客户端连接上gateway完成websocket握手时触发
     *
     * @param  integer  $client_id 断开连接的客户端client_id
     * @param  mixed    $data
     * @return void
     */
    public static function onWebSocketConnect($client_id, $data)
    {
//
//        try {
            $get_data =$data['get'];
            if(!empty($get_data) ) {
                $token= $get_data['token'];
                $type = $get_data['type'] ?? 'index';
            }else{
                $token =  '';
            }
            if(empty($type)){
                Gateway::sendToClient($client_id,json_encode(['code'=>0,'mag'=>'请传入连接类型']));
                Gateway::closeClient($client_id);
            }
            if($token){
                //后台登录
                if($type =='admin'){
                    $userInfos =  self::$redis->get(MyConst::JIUFANG_LOGIN_ADMIN.$token);
                    $userInfos = json_decode($userInfos, true)['info'];
                }else{
                    $userInfos =  self::$redis->get(MyConst::JIUFANG_LOGIN.$token);
                    $userInfos = json_decode($userInfos, true);
                }

//                var_dump(MyConst::JIUFANG_LOGIN.$token);
                if (empty($userInfos)) {
                    Gateway::sendToClient($client_id,json_encode(['code'=>0,'mag'=>'非法链接']));
                    Gateway::closeClient($client_id);
                    return;
                } else {

                    if ($userInfos['expire_time'] < time()) {
                        Gateway::sendToClient($client_id,json_encode(['code'=>0,'mag'=>'非法链接']));
                        Gateway::closeClient($client_id);
                        return;
                    }
                    $userInfos['type']  = $type;
                    $_SESSION['user']  = $userInfos;
                    //将uid 和 user_id 进行绑定
                    $user_id = $type=='admin' ? $userInfos['user_id']:$userInfos['id'];
                    Gateway::bindUid($client_id,$user_id);
                    Gateway::sendToClient($client_id,json_encode(['code'=>1,'msg'=>'链接成功']));
                    //整理群组信息
                    $userInof  = User::find($type=='admin' ? $userInfos['user_id']:$userInfos['id']);
                    $groups    = $userInof->groups->toarray();
                    if(!empty($groups)){
                        foreach ($groups as $v){
                            if(!empty($v['group_id'])){
                                Gateway::joinGroup($client_id,$v['group_id']); //加入对应群组
                            }
                        }
                    }
                    //获取消息未读数发送给前端
                    $chatServer         = new Chat();
                    $not_read_arr     = $chatServer->getCountNotRead($type=='admin' ? $userInfos['user_id']:$userInfos['id']);
                    Gateway::sendToClient($client_id,json_encode(['code'=>1,'msg'=>'未读消息数','type'=>'not_read_count','data'=> $not_read_arr]));
                }
            }else{
                Gateway::sendToClient($client_id,'非法链接');
                Gateway::closeClient($client_id);
            }
//        }catch (\Exception $e){
//            throw new Exception($e->getMessage());
//        }

    }

    /**
     * onMessage 事件回调
     * 当客户端发来数据(Gateway进程收到数据)后触发
     *
     * @access public
     * @param  int       $client_id
     * @param  mixed     $data
     * @return void
     */
    public static function onMessage($client_id, $data)
    {
            $userinof = $_SESSION['user'];
            $data     = json_decode($data,true);
//            dump($userinof);
            $type     = $data['type'] ?? '';
            $url      = $data['url'] ?? '';
//            Gateway::sendToClient($client_id,'123123123123');
            if($type == 'ping'){
                return ;
            }
            if(!$url){
                Gateway::sendToClient($client_id,json_encode(['code'=>0,'mag'=>'url错误']));
            }else{
                $urls = explode('/',$url);
                $class = "app\\".$urls[0].'\\'.$urls[1];
                if(class_exists($class)){
                    $col   = new $class($client_id,$userinof,$data);
                    if($col->check_sign && !$col->cachesign($data)){
                        Gateway::sendToClient($client_id,json_encode(['code'=>0,'mag'=>'无效签名']));
                        return;
                    }
                    $function  =  $urls[2];
                    $col->$function($data);
                }else{
                    Gateway::sendToClient($client_id,json_encode(['code'=>0,'mag'=>'控制器不存在']));
                }
            }
    }

    /**
     * onClose 事件回调 当用户断开连接时触发的方法
     *
     * @param  integer $client_id 断开连接的客户端client_id
     * @return void
     */
    public static function onClose($client_id)
    {
        GateWay::sendToAll("client[$client_id] logout\n");
    }

    /**
     * onWorkerStop 事件回调
     * 当businessWorker进程退出时触发。每个进程生命周期内都只会触发一次。
     *
     * @param  \Workerman\Worker    $businessWorker
     * @return void
     */
    public static function onWorkerStop(Worker $businessWorker)
    {
        echo "WorkerStop\n";
    }
}
