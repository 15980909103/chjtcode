<?php


namespace app\common\base;

use app\common\websocket\BroadcastProcess;
use app\common\websocket\MyPusher;
use app\common\websocket\RedisRoom;
use app\common\websocket\RedisUserFd;
use Swoole\Server;
use think\App;
use think\facade\Config;
use app\common\websocket\MyParser;
use think\swoole\coroutine\Context;
//use think\swoole\Websocket;
use think\swoole\websocket\Pusher;
use think\swoole\websocket\Room;
use InvalidArgumentException;

error_reporting(E_ERROR | E_PARSE );//关闭警告错误

class WebsocketBase
{
    const PUSH_ACTION   = 'push';
    const EVENT_CONNECT = 'connect';

    /**
     * @var Server
     */
    protected $server;

    /**
     * @var RedisRoom
     */
    protected $tableRoom;
    /**
     * @var RedisUserFd
     */
    protected $tableUser;

    /**
     * @var MyParser
     */
    protected $parser;


    protected $localConfig = [];
    protected $udpsocket_group=[];

    /**
     * Websocket constructor.
     *
     * @param Server          $server
     * @param Room            $room
     * @param MyParser $parser
     */
    public function __construct(Server $server, Room $room, MyParser $parser)
    {
        $config = Config::get('swoole');
        if(empty($config['distributed']['local'])){
            throw new InvalidArgumentException("Invalid config distributed local");
        }
        $this->localConfig = $config['distributed']['local'];
        $this->udpsocket_group = $config['distributed']['udpsocket_group'];
        $this->server = $server;
        $this->tableRoom = $room;
        $this->parser = $parser;

        //redis用户uid fd映射
        $this->tableUser = App::getInstance()->make(RedisUserFd::class, [
            [
                'local' => $this->localConfig,
                'udpsocket_group' => $this->udpsocket_group,
                'redis' => $config['pool']['redis'],
            ]
        ]);

        App::getInstance()->bind(Pusher::class,MyPusher::class);//将框架原来的类替换为我们的

        $this->tableUser->prepareRedis();
    }

    /**
     * 聊天室操作
     * @return RedisRoom
     */
    public function getTableRoom(){
        return $this->tableRoom;
    }

    /**
     * 用户信息操作
     * @return RedisUserFd
     */
    public function getTableUser(){
        return $this->tableUser;
    }


    /**
     * 通过uid获取对应的fd
     * @param $uid
     * @param string $type ['string'.'array']
     * @return array|bool|string
     */
    public function getFdByUid($uid, $type = 'string'){
        return $this->getTableUser()->getFdByUid($uid, $type);
    }

    /**
     * 通过fd找到对应的用户id
     * @param $fd
     * @return mixed
     */
    public function getUidByFd($fd){
        return $this->getTableUser()->getUidByFd($fd);
    }

    /**
     * 通过某个uid判断对应fd是否在线
     * @param $uid
     * @return array|bool
     */
    public function isOnLineByUid($uid){
        $fdInfo = $this->getFdByUid($uid, 'array');
        if(empty($fdInfo['fd'])){
            return false;
        }

        $is_online = $this->isOnlineByFd([
            'isLocal' => $fdInfo['isLocal'],
            'fd' => $fdInfo['fd'],
            'local_ip' => $fdInfo['local_ip'],
            'local_port' => $fdInfo['local_port'],
        ]);

        return $is_online==true?[ 'websocket_status'=>$is_online, 'fd'=>$fdInfo['fd'] ]: false;
    }
    /**
     * 判断当前某个fd是否在线,默认为当前机器ip
     * @param int|array $fdInfo
     * @return bool
     */
    public function isOnlineByFd($fdInfo){
        if(is_array($fdInfo)){
            if(empty($fdInfo['local_ip'])||empty($fdInfo['local_port'])){
                throw new InvalidArgumentException("Invalid remote ip|port");
            }
        }else{
            $fdInfo['fd'] = intval($fdInfo);
            if(empty($fdInfo['fd'])){
                throw new InvalidArgumentException("Invalid fd int");
            }
            $fdInfo['isLocal']=1;
        }

        if($fdInfo['isLocal']==1){
            return (bool)$this->server->getClientInfo($fdInfo['fd'])['websocket_status'] ?? false;
        }

        $pwd = false;
        foreach ($this->udpsocket_group as $item){
            if($item['ip']==$fdInfo['local_ip']&&$item['port']==$fdInfo['local_port']){
                $pwd = $item['pwd'];
                break;
            }
        }
        if($pwd===false){
            throw new InvalidArgumentException("Invalid {$fdInfo['local_ip']}-{$fdInfo['port']} pwd error");
        }
        return $this->udpPost([
            'ip' => $fdInfo['local_ip'],
            'port' => $fdInfo['local_port'],
            'pwd' => $pwd
        ],[
            'event'=>'isOnlineByFd',
            'data'=>[
                'fd'=>$fdInfo['fd']
            ]
        ]);
    }

