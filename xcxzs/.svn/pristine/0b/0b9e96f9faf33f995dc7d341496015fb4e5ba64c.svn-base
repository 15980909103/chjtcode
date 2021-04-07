<?php
declare (strict_types = 1);

namespace app\common\listens;
use app\common\manage\TaskManage;
use app\common\websocket\BroadcastProcess;
use app\common\websocket\MyParser;

//use think\queue\Job;
//https://github.com/top-think/think-queue
use app\common\websocket\RedisRoom;
use Swoole\Server;
use think\App;

use think\facade\Config;
use InvalidArgumentException;

/**
 * udp服务针对websocket集群转发
 */
class UdpServer
{
    protected $localScoket = [];
    protected $udpScoketGroup=[];
    /**
     * @var Server
     */
    protected $server = null;
    /**
     * @var MyParser
     */
    protected $parser = null;

    protected $tableRoom = null;

    /**
     * 在swoole.init 监听服务初始化启动
     * @param Server $server
     * @param MyParser $parser
     */
    public function handle(Server $server, MyParser $parser)
    {
        $config = Config::get('swoole.distributed');
        if(empty($config)){
            throw new InvalidArgumentException("Invalid swoole config distributed");
        }

        $config['local'] = [
            "ip" => "127.0.0.1",
            "port" => 9602, //udp端口 ，webSocket集群监听跨服务器接收消息
            'pwd' => '123456'
        ];

        $this->localScoket = $config['local'];
        $this->udpScoketGroup = $config['udpsocket_group'];
        $config['redis'] = Config::get('swoole.pool')['redis'];

        $udp_server = $server->addlistener($this->localScoket['ip'],$this->localScoket['port'],SWOOLE_SOCK_UDP);
        if($udp_server==false){
            throw new InvalidArgumentException("udp服务器 ip端口错误");
        }
        $udp_server->set([
            'worker_num'      => 4,
            'max_request'     => 100000,
            'dispatch_mode'   => 2,
        ]);
        //监听接收UDP数据包
        $udp_server->on('Packet',[$this,'onPacket']);

        $this->server = $server;
        $this->parser = $parser;

        echo 'udp启动'.$this->localScoket['ip'].':'.$this->localScoket['port'];
    }

    /**
     * @param $udpServer
     * @param $data //发送的数据
     * @param $clientInfo //客户端信息
     */
    public function onPacket($udpServer,$data,$clientInfo){
        $isAuth = false;
        //客户端是本机服务器ip时
        if ($clientInfo['address'] === $this->localScoket['ip']) {
            $isAuth = true;
        } else {
            // 验证客户端是否是分布式服务器
            foreach ($this->udpScoketGroup as $key => $value) {
                if ($value['ip'] === $clientInfo['address'] && $value['port'] === $clientInfo['server_port'] && $value['pwd'] === $data['pwd']) {
                    $isAuth = true;
                    break;
                }
            }
        }
        if($isAuth === false){
            return $udpServer->sendto($clientInfo['address'], $clientInfo['port'], '校验失败' );
        }

        $data = $data?json_decode($data,true):'';
        if(empty($data)||empty($data['event'])){
            return $udpServer->sendto($clientInfo['address'], $clientInfo['port'], '缺失参数' );
        }
        if(method_exists($this,$data['event'])==false){
            return $udpServer->sendto($clientInfo['address'], $clientInfo['port'], '参数错误' );
        }

        //进行分配处理
        $this->{$data['event']}($udpServer, $clientInfo, $data);

        $udpServer->sendto($clientInfo['address'], $clientInfo['port'], 'ok' );
    }

    /**
     * 判断当前fd是否在线
     * @param $udpServer
     * @param $clientInfo
     * @param $data
     */
    protected function isOnlineByFd($udpServer, $clientInfo, $data){
        TaskManage::getInstance($this->server)->asyncPost([
            'fd' => $data['fd'],
            'address' => $clientInfo['address'],
            'port' => $clientInfo['port'],
        ],function ($data, $server){
            $rs = (bool)$server->getClientInfo($data['fd'])['websocket_status'] ?? false;
            $server->sendto($data['address'], $data['port'],json_encode(['result'=>$rs],JSON_UNESCAPED_UNICODE));
        });
    }

    /**
     * 进行websocket数据发送，通过task进行websocket转发
     * @param $udpServer
     * @param $clientInfo
     * @param $data
     */
    protected function msgCallback($udpServer, $clientInfo, $data){
        $event = 'msgCallback';//与方法名字一致

        if($data['type']=='isBroadcast'){//进行广播
            //通过task进行websocket转发
            $this->broadcast([
                'payload' => $this->parser->encode($event, $data['payload_data']),
                'isBroadcast' => 1
            ]);
        }elseif ($data['type']=='isRoom'){//进行房间群发
            if(!empty($data['toRoom'])){
                $this->broadcast([
                    'payload' => $this->parser->encode($event, $data['payload_data']),
                    'toRoom'  => $data['toRoom']
                ]);
            }
        }elseif ($data['type']=='isTo'){//进行指向发送
            $descriptors = $data['descriptors'];

            if(!empty($descriptors)){
                //通过task进行websocket转发
                $this->server->task([
                    'action' => 'push',
                    'data'   => [
                        'sender'      => '',
                        'descriptors' => $descriptors,//[2],
                        'broadcast'   => false,
                        'assigned'    => true,
                        'payload'     => $this->parser->encode($event, $data['payload_data']),
                    ],
                ]);
            }
        }
    }

    protected function broadcast($data){
        $post = [
            'sender'      => '',
            'payload'     => $data['payload'],
        ];
        if(!empty($data['isBroadcast'])){
            $post['isBroadcast'] = 1;
        }
        if(!empty($data['toRoom'])){
            $post['toRoom'] = $data['toRoom'];
        }

        BroadcastProcess::getInstance()->task($post);
    }

    /**
     * @return RedisRoom
     */
    public function getTableRoom( ){
        return $this->tableRoom = $this->tableRoom?? App::getInstance()->make(RedisRoom::class);
    }
}
