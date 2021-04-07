<?php
declare (strict_types = 1);

namespace app\command\crontab;

use app\common\base\CommandBase;
use app\common\traits\TraitAsyncHttp;
use Swoole\Process;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\Exception;
use think\facade\Config;
use think\facade\Console;
use think\facade\Db;

class CrontabManage extends CommandBase
{

    // config/console.php中需要配置，把命令绑定到相应类 旧版 弃用
    private $cronJobs= [
        'estatesRank' => [
            'rule'=> '0 0 * * *',
            'method' => 'index/estates/setRank',
        ],// 楼盘排行榜 每天零点执行
        
    ];

    private $config = [];
    private $salt = 'g#ydZAru3SCvTPYv#B#!yn2wc9Ux4e2T';

    // 获取token
    private function getToken()
    {
        // 更安全的token TODO

        return $this->salt;
    }


    //协程请求
    private function doCoHttp($config = []){
        if(strpos($config['url'], 'http')===false){
            throw new Exception('请带上http格式');
        }
        $data = $config['data']??[];
        unset($config['data']);
        $http_headers = $config['headers'];
        $http_type = $config['http_type'];//请求类型 get,post
        $url_arr = parse_url($config['url']);//解析URL
        unset($config);

        $host = $url_arr['host']; //请求的host地址
        $method_url = strpos($url_arr['path'],'/')===0?substr($url_arr['path'],1):$url_arr['path'];//请求方法
        if(!empty($url_arr['query'])){//url存在请求参数时候合并请求参数
            parse_str($url_arr['query'],$url_data);
            $data = array_merge($url_data,$data);
        }

        $port = 80;
        $isHttps = false;
        if($url_arr['scheme']=='https') {
            $port = 443;
            $isHttps = true;
        }
        unset($url_arr);

        $client = new \Swoole\Coroutine\Http\Client($host, intval($port), boolval($isHttps));
        $client->set(['timeout' => 3.5]);//3.5秒请求超时

        if(!empty($http_headers)){
            $client->setHeaders($http_headers);
            if($http_headers['Content-Type']=='application/json'){
                $data = json_encode($data,JSON_UNESCAPED_UNICODE);
            }
        }
        $client->setMethod(strtoupper($http_type));
        $client->setData($data);
        $status = $client->execute('/'.$method_url);
        $body = $client->getBody();
        $client->close();

        if(!empty($body)){
            $arr = json_decode($body,true);
            $body = $arr? $arr: $body;
        }
        return [
            'status'=> $status,
            'body'=> $body
        ];
    }

    // 获取及设置配置
    private function getHttpConfig(){
        $port = '80';
        $domain = Config::get('app')['domain_name'] ?? '';

        $isHttps = false;
        if(!empty($domain)) {
            $isHttps = strpos($domain, 'https');
            if($isHttps!==false) {
                $port = 443;
            }
        }

        if($isHttps!==false){
            $host = str_replace( 'https://','',$domain);
        }else{
            $host = str_replace( 'http://','',$domain);
        }


        $this->config = [
            // 'host' => '127.0.0.1',
            // 'port' => 9501,
            'host' => $host,
            'port' => $port,
        ];
    }
    

    private $shellPath = '';//指定php版本路径'/www/server/php/74/bin/'无就'',

