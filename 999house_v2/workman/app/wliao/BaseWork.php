<?php


namespace app\wliao;


use app\common\base\HhDb;
use app\common\MyConst;
use app\common\pool\RedisPool;
use app\model\User;
use GatewayWorker\Lib\Gateway;
use think\Cache;
use think\Model;

class BaseWork extends Model
{
    public $client_id;
    public $user;
    public $user_id;
    public $check_sign = false;
    public function __construct($client_id,$user,$data)
    {
        $this->user          =  $user;
        $this->user_id       =  $user['type'] == 'admin' ? $user['user_id']:$user['id'];
        $this->client_id     =  $client_id;
    }

    /**
     * 校验登录
     */
    protected function check_login(){
          return true;
    }

    /**
     * cacheSing
     */
    public function cachesign($data){
        if(empty($data) || !is_array($data)){
            return false;
        }
        $req_sing = $data['sign'] ?? '';
        $time     = $data['time'];
        unset($data['sign']);
        if(empty($req_sing)){
            return false;
        }

        ksort($data);
        $sign ='';
        foreach ($data as $k=>$v){
            $sign.= $k.'='.$v.'&';
        }
        $sign = trim($sign,'&');
        $sign = $sign.MyConst::SING_KEY;
        Gateway::sendToAll($this->error($sign));

        if($time+3*60 < time()){
            return false;
        }
        $sign = md5($sign);

        Gateway::sendToAll($this->success([$sign]));
        strtolower($sign);
        if($sign != $req_sing){
            return false;
        }

        return true;

    }
    /**
     * @param $msg
     * @param $data
     * @return false|string
     */
    protected function error($msg,$data=[],$type){
        $err = [
            'code'  => 0,
            'msg'   => $msg ?? '失败',
            'type'              =>  $type,
            'sendUserInfo' =>[
               'user_name'  => $this->user['type'] =='admin' ? $this->user['account'] :($this->user['user_name'] ?? $this->user['nickname']),
               'id'         => $this->user_id,
            ],
            'returndata'  => $data ?? []
        ];
        return json_encode($err);
    }

    protected function success($data,$msg='成功',$type){
        $succ =[
            'code'              => 1,
            'msg'               => $msg ?? '成功',
            'type'              =>  $type,
            'sendUserInfo'      =>[
                'user_name'     => $this->user['type'] =='admin' ? $this->user['account'] :($this->user['user_name'] ?? $this->user['nickname']),
                'user_avatar'   => $this->user['type'] =='admin' ? $this->user['head_ico_path'] :($this->user['user_avatar'] ?? $this->user['headimgurl']),
                'id'            => $this->user_id,
            ],
            'returndata'      => $data ?? []
        ];
        return json_encode($succ);
    }

}