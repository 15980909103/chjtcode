<?php


namespace app\index\controller;


use app\common\base\UserBaseController;
use app\server\estates\Tag;
use app\server\user\Attention;
use app\server\user\BrowseRecords;
use app\server\user\ShortMessage;
use app\server\user\User;


class UserController extends UserBaseController
{


    //我的浏览记录
    public function browseRecords()
    {
        $res = (new BrowseRecords())->browseRecords($this->userId);
        if($res['code'] == 0){
            return $this->error($res['msg']);
        }

        return $this->success($res['result']);
    }

    //我的信息
    public function getInfo()
    {
        $res = (new User())->getInfo($this->userId);
//        $res = (new User())->getInfo(10,'h5');
        if ($res['code'] == 0) {
            return $this->error($res['msg']);
        }
        return $this->success($res['result']);
    }

    //我的关注-房源列表
    public function myListings()
    {
//        $this->userId = 2;
        $res = (new Attention())->myListings($this->userId);
        if ($res['code'] == 0) {
            return $this->error($res['msg']);
        }

        $result[] = [
                'title' => '房源',
                'list'  => $res['result'],
            ];
            $result[] = [
                'title' => '资讯',
                'list'  => [],
            ];
        return $this->success($result);
    }

    //我的关注-咨询列表
    public function myAdvisory()
    {
//        $this->userId = 2;
        $res = (new Attention())->myAdvisory($this->userId);

        if ($res['code'] == 0) {
            return $this->error($res['msg']);
        }
           $result[] = [
                'title' => '房源',
                'list'  => [],
            ];
            $result[] = [
                'title' => '资讯',
                'list'  => $res['result'],
            ];
        return $this->success($result);
    }

    //测试用-创建表，不用调
    public function createTable(){
        $server = new User();
        $res = $server->createBrowseRecord();
        return $this->success();
    }

    //房源关注
    public function attentionListings(){
        $param = $this->request->param();
        $param['user_id'] = $this->userId;
        $res = (new Attention())->attentionListings($param);
        if($res['code'] == 0){
            return $this->error($res['msg']);
        }
        return $this->success($res['result']);
    }

    /**
     * 我的广告图
     */
    public function getMyAd(){
        $param = $this->request->param();
        //测试数据
        //$param['region_no'] = '350200';
        $res = (new User())->getMyad($param);
        if($res['code'] == 0){
            return $this->error($res['msg']);
        }

        return $this->success($res['result']);
    }

    public function editUserInfo(){
        $param = $this->request->param();
        $param['user_id'] = $this->userId;

        if(!empty($param['headimgurl'])){
            if(false!==strpos($param['headimgurl'],'data:image/')){//判断是否是base64格式图片
                $param['headimgurl'] = $this->base64ImgSave($param['headimgurl'])['url'];
            }
        }
        $res = (new User())->editUserInfo($param);

        if($res['code'] == 0){
            return $this->error($res['msg']);
        }

        return $this->success($res['result']);
    }

    public function editUserPhone(){

        $param = $this->request->param();
        $param['user_id'] = $this->userId;

        //进行验证码校验
        $msgServer = new ShortMessage();
        if (!$msgServer->checkCode([
            'mobile' => $param['mobile'],
            'code'   => $param['code'],
            'sence'  => 'sign_up'
        ])) {
            return $this->error('请输入正确的验证码');
        }
        $res = (new User())->editUserPhone($param);

        if($res['code'] == 0){
            return $this->error($res['msg']);
        }

        return $this->success($res['result']);

    }

    //测试用方法
    public function getTableName(){
        $res = (new BrowseRecords())->getTableName();

        $this->success($res);
    }

    /**
     * base64图片格式存储
     * @param $image
     * @return string[]
     * @throws \Exception
     */
    public function base64ImgSave($image){
        try{
            if(false===strpos($image,'data:image/')){//判断是否是base64格式图片
                throw new \Exception('格式错误');
            }

            //设置图片名称
            $rand_str = '_'.mt_rand(1000,9999);
            $imageName = 't'.md5(strval(uniqid(md5(strval(microtime(true)).$rand_str,true)))).'.jpg';
            //判断是否有逗号 如果有就截取后半部分
            if (strstr($image,",")){
                $image = explode(',',$image);
                if(empty($image[1])){
                    throw new \Exception('格式错误');
                }
                $image = $image[1];
            }

            $de_image = base64_decode($image);
            if($image != base64_encode($de_image) ){
                throw new \Exception('格式错误');
            }
            $image = $de_image;unset($de_image);

            //设置图片保存路径
            $img_movpathForTemporary= 'upload/images';
            $img_movpath = $img_movpathForTemporary.'/'.$this->moduleName.'/user';
            $disk_img_movpath = WEB_ROOT.$img_movpath;//磁盘实际位置

            if (!is_dir($disk_img_movpath)){
                $mk = mkdir($disk_img_movpath,0777,true);
                if($mk===false){
                    $this->error('文件目录创建失败');
                }
            }
            $pathname = $img_movpath.'/'.$imageName;//数据库存储
            $disk_pathname = $disk_img_movpath.'/'.$imageName;//磁盘地址

            //生成文件夹和图片
            $rs = file_put_contents($disk_pathname, $image);
            if (!$rs) {
                throw new \Exception('图片生成失败');
            }else {
                return [
                    'name' => $imageName,
                    'url' => '/' . $pathname,
                ];
            }
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }
}