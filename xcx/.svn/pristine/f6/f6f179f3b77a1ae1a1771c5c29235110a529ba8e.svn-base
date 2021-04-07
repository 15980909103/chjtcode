<?php

namespace app\admin\validate;

use app\common\base\BaseValidate;


/**
 * 文件删除验证规则
 * Class DelFileValidate
 * @package api\admin\validate
 */
class DelFileValidate extends BaseValidate
{
    protected $rule = [
        'tablename' =>'require',
    ];

    protected $message = [
        'tablename.require'  => '文件所在的数据表名不能为空',
    ];

    // 自定义验证规则 验证文件是否是数据里面的
    protected function checkFileInDb($value,$rule,$data=[])
    {
        if(empty($data['url'])){
            return '文件的地址不能为空';
        }
        if(empty($data['url_key'])){
            return '文件的地址的key不能为空';
        }

        $tablename= $value;
        $key = $data['url_key'];
        $find = $this->db->name($tablename)->where($key, $data['url'])->value($key);

        if(empty($find)) {
            return '该文件未找到不可删除';
        }
        return true;
    }
}