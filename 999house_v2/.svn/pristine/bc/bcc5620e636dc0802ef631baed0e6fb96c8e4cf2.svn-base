<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: pl125 <xskjs888@163.com>
// +----------------------------------------------------------------------

namespace api\common\lib\wxapi\module;

use api\common\lib\TraitInstance;
use app\server\merchant\Activities;
use think\Exception;
use think\Db;

class  WxWeb extends WxBase
{
    protected $appid;
    protected $appSecret;

    use TraitInstance;
    //用于第一步微信公众号官网token配置
    public function validateWxTonkenConfig(){
        $signature = $_GET['signature'];
        $nonce = $_GET['nonce'];
        $timestamp = $_GET['timestamp'];
        $token = $this->wxwebToken;
        if($signature&&$timestamp&&$nonce){
            $arr=[$nonce,$timestamp,$token];
            sort($arr);

            $tmpstr = implode('',$arr);
            $tmpstr = sha1($tmpstr);

            if($tmpstr == $signature){
                echo $_GET['echostr'];
                exit;
            }
        }
    }

    public function getCityCodeObject($cityCodeId){
        $server = new Activities();
        $config = $server->wxConfigurationInfo($cityCodeId);
        $this->appid = $config['h5']['subscribe']['appid'];
        $this->appSecret = $config['h5']['subscribe']['secret'];
        return $this;
    }

    public function getAccessToken(){
        $session_AccessToken=$this->getCache_AccessToken();
        if(empty($session_AccessToken)){
            $appid =  $this->appid;
            $appSecret =  $this->appSecret;
            $rs = self::curlGet('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$appSecret);
            if(!empty($rs['access_token'])){
                $session_AccessToken = $this->getCache_AccessToken($rs['access_token']);
            }
        }
        return $session_AccessToken;
    }

    /**
     * 获取公众号jssdk配置需要的临时票据
     * @return mixed
     */
    private function getJsapiTicket(){
        //https://mp.weixin.qq.com/wiki?action=doc&id=mp1421141115&t=0.15947710316920038#62
        $session_Ticket = $this->getCache_Ticket();

        if(empty($session_Ticket)){
            $access_token = $this->getAccessToken();
            $rs = self::curlGet('https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$access_token.'&type=jsapi');
            if(!empty($rs['ticket'])){
                $session_Ticket = $this->getCache_Ticket($rs['ticket']);
            }
        }
        return $session_Ticket;
    }

    /**
     * 获取公众号jssdk的api调用时需要的配置信息
     */
    public function getJsSdkConfig($url=''){
        $rs['noncestr'] = $this->createNonceStr();
        $rs['timestamp'] = time();
        //$url = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        if(empty($url)){
            $url = 'https://'.$_SERVER['HTTP_HOST'];
        }

        $arr = [
            'noncestr' =>$rs['noncestr'],
            'timestamp' =>$rs['timestamp'],
            'jsapi_ticket' =>$this->getJsapiTicket(),
            'url' => $url
        ];
        //print_r($arr);

        ksort($arr);
        $arr = http_build_query($arr);
        $arr= urldecode($arr);

        $rs['signature'] = sha1($arr);
        $rs['appid'] = $this->appid;
        return $rs;
    }

    private function getCache_AccessToken($data=null){
        if(!empty($data)){
            cache('_wxaccesstoken', $data, 5400); //缓存1个半小时
        }

        return cache('_wxaccesstoken');
    }
    private function getCache_Ticket($data=null){
        if(!empty($data)){
            cache('_wxjsticket', $data, 5400); //缓存1个半小时
        }

        return cache('_wxjsticket');
    }

    //创建微信菜单
    public function menuCreate($data)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->getAccessToken();
        $menu = array();
        $i=0;
//        dump($url);
//        dump($data);
        foreach ($data['button'] as $item){
//            dump($data['button']);
//            dump($item);
            $menu['button'][$i]['name'] = $item['name'];
            $menu['button'][$i]['type'] = $item['type'];

            if($item['sub_button']){
                $j=0;
                foreach ($item['sub_button'] as $sub){


                    $menu['button'][$i]['sub_button'][$j]['name'] = $sub['name'];
//                    $menu['button'][$i]['sub_button'][$j]['url'] = 'http://www.baidu.com/';
//                    $menu['button'][$i]['sub_button'][$j]['url'] = $sub['url'];

                    $menu['button'][$i]['sub_button'][$j]['type'] =  $sub['type'];
                    $menu['button'][$i]['sub_button'][$j]['url'] = $sub['url'];
                    if(!empty($sub['appid'] && !empty($sub['pagepath']))){
                        $menu['button'][$i]['sub_button'][$j]['appid'] = $sub['appid'];
                        $menu['button'][$i]['sub_button'][$j]['pagepath'] = $sub['pagepath'];
//                        unset($menu['button'][$i]['sub_button'][$j]['url']);
                    }
                    if($sub['type'] == 'media_id'){
                        $menu['button'][$i]['sub_button'][$j]['type'] = 'media_id';
                        $menu['button'][$i]['sub_button'][$j]['name'] = $sub['name'];
                        $menu['button'][$i]['sub_button'][$j]['media_id'] = $sub['media_id'];
                    }
                    $j++;
                }
            }else{
                $menu['button'][$i]['type'] = 'view';
                $menu['button'][$i]['url'] = $item['url'];
            }
            $i++;
        }
        $result = $this->curlPost($url,json_encode($menu,JSON_UNESCAPED_UNICODE));
        $list = [
            $result,$data['button']
        ];
        return $list;
    }

