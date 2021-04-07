<?php
declare (strict_types = 1);

namespace app\common\base;
use app\server\admin\Upload;
use think\Config;
use think\exception\ValidateException;
use think\facade\Filesystem;
use think\Validate;

/**
 * 控制器基础类
 */
class HhUpload
{
    private $img_movpathForTemporary= 'upload/images';//文件夹位置
    private $video_movpathForTemporary= 'upload/video';//文件夹位置
    private $img_limit_size = 1048576*10;

    public function setLimitSize($limit_size = 1048576*10){
        $this->img_limit_size = $limit_size;
        return $this;
    }
    public function setMovpathForTemporary($path){
        $path = ltrim($path,'/');
        $this->img_movpathForTemporary = $this->img_movpathForTemporary.'/'.$path;
        return $this;
    }

    public function imgUpload($file,$parms){
        $img_limit_size = $this->img_limit_size;

        // 获取表单上传文件
        //$file = $this->request->file('image');
        //$parms = $this->request->param();

        //将可能的../或者被改至多级，只设置一级
        $subpath = str_replace('.','',$parms['subpath']);
        $subpath = str_replace('/','',$subpath); //
        $img_movpath = $this->img_movpathForTemporary.'/'.$subpath;
        $disk_img_movpath = WEB_ROOT.$img_movpath;//磁盘实际位置

        try{
            if(empty($file)){
                throw new ValidateException('请上传文件');
            }
            if (!is_dir($disk_img_movpath)){
                $mk = mkdir($disk_img_movpath,0775,true);
                if($mk===false){
                    throw new ValidateException('文件目录创建失败');
                }
            }
            if($parms['isthumb']==1){//是否生成缩略图
                if(empty($parms['width'])||empty($parms['height'])){
                    $parms['width']=90;
                    $parms['height']=90;
                }
            }

//            $rs_check = $this->validate([
//                'file'=> $file
//            ],['file'=>[
//                'fileSize'=> $img_limit_size,
//                //'fileExt'=> 'jpg,jpeg,png,gif',
//                'fileMime'=> 'image/jpeg,image/png,image/gif,image/gif'
//            ]],[
//                'file.fileSize' => '超出上传文件大小限制',
//                'file.fileExt' => '请上传图片格式文件',
//                'file.fileMime' => '请上传图片格式文件',
//            ]);

            if(true){//$rs_check==
//                $info = \think\facade\Filesystem::putFile( 'topic', $file);
                $image = \think\Image::open($file);
                if($parms['isthumb']==1){
                    $image->thumb($parms['width'], $parms['height']);
                }
                $rand_str = '_'.mt_rand(1000,9999);
                $filename = 't'.md5(strval(uniqid(md5(strval(microtime(true)).$rand_str,true)))).'.'.$image->type();

                $pathname = $img_movpath.'/'.$filename;//数据库存储
                $disk_pathname = $disk_img_movpath.'/'.$filename;//磁盘地址
                $result  = $image->save($disk_pathname,null,100);

//                var_dump($result);return ;
                $data  =  [
                   'storage'        => 'local',
                   'file_url'       => \think\facade\Config::get('app.domain_name'),
                    'file_path'     => '/' . $pathname,
                    'file_size'     => $file->getSize(),
                    'file_type'     => $result->type(),
                    'extension'     => $result->mime(),
                    'file_hash'     => $file->hash(),
                    'width'         =>    $result->width(),
                    'height'        => $result->height(),
                    'name'          => $filename,
                    'create_time'   => time(),
                ];
                $id  = (new Upload())->add($data);
                unset( $image);
                $rs_info = [
                    'name' => $filename,
                    'url' => '/' . $pathname,
                    'id'    => $id,
                ];
            }else{
                throw new ValidateException($file->getError());
            }

            return $rs_info;
        }catch (ValidateException  $e){
            return ['msg'=>$e->getMessage()];
        }
    }

    /**
     * @param $file
     * @param $parms
     * @return array|string[]
     *
     * 文件上传
     */
    public function fileUpload($file,$parms){
        $img_movpath = $this->video_movpathForTemporary.'/'.date('Y-m-d',time()).'/'.$parms['md5'];
        $disk_img_movpath = WEB_ROOT.$img_movpath;//磁盘实际位置

        try{
            if(empty($file)){
                throw new ValidateException('请上传文件');
            }
//            if (!is_dir($disk_img_movpath)){
//                $mk = mkdir($disk_img_movpath,0775,true);
//                if($mk===false){
//                    throw new ValidateException('文件目录创建失败');
//                }
//            }
            $info = \think\facade\Filesystem::disk('public')->putFileAs( $img_movpath, $file,$parms['fileName']);
            $name    = explode('/',$info);
            $name    = end($name);
            $rs_info = [
                'name' =>   $name   ,
                'url' => '/storage/'.$info,
            ];
            return $rs_info;
        }catch (ValidateException  $e){
            return ['msg'=>$e->getMessage()];
        }
    }


    /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v     = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }
        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }
        return $v->failException(true)->check($data);
    }

    /**
     * 文件切片合并
     * @param $md5 文件md5
     * @param $fileName  文件命
     * @param $fileChunkNum  文件切片数量
     * @return bool
     *
     *
     */
    public function merge($md5,$fileName,$fileChunkNum){
        $path =  $img_movpath = $this->video_movpathForTemporary.'/'.date('Y-m-d',time()).'/'.$md5;
        $disk_img_movpath = WEB_ROOT.$img_movpath;//磁盘实际位置
        if( is_dir($disk_img_movpath) ){
              if(!empty($fileChunkNum) && $fileChunkNum >1){
                  $allfile  = @fopen($disk_img_movpath.'/'.$fileName,'a');
                  if(!$allfile){

                      return  false;
                  }
                  for ($i=0;$i<$fileChunkNum;$i++){
                      $file =  fopen($disk_img_movpath.'/'.$i,'r');

                      if(!$file){
                          return  false;
                      }

                      flock($file,LOCK_EX);

                      fwrite($allfile,file_get_contents($disk_img_movpath.'/'.$i));

                      unlink($disk_img_movpath.'/'.$i);

                      flock($file,LOCK_UN);

                      fclose($file);
                  }
                  @fclose($allfile);
                  if( is_file($disk_img_movpath.'/'.$fileName) ){
                      return  [
                          'path'    =>$path.'/'.$fileName,
                          'name'    =>$fileName
                      ];
                  }
                  return  false;
              }

              if( rename($disk_img_movpath.'/0',$disk_img_movpath.'/'.$fileName )  ){
                  return  [
                      'path'    =>$path,
                      'name'    =>$path.'/'.$fileName
                  ];
              }
              return  false;
        }else{
           return false;
        }
    }

}
