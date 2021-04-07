<?php

namespace app\admin\validate;

use app\common\base\BaseValidate;

/**
 * 后台菜单的验证规则 对应admin_mymenu
 * Class AdminMenuValidate
 * @package app\admin\validate
 */
class LandDate extends BaseValidate
{
    /**
     * @var string[]
     *
     *
     *
     */
    protected $rule = [
        'id'        =>'require|gt:0',
        'title'  => 'require',
        'recipients'     => 'require',
        'img_url'       => 'require',
        'coordinate'       => 'require',
        'city_no'       => 'require',
        'status'       => 'require',
        'type'       => 'require',
        'landaddr'       => 'require',
        'transaction_time'       => 'require',
        'premium_rate'       => 'number',
        'transaction_price'       => 'number',
        'total_transaction_price'       => 'number',
        'total_starting_price'       => 'number',
        'starting_price'       => 'number',
        'plot_ratio'       => 'number',
        'use_year'       => 'number',
        'area'       => 'number',


    ];

    protected $message = [
        'id.require'            => 'id不能为空',
        'title.require'         => '地块名不能为空',
        'recipients.require'    => '竞得方法必须填写',
        'img_url.require'       => '图片不能为空',
        'coordinate.require'    => '请选择位置',
        'city_no.require'       => '请选择城市地块',
        'status.require'        => '请选择交易状态',
        'type.require'          => '请选择土块类型',
        'landaddr.require'      => '请选择土块所在地',
        'transaction_time.require'      => '成交时间不能为空',
        'premium_rate.number'      => '溢价率必须是数字',
        'transaction_price.number'      => '成交楼面价必须时数字',
        'total_transaction_price.number'      => '成交总价必须时数字',
        'total_starting_price.number'      => '起拍总价必须时数字',
        'starting_price.number'      => '起拍楼面价必须时数字',
        'plot_ratio.number'      => '容积率必须是数字',
        'use_year.number'      => '使用年限',
        'area.number'      => '面积是数字',


    ];

    protected $scene = [
        'add'  => [  'title', 'recipients','img_url','coordinate','type'
                    ,'type','landaddr','transaction_time','premium_rate'
                    ,'total_starting_price','starting_price','plot_ratio',
                     'use_year','area'],
        'edit'  => [ 'id','title', 'recipients','img_url','coordinate','type'
                    ,'type','landaddr','transaction_time','premium_rate'
                    ,'total_starting_price','starting_price','plot_ratio',
                    'use_year','area'],
    ];


}