    //删除微信全部菜单
    public function menuDelete(){
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='.$this->getAccessToken();
        $result = $this->curlPost($url);
        return $result;
    }

    public function materialBase(){
        $url = 'https://api.weixin.qq.com/cgi-bin/material/get_material?access_token='.$this->getAccessToken();
        $data = [
            'access_token' => $this->token,
            'media_id' => 'GaRb28HjrkhxcSuU7QxQoHp55AsyGS-WtQROPLIpXdA'
        ];
        $result = $this->curlPost($url,json_encode($data));
        return $result;
    }
    //获取素材库列表
    public function materialList($data,$count = 10){
        try{
                $url = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token='.$this->getAccessToken();
            $curlData = [
                'type' => $data['type'],
                'offset' => isset($data['page']) ? (($data['page']-1)*$count):0,
                'count' => $count
            ];

            $result = $this->curlPost($url,json_encode($curlData));
            $pageData = [
                'list'  =>  [],
                'last_page' =>  0,
            ];
            if(isset($result['errcode'])){
                throw new Exception($result['errmsg']);
            }
            $list = [];
            if(isset($result['item'])&&!empty($result['item'])){
                foreach($result['item'] as $v){
                    //news_item 一次发表多篇文章
                    foreach($v['content']['news_item'] as $item){
                        $newsItem = [];
                        $newsItem = $item;
                        $newsItem['create_time'] = date('Y-m-d H:i:s',$v['content']['create_time']);
                        $list[]=$newsItem;
                    }
                }
                $pageData['list'] = $list;
                $pageData['last_page'] = ceil($result['total_count']/$count);
            }
            return $pageData;
        }catch(Exception $e){
            //throw new Exception($e->getMessage());
        }
    }

