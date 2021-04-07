<?php
declare (strict_types = 1);

namespace app\common\websocket;
use app\common\base\WebsocketBase;
use app\common\pool\RedisPool;
use app\common\traits\TraitContext;
use Swoole\Server;
use think\App;
use think\Container;
use think\swoole\Websocket;
use Throwable;


trait TraitWebSocket{

    use TraitContext;

    /**
     * @var WebsocketBase
     */
    public $websocket = null;
    /**
     * @var Server
     */
    public $server = null;

    public $table = null;


    //当前登录用户对应的用于标识用户身份 由$userId.'!'.$merchantId组成
    protected function getUid($uid = null){
        if(!is_null($uid)){
            self::contextSetData('uid',$uid);
        }else{
            $uid = self::contextGetData('uid');
        }

        return $uid;
    }
    //当前登录用户对应的fd信息, $fdInfo为此次登录的fd信息
    protected function getMyFdInfo($fdInfo = null){
        if(!is_null($fdInfo)){
            self::contextSetData('fdInfo',$fdInfo);
        }else{
            $fdInfo = self::contextGetData('fdInfo');
        }

        return $fdInfo;
    }


    /**
     * constructor. 公用初始化的一些操作
     * @param Container $container
     * @param Server $server
     */
    public function initBase(Container $container, Server $server){
        $this->server    = $server;

        $this->websocket = $container->make(WebsocketBase::class);
        $container->bind(Websocket::class,$this->websocket);//将框架原来的Websocket类替换为我们的

    }

    /**
     * id解密
     * @param string $val
     * @param string $field
     * @return array|int|mixed
     */
    protected function deCodeId($val = '',$field =''){
        // return $val;
        if($field == 'uid'){//uid带格式拼接
            $val = hashids_decode($val,1);
            $val = implode('!',$val);
        }else{
            $val = hashids_decode($val);
        }
        return $val;
    }

    /**
     * id加密
     * @param string $val
     * @param string $field
     * @return int|string
     */
    protected function enCodeId($val = '',$field=''){
        // return $val;
        if($field=='uid'){
           $val = explode('!',$val);
           return $val = hashids_encode($val[0],$val[1]);
        }

        return hashids_encode($val);
    }

    /**
     * 生产房间号
     * @param string|int $mod 混进的取模值
     * @return string
     */
    protected function createRoomNo($mod = '')
    {
        if($mod!==''){
            str_pad(strval($mod),3,'0',STR_PAD_LEFT );
        }
        $no = 'pk' . date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).str_pad(strval(mt_rand(1, 99999)), 5, '0', STR_PAD_LEFT).$mod;
        return $no;
    }

    /**
     * 获取所有的Websocket链接
     * @return array
     */
    protected function getWebsocketConnections()
    {
        //echo "当前服务器共有 " . count($this->server->connections) . " 个连接\n";
        //echo PHP_EOL;

        //过滤非状态的 websocket_status 0不是，1连接等待握手，2正在握手，3握手成功连接
        return array_filter(iterator_to_array($this->server->connections), function ($fd) {
            return (bool)$this->server->getClientInfo($fd)['websocket_status'] ?? false;
        });
    }


    /**
     * 设置错误状态给客户端
     */
    protected function setError($msg ='')
    {
        self::contextSetData('status_msg',[
            'code' => '-1',
            'msg'=> $msg
        ]);
        return $this;
    }
    /**
     * 设置正确状态给客户端
     */
    protected function setSuccess($msg = 'success'){
        self::contextSetData('status_msg',[
            'code' => '1',
            'msg'=> $msg
        ]);
        return $this;
    }
    /**
     * 执行消息发送
     * @param array $data //要发送的数据
     * @param string $flagForWeb //前端识别的名称
     */
    protected function send($data = [], $flagForWeb = 'msgCallback')
    {
        $status_msg = self::contextGetData('status_msg');
        $status_msg = $status_msg??[
            'code' => '1',
            'msg'=> 'success'
        ];
        $status_msg['data'] = $data;
        $status_msg['fd'] = $this->getCurrentSenderFd();
        //重置为默认正确状态信息
        $this->setSuccess();

        $this->websocket->emit($flagForWeb, $status_msg);
    }

    /**
     * 设置广播群发，如果当时是由当前某个fd (getCurrentSenderFd) 触发广播则不会广播发送该人，如果需所有人员发送就在广播发送后在补上该fd的发送
     * $this->setBroadcast()->send(['msg'=>'广播信息-'.$this->getCurrentSenderFd().'上线了']);
     * @param bool|integer $flag //是否进行广播群发
     * @return $this
     */
    protected function setBroadcast($flag = true)
    {
        if ($flag) {
            $this->websocket->broadcast();
        }
        return $this;
    }

    /**
     * 设置指向进行群发
     * $this->setMass($target)->send(['msg'=>'欢迎来到']);
     * @param null $target //要发送的目标 integer, string, array //fd or room names
     * @param string $type
     * @return $this
     */
    protected function setMass($target = null,$type='room')
    {
        switch ($type){
            case 'room':
                $this->websocket->setToRoom($target);
                break;
            case 'uid':
                $this->websocket->setToUid($target);
                break;
            case 'fd':
                $this->websocket->setToFd($target);
                break;
        }

        return $this;
    }

    /**
     * 获取当前操作的客户端fd
     * @return mixed
     */
    protected function getCurrentSenderFd()
    {
        return $this->websocket->getSender();
    }

    /**
     * 设置当前要操作的客户端fd //用于指定人员对应的相关操作
     * 指定客户端发送，假设已知某一客户端连接fd为1 $this->setCurrentSenderFd(1)->send(['guangbo' => 1, 'getdata' => $event['asd']]);
     * @param int $val
     * @return $this
     */
    protected function setCurrentSenderFd(int $val)
    {
        $this->websocket->setSender($val);
        return $this;
    }


    /**
     *  用户断开链接的回调
     */
    public function onClose()
    {
        $this->close($this->getCurrentSenderFd());
    }
    /**
     * 压缩字符串内容
     * @param string|array $data
     * @return false|string
     */
    protected function compressData($data){
        return gzcompress(json_encode($data,JSON_UNESCAPED_UNICODE));
    }
    /**
     * 解压字符串内容
     * @param string $data
     * @return false|string
     */
    protected function unCompressData($data = ''){
        return json_decode(gzuncompress($data),true);
    }

    /**
     * @return \Redis|void
     */
    protected function getRedis($config = []){
        if(empty($config)) {
            $config = [
                'database'=>8,
                'max_active'=> 2
            ];
        }
        $redisObj = RedisPool::getInstance();
        $config = $redisObj->setConfig('swoole.pool.redis', $config);

        return $redisObj->init($config);
    }

    /**
     * fd 关闭时的操作进行资源回收
     * @return mixed
     */
    abstract protected function close();

    /**
     * 连接时进行用户身份验证和 uid/fd的绑定
     * @param array $data
     * @return bool
     */
    abstract function onBeforeConnect($data);
    /**
     * 前置校验是否可以往下执行监听方法, 内部调用isLogin()判断是否登录, 进行此次请求的用户赋值
     * @param $data ['event','data']
     * @return bool true 可以往下执行 false不可往下执行
     */
    abstract function onCheckCanLoad($data);

    /**
     * //校验 fd 是否登录成功
     * @param $data ['fd']
     * @return bool|array 无登录时返回fasle，有登录时返回用户信息
     */
    abstract function isLogin($data);

}