<?php

namespace app\index\controller;

use app\common\base\UserBaseController;
use app\server\marketing\Signup;
use app\server\user\ShortMessage;

class SignUpController extends UserBaseController
{


    /**
     * 报名
     */
    public function add()
    {
        $params = $this->request->param();
        $params['sign_id'] = intval($params['sign_id']);

        if (empty($params['sign_id']) || empty($params['mobile'])) {
            $this->error('缺少必要参数');
        }

        if (!preg_match('/^1\d{10}$/', $params['mobile'])) {
            $this->error('手机格式不正确');
        }
        $browser_cache = intval($params['type']) ? 1 : 0;//$params['type']无值的时候需要验证码校验


        $userId = $this->userId ?? 0;

        $data = [
            'signup_id'     => $params['sign_id'],
            'active_id'     => $params['active_id'],
            'user_id'       => $userId,
            'module'        => 'estates_new',
            'name'          => $params['name'] ?? '',
            'phone'         => $params['mobile'],
            'code'          => $params['code'],
            'source'        => $params['source'] ?? 'wx_h5',
            'browser_cache' => $browser_cache,//$browser_cache 1的时候需要验证码校验0不需要直接数据库判断
        ];

        $res = (new Signup())->addLog($data);

        if (empty($res['code']) || 1 != $res['code']) {
            $this->error($res);
        }

        $this->success([],'报名成功');
    }

    public function discountRegistration()
    {
        $params = $this->request->param();
        $params['sign_id'] = intval($params['sign_id']); //活动id

        if (empty($params['sign_id']) || empty($params['mobile'])) {
            $this->error('缺少必要参数');
        }

        if (!preg_match('/^1\d{10}$/', $params['mobile'])) {
            $this->error('手机格式不正确');
        }
        $browser_cache = intval($params['type']) ? 1 : 0;//$params['type']无值的时候需要验证码校验

//        $userId = $this->userId ?? 0;
        $userId = $this->getUserId(false);


        $data = [
            'signup_id'     => $params['sign_id'],
            'active_id'     => $params['sign_id'],
            'user_id'       => $userId ?? 0,
            'module'        => 'discount',
            'name'          => $params['name'] ?? '',
            'phone'         => $params['mobile'],
            'code'          => $params['code'],
            'source'        => $params['source'] ?? 'wx_h5',
            'browser_cache' => $browser_cache,//$browser_cache 1的时候需要验证码校验0不需要直接数据库判断
        ];

        $res = (new Signup())->discountRegistration($data);

        if (empty($res['code']) || 1 != $res['code']) {
            $this->error($res);
        }

        $this->success([],'报名成功');
    }

}