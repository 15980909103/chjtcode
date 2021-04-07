<?php

namespace app\common\base;

use think\Validate;
use think\Db;

/**
 * 后台菜单的验证规则 对应admin_mymenu
 * Class AdminMenuValidate
 * @package app\admin\validate
 */
class BaseValidate extends Validate
{
    public $db = null;
    public function __construct()
    {
        parent::__construct();
        $this->db = (new HhDb())->init();
    }

}