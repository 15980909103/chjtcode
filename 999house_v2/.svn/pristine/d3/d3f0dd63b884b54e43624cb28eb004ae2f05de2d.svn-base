<?php


namespace app\admin\validate;

use app\common\base\BaseValidate;

class PointsMallValidate extends BaseValidate
{
    protected $rule = [
        'id'            => 'require',
        'activities_id' => 'require',
        'name'          => 'require',
        'img'           => 'require',
        'integral'      => 'require',
        'quantity'      => 'require',
        'content'       => 'require',
        'start_time'    => 'require',
        'end_time'      => 'require',

    ];
    protected $message = [
        'id.require'            => 'id不能为空',
        'activities_id.require' => '活动类型不能为空',
        'name.require'          => '商品名称不能为空',
        'img.require'           => '商品图片不能为空',
        'integral.require'      => '商品积分不能为空',
        'quantity.require'      => '商品数量不能为空',
        'content.require'       => '商品描述不能为空',
        'start_time.require'    => '活动开始时间不能为空',
        'end_time.require'      => '活动结束时间不能为空',
    ];

    // add 验证场景定义
    public function sceneAdd()
    {
        return $this->only(['activities_id', 'name', 'img', 'integral', 'quantity', 'content']);
    }

    // edit 验证场景定义
    public function sceneEdit()
    {
        return $this->only(['id', 'name', 'img', 'integral', 'quantity', 'content']);
    }

    //添加活动 验证场景定义
    public function sceneAddactivity()
    {
        return $this->only(['name', 'img', 'start_time', 'end_time']);
    }
}