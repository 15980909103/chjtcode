<?php


namespace app\index\controller;


use app\common\base\UserBaseController;
use app\common\manage\TaskManage;
use app\common\pool\RedisPool;
use Co\Channel;
use think\facade\Config;

class TestController extends UserBaseController
{

    protected $access_token = null;

    //33453333
    public function index()
    {
        if (!empty($this->request->get('echostr'))) {
            echo $this->request->get('echostr');
            return;
        }

        $get = $this->request->get();
        $wx_data = $this->request->getInput();
        $wx_data = simplexml_load_string($wx_data, 'SimpleXMLElement', LIBXML_NOCDATA);

        $get = json_encode([$wx_data, $get]);

        $this->db->name('log')->insert([
                'content'    => $get,
                'created_at' => time(),
                'source'     => 'subscribe'
            ]
        );

//            $get   = '[{"ToUserName":"gh_0b9b6663ce00","FromUserName":"ocwmgt1jrzjQLdCyjmGBz_p_Igb0","CreateTime":"1599242988","MsgType":"event","Event":"unsubscribe","EventKey":{}},{"mchid_id":"6","act_id":"26","signature":"bb2bf86b9e9bdb24cfab536359705289490ee4c3","timestamp":"1599242988","nonce":"1280712375","openid":"ocwmgt1jrzjQLdCyjmGBz_p_Igb0"}]';
        $get = json_decode($get, true);

        $wx_data = $get[0];
        $get = $get[1];
        $this->db->name('log')->insert([
            'content'    => json_encode($get['city_code']),
            'created_at' => time(),
            'source'     => 'subscribe'
        ]);
        $city_code = $get['city_code'];

        $config = $this->getconfig($city_code);

        $this->db->name('log')->insert([
            'content'    => json_encode([$config,$city_code]),
            'created_at' => time(),
            'source'     => 'subscribe.$config'
        ]);


        switch ($wx_data['Event']) {
            case 'subscribe' :
                $wx_data['subscribe'] = 1;
                $this->saveinfo($city_code, $wx_data);
                if ($config) {
                    if ($config['follow_type'] == 1) {
                        $this->send($wx_data['FromUserName'], $wx_data['ToUserName'], time(),
                            'text', $config['follow_content']);//回复微信信息
                    } else {

                        $this->sendImg($wx_data['FromUserName'], $wx_data['ToUserName'], time(), $config);//回复微信信息
                    }


                }
                break;
            case 'unsubscribe' :
                $wx_data['subscribe'] = 0;
                $this->saveinfo($city_code, $wx_data);
                break;
            case 'SCAN' :
                $wx_data['subscribe'] = $wx_data['Event'] == 'subscribe' ? 1 : 0;
                $this->saveinfo($city_code, $wx_data);
                break;
            case 'VIEW' :
                $wx_data['subscribe'] = 1;
                $this->saveinfo($city_code, $wx_data);
                break;
            case 'text' :
                $wx_data['subscribe'] = 1;
                $this->saveinfo($get['mchid_id'], $wx_data, $get['act_id']);

                break;
        }
        if (!empty($wx_data['MsgType']) && $wx_data['MsgType'] == 'text' && !empty($config)) {
            if (mb_stripos($wx_data['Content'], $config['lookup_text']) !== false) {
                if ($config['follow_type'] == 1) {
                    $this->send($wx_data['FromUserName'], $wx_data['ToUserName'], time(), 'text', $config['lookup_content']);//回复微信信息
                } else {

                    $this->sendImg($wx_data['FromUserName'], $wx_data['ToUserName'], time(), $config);//回复微信信息
                }

            } else {
                $this->send($wx_data['FromUserName'], $wx_data['ToUserName'], time(), 'text', 'success');//回复微信信息
            }

        }

        echo '';


    }

    private function getconfig($cityCode)
    {
        $confog = $this->db->name('site_city_set')
            ->where('region_no', '=', $cityCode)
            ->where('key', '=', 'wxh5')
            ->field('val')
            ->find();

        if ($confog['val']) {
            $confog = json_decode($confog['val'], true);
            if (!empty($confog['reply_setting'])) {
                return $confog;
            }
        }

        return [];
    }

