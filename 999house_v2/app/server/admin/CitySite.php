<?php

namespace app\server\admin;

use app\common\traits\TraitInstance;
use app\common\base\ServerBase;
use think\Db;
use think\Exception;

/*
 *
 * */
class CitySite extends ServerBase
{

    private $toHtmlDecodeList = [
            ''
    ];

    public function setInfo($parms){
        if(empty($parms['key'])||empty($parms['region_no'])){
            return $this->responseFail('','缺失参数');
        }
        if(is_array($parms['key'])){
            $where[] = ['key',$parms['key'][0],$parms['key'][1]];
            $where[] = ['region_no','=',$parms['region_no']];
            $result['list'] = $this->db->name('site_city_set')->where($where)->select()->toArray();

            if(!empty($result['list'])){
                foreach ($result['list'] as &$item){
                    if(in_array($item['key'],$this->toHtmlDecodeList)&&!empty($item['val'])){
                        $item['val'] = htmlspecialchars_decode($item['val']);
                    }else{
                        $str = json_decode($item['val'],true);
                        if(is_null($str)===false){
                            $item['val'] = $str;
                        }
                    }
                }
            }
        }else{
            $where[] = ['key','=',$parms['key']];
            $where[] = ['region_no','=',$parms['region_no']];
            $result['info'] = $this->db->name('site_city_set')->where($where)->find();

            if(!empty($result['info'])&&in_array($result['info']['key'],$this->toHtmlDecodeList)&&!empty($result['info']['val'])){
                $result['info']['val'] = htmlspecialchars_decode($result['info']['val']);
            }else{
                $str = json_decode($result['info']['val'],true);
                if(is_null($str)===false){
                    $result['info']['val'] = $str;
                }
            }
        }

        return $this->responseOk($result);
    }

    public function setEdit($data){
        $where[] = ['key','=',$data['key']];
        $where[] = ['region_no','=',$data['region_no']];
        $has = $this->db->name('site_city_set')->where($where)->value('id');
        if(empty($has)){

            switch ($data['key']){
                case 'wxh5';
                    $data['describe'] = 'wxh5配置';
                    break;
                case 'seo';
                    $data['describe'] = 'seo配置';
                    break;
                case 'wxh5menu';
                    $data['describe'] = 'wx公众号菜单配置';
                    break;
                case 'average_area';
                    $data['describe'] = '城市面积';
                    break;
                case 'average_price';
                    $data['describe'] = '城市均价';
                    break;
                case 'total_price':
                    $data['describe'] = '城市总价';
                    break;
                case 'reply_setting';
                    $data['describe'] = '微信消息自动回复';
                    break;
                case "subway":
                    $data['describe'] = '地铁线路';
                    break;
            }

            $result = $this->db->name('site_city_set')->insert($data);
        }else{
            unset($data['key']);
            unset($data['region_no']);
            $result = $this->db->name('site_city_set')->where($where)->update($data);
        }

        if($result){
            return $this->responseOk();
        }else{
            return $this->responseFail();
        }
    }

    public function getCityInfo($city_no){
        if($city_no){
            $info = $this->db->name('site_city')->where('id','=',$city_no)->find();
            return $this->responseOk($info);
        }

        return $this->responseFail('城市编码不能为空');
    }
}
