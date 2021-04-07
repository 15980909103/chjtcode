<?php


namespace app\miniwechat\controller;


use app\common\base\UserBaseController;
use think\facade\Config;

class TestController extends UserBaseController
{

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
            'content'    => json_encode([$city_code]),
            'created_at' => time(),
            'source'     => 'subscribe'
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
//                    $wx_data['subscribe'] = 1;
//                    $this->saveinfo($get['mchid_id'],$wx_data,$get['act_id']);

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
                'content'    => json_encode([$info, $user_info]),
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
                ];
                $this->db->name('user_association')->insert($arr);

                $this->db->name('log')->insert([
                    'content'    => json_encode($this->db->getLastSql()),
                    'created_at' => time(),
                    'source'     => '数据写入'
                ]);
            }

        } else {

            if (empty($user_info['unionid']) || empty($data['subscribe'])) {
                $arr['subscribe'] = $data['subscribe'];
            } else {
                $arr = [
                    'openid'          => $data['FromUserName'],
                    'create_time'     => time(),
                    'update_time'     => time(),
                    'unionid'         => $user_info['unionid'],
                    'bind_wx_city_no' => $city_code,
                ];
            }

            $where[] = ['openid', '=', $data['FromUserName']];
            $where[] = ['bind_wx_city_no', '=', $city_code];

            if (!empty($user_info['unionid'])) {
                $where[] = ['unionid', '=', $user_info['unionid']];
            }
            /*$this->db->name('log')->insert([
                    'content'       => json_encode([$arr,$where]),
                    'created_at'   => time(),
                    'source'=> 'subscribe'
                ]
            );*/
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
     * @return mixed|object|string|\think\App
     */
    protected function getAccessToken($info, $cityCodeId)
    {
        try {

            $session_AccessToken = $this->getCache_AccessToken(null, $cityCodeId);

            if (empty($session_AccessToken)) {
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
}