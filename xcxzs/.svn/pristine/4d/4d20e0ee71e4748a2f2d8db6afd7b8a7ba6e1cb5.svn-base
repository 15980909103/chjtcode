<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: pl125 <xskjs888@163.com>
// +----------------------------------------------------------------------

namespace app\common\lib\wxapi\module;


use app\common\lib\wxapi\co\CoWxPool;
use think\Container;
use think\Db;
use think\facade\Config;
use think\facade\Request;
use Exception;

class  WxH5 extends WxBase
{
//    use TraitInstance;

    protected $my_merch_id = 2;
    protected $redis = null;

    public function __construct($config)
    {
        parent::__construct($config);

    }

    /**
     * 公众号授权登陆
     * @return mixed
     * @throws \Throwable
     */
    public function getH5Login($dataArr = [])
    {
        try {

            // $scheme = $_SERVER['HTTPS']=='on' ? 'https://' : 'http://';
            $uri = empty($dataArr['redirect_uri']) ? empty($_SERVER['REQUEST_URI']) ? '' : $_SERVER['REQUEST_URI'] : $dataArr['redirect_uri'];
            if (strpos($this->uri, '?')) {
                $realUri = trim($this->uri) . '&url=' . $uri;
            } else {
                $realUri = $this->uri . '?url=' . $uri;
            }

            /**
             * $wxServer = new Activities();
             * $config = $wxServer->wxConfigurationInfo($dataArr['activities_id']);
             *
             * if(!$config){
             * throw new Exception('配置有误');
             * }
             *
             * $this->appid     = $config['h5']['appid']; //微信支付申请对应的公众号的APPID
             * $this->appSecret = $config['h5']['secret']; //微信支付申请对应的公众号的APP Key
             **/

            $redirectUrl = urlencode($realUri);
            $urlObj["response_type"] = "code";
            $urlObj["scope"] = "snsapi_userinfo";
            $urlObj["state"] = "STATE";
            $urlObj["appid"] = $this->appid;
            $urlObj["redirect_uri"] = $redirectUrl;


            $bizString = $this->ToUrlParams($urlObj);
            $url = "https://open.weixin.qq.com/connect/oauth2/authorize?" . $bizString;
            return $url;
        } catch (\Throwable $throwable) {
            throw $throwable;
        }
    }