    /**
     * 标识某个用户上线时间点
     * @param $uid
     * @param int $time //正代表上线时间点，负代表下线时间点，0为没有
     */
    public function setOnLineTimeByUid($uid, $time){
        $this->getTableUser()->setOnLineTimeByUid($uid, $time);
    }

    /**
     * 获取用户的上线时间
     * @param $uid //正代表上线时间点，负代表下线时间点，0为没有
     * @return int
     */
    public function getOnLineTimeByUid($uid){
        return $this->getTableUser()->getOnLineTimeByUid($uid);
    }

    /**
     * 针对uid映射fd表 增加自定义字段存储
     * @param $uid
     * @param $field
     * @param $val
     */
    public function setFieldByUid($uid, $field, $val){
        return $this->getTableUser()->setFieldByUid($uid, $field, $val);
    }


    /**
     * 添加用户进房间
     * @param string| array $rooms
     * @param $uid
     * @param $type
     * @return $this
     */
    public function joinRoomByUid($rooms, $uid, $type=1):self
    {
        //$uid = $uid??$this->getSender();使用fd时候,去掉注释

        $this->tableRoom->add($uid, $rooms, $type);
        return $this;
    }
    /**
     * 用户离开房间
     * @param string|array $rooms
     * @param $uid
     * @return $this
     */
    public function leaveRoomByUid($uid, $rooms, $fdKey, $isDelete = 0): self
    {
        //$uid = $uid??$this->getSender();使用fd时候,去掉注释

        $uidIp = $this->getTableUser()->fdKeyToFd($fdKey);
        $this->tableRoom->deleteUidInRoom($uid, $rooms, $uidIp, $isDelete);
        return $this;
    }

    /**
     */
    public function leave(){

    }

    /**
     * 设置是否广播
     * Set broadcast to true.
     */
    public function broadcast(): self
    {
        Context::setData('websocket._broadcast', true);

        return $this;
    }

    /**
     * 获取广播的状态
     * Get broadcast status value.
     */
    public function isBroadcast()
    {
        return Context::getData('websocket._broadcast', false);
    }


    /**
     * 存储要发送的对象fd 可指定单人或者多人的fd，只针对此台机子的fd
     * @param integer|string|array $values
     * @return $this
     */
    public function setToFd($values): self
    {
        $values = is_string($values) || is_integer($values) ? func_get_args() : $values;

        $to = Context::getData("websocket._toFd", []);

        foreach ($values as $value) {
            if (!in_array($value, $to)) {
                $to[] = $value;
            }
        }
        Context::setData("websocket._toFd", $to);

        return $this;
    }
    /**
     * 获取要发送的对象fd，只针对此台机子的fd
     */
    protected function getToFd()
    {
        return Context::getData("websocket._toFd", []);
    }
    /**
     * 存储要发送的对象uid 可指定单人或者多人
     * @param integer|string|array $values
     * @return $this
     */
    public function setToUid($values): self
    {
        $values = is_string($values) || is_integer($values) ? func_get_args() : $values;

        $to = Context::getData("websocket._toUid", []);

        foreach ($values as $value) {
            if (!in_array($value, $to)) {
                $to[] = $value;
            }
        }
        Context::setData("websocket._toUid", $to);

        return $this;
    }
    /**
     * 获取要发送的对象uid
     */
    protected function getToUid()
    {
        return Context::getData("websocket._toUid", []);
    }
    /**
     * 存储要发送的房间 可指定单个或者多个
     * @param integer|string|array $values
     * @return $this
     */
    public function setToRoom($values): self
    {
        $values = is_string($values) || is_integer($values) ? func_get_args() : $values;

        $to = Context::getData("websocket._toRoom", []);

        foreach ($values as $value) {
            if (!in_array($value, $to)) {
                $to[] = $value;
            }
        }
        Context::setData("websocket._toRoom", $to);

        return $this;
    }
    /**
     * 获取要发送的房间
     */
    protected function getToRoom()
    {
        return Context::getData("websocket._toRoom", []);
    }

