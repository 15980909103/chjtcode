<?php


namespace app\server\user;
use app\common\base\ServerBase;
use app\common\lib\smsapi\SmsServer;
use app\common\MyConst;
use think\validate\ValidateRule;

class ShortMessage extends ServerBase
{
    /**
     * 发送短信
     */
    public function sendMsg($data){

        $mobile        = $data['phone'];

        // 场景
        $sence = $data['sence'] ?? 'login';
        $sencekey = $this->getSenceKey($sence);
        if(!$sencekey) {
            return $this->responseFail('未知场景');
        }

        if(!$this->isMobile($mobile)){
           return $this->responseFail('请填写正确的手机号码');
        }
        $redis          = $this->getReids();

        $limitReids      = $redis->get('limitmsg'.$mobile);
        $limitDayReids      = $redis->get('limitmsg_day'.$mobile);
        if($limitReids){
            if(empty($data['is_limit'])) {
                return  $this->responseFail('1分钟内只能获取一次');
            }
        }
        if($limitDayReids>6){
            return $this->responseFail('超过次数上限');
        }

        $factoryServer = new SmsServer();
        $code          = rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
        $msgServer     = $factoryServer->make('jiwei');
        $db            = $this->db;

        $msgServer->sendVerifyCode($mobile,$code,function($res) use($code,$mobile,$db,$redis,$sencekey){
                       if($res['status']){
                           $log  = [
                             'code'     => $code,
                             'mobile'   => $mobile,
                             'status'   => 1,
                              'ip'      =>get_client_ip(),
                             'transaction_id' => $res['transactionId'] ?? '',
                              'create_time'   => time(),
                              'update_time'   => time(),

                           ];
                           $db->name('msg_log')->insert($log);
                           //现在一分钟发一次

                           $key         = 'limitmsg'.$mobile;

                           $redis->set($key,$code);
                           $redis->expire($key,60);
                           
                           $keyCode = $sencekey.$mobile;
                           $redis->set($keyCode,$code);
                           $redis->expire($keyCode,3*60);

                           //每天限制
                           $keyDay      = 'limitmsg_day'.$mobile;
                           $redis->incr($keyDay,1);
                           $redis->expireAt($keyDay,strtotime(date('Y-m-d 23:59:59',time())));


                       }


        });
      return $this->responseOk('验证码发送成功');
    }

    /**
     * 发送短信接口-分销调用
     */
    public function sendMsgApi($data)
    {
        try {
           // 允许访问的IP
            $ipAllow = ['127.0.0.1', '47.107.72.79'];

            $ip = get_client_ip();
            if(!in_array($ip, $ipAllow)) {
                return $this->responseFail('禁止访问');
            }
            if(!empty($data['list'])) {
                foreach($data['list'] as $k => $v) {
                    if(!$this->isMobile($v['mobile'])) {
                        unset($data['list'][$k]);
                    }
                }
            }
            $factoryServer = new SmsServer();
            $msgServer     = $factoryServer->make('jiwei');
            $msgServer->setDifferent(false);
            $msgServer->sendSmsMore($data); 
        } catch(\Exception $e) {
            $this->db->name('log')->insert(
                [
                    'content' => $e->getMessage(),
                    'source' => '分销调用短信',
                    'create_time' => time(),
                ]
            );
        }
    }

    private function isMobile($mobile) {
        if(preg_match('/^1\d{10}$/', $mobile))
            return true;
        return false;
    }

    public function checkCode($data){
        $mobile  = $data['mobile'];
        $code    = $data['code'];
        if(!$code  || !$mobile){
            return false;
        }

        if(!$this->isMobile($mobile) ){
            return false;
        }

        // 场景
        $sence = $data['sence'] ?? '';
        $sencekey = $this->getSenceKey($sence);
        if(!$sencekey) {
            return $this->responseFail('未知场景');
        }

        $redis      = $this->getReids();
        $codeReids      = $redis->get($sencekey.$mobile);

        if($codeReids  == $code){
            $this->db->name('msg_log')
                    ->where('mobile','=',$code)
                    ->where('code','=',$code)
                    ->update(['status'=>2]);
            return  true;
        }

        return  false;
    }

    /**
     * 根据不同场景获取不同的KEY
     */
    protected function getSenceKey($sence = '')
    {
        switch($sence) {
            // 报名
            case 'sign_up':
                $sencekey = MyConst::MSG_CODE_SIGN;
                break;
            // 登陆
            case 'login':
                $sencekey = MyConst::MSG_CODE_LOGIN;
                break;
            default:
                return false;
                break;
        }
        return $sencekey;
    }

}