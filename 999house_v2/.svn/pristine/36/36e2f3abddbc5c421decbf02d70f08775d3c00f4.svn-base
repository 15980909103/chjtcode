<?php


namespace app\index\controller;


use app\common\base\UserBaseController;
use app\common\MyConst;
use app\server\admin\WorkWriteOff;

class WorkWriteOffController extends UserBaseController
{ # 核销Controller

    //核销人员列表
    public function staffMember()
    {
        $param = $this->request->param();
        $userId = $this->userId; //用户
        $res = (new WorkWriteOff())->staffMember($param,$userId);
        if(!empty($res['result'])){
            $res['result']['coupon_number'] =  $res['result']['coupon_surplus_num'] ?? 0;
            $res['result']['sum'] =  $res['result']['coupon_send_unm'] ?? 0;
        }

        if ($res['code'] != 1) {
            return $this->error($res['msg']);
        }
        return $this->success($res['result']);

    }

    //列表
    public function list()
    {
        $params = $this->request->param();
        $params['user_id'] = $this->userId; //用户
        $params['page'] = empty($params['page']) ? 1 : $params['page'];
        $params['page_size'] = empty($params['page_size']) ? 10 : $params['page_size'];

        $res = (new WorkWriteOff())->list($params);
        if ($res['code'] != 1) {
            return $this->error($res['msg']);
        }
        return $this->success($res['result']);
    }

    public function info(){
        $params = $this->request->param();
        $data = $this->decrypt($params['code']);
        $info = (new WorkWriteOff())->info($data);

        if ($info['code'] != 1) {
            return $this->error($info['msg']);
        }
        return $this->success($info['result']);

    }

    //审核
    public function review()
    {
        $params = $this->request->param();
        $data = $this->decrypt($params['code']);
        $data['staff_member_id'] = $this->userId;
        $res = (new WorkWriteOff())->review($data);
        if ($res['code'] != 1) {
            return $this->error($res['msg']);
        }
        return $this->success($res['result']);
    }

    //解码
    public function decrypt($code){
//        $code = 'GOuGavLovOKe7VWJUAZX+HWMvkBNOnb8nvTN+ENYr6hjPJOmG2K4CfcVOk3Y6heM';
        $aes_key  = MyConst::COUPON_ACTIVITY;
        $aes_met  = 'AES-128-CBC';
        $url      = openssl_decrypt($code,$aes_met,$aes_key,0);
        $url = explode('&',$url);
        foreach ($url as $value){
            $array = explode('=',$value);
            $data[$array[0]] = $array[1];
        }

        return $data;
    }
}