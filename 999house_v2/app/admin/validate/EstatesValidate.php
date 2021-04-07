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
use app\server\merchant\Role;

/**
 * 后台账号操作的的验证规则
 * Class AdminMenuValidate
 * @package app\admin\validate
 */
class EstatesValidate extends BaseValidate
{
    protected $rule = [
        'id'         =>'require|gt:0',
        'name'    => 'require',
        'province'     =>'require',
        'province_str'     =>'require',
        'city'     =>'require',
        'city_str'     =>'require',
        'area'     =>'require',
        'area_str'     =>'require',
        'address'     =>'require',
        'logo' => 'require',
        'list_cover' => 'require',
        'detail_cover' => 'require',
        'coordinate' => 'require',
        'lng' => 'require',
        'lat' => 'require',
        'status' => 'require|number|between:0,2'
    ];

    protected $message = [
        'id.require'         => '楼盘ID不能为空',
        'name.require'         => '楼盘名称不能为空',
        'province.require'         => '城市不能为空',
        'province_str.require'         => '城市不能为空',
        'city.require'         => '城市不能为空',
        'city_str.require'         => '城市不能为空',
        'area.require'         => '区/县不能为空',
        'area_str.require'         => '区/县不能为空',
        'address.require'         => '地址不能为空',
        'logo.require'         => '请上传logo',
        'list_cover.require'         => '请上传列表封面',
        'detail_cover.require'         => '请上传详情封面',
        'coordinate.require'  => '经纬度不能为空',
        'lng.require'         => '经度不能为空',
        'lat.require'         => '纬度不能为空',
        'status.require'       => '状态不能为空',
        'status.number'       => '状态为非法格式',
        'status.between'       => '状态为非法格数据',
    ];

    // 验证草稿添加场景
    public function sceneAddDraft()
    {
    	return $this->only(['name', 'province', 'province_str', 'city', 'city_str', 'area', 'area_str', 'address', 'status']);
    }

    // 验证草稿编辑场景
    public function sceneEditDraft()
    {
    	return $this->only(['id', 'name', 'province', 'province_str', 'city', 'city_str', 'area', 'area_str', 'address', 'status']);
    } 

    // 验证添加场景
    public function sceneAdd()
    {
    	return $this->only(['name', 'province', 'province_str', 'city', 'city_str', 'area', 'area_str', 'address', 'logo', 'list_cover', 'detail_cover', 'coordinate','status']);
    } 

    // 验证编辑场景
    public function sceneEdit()
    {
    	return $this->only(['id', 'name', 'province', 'province_str', 'city', 'city_str', 'area', 'area_str', 'address', 'logo', 'list_cover', 'detail_cover', 'coordinate', 'status']);
    } 

}