<?php
// +----------------------------------------------------------------------
// | 上传组件
// | https://www.cnblogs.com/falling-maple/p/6230248.html //各种fileMime
// +----------------------------------------------------------------------

namespace app\miniwechat\controller;

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
        if(empty($file)){
            return $this->error('请上传文件');
        }
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


    /**
     * 文件上传
     */
    public function fileUpload()
    {
        // 获取表单上传文件
//        var_dump(11111);return false;
        $file = $this->request->file('voice');
        $parms = $this->request->param();
        $rs = (new HhUpload())->setLimitSize(1024 * 100)->setMovpathForTemporary($this->moduleName)->fileUpload($file, $parms);
        if (!empty($rs['url'])) {
            $this->success(['info' => $rs]);
        } else {
            $this->error(['code' => 0, 'msg' => $rs['msg']]);
        }
    }

    /**
     * 分片上传
     */
    public function uploadSimple()
    {
        $md5 = $this->request->post('md5');
        $file = $this->request->file('file');
        $fileName = $this->request->post('fileName');
        $parms = [
            'fileName' => $fileName,
            'md5'      => $md5
        ];
        $rs = (new HhUpload())->setLimitSize(1024 * 100)->setMovpathForTemporary($this->moduleName)->fileUpload($file, $parms);
        $res = [
            'code'    => 2000,
            'message' => '操作成功',
            'data'    => $rs,
        ];

        $type = 'json';
        $header['Access-Control-Allow-Origin'] = '*';
        $header['Access-Control-Allow-Headers'] = 'X-Requested-With,Content-Type,XX-Device-Type,XX-Token,XX-Api-Version,XX-Wxmini-AppId';
        $header['Access-Control-Allow-Methods'] = 'GET,POST,PATCH,PUT,DELETE,OPTIONS';
        $response = Response::create($res, $type)->header($header);
        return $response;


    }

    /**
     * 校验文件分片是否存在服务器中
     */
    public function presence()
    {
        $md5 = $this->request->get('md5');
        $fileName = $this->request->get('fileName');
        $type = $this->request->get('type');
        $info = $this->db->name('video_simple')->where('md5', '=', $md5)->find();

        if ($info&&file_exists(WEB_ROOT.$info['name'])) {
            $data = [
                'md5'      => $md5,
                'presence' => true,
                'data'     => [
                    'name' => $info['name'],
                    'path' => '/'.$info['name'],
                    'id'   => $info['id'],
                ]
            ];

        } else {
            $data = [
                'md5'      => $md5,
                'presence' => false,
            ];
        }

        $res = [
            'code'    => 2000,
            'message' => '操作成功',
            'data'    => $data,
        ];

        $type = 'json';
        $header['Access-Control-Allow-Origin'] = '*';
        $header['Access-Control-Allow-Headers'] = 'X-Requested-With,Content-Type,XX-Device-Type,XX-Token,XX-Api-Version,XX-Wxmini-AppId';
        $header['Access-Control-Allow-Methods'] = 'GET,POST,PATCH,PUT,DELETE,OPTIONS';
        $response = Response::create($res, $type)->header($header);
        return $response;
    }

    /**
     * 合并上传的文件分片
     */
    public function merge()
    {
        $md5 = $this->request->post('md5');
        $fileName = $this->request->post('fileName');
        $fileChunkNum = $this->request->post('fileChunkNum');
        $fileName = explode('.',$fileName);
        $fileName = $md5.'.'.$fileName[1] ?? 'mp4';
        $rs = (new HhUpload())->merge($md5, $fileName, $fileChunkNum);


        if ($rs) {
            $info   = $this->db->name('video_simple')->where('md5','=',$md5)->find();
            if($info){
                $this->db->name('video_simple')->where('md5','=',$md5)->update(
                    [
                        'md5'         => $md5,
                        'dir'         => $rs['path'],
                        'update_time' => time(),
                        'name'        => $rs['name'],
                    ]
                );
                $id = $info['id'];
            }else{
                $id = $this->db->name('video_simple')->insert([
                    'md5'         => $md5,
                    'dir'         => $rs['path'],
                    'update_time' => time(),
                    'name'        => $rs['name'],
                ], true);
            }
            $res = [
                'code'    => 2000,
                'message' => '操作成功',
                'data'    => [
                    'id' => $id
                ],
            ];
        } else {
            $res = [
                'code'    => 111111,
                'message' => '操作失败',
                'data'    => [],
            ];
        }
        $type = 'json';
        $header['Access-Control-Allow-Origin'] = '*';
        $header['Access-Control-Allow-Headers'] = 'X-Requested-With,Content-Type,XX-Device-Type,XX-Token,XX-Api-Version,XX-Wxmini-AppId';
        $header['Access-Control-Allow-Methods'] = 'GET,POST,PATCH,PUT,DELETE,OPTIONS';
        $response = Response::create($res, $type)->header($header);
        return $response;

    }
}
