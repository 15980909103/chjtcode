<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: pl125 <xskjs888@163.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use api\admin\validate\SettingValidate;
use api\common\lib\WeChatConst;
use api\common\lib\wxapi\module\WxWeb;
use api\server\admin\Setting;
use api\server\wechatmaterial\Material;
use app\common\base\AdminBaseController;
use app\common\lib\wxapi\module\WxH5;
use app\common\lib\wxapi\WxServe;
use app\server\admin\CitySite;
use app\server\admin\News;
use think\Db;


class WxsetController extends AdminBaseController
{

    /**
     * 获取微信菜单数据
     * */
    public function menuInfo()
    {
        $data = $this->request->param();

        if (is_array($data['key'])) {
            $data['key'] = ['in', $data['key']];
        }
        $rs = (new CitySite())->setInfo([
            'key'       => 'wxh5menu',
            'region_no' => $data['region_no']
        ])['result'];

        $this->success($rs);
    }


    public function wxmenu()
    {
        $data = $this->request->param();

        list($result, $menu) = (new WxServe())->getWxAdmin($data['region_no'])->menuCreate($data);
        if ($result['errcode'] == 0) {
            $rs = (new CitySite())->setEdit([
                'key'       => 'wxh5menu',
                'region_no' => $data['region_no'],
                'val'       => json_encode($menu)
            ])['result'];

            $this->success('', '操作成功');
        } else {
            return $this->error(['code' => $result['errcode'], 'msg' => WeChatConst::getConstList()[$result['errcode']]]);
        }
    }

    public function menuDelete()
    {
        $data = $this->request->param();
        list($result, $menu) = (new WxServe())->getWxAdmin($data['region_no'])->menuDelete($data);
        if ($result['errcode'] == 0) {
            $this->success('', '操作成功');
        } else {
            return $this->error(['code' => $result['errcode'], 'msg' => WeChatConst::getConstList()[$result['errcode']]]);
        }
    }


    /**
     * 删除图文
     * */
    public function delImageText()
    {
        $params = $this->request->param();
        if ($params['msg_id'] != 0) {
            $result = WxWeb::getInstance('wxWeb')->delMass($params['msg_id']);
        } else {
            $result = WxWeb::getInstance('wxWeb')->delMaterial($params['media_id']);
        }

        if ($result['errcode'] == 0) {
            Db::name('material')->where(['media_id' => $params['media_id']])->delete();
            return $this->success('删除成功');
        }
        return $this->error(['code' => $result['errcode'], 'msg' => WeChatConst::getConstList()[$result['errcode']]]);
    }

    /**
     * 获取素材库列表
     * */
    public function getMaterialList()
    {
        $data = $this->request->param();
        $indata = [
            'type'      => $data['type'],
            'page'      => $data['page'],
            'region_no' => $data['region_no']
        ];
        unset($data);
        $result = (new WxServe())->getWxAdmin($indata['region_no'])->materialList($indata);
        return $this->success($result);

    }


    /**
     * 获取素材库列表
     * */
    public function getImageTextList2()
    {
        $data = $this->request->param();
        $rs = (new WxServe())->getWxAdmin($data['region_no'])->getImageTextList($data, 10);
        if (!empty($rs['list'])) {

            foreach ($rs['list'] as &$value) {
                $value['create_time'] = date('Y-m-d H:i:s', $value['create_time']);
                $value['val'] = json_decode($value['val']);
            }
        } else {
            $rs = [];
        }
        $this->success($rs);
    }

    /**
     * 获取图文详情-素材库列表
     * */
    public function getImageTexInfo()
    {
        $data = $this->request->param();

        $id = intval($data['id']);
        if (empty($id)) {
            $this->error('缺少参数');
        }

        $rs = (new WxServe())->getWxAdmin($data['region_no'])->getImageTexInfo($id);

        $rs['val'] = json_decode($rs['val']);

        if ($rs) {
            $rs = $rs;
        } else {
            $rs = [];
        }
        $this->success($rs);
    }

