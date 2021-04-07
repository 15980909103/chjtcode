<?php
namespace app\common\lib\smsapi\module;


class SmsJiWei extends SmsBase{
    protected $config = [
        'host'=> 'plate.hbsmservice.com',
        'port'=> '8080',
        'account'=> 'ch0707',
        'password'=> 'jf1333',
        //'smssign'=> '【九房网】', //短信签名
        'business_code'=> '106910692515', //业务代码
    ];

    private $isDifferent = true;
    protected function getCommonData(){
        $transactionId = md5(uniqid(mt_rand(10000, 99999), true));
        $password = md5($this->config['account'].$this->config['password'].$transactionId);
        return [
            'account' => $this->config['account'],
            'password' => $password,
            'transactionId' => $transactionId,
        ];
    }

    /**
     * 是否执行不同内容或者相同内容发送
     * @param bool $is
     */
    public function setDifferent($is=true){
        $this->isDifferent = $is;
    }


    /**
     * 发送验证码
     * @param $mobile
     * @param $code
     * @param null $callFun
     */
    public function sendVerifyCode($mobile='', $code='', $callFun=null){
        $this->sendSms([
            'mobile'=> $mobile,
            'content'=> $code
        ],$callFun);
    }

    /**
     * 发送短信
     * @param array $data ['mobile','content','uuid'唯一标识,'ext'拓展码非必填]
     * @param null $callFun
     * @param array $list 最多200个
     */
    public function sendSms($data=[], $callFun=null){
        $data['uuid'] = $data['uuid']??time().$data['mobile'].str_rand(6);
        $data['content'] = $data['content'];
        $list = [];
        if($this->isDifferent==true){
            $method_url = 'sms/v2/send-different'; //发送不同内容
            array_push($list,$data);
            $postdata = array_merge($this->getCommonData(),[
                'list'=> $list,
            ]);
        }else{
            $method_url = 'sms/v2/send-same'; //发送相同内容的，可用于批量的系统通知
            $content = $data['content'];
            unset($data['content']);
            array_push($list,$data);
            $postdata = array_merge($this->getCommonData(),[
                'content' => $content,
                'list'=> $list,
            ]);
        }

        $this->setHttpHeadrs( ['Content-Type'=> 'application/json'])->doHttp($method_url,$postdata,$callFun);
    }

    /**
     * 群发短信
     * @param array $data ['mobile','content','uuid'唯一标识,'ext'拓展码非必填]
     * @param null $callFun
     * @param array $list 最多200个
     */
    public function sendSmsMore($data=[], $callFun=null){
        try {
            $data['uuid'] = $data['uuid']??time().$data['mobile'].str_rand(6);
            if(!empty($data['list'])) {
                foreach($data['list'] as $k => &$v) {
                    if(!empty($v['mobile'])) {
                        $v['uuid'] = $v['uuid'] ?? time().$v['mobile'].str_rand(6);
                    } else {
                        unset($data['list'][$k]);
                    }
                }
            }
            $list = [];
            if($this->isDifferent==true){
                $method_url = 'sms/v2/send-different'; //发送不同内容
                $list = $data['list'];
                $postdata = array_merge($this->getCommonData(),[
                    'list'=> $list,
                ]);
            }else{
                $method_url = 'sms/v2/send-same'; //发送相同内容的，可用于批量的系统通知
                $content = $data['content'];
                unset($data['content']);
                $list = $data['list'];
                $postdata = array_merge($this->getCommonData(),[
                    'content' => $content,
                    'list'=> $list,
                ]);
            }

            $this->setHttpHeadrs( ['Content-Type'=> 'application/json'])->doHttp($method_url,$postdata,$callFun);
        } catch(\Exception $e) {
            throw $e;
        }
    }

    /**
     * 批量查询发送内容
     * @param null $callFun
     */
    public function selectSend($callFun=null){
        $method_url = 'sms/v2/pull-deliver'; //发送不同内容
        $postdata = array_merge($this->getCommonData(),[
            'count'=> 200,
        ]);

        $this->setHttpHeadrs( ['Content-Type'=> 'application/json'])->doHttp($method_url,$postdata,$callFun);
    }

    /**
     * 批量查询发送的状态报告
     * @param $callFun
     */
    public function selectSendReports($callFun){
        $method_url = 'sms/v2/pull-report'; //发送不同内容
        $postdata = array_merge($this->getCommonData(),[
            'count'=> 200,
        ]);

        $this->setHttpHeadrs( ['Content-Type'=> 'application/json'])->doHttp($method_url,$postdata,$callFun);
    }

    /**
     * 查询账户余额
     * @param null $callFun
     */
    public function selectBalance($callFun=null){
        $method_url = 'sms/v2/user-balance'; //发送不同内容
        $postdata = array_merge($this->getCommonData());

        $this->setHttpHeadrs(['Content-Type'=> 'application/json'])->doHttp($method_url,$postdata,$callFun);
    }
}
