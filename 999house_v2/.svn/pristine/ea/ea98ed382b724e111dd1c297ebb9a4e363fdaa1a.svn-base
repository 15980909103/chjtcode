<?php
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------

namespace app\server\admin;

use app\common\traits\TraitInstance;
use app\common\base\ServerBase;
use think\Exception;

/*
 * 后台用户操作
 * */
class Admin extends ServerBase
{

    /**
     * 添加管理员
     * @param $data
     * @return array
     */
    public function userAdd($data){
        try{

            if(!empty($data['account'])){
                $has=$this->db->name('admin')->where(['account'=>$data['account']])->value('id');
            }
            if(!empty($has)){
                throw new Exception('该账号已经存在');
            }

            $data['salt'] = str_rand(6);
            $data['password'] = password_encrypt($data['password'],$data['salt']);
            $data['create_time'] = time();

            $user_id=$this->db->name('admin')->insertGetId([
                'account'=> $data['account'],
                'email' =>  $data['email']?$data['email']:'',
                'mobile' => $data['mobile']?$data['mobile']:'',
                'salt'   => $data['salt'],
                'password' => $data['password'],
                'role_id' => $data['role_id']?$data['role_id']:'',
                'create_time' => time(),
                'status'=> $data['status'],
                'user_id'=> $data['user_id'] ??  0,
                'estates_agent_id'=> $data['estates_agent_id'] ?? 0,
                'region_nos_info'=> $data['region_nos_info'],
            ]);

            if(empty($user_id)){
                throw new Exception('操作失败');
            }

            return $this->responseOk($user_id);
        }catch (Exception $e){

            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    /**
     * 修改管理员
     * @param $admin_id
     * @param $data
     * @throws Exception
     */
    public function userEdit($admin_id,$data){
        try{
            if(!is_int($admin_id)){
                throw new Exception('参数错误');
            }
            if(isset($data['status'])&&!in_array($data['status'],['0','1'])){//是否启用账号
                throw new Exception('参数错误');
            }

            if(!empty($data['id'])){
                unset($data['id']); //不变更id
            }
            if(!empty($data['account'])){
                unset($data['account']);//账号不可修改
            }

            if(!empty($data['password'])){ //密码有设置时候
                $data['salt'] = str_rand(6);
                $data['password'] = password_encrypt($data['password'],$data['salt']);
            }else{
                unset($data['password']);
                unset($data['salt']);
            }

            $rs= $this->db->name('admin')->where(['id'=>$admin_id])->update($data);
            if($rs !==false){
                return $this->responseOk();
            }else{
                return $this->responseFail();
            }
        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    /**
     * 修改管理员密码
     * @param $admin_id
     * @param $oldpwd
     * @param $pwd
     * @throws Exception
     */
    public function userEditPwd($admin_id,$oldpwd,$pwd){
        if(empty($admin_id)||empty($oldpwd)||empty($pwd)){
            return $this->responseFail(['code'=>0,'msg'=>'参数缺失']);
        }

        $info = $this->db->name('admin')->field('salt,password')->where(['id'=>$admin_id])->find();
        if(!empty($info['salt'])&&!empty($info['password'])){
            $oldpwd = password_encrypt($oldpwd,$info['salt']);
            if($oldpwd != $info['password']){
                return $this->responseFail(['code'=>0,'msg'=>'原始密码错误']);
            }

            $salt = str_rand(6);
            $pwd = password_encrypt($pwd, $salt);
            $rs = $this->db->name('admin')->where(['id'=>$admin_id,'password'=>$oldpwd])->update([
                'password' => $pwd,
                'salt'     => $salt
            ]);
        }

        if($rs){
            return $this->responseOk();
        }else{
            return $this->responseFail();
        }
    }

    /**
     * 更新后台账号token表
     * @param array $where
     * @throws \think\db\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function userUpdateToken($where=[]){
        $currentTime = time();
        $expireTime = $currentTime + 7200;//两小时时间
        $token = creatToken();

        try{
            $this->db->startTrans();
            //token设置
            $token_indata = [
                'token' => $token,
                'expire_time' => $expireTime,
                'create_time' => $currentTime,
                'device_type' => $where['device_type']
            ];
            $has = $this->db->name("admin_token")->where([
                'admin_id'=> $where['admin_id']
            ])->value('id');
            if (empty($has)) {
                $token_indata['admin_id'] = $where['admin_id'];
                $result = $this->db->name("admin_token")->insert($token_indata);
            } else {
                $result = $this->db->name("admin_token")
                    ->where([
                        'admin_id' => $where['admin_id'],
                        'token' => $where['token']
                    ])->update($token_indata);
            }
            if (empty($result)) {
                throw new Exception("操作失败，请重试");
            }

            $this->db->name("admin")->where(['id' =>$where['admin_id']])->update([
                'last_login_time'=> $currentTime,
                'last_login_ip'=> $where['ip'],
                'errlogin_info'=> ''
            ]);

            $this->db->commit();
            return $this->responseOk([
                'token' => $token_indata['token'],
                'expire_time' => $token_indata['expire_time'],
                'device_type' => $token_indata['device_type'],
            ]);
        }catch (Exception $e){
            $this->db->rollback();
            return $this->responseFail($e->getMessage());
        }
    }

    /**
     * 管理员账号删除
     * @param $admin_id
     * @param $data
     * @throws Exception
     */
    public function userDel($admin_id){
        try{
            if(!is_int($admin_id)){
                throw new Exception('参数错误');
            }
            if( $admin_id==1 ){ //为默认管理员账号
                throw new Exception('该账号不可删除');
            }

            $rs= $this->db->name('admin')->where(['id'=>$admin_id])->delete();
            if($rs){
                return $this->responseOk();
            }else{
                return $this->responseFail();
            }
        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    /**
     * 管理员列表
     */
    public function getUserList($search = [], $pagesize = 50){
        $where = [];
        if(!empty($search['account'])){
            $where[]=  ['a.account','like', '%'.$search['account'].'%'];
        }
        $result = array(
            'list'  =>  [],
            'total' =>  0,
            'last_page' =>  0,
            'current_page'  =>  0
        );
        $list = $this->db->name('admin')
            ->alias('a')
            ->join("admin_myrole b", "b.id=a.role_id","LEFT")
            ->field('a.id, a.last_login_time, a.last_login_ip, a.create_time, a.head_ico_id,a.head_ico_path,
            a.status, a.account, a.mobile, a.email, a.errlogin_info, a.role_id, b.name as role_name, a.region_nos_info')
            ->where($where)->paginate($pagesize);

        if($list->isEmpty()){
            $result['list'] = [];
        }else{
            $result['total'] = $list->total();
            $result['last_page'] = $list->lastPage();
            $result['current_page'] = $list->currentPage();
            $result['list'] =$list->items();
        }

        return $this->responseOk($result);
    }

    /**
     * 获取管理员信息
     * @param array $data
     */
    public function getUserInfo($data=[]){
        $where=[];
        if($data['userid']){
            $where[] =['a.id','=',$data['userid']];
        }
        if($data['token']){
            $where[] = ['at.token','=',$data['token']];
        }
        if($data['device_type']){
            $where[] =['at.device_type','=',$data['device_type']];
        }
        if($data['account']){
            $where[] = ['a.account','=',$data['account']];
        }
        if($data['estates_agent_id']){
            $where[] = ['a.estates_agent_id','=',$data['estates_agent_id']];
        }
        //区别与上面的userid  这个id为 注册用户id
        if($data['user_id']){
            $where[] = ['a.user_id','=',$data['user_id']];
        }

        if(empty($where)){
            return $this->responseFail(['code'=>0,'msg'=>'参数缺失']);
        }

        $admininfo=$this->db->name('admin')
            ->alias('a')
            ->field('a.*,at.token,at.expire_time')
            ->join('admin_token at','at.admin_id=a.id','LEFT')
            ->where($where)->find();

        if($admininfo['id']){
           return $this->responseOk($admininfo);
        }else{
           return $this->responseOk();
        }
    }

}
