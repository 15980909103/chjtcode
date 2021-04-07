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
namespace app\wechatapp\validate;

use app\common\base\BaseValidate;
use app\server\merchant\Role;

/**
 * 后台账号操作的的验证规则
 * Class AdminMenuValidate
 * @package app\admin\validate
 */
class UserValidate extends BaseValidate
{
    protected $rule = [
        'mobile'     =>'require|mobile',
    ];

    protected $message = [
        'mobile.require'      => '手机不能为空',
        'mobile.mobile'      => '手机格式错误',
    ];

    // edit 验证场景定义
    public function sceneEdit()
    {
        return $this->only(['realname', 'mobile']);
    }

}