    /**
     * 公众号图文回复
     */
    public function sendImg($fromUsername, $toUsername, $time, $config)
    {
        $xml = '<xml>
              <ToUserName><![CDATA[%s]]></ToUserName>
              <FromUserName><![CDATA[%s]]></FromUserName>
              <CreateTime>%s</CreateTime>
              <MsgType><![CDATA[news]]></MsgType>
              <ArticleCount>2</ArticleCount>
              <Articles>
                <item>
                  <Title><![CDATA[%s]]></Title>
                  <Description><![CDATA[%s]]></Description>
                  <PicUrl><![CDATA[%s]]></PicUrl>
                  <Url><![CDATA[%s]]></Url>
                </item>
                
                 <item>
                  <Title><![CDATA[详情]]></Title>
                  <Description><![CDATA[    ]]></Description>
                  <PicUrl><![CDATA[    ]]></PicUrl>
                  <Url><![CDATA[%s]]></Url>
                </item> 
              </Articles>
            </xml>';
        $config['PicUrl'] = Config::get('app.domain_name') . '/' . $config['PicUrl'];
        $resultStrq = sprintf($xml, $fromUsername, $toUsername, $time, $config['Title'], $config['description'], $config['PicUrl'], $config['Url'], $config['Url']);
        $this->db->name('log')->insert([
                'content'    => $resultStrq,
                'created_at' => time(),
                'source'     => 'subscribe'
            ]
        );
        echo trim_all($resultStrq);


    }


    /**
     * 回复公众号消息
     */
    public function send($fromUsername, $toUsername, $time, $msgtype, $contentStrq)
    {
        $xml = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            </xml>";


        $resultStrq = sprintf($xml, $fromUsername, $toUsername, $time, $msgtype, $contentStrq);
        echo trim_all($resultStrq);
    }