    protected function getTo(){
        return !empty($this->getToUid())||!empty($this->getToRoom())||!empty($this->getToFd());
    }

    /**
     * Get all fds we're going to push data to.
     */
    protected function getFds()
    {
        $toRoom  = $this->getToRoom();
        /*$toRoom_fds = [];
        foreach ($toRoom as $value){
            $toRoom_fds = array_merge($toRoom_fds,$this->getTableRoom()->getClients($value));//获取本机中room的Clients的fd集
        }*/

        $toUid  = $this->getToUid();
        $toUid_fds = []; //本机fd集
        $tuUid_remoteFds = []; //远程的fd集
        foreach ($toUid as $value){
            $fdInfo = $this->getFdByUid($value,'array');
            if(empty($fdInfo['fd'])){
                continue;
            }
            if($fdInfo['isLocal']==1){
                $toUid_fds[] = $fdInfo['fd'];
            }else{
                $tuUid_remoteFds[$fdInfo['local_ip'].'-'.$fdInfo['local_port']][] = $fdInfo['fd']; //以服务器群分组fd集
            }
        }
        unset($toUid);

        $fds = $this->getToFd();
        //$fds = array_merge($fds,$toUid_fds,$toRoom_fds);
        $fds = array_merge($fds,$toUid_fds);
        
        return [
            'localFds' => array_values(array_unique($fds)),
            'remoteFds' => $tuUid_remoteFds,
            'toRoom' => $toRoom //房间号集
        ];
    }


    //===========基础实现==========//
    /**
     * 使用udp转发请求
     * @param $remoteConfig //请求地址配置 [ip,port,pwd]
     * @param $postData //请求数据 [event,data]
     * @return mixed
     */
    protected function udpPost($remoteConfig,$postData){
        if(empty($postData['event'])||empty($postData['data'])){//当前emit的event值，也用于标识udp服务器处理方法分发
            throw new InvalidArgumentException("Invalid event|data empty");
        }

        $postData['pwd'] = $remoteConfig['pwd'];
        if(empty($postData['pwd'])){
            throw new InvalidArgumentException("Invalid pwd empty");
        }

        $client = new \Swoole\Coroutine\Client(SWOOLE_SOCK_UDP);
        if (!$client->connect($remoteConfig['ip'], $remoteConfig['port'], 0.5))
        {
            throw new InvalidArgumentException("connect failed. Error: {$client->errCode}".PHP_EOL);
        }
        $client->send(json_encode($postData,JSON_UNESCAPED_UNICODE));
        $rs = $client->recv();

        $client->close();

        return $rs;
    }

