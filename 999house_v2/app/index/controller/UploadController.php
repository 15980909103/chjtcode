<?php
// +----------------------------------------------------------------------
// | 上传组件
// | https://www.cnblogs.com/falling-maple/p/6230248.html //各种fileMime
// +----------------------------------------------------------------------

namespace app\index\controller;

use app\common\base\AdminBaseController;
use app\common\base\HhUpload;
use think\App;
use think\Response;


class UploadController extends AdminBaseController
{
    //todo 注意事项 ：多文件上传时候要注意有个为 name 的key 值 例子：image['name'][0] image['name'][1]
    public function imgUpload()
    {
        // 获取表单上传文件
        $item = [];
        $file = $this->request->file('image');
        $parms = $this->request->param();
        if (is_array($file)) {
            $files = $file;
            foreach ($files as $key => $file) {

                $image = \think\Image::open($file);
                $parms['width'] = $parms['width'] ? $parms['width'] : $image->width();
                $res = $this->db->name('upload_file')->where('file_hash', '=', $file->hash())->order('file_id desc')->find();

                //文件存在直接返回
                if (!empty($res) && $parms['width'] == $res['width'] && is_file($this->app->getRootPath() . 'public' . $res['file_path'])) {
                    $name = explode('/', $res['file_path']);
                    $name = end($name);
                    $info = [
                        'name' => $name,
                        'url'  => $res['file_path'],
                        'id'   => $res['file_id'],
                    ];
                    $item[] = $info;
                    continue;
                }
                $rs = (new HhUpload())->setLimitSize(1048576 * 10)->setMovpathForTemporary($this->moduleName)->imgUpload($file, $parms);
                if (!empty($rs['url'])) {
                    $item[] = $rs;
                } else {
                    return $this->error(['code' => 0, 'msg' => $rs['msg']]);
                }

            }
            return $this->success($item);

        } else {

            $image = \think\Image::open($file);
            $parms['width'] = $parms['width'] ? $parms['width'] : $image->width();
//        $parms['height'] = $parms['height'] ? $parms['height'] : $image->height();
            $res = $this->db->name('upload_file')->where('file_hash', '=', $file->hash())->order('file_id desc')->find();
//        var_dump($res);
            //文件存在直接返回
            if (!empty($res) && $parms['width'] == $res['width'] && is_file($this->app->getRootPath() . 'public' . $res['file_path'])) {
                $name = explode('/', $res['file_path']);
                $name = end($name);
                $info = [
                    'name' => $name,
                    'url'  => $res['file_path'],
                    'id'   => $res['file_id'],
                ];
                $this->success(['info' => $info]);
            }
            $rs = (new HhUpload())->setLimitSize(1048576 * 10)->setMovpathForTemporary($this->moduleName)->imgUpload($file, $parms);
            if (!empty($rs['url'])) {
                $this->success(['info' => $rs]);
            } else {
                $this->error(['code' => 0, 'msg' => $rs['msg']]);
            }

        }


    }
    

}
