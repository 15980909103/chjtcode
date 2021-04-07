<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: pl125 <xskjs888@163.com>
// +----------------------------------------------------------------------

namespace api\admin\controller;

use api\common\Base\AdminBaseController;
use api\common\lib\Botany\Botany;
use api\common\lib\wxapi\module\WxWeb;
use api\server\admin\Role;
use think\Cache;
use think\Db;
use think\Exception;
use think\Validate;

class WechatuploadController extends AdminBaseController
{
    private $img_movpathForTemporary= 'upload/img_temporary';//临时文件夹位置
    //private $img_movpathForFormal= 'upload/img_formal';//正式文件夹位置
    private $voice_movpathForTemporary= 'upload/voice_temporary';//临时文件夹位置
    //private $voice_movpathForFormal= 'upload/voice_formal';//正式文件夹位置

    public function imgUpload(){
        // 获取表单上传文件
        $file = request()->file('image');
        $parms = request()->param();
        //将可能的../或者被改至多级，只设置一级
        $subpath = str_replace('.','',$parms['subpath']);
        $subpath = str_replace('/','',$subpath); //
        $img_movpath = $this->img_movpathForTemporary.'/'.$subpath;

        try{
            if(empty($file)){
                throw new Exception('请上传文件');
            }
            $rs_info = [];
//           dump($parms['type']);
            if($parms['type']=='getMediaId'){
                // 移动到框架应用根目录/uploads/ 目录下 //验证文件格式并且移动文件
                $info = $file->validate(['size' => 1048576 * 3, 'ext' => 'jpg,png'])->move($img_movpath);

//               dump($info);

                if ($info) {
                    // 成功上传后$info获取上传信息 //$info->getPathname()返回 upload/img_temporary/20190627/42a79759f284b767dfcb2a0197904287.jpg
                    $inpath = $info->getPathname();
                    $inpath = str_replace('\\', '/', $inpath);//替换windos下路径的\
                    $inpath_filename = $info->getFilename();

                    $imageSize = filesize('./'.$inpath);
                    $fileArr = [
                        'filename' =>  './'.$inpath,
                        'content-type' => 'image/jpg',
                        'filelength' => $imageSize
                    ];
                    $rs = WxWeb::getInstance('wxWeb')->upload_meterial($fileArr);
//                   halt($rs);
                    if(is_string($rs)){
                        $rs_info = [
                            'name' => $inpath_filename,
                            'url' => '/'.$inpath,
                            'get_media_id' => $rs
                        ];
                    }
                }
            }else{
                // 移动到框架应用根目录/uploads/ 目录下 //验证文件格式并且移动文件
                $info = $file->validate(['size' => 1048576 * 3, 'ext' => 'jpg,png'])->move($img_movpath);

                if ($info) {
                    // 成功上传后$info获取上传信息 //$info->getPathname()返回 upload/img_temporary/20190627/42a79759f284b767dfcb2a0197904287.jpg
                    $inpath = $info->getPathname();
                    $inpath = str_replace('\\', '/', $inpath);//替换windos下路径的\
                    $inpath_filename = $info->getFilename();

//                    dump($inpath);
                    $imageSize = filesize('./'.$inpath);
//                   halt($imageSize);


                    $fileArr = [
                        'filename' =>  './'.$inpath,
                        'content-type' => 'image/jpg',
                        'filelength' => $imageSize
                    ];
//                   dump($fileArr);
                    $rs = WxWeb::getInstance('wxWeb')->uploadImgGetWeChatImgUrl($fileArr);
//                   dump($rs);

                    if(isset($rs['url'])){
                        $img_id = Db::name('material_img')->insertGetId([
                            'local_img_url' =>  '/'.$inpath,
                            'wechat_img_url' =>$rs['url'],
                            'create_time' => time()
                        ]);
//                        halt($inpath);


                        $rs_info = [
                            'name' => $inpath_filename,
                            'url' => '/'.$inpath,
                        ];
                    }
                }
            }


            $this->success(['info'=> $rs_info]);
        }catch (Exception $e){
            $this->error(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }



    public function voiceUpload(){
        // 获取表单上传文件
        $file = request()->file('voice');
        $parms = request()->param();

        //将可能的../或者被改至多级，只设置一级
        $subpath = str_replace('.','',$parms['subpath']);
        $subpath = str_replace('/','',$subpath); //
        $voice_movpath = $this->voice_movpathForTemporary.'/'.$subpath;

        try{
            if(empty($file)){
                throw new Exception('请上传文件');
            }
            $rs_info=[];
            // 移动到框架应用根目录/uploads/ 目录下 //验证文件格式并且移动文件
            $info = $file->validate(['size'=> 1048576*3, 'ext'=> 'mp3'])->move($voice_movpath);
            if($info){
                // 成功上传后$info获取上传信息 //$info->getPathname()返回 upload/img_temporary/20190627/42a79759f284b767dfcb2a0197904287.jpg
                $inpath = $info->getPathname();
                $inpath = str_replace('\\','/',$inpath);//替换windos下路径的\
                $inpath_filename =$info->getFilename();
                $rs_info=[
                    'name' => $inpath_filename,
                    'url' => '/'.$inpath,
                ];
            }else{
                // 上传失败获取错误信息
                throw new Exception($file->getError());
            }

            $this->success(['info'=> $rs_info]);
        }catch (Exception $e){
            $this->error(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }




}