    /**
     * Emit data and reset some status.
     *
     * @param string
     * @param mixed
     *
     * @return boolean
     */
    public function emit(string $event, $data = null): bool
    {
        $fds      = $this->getFds();
        $assigned = !empty($this->getTo());

        try {
            if (empty($fds) && $assigned) {
                return false;
            }

            $isBroadcast = $this->isBroadcast();
            $result = true;

            /////////////////////////////////////////
            $dd = json_encode($fds,true);
            $pd = strval($isBroadcast==true||!empty($fds['localFds'])||!empty($fds['toRoom'])||(empty($fds['localFds'])&&empty($fds['toRoom'])&&empty($fds['remoteFds'])));
            $this->server->push($data['sender'], '42["dd", {cc: "进行广播投递条件",dd:'.$dd.',pd:'.$pd.'}]');
            /////////////////////////////////////////
            ///

            //当前机子负责的发送 //empty($fds['localFds'])&&empty($fds['toRoom'])&&empty($fds['remoteFds'])为发送给自己
            if($isBroadcast==true||!empty($fds['localFds'])||!empty($fds['toRoom'])||(empty($fds['localFds'])&&empty($fds['toRoom'])&&empty($fds['remoteFds']))){
                if($isBroadcast == true||!empty($fds['toRoom'])){
                    $post = [
                        'sender'      => $this->getSender(),
                        'payload'     => $this->parser->encode($event, $data),
                    ];
                    if(!empty($isBroadcast)){
                        $post['isBroadcast'] = 1;
                    }
                    if(!empty($fds['toRoom'])){
                        $post['toRoom'] = $fds['toRoom'];
                    }

                    BroadcastProcess::getInstance()->task($post);
                }else{
                    $result = $this->server->task([
                        'action' => static::PUSH_ACTION,
                        'data'   => [
                            'sender'      => $this->getSender(),
                            'descriptors' => $fds['localFds'],
                            'broadcast'   => $isBroadcast,
                            'assigned'    => $assigned,
                            'payload'     => $this->parser->encode($event, $data),
                        ],
                    ]);
                }
            }
            unset($fds['localFds']);

            //判断是否需要分布式转发
            if($isBroadcast == true){//进行广播
                foreach ($this->udpsocket_group as $item){
                    $this->udpPost([
                        'ip' => $item['ip'],
                        'port' => $item['port'],
                        'pwd' => $item['pwd'],
                    ],[
                        'event' => $event,
                        'type' => 'isBroadcast',
                        'data' => [
                            'payload_data' => $data,
                        ]
                    ]);
                }
            }else {
                if(!empty($fds['toRoom'])){//房间群发
                    foreach ($this->udpsocket_group as $item){
                       $this->udpPost([
                            'ip' => $item['ip'],
                            'port' => $item['port'],
                            'pwd' => $item['pwd'],
                       ],[
                            'event' => $event,
                            'type'  => 'isRoom',
                            'data' => [
                                'payload_data' => $data,
                                'toRoom' => $fds['toRoom']
                            ]
                       ]);
                    }
                }

                //进行指向发送
                //$fds['remoteFds']以服务器分组切割要发送的fd集
                if(!empty($fds['remoteFds'])){
                    foreach ($this->udpsocket_group as $item){
                        $itemRemote = $fds['remoteFds'][$item['ip'].'-'.$item['port']];
                        if(!empty($itemRemote)){
                            $this->udpPost([
                                'ip' => $item['ip'],
                                'port' => $item['port'],
                                'pwd' => $item['pwd'],
                            ],[
                                'event' => $event,
                                'type' => 'isTo',
                                'data'=>[
                                    //'sender'      => $this->getSender(),
                                    'descriptors' => $itemRemote,
                                    'broadcast'   => $isBroadcast,
                                    'assigned'    => $assigned,
                                    'payload_data' => $data,
                                ]
                            ]);
                        }
                    }
                }
            }

            return $result !== false;
        } finally {
            $this->reset();
        }
    }



    /**
     * Close current connection.
     * @param integer
     *
     * @return boolean
     */
    public function close(int $fd = null)
    {
        return $this->server->close($fd ?: $this->getSender());
    }

    /**
     * Set sender fd.
     * 设置当前机子发送者的fd
     * @param integer
     *
     * @return $this
     */
    public function setSender(int $fd)
    {
        Context::setData('websocket._sender', $fd);

        return $this;
    }

    /**
     * Get current sender fd.
     * 获取当前机子发送者的fd
     */
    public function getSender()
    {
        return Context::getData('websocket._sender');
    }


    protected function reset()
    {
        Context::removeData("websocket._toRoom");
        Context::removeData("websocket._toUid");
        Context::removeData("websocket._toFd");


        Context::removeData("websocket._to");
        Context::removeData('websocket._broadcast');
    }
    //===========基础实现==========//
}