    /**
     * 新增、编辑图文
     * */
    public function editMateria()
    {
        $data = $this->request->param();
        $params = $data;//发送到微信的参数
        foreach ($data['material'] as &$v) {
            $v['content'] = htmlspecialchars_decode(str_replace("\"", "'", $v['content']));
        }
        if (empty($data['id'])) {
            //图文信息新增至微信素材库中
            $result = WxWeb::getInstance('wxWeb')->addMaterial($params['material']);
            if ($result == false) {
                return $this->error(['code' => 0, 'msg' => '不允许从网络上复制图片，请将图片保存至本地在上传该图片！']);
            }
            if (!empty($result['media_id'])) {
                //发到图文数据保存本地数据库
                $rs = Material::getInstance()->imageTextAdd($data['material'], $result['media_id'])['result'];
                if (!empty($rs['id'])) {
                    return $this->success(['id' => $rs['id'], 'media_id' => $result['media_id']]);
                }
            }
            return $this->error(['code' => $result['errcode'], 'msg' => WeChatConst::getConstList()[$result['errcode']]]);
        } else {
            //到微信删除medie_id
            WxWeb::getInstance('wxWeb')->delMaterial($params['media_id']);
            //图文信息新增至微信素材库中
            $result = WxWeb::getInstance('wxWeb')->addMaterial($params['material']);
            if ($result == false) {
                return $this->error(['code' => 0, 'msg' => '不允许从网络上复制图片，请将图片保存至本地在上传该图片！']);
            }
            if (!empty($result['media_id'])) {
                //发到图文数据保存本地数据库
                $rs = Material::getInstance()->imageTextEdit($data['id'], $data['material'], $result['media_id'])['result'];
                if ($rs) {
                    return $this->success(['id' => $data['id'], 'media_id' => $result['media_id']]);
                }
            }
            return $this->error(['code' => $result['errcode'], 'msg' => WeChatConst::getConstList()[$result['errcode']]]);

        }
    }

    /**
     * 群发图文
     * */
    public function sendall()
    {
        $params = $this->request->param();
        $params['media_id'] = 'x_bZ6PIyTyQeQ8SljZBsP9KJsPLvzd7-_JRXmRDoU3w';
        if (!empty($params['media_id'])) {
            //群发所有用户 服务号每月限4次发送
            $data['filter'] = [
                'is_to_all' => true
            ];

            $where = [
                ['region_no' ,'=',$params['city_no']],
                ['article_id' ,'=',$params['article_id']]
            ];
            $info = $this->db->name('wx_graphic_material')->where($where)->field('media_id')->find();

            if(!$info){
                return $this->error('数据有误');
            }
            if($info['media_id'] != $params['media_id']){
                return $this->error('media_id数据有误');
            }

            /**
            //群发只标签组用户 群发的次数对标签组每月限100次，标签中的用户接收到4条消息后，便无法再接收图文消息
            //$data['filter'] = [
            //'is_to_all' => false,//true会进入到历史消息，反之一个道理
            //'tag_id' => 2//标签组id
            //];
             * **/

            $data['mpnews'] = ['media_id' => $params['media_id']];
//            $data['mpnews'] = ['media_id' => ''];
            $data['msgtype'] = 'mpnews';
            $data['send_ignore_reprint'] = 0; //为0时，文章被判定为转载时，将停止群发操作。为1时，文章被判定为转载时，且原创文允许转载时，将继续进行群发操作。
//            $result = WxWeb::getInstance('wxWeb')->sendall($data);
            $result = (new WxServe())->getWxAdmin($params['city_no'])->sendall($params['city_no'],$data);

            if ($result['errcode'] == 0) {
                $this->db->name('wx_graphic_material')->where($where)->update([
                    [
                        'status' => 1,
                        'msg_id' => $result['msg_id'],
                        'update_time' => time()
                    ]
                ]);
//                Db::name('material')->where(['id' => $params['id']])->update(['status' => 1, 'msg_id' => $result['msg_id']]);
                return $this->success();
            } else {
                return $this->error(['code' => $result['errcode'], 'msg' => WeChatConst::getConstList()[$result['errcode']]]);
            }
        }
        return $this->error(['code' => 1, 'msg' => '群发失败']);
    }

