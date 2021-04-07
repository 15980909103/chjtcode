<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\validate;

use app\common\base\BaseValidate;
use app\server\admin\Role;

/**
 * 后台账号操作的的验证规则
 * Class AdminMenuValidate
 * @package app\admin\validate
 */
class AccountValidate extends BaseValidate
{
    protected $rule = [
        'id'         =>'require|gt:0',
        'account'    => 'require',
        'email'      => 'email',
        'mobile'     =>'mobile',
        'newpassword' => 'checkNewpassword',
        'role_id' => 'require|checkRoleId'
    ];

    protected $message = [
        'id.require'         => 'id不能为空',
        'id.gt'              => 'id需要为数字',
        'account.require'    => '请输入账号',
        'newpassword.require'=> '请输入要设置的密码',
        'email.email'        => '邮箱格式错误',
        'mobile.mobile'      => '手机格式错误',
        'role_id.require'   => '请勾选权限',
    ];

    // add 验证场景定义
    public function sceneAdd()
    {
        return $this->only(['account', 'email', 'mobile', 'newpassword', 'role_id'])
            ->append('newpassword', 'require');//添加某个规则
            //->remove('age', 'between');//移除某个规则
    }
    // edit 验证场景定义
    public function sceneEdit()
    {
        return $this->only(['id', 'email', 'mobile', 'newpassword', 'role_id']);
        //->remove('age', 'between');//移除某个规则
    }
    public function sceneEditsuper()//账号为默认的超级管理员时
    {
        return $this->only(['id', 'email', 'mobile', 'newpassword']);
    }

    protected function checkNewpassword($value,$rule,$data=[]){

        if(!empty($data['newpassword'])||!empty($data['newpassword2'])){
            if($data['newpassword']!=$data['newpassword2']){
                return '两次输入的新密码不一致';
            }
            $len=mb_strlen($data['newpassword']);
            if(!($len>=6&&$len<=20)){
                return '新密码长度为6-20位';
            }
        }

        return true;
    }
    // 自定义验证规则
    protected function checkRoleId($value,$rule,$data=[])
    {
        if($value=='-1'){//roleid=-1超级管理员角色
            return true;
        }
        $roleinfos=(new Role())->getRoleInfo($value);

        if(empty($roleinfos['result']['info'])){
            return '请重新勾选权限';
        }

        return true;
    }

}