<?php

namespace app\admin\validate;

use app\common\base\BaseValidate;

/**
 * 后台菜单的验证规则 对应admin_mymenu
 * Class AdminMenuValidate
 * @package app\admin\validate
 */
class ArticleDate extends BaseValidate
{

    protected $rule = [
        'id'        =>'require|gt:0',
        'title'  => 'require',
        'name'     => 'require',
        'resource_type'       => 'checkRes',
        'keyword'       => 'require',
        'order_type'       => 'checkOrderType',
        'click'       => 'number',
        'is_top'       => 'in:0,1',
        'is_index'       => 'in:0,1',
        'lable'       => 'require',
        'context'       => 'checkContext',
        'source_type'       => 'number',
        'release_time'       => 'number',
        'top_time'       => 'checkTopTime',
        'has_comment'       => 'in:0,1',
        'num_read'      => 'number',
        'num_thumbup'      => 'num_thumbup',
        'num_share'      => 'num_share',
        'num_collect'      => 'num_collect',
    ];

    protected $message = [
        'id.require'        => 'id不能为空',
        'title.require'        => '副标题不能为空',
        'keyword.require'        => '关键词不能为空',
        'click.number'        => '点击数必须为数字',
        'is_top.in:0,1'        => '类型错误',
        'lable.require'        => '标签不能为空',
        'source_type.require'        => '文章来源不能为空',

    ];

    protected $scene = [
        'add'  => [  'title', 'name','resource_type','keyword','order_type'
                    ,'click','is_top','is_index','author','lable','context','source_type','release_time','top_time','has_comment'],
        'edit' => ['id',  'title', 'name','resource_type','keyword','order_type'
            ,'click','is_top','is_index','author','lable','context','source_type','release_time','top_time','has_comment'],
    ];

   protected function  checkOrderType($value ,$rule,$data){
        if($data['resource_type'] == 1){
            if(!in_array($data['order_type'],[1,2,3,0])){
                return  '分类类型错位';
            }
        }
        if($value > count(explode(',',$data['img_url']))){
            return  '请上传对应图片数量';
        }
        return true;
    }

    /**
     *
     */
    protected function checkTopTime($value ,$rule,$data){
       if($data['is_top'] ==1 && empty($data['top_time'])){
           return '开启置顶需要填写到期时间';
       }

       return  true;
    }

    protected function checkRes($value ,$rule,$data){
//        var_dump($data['video_url']);
        if(!in_array($value,[1,2,3,0]) ){
            return '资源类型错误';
        }
        if(($value == 2 || $value==3) && empty($data['video_url']) ){
            return  '视频类型必须上传视频资源';
        }

        return  true;

    }

    /**
     *
     * @param $value
     * @param $rule
     * @param $data
     */
    protected function checkContext($value ,$rule,$data){
        if( $data['resource_type'] == 1  && empty($value)){
            return '文章内容不能为空';
        }
        return true;
    }

}