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
use app\server\marketing\CouponActivity;
use app\server\marketing\Subject;
use app\server\user\User;
use app\websocket\BobingStore;
use Swoole\Coroutine\WaitGroup;
use think\App;
use think\Config;
use think\contract\Arrayable;
use think\initializer\BootService;
use function Co\run;

class ActivityCouponController extends UserBaseController
{
    //获取首页数据
     public function index(){
         $indexADv = new \app\index\controller\ActivityCouponController($this->app);
         $indexADv->index();
         return ;
     }

     public function getCouponInfo(){
         $indexADv = new \app\index\controller\ActivityCouponController($this->app);
         $indexADv->getCouponInfo();
         return ;

     }
     //查询用户是否有资格
     public function checkQualifications(){
         $indexADv = new \app\index\controller\ActivityCouponController($this->app);
         $indexADv->checkQualifications();
         return ;

     }
}
