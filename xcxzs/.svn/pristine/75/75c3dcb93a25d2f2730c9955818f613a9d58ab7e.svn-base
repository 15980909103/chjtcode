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
        /*'estatesRank' => [
            'rule'=> '0 0 * * *',
            'method' => 'index/estates/setRank',
        ],*/
        'estatesRank' => [
            'rule'=> '0 0 * * *',
            'method' => '999admin/Notify/cronTabNotify',
        ],
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
        $http_type = $config['http_type']?$config['http_type']:'POST';//请求类型 get,post
        $url_arr = parse_url($config['url']);//解析URL

        $file = $config['file']??[];//文件操作
        $fileString = $config['fileString']??[];//文件操作
        unset($config);

        $host = $url_arr['host']; //请求的host地址
        $method_url = strpos($url_arr['path'],'/')===0?substr($url_arr['path'],1):$url_arr['path'];//请求方法
        $http_type = strtoupper($http_type);
        if($url_arr['query']){//url存在请求参数时候合并请求参数
            //parse_str($url_arr['query'],$url_data);
            //$data = array_merge($url_data,$data);
            $method_url = $method_url.'?'.$url_arr['query'];
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

        if(!empty($data)){
            $decodeData = json_decode($data,true);
            if(!is_null($decodeData)){//是json格式
                $http_headers['Content-Type'] = 'application/json';
                $data = $decodeData;
            }
        }
        if(!empty($http_headers)){
            $client->setHeaders($http_headers);
            if($http_headers['Content-Type'] == 'application/json'){
                $data = json_encode($data,JSON_UNESCAPED_UNICODE);
            }
        }

        $client->setMethod($http_type);
        !empty($data)&&$client->setData($data);//请求数据操作
        if(!empty($file)){
            $client->addFile($file['path'],$file['name']);//进行文件上传操作
        }
        if(!empty($fileString)){
            $client->addData($file['data'],$file['name']);//进行文件上传操作
        }

        $status = $client->execute('/'.$method_url);

        //echo socket_strerror($client->errCode);
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

        $auth = $this->getToken();
        foreach($this->cronJobs as $method => $v)
        {
            $time = time();

            if( $this->parseTimeForCron($time , $v['rule']) )
            {
                if(!empty($v['method'])){
                    $num++;
                    array_push($this->taskQueue,$v['method']);
                }
            }
        }
        /*$output->writeln('Parent #' . getmypid() . " exit ");*/

        if($num>0){
            $sch = new \Swoole\Coroutine\Scheduler;
            $sch->parallel($num, function () use($auth, $output) {
                $path = array_shift($this->taskQueue);
                //$path = $v['method'] ?? null;

                if(!empty($path)) {
                    $domain = Config::get('app')['domain_name'];

                    $res = $this->doCoHttp([
                        'url' =>  $domain.'/'.$path,
                        'data'=> [
                            'auth'=> $auth
                        ],
                        'headers' => ['Content-Type'=> 'application/json'],
                        'http_type' => 'POST',
                    ]);
                }
            });
            $sch->start();
        }
    }




}