    /**
     * 存储数据
     * @param $city_code 城市编码
     * @param $data  数据
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function saveinfo($city_code, $data)
    {
//        $city_code = '350200';
//        $data['FromUserName'] = 'oWKGc1bPjWfaS0iOXfJGVHGZLtmQ';
        $info = $this->db->name('user_association')->where('bind_wx_city_no', '=', $city_code)
            ->where('openid', '=', $data['FromUserName'])
            ->find();

        $user_info = $this->getuserinfo($data['FromUserName'], $city_code);

        $this->db->name('log')->insert([
                'content'    => json_encode([$user_info, $city_code, $info]),
                'created_at' => time(),
                'source'     => '获取用户'
            ]
        );


        if (empty($info)) {

            if (!empty($user_info['unionid'])) {

                $arr = [
                    'bind_wx_city_no' => $city_code,
                    'openid'          => $data['FromUserName'],
                    'unionid'         => $user_info['unionid'],
                    'create_time'     => time(),
                    'update_time'     => time(),
                    'subscribe'       => 1
                ];
                $this->db->name('log')->insert([
                    'content'    => json_encode($arr),
                    'created_at' => time(),
                    'source'     => '数据写入11'
                ]);
                $this->db->name('user_association')->insert($arr);

                $this->db->name('log')->insert([
                    'content'    => ($this->db->getLastSql()),
                    'created_at' => time(),
                    'source'     => '数据写入'
                ]);
            }

        } else {
            $this->db->name('log')->insert([
                    'content'    => json_encode([$user_info, $data]),
                    'created_at' => time(),
                    'source'     => 'mimimi'
                ]
            );

            if (empty($user_info['unionid'])) {
                $arr['subscribe'] = 0;
            } else {
                $arr = [
                    'openid'          => $data['FromUserName'],
                    'create_time'     => time(),
                    'update_time'     => time(),
                    'unionid'         => $user_info['unionid'],
                    'bind_wx_city_no' => $city_code,
                    'subscribe'       => $data['subscribe'],
                ];
            }

            $where[] = ['openid', '=', $data['FromUserName']];
            $where[] = ['bind_wx_city_no', '=', $city_code];

            if (!empty($user_info['unionid'])) {
                $where[] = ['unionid', '=', $user_info['unionid']];
            }

            $this->db->name('user_association')->where($where)->update($arr);
        }

        //更新完后合并
        $where = [
            ['ua.openid', '=', $data['FromUserName']],
            ['ua.city_no', '=', $city_code]

        ];
        $userInfo = $this->db->name('user_association')->alias('ua')
            ->join('user u', 'u.unionid = ua.unionid')
            ->where($where)->field('u.id')->find();

        $this->db->name('user_association')->alias('ua')
            ->where($where)->update([
                'user_id'     => $userInfo['id'],
                'update_time' => time()
            ]);


        //   $this->setUserInfo($mch_id, $user_info);

    }


    public function getuserinfo($open_id, $city_code)
    {
        $type = 'wxh5';

        $info = $this->db->name('site_city_set')->where('region_no', $city_code)->where('key', $type)->field('val')->find();

        if (empty($info['val'])) {
            return false;
        }
        $info['val'] = json_decode($info['val'], true);

        $access_token = $this->getAccessToken($info['val'], $city_code);

        $this->db->name('log')->insert([
                'content'    => json_encode($access_token),
                'created_at' => time(),
                'source'     => 'lyaccesstoken'
            ]
        );

        $cli = new \Swoole\Coroutine\Http\Client('api.weixin.qq.com', 443, true);

        $cli->setMethod("GET");
        $status = $cli->execute('/cgi-bin/user/info?access_token=' . $access_token . '&openid=' . $open_id . '&lang=zh_CN');
        $rs = $cli->getBody();
        $this->db->name('log')->insert([
                'content'    => json_encode($rs),
                'created_at' => time(),
            ]
        );

        $rs = json_decode($rs, true);
        $cli->close();

        return $rs;

    }

    /**
     * @param $info
     * @param $cityCodeId
     * @param $compulsory
     * @return mixed|object|string|\think\App
     */
    protected function getAccessToken($info, $cityCodeId, $compulsory = 0)
    {
        try {

            $session_AccessToken = $this->getCache_AccessToken(null, $cityCodeId);

            $this->db->name('log')->insert([
                    'content'    => json_encode([$session_AccessToken, $cityCodeId]),
                    'created_at' => time(),
                    'source'     => 'getAccessToken'
                ]
            );

            if (empty($session_AccessToken) || !empty($compulsory)) {
                //订阅号
                $appid = $info['appid'];
                $appSecret = $info['secret'];
                $rs = self::curlGet('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $appSecret);

                if (!empty($rs['access_token'])) {
//                    $session_AccessToken = $rs['access_token'];
                    $session_AccessToken = $this->getCache_AccessToken($rs['access_token'], $cityCodeId);

                } else {
                    $msg = "{$rs['errcode']}-{$rs['errmsg']}";
                    throw new Exception($msg);
                }
            }

            return $session_AccessToken;
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param null $data
     * @param $cityCodeId
     * @return mixed|object|string|\think\App
     */
    private function getCache_AccessToken($data = null, $cityCodeId)
    {
        try {
            if (!empty($data)) {
                cache('wxdyaccesstokens_' . $cityCodeId, strval($data), 5400); //缓存1个半小时
            }

            return cache('wxdyaccesstokens_' . $cityCodeId);
        } catch (\TypeError $e) {
            return '';
        }
    }

    public function setUserInfo($mch_id, $user_info_wx)
    {
        if (empty($user_info_wx['unionid'])) {
            return;
        }
        $usetTable = $this->db->setPartition(['merchant_id' => $mch_id])
            ->name('user')
            ->where('unionid', '=', $user_info_wx['unionid'])
            ->find();

        if (empty($usetTable)) {
            $arr['merch_id'] = $mch_id;
            $arr['nickname'] = $user_info_wx['nickname'];
            $arr['unionid'] = $user_info_wx['unionid'];
            $arr['headimgurl'] = $user_info_wx['headimgurl'];
            $arr['sex'] = $user_info_wx['sex'];
            $arr['language'] = $user_info_wx['language'];
            $arr['country'] = $user_info_wx['country'];
            $arr['province'] = $user_info_wx['province'];
            $arr['city'] = $user_info_wx['city'];
            $arr['created_at'] = time();
            $arr['updated_at'] = time();
            $this->db->setPartition(['merchant_id' => $mch_id])->name('user')->insert($arr);
        }

    }

    public static function curlGet($url = '', $options = array())
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }
        //https请求 不验证证书和host
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);

        return json_decode($data, true);
    }