    //网络图片地址存储到本地，获取图片的本地路径、图片大小、图片类型
    function file_exists_S3($imgFromUrl='',$newFilePath='./upload/img_temporary/weChat')
    {
//        $imgFromUrl = 'https://cdn.jsdelivr.net/npm/tinymce-all-in-one@4.9.3/plugins/emoticons/img/smiley-yell.gif';
        //如果$imgFromUrl地址为空，直接退出即可
        if ($imgFromUrl == "") {return false;}
        //如果没有指定新的文件名
        //得到 $imgFromUrl 的图片格式
        $ext = strrchr($imgFromUrl, ".");
        //如果图片格式不为.jpg .png，直接退出即可
        if ($ext != ".jpg" && $ext != 'png'){
            return false;
        }
        $newFileName = str_rand(30) . $ext;
        $newFilePath = $newFilePath.'/'.date('Ymd',time()).'/'.$newFileName;

        ob_start();//打开输出
        readfile($imgFromUrl);//输出图片文件
        $img = ob_get_contents();//得到浏览器输出
        ob_end_clean();//清除输出并关闭
        $size = strlen($img);//得到图片大小

        $fp2 = fopen($newFilePath, "a");

        fwrite($fp2, $img);//向当前目录写入图片文件，并重新命名
        fclose($fp2);
        unset($img,$url);

        return [
            'filename' => $newFilePath,
            'content-type' => 'image/jpg',
            'filelength' => $size
        ];
    }
    /**
     * 删除群发,群发之后，随时可以通过该接口删除群发。
     * */
    public function delMass($msg_id){
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/delete?access_token='.$this->getAccessToken();
        $arr = [
            'msg_id' => $msg_id,//发送出去的消息ID
            'article_idx' => 0//要删除的文章在图文消息中的位置，第一篇编号为1，该字段不填或填0会删除全部文章
        ];
        $result = $this->curlPost($url,json_encode($arr));
        return $result;
    }
    /**
     * 删除永久图文素材
     * */
    public function delMaterial($media_id){
        $url = 'https://api.weixin.qq.com/cgi-bin/material/del_material?access_token='.$this->getAccessToken();
        $arr = [
            'media_id' => $media_id
        ];
        $result = $this->curlPost($url,json_encode($arr));
        return $result;
    }
    /**
     * 新增永久图文素材
     * */
    public function addMaterial($param){
        $url = 'https://api.weixin.qq.com/cgi-bin/material/add_news?access_token='.$this->getAccessToken();
        $materialList = $param;
        foreach($materialList as &$value){
            if($value['type'] == 1){
                preg_match_all('/(?<=src=").*?(?=")/', $value['content'], $out, PREG_PATTERN_ORDER);
                foreach($out[0] as $ov){
                    if(stripos( $ov,'https') !== false || stripos( $ov,'http') !== false){
                        return false;
                    }
                }
                if(!empty($out[0])){
                    $urlData = Db::name('material_img')->field('local_img_url,wechat_img_url')->where('local_img_url','in',$out[0])->select();
                    if(!$urlData->isEmpty()){
                        $urlData = $urlData->toArray();
                        foreach($out[0] as $v){
                            foreach($urlData as $d){
                                if($v == $d['local_img_url']){
                                    $value['content'] = str_replace($v, $d['wechat_img_url'], $value['content']);
                                }
                            }
                        }
                    }
                }
            }
            $value['content'] = htmlspecialchars_decode(str_replace("\"","'",$value['content']));
            unset($value['media_id']);
            unset($value['img']);
            unset($value['type']);
            unset($value['contentStatus']);
            $value['show_cover_pic'] = 0;
        }
        $data = array(
            'articles' => $materialList,
        );
        $result = $this->curlPost($url,json_encode($data,JSON_UNESCAPED_UNICODE));
        return $result;
    }

