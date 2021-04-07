<?php
namespace app\common\traits;

use think\Exception;


/**
 * 异步协程请求
 * Trait asyncHttp
 * @package app\common\lib\smsapi\module
 */
trait TraitAsyncHttp{
    private $http_type = 'post';
    private $http_headers = [];

    private function connect(){
        $host = $this->config['host'];
        if(empty($host)){
            throw new Exception('缺失host域名设置');
        }
        $isHttps = $this->config['isHttps']??false;

        $https_str = strpos($host, 'https');
        if($https_str!==false) {
            $this->config['port'] = $this->config['port']??443;
        }
        if($https_str!==false){
            $host = str_replace( 'https://','',$host);
        }else{
            $host = str_replace( 'http://','',$host);
        }

        $port = $this->config['port']??80;

        if($port==443){ $isHttps=true; }
        if($isHttps==true&&$port==80){
            $port = 443;
        }

        $client = new \Swoole\Coroutine\Http\Client($host, intval($port), boolval($isHttps));
        $client->set(['timeout' => 3.5]);//3.5秒请求超时
        return $client;
    }

    protected function setHttpHeadrs($headers = []){
        $this->http_headers = $headers;
        return $this;
    }
    protected function setHttpType($type = 'post'){
        $this->http_type = $type;
        return $this;
    }
    protected function doHttp($method_url = '', $data=[],$callfun=null){
        //echo $method_url.'-----';echo strtoupper($this->http_type).'----';print_r($this->http_headers);print_r($data);

        $client = $this->connect();
        if(!empty($this->http_headers)){
            $client->setHeaders($this->http_headers);
            if($this->http_headers['Content-Type']=='application/json'){
                $data = json_encode($data,JSON_UNESCAPED_UNICODE);
            }
        }

        $client->setMethod(strtoupper($this->http_type));
        $client->setData($data);
        $status = $client->execute('/'.$method_url);
        $body = $client->getBody();
        $client->close();

        if(is_callable($callfun)){
            $callfun([
                'status'=> $status,
                'body'=> $body
            ]);
        }
    }
}