    public static function curlPost($url = '', $postData = '', $options = array())
    {
        if (is_array($postData)) {
            $postData = http_build_query($postData);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //设置cURL允许执行的最长秒数
        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }

//        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); //强制协议为1.0
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect: ')); //头部要送出'Expect: '
//        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); //强制使用IPV4协议解析域名

        //https请求 不验证证书和host
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);
        $jsonData = json_decode($data, true);
        if (is_null($jsonData)) {
            return $data;
        } else {
            return $jsonData;
        }
    }

    public function getOpenAll()
    {

        $param = $this->request->param();
        $type = 'wxh5';
        $redis = $this->getReids();
        $openInfo = $redis->hGet('user_open_id1', $param['city_code']);
        $openInfo = empty($openInfo) ? [] : json_decode($openInfo, true);
        $end = 'ogQRvt5uee6x_8dz8vC3SQychrKY';

        $info = $this->db->name('site_city_set')
            ->where('region_no', $param['city_code'])
            ->where('key', $type)->field('val')->find();

        if (empty($info['val'])) {
            return false;
        }

        $info['val'] = json_decode($info['val'], true);

        $access_token = $this->getAccessToken($info['val'], $param['city_code'],1);


        $url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token=' . $access_token;
        if (!empty($end)) {
            $url = $url . '&next_openid=' . $end;
        }
        $res = self::curlGet($url);

        if (!empty($res['data'])) {
            $userData = [];
            foreach ($res['data']['openid'] as $value) {
//            $redis->lPush('user_open_list',json_encode(['openid' => $value, 'lang' => 'zh-CN']));
                $userData[] = ['openid' => $value, 'lang' => 'zh-CN'];
                $end = $value;
            }

            //合并存入redis
            $openInfo = array_merge($openInfo, $userData);
            $redis->hSet('user_open_id1', $param['city_code'], json_encode($openInfo));

        } else {
            return 'ok';
        }

        return $end;
    }

    public function taskRun()
    {
        $param['city_code'] = '350200';
        $redis = $this->getReids();
        $openInfo = $redis->hGet('user_open_id1', $param['city_code']);

        $openInfo = empty($openInfo) ? [] : json_decode($openInfo, true);
var_dump(count($openInfo));
        return '121';
        foreach ($openInfo as $key => $value){

            if($value['openid'] == 'ogQRvt9EwbHwAE5kAkmkdXbXWhpg'){
                break;
            }
            unset($openInfo[$key]);
        }
        return count($openInfo);
    }

    public function goRun()
    {

            go(function () {
                $userUrl = 'http://jiufang.test.com/index/test/insertRedis';

                $rs = doCoHttp([
                    'url' => $userUrl,
                ])['body'];
            });


    }

    //获取用户数据存入redis
    public function insertRedis()
    {
        $param['city_code'] = '350200';
        $redis = $this->getReids();
        $openInfo = $redis->hGet('user_open_id1', $param['city_code']);

        $openInfo = empty($openInfo) ? [] : json_decode($openInfo, true);


//        $userInfoData = $redis->hGet('user_info_data', $param['city_code']);
//        $userInfoData = empty($userInfoData) ? [] : json_decode($userInfoData, true);


        $info = $this->db->name('site_city_set')
            ->where('region_no', $param['city_code'])
            ->where('key', 'wxh5')->field('val')->find();

        if (empty($info['val'])) {
            return false;
        }
        $info['val'] = json_decode($info['val'], true);

        $this->access_token = $this->getAccessToken($info['val'], $param['city_code'],1);


        $openIdArray = array_chunk($openInfo, 100);
        $i = 0;


        $redis = RedisPool::getInstance();
        $config = [
            'database'   => 2,
            'max_active' => 10
        ];
        $config = $redis->setConfig('swoole.pool.redis', $config);
        $redis = $redis->init($config);

        foreach ($openIdArray as $key => $value) {
            $userUrl = 'https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=' . $this->access_token;
            $u['user_list'] = $value;
            $i++;

            $chan = new Channel(1);
            go(function () use ($userUrl, $u, $param, $i, $redis, &$chan) {
                $rs = doCoHttp([
                    'url'  => $userUrl,
                    'data' => json_encode($u)
                ])['body'];
                $chan->push($rs);
                print_r($rs);
            });

            $rs = $chan->pop();
            $insertData = [];
            foreach ($rs['user_info_list'] as $key => $userValue) {

//                $redis->lPush('user_info_data', json_encode());
              $insertData[] =  [
                    'bind_wx_city_no' => $param['city_code'],
                    'openid'          => $userValue['openid'],
                    'unionid'         => $userValue['unionid'],
                    'subscribe'       => $userValue['subscribe'],
                    'device_type'     => 'wxh5',
                    'create_time'     => time(),
                    'update_time'     => time(),
                ];

            }
            $this->db->name('user_associationes')->insertAll($insertData);
//            $userInfoData = array_merge($userInfoData,$insertData);

        }

//        $redis->hSet('user_info_data',$param['city_code'],json_encode($userInfoData));

        return 'ok';
    }


    //请求获取open
    public function getUserOpenList($res, $info, $param, $access_token, $end = null)
    {
        try {
            //取redis
            $redis = $this->getReids();
            $openInfo = $redis->hGet('user_open_id', $param['city_code']);
            $openInfo = empty($info) ? [] : json_decode($openInfo, true);

            file_put_contents('openid.txt', json_encode($openInfo) . PHP_EOL, FILE_APPEND);
            $url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token=' . $access_token;

            if ($res['errcode'] == '40001') { //如果等于4001再次请求一遍
                $access_token = $this->getAccessToken($info['val'], $param['city_code'], 1);
                return $this->getUserOpenList($info, $param, $access_token);
            }


            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }


    public function insertUserInfo($param, $access_token, $end = null)
    {

        //初始
        $url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token=' . $access_token;
        if (!empty($end)) {
            $url = $url . '&next_openid=' . $end;
        }
        $res = self::curlGet($url);

        if (!empty($res['data'])) {

            $openIdArray = array_chunk($res['data']['openid'], 100);
            foreach ($openIdArray as $value) {
                $userData = [];
                foreach ($value as $v) {
                    $userData[] = ['openid' => $v, 'lang' => 'zh-CN'];
                    $end = $v;
                }

                //获取用户信息 - 插入
                $userUrl = 'https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=' . $access_token;
                $u['user_list'] = $userData;
                $rs = self::curlPost($userUrl, json_encode($u));

                $insertData = [];
                foreach ($rs['user_info_list'] as $key => $userValue) {
                    $insertData[] = [
                        'bind_wx_city_no' => $param['city_code'],
                        'openid'          => $userValue['openid'],
                        'unionid'         => $userValue['unionid'],
                        'subscribe'       => $userValue['subscribe'],
                        'device_type'     => 'wxh5',
                        'create_time'     => time(),
                        'update_time'     => time(),
                    ];
                }

                if (!empty($insertData)) {
                    try {
                        $this->db->name('user_associationes')->insertAll($insertData);
                    } catch (\Exception $exception) {
                        return $exception->getMessage();
                    }
                }
            }

            file_put_contents('openid3.txt', '测我是' . PHP_EOL, FILE_APPEND);
            if (!empty($end)) {
                return $this->insertUserInfo($param, $access_token, $end);
            }
        } else {
            return true;
        }

        return true;

    }
}