    /**
     * 图文列表
     * @param array $search $search['status'] //线路数据点的状态  $search['category_status']//类别的状态
     * @param int $pagesize
     * @return array
     */
    public function getImageTextList($search = [], $pagesize = 50, $field='*'){
        $where = [];
        if(!empty($search['title'])){
            $where[]=  ['title','like', '%'.$search['title'].'%'];
        }

        if(!in_array($search['status'],['0','1'])){
            unset($search['status']);
        }
        if(isset($search['status'])){//状态
            $where[]=  ['status','=', $search['status']];
        }

        if(!empty($search['order'])){//排序操作
            $order= $search['order'];
        }else{//默认排序
            $order= ['id'=>'desc' ];
        }

        $result = array(
            'list'  =>  [],
            'total' =>  0,
            'last_page' =>  0,
            'current_page'  =>  0
        );

        $list = Db::name('material')
            ->where($where)->order($order)->field($field)->paginate($pagesize);
        $list = $list->toArray();

        if(empty($list)){
            $result['list'] = [];
        }else{
            $result['total'] = $list['total'];
            $result['last_page'] = $list['last_page'];
            $result['current_page'] = $list['current_page'];
            $result['list'] =$list['data'];
        }

        return $result;
    }
    public function getImageTexInfo($id,$field='*'){
        if(empty($id)){
            return $this->responseFail(['code'=>0,'msg'=>'参数缺失']);
        }

        $info= Db::name('material')
            ->where([
                ['id','=',$id]
            ])->field($field)->find();

        if($info['id']){
            return $info;
        }else{
            return false;
        }
    }




    /**
     * 预览图文
     * */
    public function preview($data){
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token='.$this->getAccessToken();
        $result = $this->curlPost($url,json_encode($data,JSON_UNESCAPED_UNICODE));
        return $result;
    }

    /**
     * 群发图文
     * */
    public function sendall($data){
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token='.$this->getAccessToken();
        $result = $this->curlPost($url,json_encode($data,JSON_UNESCAPED_UNICODE));
        return $result;
    }


    /**
     * 上传图文消息内的图片获取URL
     * */
    public function uploadImgGetWeChatImgUrl($media){
        $url = 'https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token='.$this->getAccessToken();

        $ch1 = curl_init ();
        $timeout = 100;
        //$real_path=str_replace("/", "\\", $real_path);
        $data= array("media"=>new \CURLFile($media['filename']),'form-data'=>$media);
        curl_setopt ( $ch1, CURLOPT_URL, $url );
        curl_setopt ( $ch1, CURLOPT_POST, 1 );
        curl_setopt ( $ch1, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch1, CURLOPT_CONNECTTIMEOUT, $timeout );
//        curl_setopt( $ch1, CURLOPT_TIMEOUT, 30); //设置cURL允许执行的最长秒数
        curl_setopt ( $ch1, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt ( $ch1, CURLOPT_SSL_VERIFYHOST, false );
        curl_setopt ( $ch1, CURLOPT_POSTFIELDS, $data );
        $result = curl_exec ( $ch1 );
        curl_close ( $ch1 );
//        halt($result);
        if(curl_errno($ch1)==0){
            $result=json_decode($result,true);
//            dump($result);
            return $result;
        }else {
            return false;
        }

    }

    function upload_meterial($file_info){
        $url="https://api.weixin.qq.com/cgi-bin/material/add_material?access_token={$this->getAccessToken()}&type=image";
        $ch1 = curl_init ();
        $timeout = 5;
        //$real_path=str_replace("/", "\\", $real_path);
        $data= array("media"=>new \CURLFile($file_info['filename']),'form-data'=>$file_info);
        curl_setopt ( $ch1, CURLOPT_URL, $url );
        curl_setopt ( $ch1, CURLOPT_POST, 1 );
        curl_setopt ( $ch1, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch1, CURLOPT_CONNECTTIMEOUT, $timeout );
        curl_setopt ( $ch1, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt ( $ch1, CURLOPT_SSL_VERIFYHOST, false );
        curl_setopt ( $ch1, CURLOPT_POSTFIELDS, $data );
        $result = curl_exec ( $ch1 );
//        echo '<br/>';
//        echo 'reulst is ==========>'.$result;
        curl_close ( $ch1 );
        if(curl_errno($ch1)==0){
            $result=json_decode($result,true);
//            var_dump($result);
            return $result['media_id'];
        }else {
            return false;
        }
    }

}



