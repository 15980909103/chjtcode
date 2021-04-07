<?php

namespace app\common\websocket;


use app\websocket\Bobing;
use app\websocket\BobingStore;
use Swoole\Server;
use Swoole\WebSocket\Frame;
use think\Config;
use think\Container;
use think\facade\Event;
use think\Request;
use think\swoole\websocket\socketio\Handler;
use think\swoole\websocket\socketio\Packet;

class MyHandler extends Handler
{
    private $privateEvents = [
        'BeforeConnect',
        'CheckCanLoad'
    ];
    private $modules = [
        'bobing' => Bobing::class,
        'bobingstore' => BobingStore::class
    ];
    private $filePath = '';
    /**
     * @var MyParser
     */
    private $parser;

    public function __construct(Server $server, Config $config)
    {
        $this->filePath = root_path().'app'.DIRECTORY_SEPARATOR.'websocket'.DIRECTORY_SEPARATOR;

        foreach ($this->modules as $module){
            Container::getInstance()->make($module);
        }
        $this->parser = $config->get('swoole.websocket')['parser'];

        parent::__construct($server, $config);
    }

    /**
     * "onOpen" listener.
     *
     * @param int     $fd
     * @param Request $request
     */
    public function onOpen($fd, Request $request)
    {

        //进行前置埋点校验是否建立连接
        $data = $request->param();
        if(empty($data['m'])){//模块标识
            return $this->server->close($fd);
        }
        $module = $this->getModule($data['m']);
        if(empty($module)){
            return $this->server->close($fd);
        }

        $data['fd'] = $fd;
        $isLogin = $this->assignMethod($module, [
            'event' => 'BeforeConnect', //操作token和用户关系
            'data'  => $data
        ]);
        if(empty($isLogin)){
           return $this->server->close($fd);
        }


        if (!$request->param('sid')) {
            $payload        = json_encode(
                [
                    'sid'          => base64_encode(uniqid()),
                    'upgrades'     => [],
                    'pingInterval' => $this->config->get('swoole.websocket.ping_interval'),
                    'pingTimeout'  => $this->config->get('swoole.websocket.ping_timeout'),
                ]
            );
            $initPayload    = Packet::OPEN . $payload;
            $connectPayload = Packet::MESSAGE . Packet::CONNECT;

            $this->server->push($fd, $initPayload);
            $this->server->push($fd, $connectPayload);
        }

    }

    /**
     * "onMessage" listener.
     *  only triggered when event handler not found
     *
     * @param Frame $frame
     * @return bool
     */
    public function onMessage(Frame $frame)
    {
        $payload = MyPacket::getPayload($frame->data);//解码请求的的数据

        if ($payload&&!empty($payload['module'])&&!empty($payload['event'])) {  //用于实现自定义多应用路由派发
            $module = $this->getModule($payload['module']);
            if(!empty($module)&&!$this->checkInList($this->privateEvents, $payload['event'])){
                if($this->assignMethod($module, [
                        'event' => 'CheckCanLoad', //操作token和用户关系
                        'data'  => [ 'data'=> $payload['data'], 'fd'=>$frame->fd ]
                    ]) == true){
                    //$this->server->push($frame->fd,'42["dd", {cc: "test"}]'.$frame->fd);

                    $this->assignMethod($module, $payload);
                    return true;//不执行后续的框架自带的websocket事件处理
                }
            }
        }

        $this->checkHeartbeat($frame->fd, $frame->data);

        return true;
    }

    //获取应用对象
    public function getModule($module){
        $module = trim_all($module);
        return $this->modules[strtolower($module)]??'';
    }
    //调用对应方法
    public function assignMethod($module, $payload){
        $module = Container::getInstance()->make($module);
        $payload['data'] = !empty($payload['data'])?$payload['data']:[];
        $payload['event'] = trim_all($payload['event']);

        return $module->{'on'.ucfirst($payload['event'])}($payload['data']);
    }
    //检测是否是名单列表
    protected function checkInList($list, $name){
        $flag = false;
        $name = strtolower($name);
        foreach ($list as $key=>$item){
            if(strtolower($item)==$name){
                $flag = true;
                break;
            }
        }

        return $flag;
    }


    /**
     * 获取目录文件
     * @param $path
     * @return array
     */
    protected function getDir($path){
        $list = [];
        if(is_dir($path)){
            $dir = scandir($path);
            foreach ($dir as $value){
                $sub_path =$path .'/'.$value;
                if($value == '.' || $value == '..'){
                    continue;
                }else if(is_dir($sub_path)){
                    //echo '目录名:'.$value .'<br/>';
                   $this->getDir($sub_path);
                }else{
                    //.$path 可以省略，直接输出文件名
                    //echo ' 最底层文件: '.$path. ':'.$value.' <hr/>';
                    $list[] = $path.$value; //目录加文件名
                }
            }
        }
        return $list;
    }

}