    /**
     * 预览图文
     * */
    public function preview()
    {
        $data = $this->request->param();
        if (empty($data['towxname'])) {
            return $this->error(['code' => 0, 'msg' => '请输入微信号']);
        }
        $id = $data['id'];
        $params = $data;//发送到微信的参数
        foreach ($data['material'] as &$v) {
            $v['content'] = htmlspecialchars_decode(str_replace("\"", "'", $v['content']));
        }
        if (empty($data['id'])) {
            $result = WxWeb::getInstance('wxWeb')->addMaterial($params['material']);
            if (!empty($result['media_id'])) {
                //发到微信保存
                $rs = Material::getInstance()->imageTextAdd($data['material'], $result['media_id']);
                if ($rs['code'] != 1) {
                    return $this->error(['code' => 0, 'msg' => '预览失败']);
                } else {
                    if ($rs['result']['id']) {
                        $id = $rs['result']['id'];
                    }
                }
            }
        } else {
            //到微信删除medie_id
            $rs = WxWeb::getInstance('wxWeb')->delMaterial($params['media_id']);
            //发到微信保存
            $result = WxWeb::getInstance('wxWeb')->addMaterial($params['material']);
            if (!empty($result['media_id'])) {
                $rs = Material::getInstance()->imageTextEdit($data['id'], $data['material'], $result['media_id']);
                if ($rs['code'] != 1) {
                    return $this->error(['code' => 0, 'msg' => '预览失败']);
                }
                Db::name('material')->where(['id' => $data['id']])->update(['media_id' => $result['media_id']]);
            }
        }

        if (!empty($result['media_id'])) {
            $wxData = [];
            $wxData['towxname'] = $params['towxname'];
            $wxData['mpnews'] = ['media_id' => $result['media_id']];
            $wxData['msgtype'] = 'mpnews';
            $previewResult = WxWeb::getInstance('wxWeb')->preview($wxData);
            if ($previewResult['errcode'] == 0) {
                $this->success(['id' => $id, 'media_id' => $result['media_id']], '操作成功，请注意查收');
            } else {
                return $this->error(['code' => $previewResult['errcode'], 'msg' => '没有找到微信号或该微信号没有关注公众号']);
            }
        } else {
            return $this->error(['code' => $result['errcode'], 'msg' => WeChatConst::getConstList()[$result['errcode']]]);
        }

    }

    /**
     * 获取已上传列表
     */
    public function getMaterialData(){
        $data = $this->request->param();
        $where  =array();
        if(!empty($data['name']) ) {
            $where[] =[
                'name',
                'linke',
                "%{$data['name']}%"
            ];
        }

        $list  = $this->db->name('wx_graphic_material')->alias('wx')
            ->leftJoin('article a','a.id= wx.article_id')
            ->field('a.name,wx.article_id,wx.type,wx.media_id,wx.id')
            ->where($where)
            ->paginate(16);

        if($list->isEmpty()){
            $result['list'] = [];
        }else{
            $result['total'] = $list->total();
            $result['last_page'] = $list->lastPage();
            $result['current_page'] = $list->currentPage();
            $result['list'] =$list->items();
        }
        $this->success($result);
    }