    /**
     * 公众号获取用户信息32423
     * @param string $openid 调用【网页授权获取用户信息】接口获取到用户在该公众号下的Openid
     * @return string
     */
    public function getUserInfo($param = [])
    {
        try {
            //获取code码，进行授权登陆
            $code = $param['code'];
            // 获取网页授权的access_token
            $urlObj["appid"] = $this->appid;
            $urlObj["secret"] = $this->appSecret;
            $urlObj["code"] = $code;
            $urlObj["grant_type"] = "authorization_code";
            $bizString = $this->ToUrlParams($urlObj);
            $url = "https://api.weixin.qq.com/sns/oauth2/access_token?" . $bizString;
            //$res = self::curlGet($url);
            //使用协程请求替代curl
            $res = doCoHttp([
                'url' => $url
            ])['body'];

            if (!empty($res['errcode'])) {
                $msg = "{$res['errcode']}-{$res['errmsg']}";
                throw new Exception($msg);
            }
            $access_token = $res['access_token'];//$res['access_token'];
            $openid = $res['openid'];
            //获取公众号相应的高级接口的access_token
            $access_token1 = $this->getAccessToken(0, 1);

            // 获取用户信息-以$openid更换高级用户信息如服务号关注状态等
            $response = $this->getInfoUser($access_token, $openid, $access_token1);

            if (!empty($response['result1']['errcode'])) {
                if ($response['result1']['errcode'] == 40001) {
                    $access_token1 = $this->getAccessToken(1, 1);
                    //$response['result1'] = self::curlGet('https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $access_token1 . '&openid=' . $openid . '&lang=zh_CN');
                    //使用协程替代curl
                    $response['result1'] = doCoHttp([
                        'url' => 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $access_token1 . '&openid=' . $openid . '&lang=zh_CN'
                    ]);
                }
                if (!empty($response['result1']['errcode'])) {
                    $msg = "{$response['result1']['errcode']}-{$response['result1']['errmsg']}";
                    throw new Exception($msg);
                }
            }
            $newResponse = $response['result'];
            $newResponse['subscribe'] = $response['result1']['subscribe'];
            unset($response);
            return $newResponse;

        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    //用于第一步微信公众号官网token配置
    public function validateWxTonkenConfig($param)
    {
        $signature = $param['signature'];
        $nonce = $param['nonce'];
        $timestamp = $param['timestamp'];
        $token = $param['token'];//Config::get('app')['wxH5config']['token'];
//        (new HhDb())->init()->name('log')->insert(['content' => json_encode([$nonce, $timestamp, $token, $signature])]);
        if ($signature && $timestamp && $nonce) {
            $arr = [$nonce, $timestamp, $token];
            sort($arr, SORT_STRING);

            $tmpstr = implode('', $arr);
            $tmpstr = sha1($tmpstr);
            $arr = [$tmpstr, $signature];
//            (new HhDb())->init()->name('log')->insert(['content' => json_encode($arr)]);
            if ($tmpstr == $signature) {
                return $param['echostr'];
            } else {
                return '验签错误';
            }
        }
    }

    /**
     * @param int $reflash
     * @param int $type 为1是登录服务号  2是订阅号
     * @return mixed|object|string|\think\App
     * @throws Exception
     */
    public function getAccessToken($reflash = 1, $type = 1)
    {
        try {
            if($type == 1){//服务号配置
                $appId = $this->appid;
                $appSecret = $this->appSecret;
            }else{//订阅号配置
                $appId = $this->subscribeJson['appid'];
                $appSecret = $this->subscribeJson['secret']; //订阅号
            }

            $session_AccessToken = $this->getCache_AccessToken(null, $type);

            if (empty($session_AccessToken) || $reflash == 1) {
                //$rs = self::curlGet('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appId . '&secret=' . $appSecret);
                //使用协程替代curl
                $rs = doCoHttp([
                    'url' => 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appId . '&secret=' . $appSecret
                ])['body'];
                if (!empty($rs['access_token'])) {
//                    $session_AccessToken = $rs['access_token'];
                    $session_AccessToken = $this->getCache_AccessToken($rs['access_token'], $type);
                } else {
                    $msg = "{$rs['errcode']}-{$rs['errmsg']}";
                    throw new Exception($msg);
                }
            }

            return $session_AccessToken;
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param string $wxAppId //微信wxappid
     * @param int $type //1服务号 2订阅号
     * @return mixed|object|string|\think\App
     * @throws Exception
     */
    private function getJsapiTicket($type=1)
    {
        $session_Ticket = $this->getCache_Ticket(null, $type);
        if (empty($session_Ticket)) {
            $access_token = $this->getAccessToken(0, $type);
            //$rs = self::curlGet('https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=' . $access_token . '&type=jsapi');
            //使用协程请求替换curl
            $rs = doCoHttp([
                'url' => 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=' . $access_token . '&type=jsapi'
            ])['body'];
            if (!empty($rs['ticket'])) {
                $session_Ticket = $this->getCache_Ticket($rs['ticket'], $type);
            }
        }

        //https://mp.weixin.qq.com/wiki?action=doc&id=mp1421141115&t=0.15947710316920038#62

        return $session_Ticket;
    }

    /**
     * 获取公众号jssdk的api调用时需要的配置信息
     * @param $param
     * @param $type //1服务号2订阅号
     * @return mixed
     * @throws Exception
     */
    public function getJsSdkConfig($param,$type = 1)
    {
        $server = Request::server();
        $rs['noncestr'] = $this->createNonceStr();
        $rs['timestamp'] = time();
        //$url = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
//        $url = 'https://'.$server['HTTP_HOST'];
        $param['url'] = str_replace('amp;', '', $param['url']);

        $arr = [
            'noncestr'     => $rs['noncestr'],
            'timestamp'    => $rs['timestamp'],
            'jsapi_ticket' => $this->getJsapiTicket($type),
            'url'          => $param['url'],//Config::get('app')['domain_name'].'#/'
        ];
        $arr1 = $arr;
        ksort($arr);
        $arr = http_build_query($arr);
        $arr = urldecode($arr);

        $rs['signature'] = sha1($arr);
        $rs['url'] = $arr1['url'];
        $rs['jsapi_ticket'] = $arr1['jsapi_ticket'];
        //判断是订阅号还是服务号
        if ($type==2) {//订阅号
            $rs['appid'] = $this->subscribeJson['appid'];
        } else {//服务号
            $rs['appid'] = $this->appid;
        }

        $rs['ass_ess'] = $this->getAccessToken(0, $type);
        return $rs;
    }

    /**
     * @param null $data
     * @param $type 1为服务号 2为订阅号
     * @return mixed|object|string|\think\App
     */
    private function getCache_AccessToken($data = null, $type=1)
    {

        try {
            if($type ==1){ //服务号

                $wxAppId = $this->appid;
                $key = 'wxfwaccesstokens_' . $wxAppId;
            }else{//订阅号配置
                $wxAppId = $this->subscribeJson['appid'];
                $key = 'wxdyaccesstokens_' . $wxAppId;
            }
            if (!empty($data)) {
                cache($key, strval($data), 5400); //缓存1个半小时
            }

            return cache($key);

        } catch (\TypeError $e) {
            return '';
        }
    }


    /**
     * @param null $data
     * @param int $type //1为服务号 2为订阅号
     * @return mixed|object|string|\think\App
     */
    private function getCache_Ticket($data = null, $type=1)
    {
        try {
            //配置$wxAppId 作为区分
            if ($type==2) {//订阅号
                $wxAppId = $this->subscribeJson['appid'];
            } else {//服务号
                $wxAppId = $this->appid;
            }

            if ($type ==1) {//服务号
                $key = Config::get('app')['domain_name'] . '_wxjstickets_' . $wxAppId;
            } else {//订阅号
                $key = Config::get('app')['domain_name'] . '_wxdyjstickets_' . $wxAppId;
            }
            if (!empty($data)) {
                cache($key, strval($data), 5400); //缓存1个半小时
            }

            return cache($key);

        } catch (\TypeError $e) {
            return '';
        }
    }

    public function getInfoUser($access_token, $openid, $access_token1)
    {
        /*$chan = new \chan(2);
        go(function () use ($chan, $access_token,$openid) {
            $result1 = self::curlGet('https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN');
            $chan->push(['result' => $result1]);
        });

        go(function () use ($chan, $openid,$access_token1) {
            $result2 = self::curlGet('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token1.'&openid='.$openid.'&lang=zh_CN');
            $chan->push(['result1' => $result2]);
        });

        $result = [];
        for ($i = 0; $i < 2; $i++)
        {
            $result += $chan->pop();
        }
        $chan->close();*/

        $obj = Container::getInstance()->make(CoWxPool::class);
        $result = $obj->addTask([
            [
                'key'     => 'result', //获取用户信息
                'data'    => '',
                'callFun' => function () use ($access_token, $openid) {
                    $cli = new \Swoole\Coroutine\Http\Client('api.weixin.qq.com', 443, true);
                    $cli->setMethod("GET");
                    $status = $cli->execute('/sns/userinfo?access_token=' . $access_token . '&openid=' . $openid . '&lang=zh_CN');
                    $rs = $cli->getBody();
                    $rs = json_decode($rs, true);
                    $cli->close();

                    return $rs;
                    //return self::curlGet('https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN');
                }
            ],
            [
                'key'     => 'result1', //获取是否关注
                'data'    => '',
                'callFun' => function () use ($openid, $access_token1) {
                    $cli = new \Swoole\Coroutine\Http\Client('api.weixin.qq.com', 443, true);
                    $cli->setMethod("GET");
                    $status = $cli->execute('/cgi-bin/user/info?access_token=' . $access_token1 . '&openid=' . $openid . '&lang=zh_CN');
                    $rs = $cli->getBody();
                    $rs = json_decode($rs, true);
                    $cli->close();

                    return $rs;
                    //return self::curlGet('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token1.'&openid='.$openid.'&lang=zh_CN');
                }
            ]
        ]);

        //var_dump($result);

        return $result;
    }

    //创建微信菜单
    public function menuCreate($data)
    {
        try {
            $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->getAccessToken(0,1);
            $menu = array();
            $i=0;

            foreach ($data['button'] as $item){
//            dump($data['button']);
//            dump($item);
                $menu['button'][$i]['name'] = $item['name'];
                $menu['button'][$i]['type'] = $item['type'];

                if($item['sub_button']){
                    $j=0;
                    foreach ($item['sub_button'] as $sub){


                        $menu['button'][$i]['sub_button'][$j]['name'] = $sub['name'];
//                    $menu['button'][$i]['sub_button'][$j]['url'] = 'http://www.baidu.com/';
//                    $menu['button'][$i]['sub_button'][$j]['url'] = $sub['url'];

                        $menu['button'][$i]['sub_button'][$j]['type'] =  $sub['type'];
                        $menu['button'][$i]['sub_button'][$j]['url'] = $sub['url'];
                        if(!empty($sub['appid'] && !empty($sub['pagepath']))){
                            $menu['button'][$i]['sub_button'][$j]['appid'] = $sub['appid'];
                            $menu['button'][$i]['sub_button'][$j]['pagepath'] = $sub['pagepath'];
//                        unset($menu['button'][$i]['sub_button'][$j]['url']);
                        }
                        if($sub['type'] == 'media_id'){
                            $menu['button'][$i]['sub_button'][$j]['type'] = 'media_id';
                            $menu['button'][$i]['sub_button'][$j]['name'] = $sub['name'];
                            $menu['button'][$i]['sub_button'][$j]['media_id'] = $sub['media_id'];
                        }
                        $j++;
                    }
                }else{
                    $menu['button'][$i]['type'] = 'view';
                    $menu['button'][$i]['url'] = $item['url'];
                }
                $i++;
            }
            $result = $this->curlPost($url,json_encode($menu,JSON_UNESCAPED_UNICODE));
            $list = [
                $result,$data['button']
            ];
            return $list;
        }catch (Exception $exception){
            return false;
        }

    }

    //删除微信全部菜单
    public function menuDelete($data){
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='.$this->getAccessToken(0,1);
        $result = $this->curlPost($url);
        return $result;
    }

    //获取素材库列表
    public function materialList($data,$count = 10){
        try{
            $url = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token='.$this->getAccessToken(0,1);

            $curlData = [
                'type' => $data['type'],
                'offset' => isset($data['page']) ? (($data['page']-1)*$count):0,
                'count' => $count
            ];

            $result = $this->curlPost($url,json_encode($curlData));
            $pageData = [
                'list'  =>  [],
                'last_page' =>  0,
            ];
            if(isset($result['errcode'])){
                throw new \think\Exception($result['errmsg']);
            }
            $list = [];
            if(isset($result['item'])&&!empty($result['item'])){
                foreach($result['item'] as $v){
                    //news_item 一次发表多篇文章
                    foreach($v['content']['news_item'] as $item){
                        $newsItem = [];
                        $newsItem = $item;
                        $newsItem['create_time'] = date('Y-m-d H:i:s',$v['content']['create_time']);
                        $list[]=$newsItem;
                    }
                }
                $pageData['list'] = $list;
                $pageData['last_page'] = ceil($result['total_count']/$count);
            }
            return $pageData;
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 图文列表
     * @param array $search $search['status'] //线路数据点的状态  $search['category_status']//类别的状态
     * @param int $pagesize
     * @return array
     */
    public function getImageTextList($search = [], $pagesize = 50, $field='*'){
        $where = [];
        if(!empty($search['title'])){
            $where[]=  ['title','like', '%'.$search['title'].'%'];
        }

        if(!in_array($search['status'],['0','1'])){
            unset($search['status']);
        }
        if(isset($search['status'])){//状态
            $where[]=  ['status','=', $search['status']];
        }

        if(!empty($search['order'])){//排序操作
            $order= $search['order'];
        }else{//默认排序
            $order= ['id'=>'desc' ];
        }

        $result = array(
            'list'  =>  [],
            'total' =>  0,
            'last_page' =>  0,
            'current_page'  =>  0
        );

        $list = Db::name('material')
            ->where($where)->order($order)->field($field)->paginate($pagesize);
        $list = $list->toArray();

        if(empty($list)){
            $result['list'] = [];
        }else{
            $result['total'] = $list['total'];
            $result['last_page'] = $list['last_page'];
            $result['current_page'] = $list['current_page'];
            $result['list'] =$list['data'];
        }

        return $result;
    }

    public function getImageTexInfo($id,$field='*'){
        if(empty($id)){
            return $this->responseFail(['code'=>0,'msg'=>'参数缺失']);
        }

        $info= Db::name('material')
            ->where([
                ['id','=',$id]
            ])->field($field)->find();

        if($info['id']){
            return $info;
        }else{
            return false;
        }
    }

    //上传素材文件
    public function uploadWxFile($data){
        try {
            $url = 'https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token='.$this->getAccessToken(0,1);
            $curlData = [
                'media' => $data['file'],
                'type'  => ''
            ];
        }catch (Exception $exception){
            return false;
        }
    }


    //上传素材并获取唯一标识
    public function uploadImg($region_no, $filepath,$type = '1')
    {
        $access_token = $this->getAccessToken(0,1);

        if($type == '1'){
            $url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token=$access_token&type=image";		// 1代表上传临时图片素材
        } else if($type == '2') {
            $url = "https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=$access_token&type=image";		// 2代表上传永久图片素材
        }else if($type  == '3'){
            $url = "https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token=$access_token";  // 3代表上传永久图文返回url
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, 1);
        $data = array('media' => new \CURLFile($filepath));//php5.6

        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);

        if (curl_errno($curl)) {
            return 'Errno' . curl_error($curl);
        }
        curl_close($curl);
        return $result;
    }

    //新增永久素材
    public  function material($region_no, $data, $type = 1)
    {
        $access_token = $this->getAccessToken(0,1);
        if ($type == 1) {
            $url = "https://api.weixin.qq.com/cgi-bin/material/add_news?access_token=$access_token";
        } else if ($type == 2) {
            $url = "https://api.weixin.qq.com/cgi-bin/material/update_news?access_token=$access_token";
        } else if ($type == 3) {
            $url = "https://api.weixin.qq.com/cgi-bin/material/del_material?access_token=$access_token";
        }

        $resultData = $this->curlPost($url,json_encode($data,JSON_UNESCAPED_UNICODE));
//        var_dump($resultData);
        return $resultData;
    }

    /**
     * 群发图文
     * */
    public function sendall($city_no,$data){
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token='.$this->getAccessToken(0,1);
        $result = $this->curlPost($url,json_encode($data,JSON_UNESCAPED_UNICODE));
        return $result;
    }


    /**
     * @param $data
     * @throws Exception
     *$data = [
     *  "touser"      => $vo,
     *  "template_id" => 'Tm8xwzV5Em7GI48FXN2J9WLih8S_Xarz1seXpPaNAaQ',
     *  "url"         => WX_HOST . '/agentside/pages/customer/record_detail.html?id=' . $sendParam['report_id'],
     *  "data"        => [
     *      'first'    => ['value' => '报备通知', 'color' => '#173177'],
     *      'keyword1' => ['value' => $agent['agent_name'], 'color' => '#173177'],
     *      'keyword2' => ['value' => $agent['phone'], 'color' => '#173177'],
     *      'keyword3' => ['value' => $sendParam['customer_name'], 'color' => '#173177'],
     *      'keyword4' => ['value' => $sendParam['customer_phone'], 'color' => '#173177'],
     *      'keyword5' => ['value' => $sendParam['building_name'], 'color' => '#173177'],
     *      'remark'   => ['value' => "状态：{$statusStr}", 'color' => '#173177']
     *    ]
     * ]
     */
    public function sendWxMsgTpl($data){
        $accessToken=$this->getAccessToken();
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$accessToken;
        if(empty($data['touser'])||empty($data['template_id'])||empty($data['url'])||empty($data['data'])){
            return;
        }

        //$result = $this->curlPost($url,json_encode($data,JSON_UNESCAPED_UNICODE));
        $result = doCoHttp([
            'url' => $url,
            'data' => json_encode($data,JSON_UNESCAPED_UNICODE)
        ]);
        $result = json_decode($result, TRUE);

        return $result;
    }
}



