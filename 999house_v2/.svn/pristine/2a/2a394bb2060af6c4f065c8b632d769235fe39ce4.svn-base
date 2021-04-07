<?php
namespace app\miniwechat\controller;

use app\common\base\UserBaseController;
use app\common\MyConst;
use app\server\admin\Admin;
use app\server\admin\ArticleTag;
use app\server\admin\Banner;
use app\server\admin\City;
use app\server\admin\CityPriceLog;
use app\server\admin\Column;
use app\server\admin\ConsultingComments;
use app\server\admin\InformationVideo;
use app\server\admin\News;
use app\server\estates\Estatesnew;
use app\server\index\Adv;
use app\websocket\BobingStore;
use Swoole\Coroutine\WaitGroup;
use think\Config;
use think\contract\Arrayable;
use think\initializer\BootService;
use function Co\run;

class AdvController extends UserBaseController
{

    /**
     * 通过标识获取广告
     */
   public function getAdvByFlag(){
       $indexADv = new \app\index\controller\AdvController($this->app);
       $indexADv->getAdvByFlag();
       return ;
       $flage       = $this->request->post('falg');
       $city_no     = $this->request->post('city_no');
//       $limit       = $this->request->post('limit');
//       var_dump($city_no);
       if(empty($flage) ){
           return $this->error('请传入有效标识位');
       }
       if(empty($city_no) ){
           return $this->error('请传入城市标识');
       }
       $advServer  = new Adv();
       if(is_array($flage)){
           foreach ($flage as $k => $v) {
               if($v  && !$advServer->isFlag($v)){
                   return $this->error('无效的标识');
               }
           }
       }else{
           if($flage && !$advServer->isFlag($flage)){
               return $this->error('无效的标识');
           }
       }


       $where =[
           'flag' => $flage,
           'status' => 1,
           'time'  => time(),
           'city_no'=> $city_no,

       ];
//       var_dump($where);
       $list = ($advServer)->getFlagAdlist($where);
       $list = $advServer->getAdlist($list);
       //如果是数组将数组进行风分组
       if(is_array($flage)){
           $group_list = [];
           foreach ($flage as $k => $v){
               foreach ($list as $key => $value){
                   if($v == $value['place']){
                       $group_list[$v][] = $value;
                   }
               }
           }

        $list = $group_list;
       }
        if(empty($list)){
             $this->success([]);
        }


        $this->success($list);

   }

}
