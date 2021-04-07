<?php

namespace app\common\lib\wxapi;

use app\common\base\HhDb;
use app\common\lib\wxapi\module\WxH5;
use app\common\lib\wxapi\module\WxMini;
use app\server\merchant\Activities;
use app\server\user\User;
use Exception;
use think\facade\Db;

class  WxServe
{
//    use TraitInstance;

    protected $wxMini;
    protected $wxH5;
    protected $activitieId;
    protected $cityCode;

    public function __construct()
    {
        try {
            //$this->wxMini = new WxMini();
        } catch (\Throwable $throwable) {
//            (new HhDb())->init()->name('log')->insert(['content' => $throwable->getMessage()]);
        }
    }

    public function setCodeId($cityCode = 0){
        $this->cityCode = $cityCode;
        return $this;
    }

//    public function setActivitieId($activitieId)
//    {
//        $this->activitieId = $activitieId;
//        return $this;
//    }

    /**
     * 获取微信配置
     * @return WxH5
     */
    public function getWxH5(){
        $config = (new User())->wxConfigurationInfo($this->cityCode); //获取微信配置
//        var_dump($config); return ;
        if(empty($config)){
            Db::name('log')->insert([
                'content'=>json_encode([$this->cityCode,$config]),
                'source'=> 'wxconfig',
                'created_at'=>time()
            ]);
        }
        return new WxH5($config);
    }

    /**
     * 后台菜单配置使用
     * @param $cityCode
     * @return WxH5
     */
    public function getWxAdmin($cityCode){
        $active = new Activities();
        $config = $active->wxConfigurationInfo($cityCode); //获取微信配置
        if(empty($config)){
            Db::name('log')->insert([
                'content'=>json_encode([$this->cityCode,$config]),
                'source'=> 'wxconfig',
                'created_at'=>time()
            ]);
        }

        return new WxH5($config);
    }

    //==================小程序操作==============//
    /**
     * 微信小程序授权登陆和用户信息
     * @param array $dataArr
     * @return string
     * @throws \Throwable
     */
    public function getWxMiniOauthLogin($dataArr=[]){
        try {
            return $this->wxMini->getOauthLogin($dataArr);
        } catch (\Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     * 小程序二维码
     * @param $dataArr
     * @return string
     * @throws \Throwable
     */
    public function getWxMiniEwcode($dataArr){
        try {
            return $this->wxMini->getWxMiniEwcode($dataArr);
        } catch (\Throwable $throwable) {
            throw $throwable;
        }
    }


    //==================公众号操作==============//
    /**
     * 微信公众号授权登陆
     * @param array $dataArr
     * @return string
     * @throws \Exception
     */
    public function getH5Login($dataArr=[]){
        try {
            return $this->getWxH5()->getH5Login($dataArr);
        } catch (\Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     * 微信公众号获取用户信息
     * @return string
     * @throws \Exception
     */
    public function getWxH5UserInfo($param = []){
        try {
            return $this->getWxH5()->getUserInfo($param);
           
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 微信公众号，获取AccessToken
     * @return mixed|object|\think\App
     * @throws \Throwable
     */
    public function getWxH5AccessToken(){
        try {
            return $this->getWxH5()->getAccessToken();
        } catch (\Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     *  获取公众号jssdk的api调用时需要的配置信息
     * @return mixed
     * @throws \Throwable
     */
    public function getJsSdkConfig($param){
        try {
            return $this->getWxH5()->getJsSdkConfig($param);
        } catch (\Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     * 服务器验证
     * @return mixed
     * @throws \Throwable
     */
    public function valid($param){
        try {
            return $this->getWxH5()->validateWxTonkenConfig($param);
        } catch (\Throwable $throwable) {
            throw $throwable;
        }
    }

}