    //图文素材上传 - 新增的
    public function uploadWxFile()
    {
        $param = $this->request->param();

        if(empty($param['id']) ) {
            $this->error('参数错误');
        }
        //从数据库获取数据
        $management_data = $this->db->name('article')->where('id', $param['id'])->field('img_url,region_no,title,context as content,author')->find();

        if(empty($management_data) ){

            return   $this->error('文章不存在');
        }
        $material_info  = $this->db->name('wx_graphic_material')->where('article_id','=',$param['id'])->find();

        if(!empty($material_info) ) {

            return  $this->error('该文章已上传为微信素材');
        }

        $img = json_decode($management_data['img_url'], true);
        $management_data['content'] = htmlspecialchars_decode($management_data['content']);

        if (strpos($img[0]['url'], '/') === 0) {
            $imgpath = WEB_ROOT . substr($img[0]['url'], 1);
        } else {
            $imgpath = WEB_ROOT . ($img[0]['url']);
        }

        $media = json_decode((new WxServe())->getWxAdmin($management_data['region_no'])->uploadImg($management_data['region_no'],$imgpath,2),true);

        $this->db->name('wx_graphic_material')->insert([
            'article_id' => $param['id'],
            'media_id'  => $media['media_id'],
            'type' => 1,
            'create_time' => time(),
            'update_time' => time()
        ]);

        //测试用的数据
//        $media = "{\"media_id\":\"x_bZ6PIyTyQeQ8SljZBsP1y38gM-a5tclPSrBSfbCH0\",\"url\":\"http: //mmbiz.qpic.cn/mmbiz_jpg/7xgZteI2CCynZ2sFXnFBF7ciaNic0ibIHNm3JbCSy0u7rYYOYwicUnia5Lia2N2ibktibPX18uzrSSIDXeDib6KKl88BZ7A/0?wx_fmt=jpeg\",\"item\":[]}";
//        $media['media_id'] = "x_bZ6PIyTyQeQ8SljZBsP1y38gM-a5tclPSrBSfbCH0";

        $preg = '/<img[\s\S]*?src\s*=\s*[\"|\'](.*?)[\"|\'][\s\S]*?>/';
        preg_match_all($preg, $management_data['content'], $imgArr);

        foreach ($imgArr[1] as $key => $val) {
            $img_path = $val;

            $img_path = str_replace('http://jiufang.test.com//', '', $img_path);
            $img_path = WEB_ROOT . ($img_path);

            $media_url = json_decode((new WxServe())->getWxAdmin($management_data['region_no'])->uploadImg($management_data['region_no'],$img_path,2),true);

            $this->db->name('wx_graphic_material')->insert([
               'article_id' => $param['id'],
                'media_id'  => $media_url['media_id'],
                'type' => 1,
                'create_time' => time(),
                'update_time' => time()
            ]);

//            $media_url['url'] = "http://mmbiz.qpic.cn/mmbiz_jpg/7xgZteI2CCynZ2sFXnFBF7ciaNic0ibIHNm9HdooVo68ib31zgpRdvm4rEr146vJYoTsOeekTOd0mhl0bBGz7nvRFg/0?wx_fmt=jpeg";
            if ($media_url == '') {
                return $this->error('保存失败');
            }
            $management_data['content'] = str_replace($val, $media_url['url'], $management_data['content']);
        }

        $data_material = [
            'articles' => [
                [
                    'title'              => ($management_data['title']),
                    'thumb_media_id'     => $media['media_id'],
                    'author'             => ($management_data['author']),
                    'digest'             => ($management_data['title']),
                    'content'            => ($management_data['content']),
                    'show_cover_pic'     => '1',
                    'content_source_url' => '1',
                ]
            ]
        ];

        $material = ((new WxServe())->getWxAdmin($management_data['region_no'])->material($management_data['region_no'], $data_material, 1));
        if (!$material) {
            return $this->error('保存失败1');
        }
        $this->db->name('wx_graphic_material')->insert([
            'article_id' => $param['id'],
            'media_id'  => $material['media_id'],
            'type' => 2,
            'create_time' => time(),
            'update_time' => time()
        ]);
         $this->success();
    }

    //删除素材  -新增的
    public function deleteWxFile(){

        $param = $this->request->param();

        if( empty($param['id']) ){
            return  $this->error('参数错误');
        }
        $data = $this->db->name('wx_graphic_material')->where('id',$param['id'])->field('media_id,region_no')->select();

        if($data->isEmpty()){
            return $this->error('暂无数据');
        }else{
            $data = $data->toArray();
            foreach ($data as $key => $value){
                $material = ((new WxServe())->getWxAdmin($value['region_no'])->material($value['region_no'], ['media_id' => $value['media_id']], 3));
                if($material['errcode'] != 0){
                    return $this->error($material['ERRMSG']);
                }
            }
        }
        //数据库删除
        $this->db->name('wx_graphic_material')->where('id',$param['id'])->delete();
        $this->success();
    }




}