    protected function parseTimeForCron($time , $cron)
    {
        $cron_parts = explode(' ' , $cron);
        if(count($cron_parts) != 5)
        {
            return false;
        }

        list($min , $hour , $day , $mon , $week) = explode(' ' , $cron);

        $to_check = array('min' => 'i' , 'hour' => 'G' , 'day' => 'j' , 'mon' => 'n' , 'week' => 'w');

        $ranges = array(
            'min' => '0-59' ,
            'hour' => '0-23' ,
            'day' => '1-31' ,
            'mon' => '1-12' ,
            'week' => '0-6' ,
        );

        foreach($to_check as $part => $c)
        {
            $val = $$part;
            $values = array();

            /*
                For patters like 0-23/2
            */
            if(strpos($val , '/') !== false)
            {
                //Get the range and step
                list($range , $steps) = explode('/' , $val);

                //Now get the start and stop
                if($range == '*')
                {
                    $range = $ranges[$part];
                }
                list($start , $stop) = explode('-' , $range);

                for($i = $start ; $i <= $stop ; $i = $i + $steps)
                {
                    $values[] = $i;
                }
            }
            /*
                For patters like :
                2
                2,5,8
                2-23
            */
            else
            {
                $k = explode(',' , $val);

                foreach($k as $v)
                {
                    if(strpos($v , '-') !== false)
                    {
                        list($start , $stop) = explode('-' , $v);

                        for($i = $start ; $i <= $stop ; $i++)
                        {
                            $values[] = $i;
                        }
                    }
                    else
                    {
                        $values[] = $v;
                    }
                }
            }

            if ( !in_array( date($c , $time) , $values ) and (strval($val) != '*') )
            {
                return false;
            }
        }

        return true;
    }

    private $taskQueue = [];

    protected function doJob($input, $output){
        $num = 0;
        $this->getHttpConfig();
        $token = $this->getToken();
        foreach($this->cronJobs as $method => $v)
        {
            $time = time();

            if( $this->parseTimeForCron($time , $v['rule']) )
            {
                //$num++;
                // $process = new \Swoole\Process(function(\Swoole\Process $worker)use($method, $output)
                // {
                //     $output->writeln('Child #' . getmypid() . " start ");

                //     $shell = $this->shellPath.'php think '.$method;
                //     $ret =  $worker->exec('/bin/sh', ['-c',$shell]);//$worker->exec('/bin/sh', ['-c',"php think dest"]);
                //     $worker->exit(0);
                // });
                // $process->start();
                
                /*$path = $v['method'] ?? null;
                if(!empty($path)) {
                    $this->setHttpHeadrs( ['Content-Type'=> 'application/json']);
                    $this->setHttpType('POST');
                    $this->doHttp($path, ['token' => $token]);
                }*/
                if(!empty($v['method'])){
                    $num++;
                    array_push($this->taskQueue,$v['method']);
                }
            }
        }
        /*for ($n = $num; $n--;) {//堵塞监听子进程退出
            $status = \Swoole\Process::wait(true);
            $output->writeln("Recycled #{$status['pid']}, code={$status['code']}, signal={$status['signal']}");
        }
        $output->writeln('Parent #' . getmypid() . " exit ");*/

        if($num>0){
            $sch = new \Swoole\Coroutine\Scheduler;
            $sch->parallel($num, function () use($token, $output) {
                $path = array_shift($this->taskQueue);
                //$path = $v['method'] ?? null;

                if(!empty($path)) {
                    // echo $path.PHP_EOL;
                    //$this->setHttpHeadrs( ['Content-Type'=> 'application/json']);
                    //$this->setHttpType('POST');
                    //$this->doHttp($path, ['token' => $token]);

                    $domain = Config::get('app')['domain_name'];
                    

                    $res = $this->doCoHttp([
                        'url' =>  $domain.'/'.$path,
                        'data'=> [],
                        'headers' => ['Content-Type'=> 'application/json'],
                        'http_type' => 'POST',
                    ]);
                }
            });
            $sch->start();
        }
    }



    ///////http请求////////
    private $http_type = 'post';
    private $http_headers = [];

    private function connect(){
        $host = $this->config['host'];
        if(empty($host)){
            throw new Exception('缺失host域名设置');
        }
        $port = $this->config['port']??80;
        $isHttps = $this->config['isHttps']??false;

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
        return $this->http_type = $type;
    }
    protected function doHttp($method_url = '', $data=[],$callfun=null){
        // echo $method_url.'-----';echo strtoupper($this->http_type).'----';print_r($this->http_headers);print_r($data);
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
