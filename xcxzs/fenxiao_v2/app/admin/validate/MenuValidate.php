<?php

namespace app\admin\validate;

use app\common\base\BaseValidate;

/**
 * 后台菜单的验证规则 对应admin_mymenu
 * Class AdminMenuValidate
 * @package app\admin\validate
 */
class MenuValidate extends BaseValidate
{

    protected $rule = [
        'id'        =>'require|gt:0',
        'name'       => 'require',
        'parent_id'  => 'require|checkParentId',
        'url'     => 'require',
        'sort'       => 'number',
    ];

    protected $message = [
        'id.require'        => 'id不能为空',
        'id.gt'             => 'id需要为数字',
        'name.require'      => '名称不能为空',
        'url.require'       => '权限url设置不能为空',
        'parent_id.require' =>  '请选择上级',
        'sort.number'       =>  '排序需要为数字越大越靠前',
    ];

    protected $scene = [
        'add'  => ['name',  'url', 'parent_id','sort'],
        'edit' => ['name',  'url', 'id', 'parent_id','sort'],
        'add_onlymenu'  => ['name',  'parent_id','sort'],//只是做为菜单的一个层级
        'edit_onlymenu' => ['name',  'id', 'parent_id','sort'],//只是做为菜单的一个层级
    ];

    // 自定义验证规则
    protected function checkParentId($value,$rule,$data=[])
    {
        if($data['id'] == $data['parent_id']){
            return '不能选择自己为上级';
        }

        $find = $this->db->name('admin_mymenu')->where("id", $value)->value('parent_id');

        if ($find) {
            $find2 = $this->db->name('admin_mymenu')->where("id", $find)->value('parent_id');
            if ($find2) {
                $find3 = $this->db->name('admin_mymenu')->where("id", $find2)->value('parent_id');
                if ($find3) {
                    return '超过了4级';
                }
            }

        }
        return true;
    }

}