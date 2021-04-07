<?php
namespace app\common\lib;

use app\common\traits\TraitAsyncHttp;
use think\facade\Db;

/**
 * 调用备注
 * $server = new MsgRobot
 * $server->sendRobotMsg($param)
 * 参数
 * msg string 要推送的消息
 * phone array|string  推送人手机，不传默认全推送
 */
class MsgRobot {

    use TraitAsyncHttp;
    
    private $config = [
        'host' => "oapi.dingtalk.com",// 机器人消息接口
        'path' => "robot/send?access_token=09190ac7939cf43e1906678c474f756aaa045cf943a555b6aa4bd3cd0981b7f6",// 方法
        'secret' => "SECcc3aa6a1b1567d56c2bfbce6c782a2d5fd01472b82e073fd2657c9cddd76231d",// 秘钥
        'isHttps' => true,
    ];

    public function sendRobotMsg($param)
    {
        $msg = $param['msg'] ?? "未定义消息";
        // 推送人判断
        $isAtAll = TRUE;// 默认全推送
        if(!empty($param['phone'])) {// 定义了推送目标
            $isAtAll = FALSE;
            if(is_array($param['phone'])) {
                $phone = $param['phone'];
            } else {
                $phone[] = $param['phone'];
            }
        }
        // 加签
        $secret = $this->config['secret'];
        $time = time() *1000;
        $sign = hash_hmac('sha256', $time . "\n" . $secret, $secret, true);
        $sign = base64_encode($sign);
        $sign = urlencode($sign);
        // 消息
        $data = [
            'msgtype' => 'text',
            'text' => [
                'content' => $msg,
            ],
            'at' => [
                'atMobiles' => $phone,
                'isAtAll' => $isAtAll,
            ],
        ];
        $path = "{$this->config['path']}&timestamp={$time}&sign={$sign}";
        // 推送
        $this->setHttpHeadrs( ['Content-Type'=> 'application/json']);
        $this->setHttpType('POST');
        $this->doHttp($path, $data, function($res) {// 回调记录错误
            $body = $res['body'];
            $result = json_decode($body, TRUE);
            // 错误记录
            if(!empty($result['errcode'])) {
                $content = "钉钉报警信息推送异常：{$result['errcode']}-{$result['errmsg']}";
                $logData = [
                    'content' => $content,
                    'created_at' => time(),
                ];
                Db::name('log')->insert($logData);
            }
        });
    }

}