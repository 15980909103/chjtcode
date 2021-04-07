<?php
declare (strict_types = 1);

namespace app\websocket;
use app\common\manage\TaskManage;
use app\common\pool\RedisPool;
use app\server\index\Activity;
use app\server\index\BoRes;
use app\server\index\PointsMall;
use app\task\Message;
use Swoole\Server;
use Swoole\Timer;
use think\App;
use think\Container;
use Exception;
use think\facade\Cache;
use Throwable;

/**
 * swoole的websocket的订阅事件监听处理
 * Class MyWebsocket
 * @package app\common\listens
 */
class BobingStore
{
    protected $quekey     = 'bobstorekey';
    protected $timerkey   =  'timerkey';
    protected $pkResKey   =  'pkRes_';
    protected $pkNumJoin = 'pkNum:Join:';// pkNum:Join:{日期ymd}
    protected $pkNumTotal = 'pkNum:Total:';// pkNum:Total:{日期ymd}

    public static $drawIntegral = 4;// PK平局
    public static $PkGetIntegral = 2;// PK所得积分
    public static $PkSubIntegral = 3;// PK所减积分

    public $drawStr = 'draw';
    public $winStr = 'win';
    public $loseStr = 'lose';

    public $limitLen = 3;
    public $singleTime = 15;// 单局时长/秒
    public $readyTime = 6;// 预备时长/秒
    public $waitTime = 10;// 第二局等待准备时长
    public $ownerReday = "owner_ready";
    public $otherReday = "other_ready";
    public $readyBegin = "ready_begin";
    public $owner = "owner";
    public $other = "other";
    public $timerId = "timer_id";

    protected $autoNum = ['3', '3', '3', '2', '5', '6'];// 未博饼默认结果

    protected $pkDefaultPoint = ['win' => 0, 'lose' => 0, 'level' => 0];// PK默认分数


    use \app\common\websocket\TraitWebSocket;


    public function __construct(Container $container, Server $server)
    {
        $this->initBase($container,$server);
    }


    //避免形成获取时全局变量影响更改为动态函数操作
    // 活动ID
    protected function getActivityId($aid = null){
        if(!is_null($aid)){
            self::contextSetData('aid',$aid);
        }else{
            $aid = self::contextGetData('aid');
        }

        return $aid;
    }
    /**
     * 判断活动是否有效
     */
    protected function checkActivity($activityId){
        //return true;
        return self::contextRememberData('activityinfo',function() use ($activityId) {
            $activityServer = new Activity();
            $activity = $activityServer->getActivityByWhere(['id' => $activityId], 'id, pk_point, start_time, end_time, pk_number, total_pk_number')['result'];
            if(empty($activity)) {
                $this->setError('活动不存在')->send();
                return false;
            }
            $time = time();
            if($activity['start_time'] > $time || $activity['end_time'] < $time) {
                $this->setError('不在活动时间范围内')->send();
                return false;
            }
            return $activity;
        });
    }
    //当前登录对应的用户id由于分表非唯一，需结合商户id
    protected function getUserId(){
        $info = $this->getSimpleUserByUid($this->getUid(),['user_id']);
        if(empty($info['user_id'])){
            throw new Exception('用户数据错误，请重新登录');
        }
        return $info['user_id'];
    }
    //当前登录对应的商户id
    protected function getMerchantId(){
        $info = $this->getSimpleUserByUid($this->getUid(),['merch_id']);
        if(empty($info['merch_id'])){
            throw new Exception('用户数据错误，请重新登录');
        }
        return $info['merch_id'];
    }

    /**
     * 标识某个用户上线时间点
     * @param $uid
     * @param int $time //正代表上线时间点，负代表下线时间点，0为没有
     */
    private function setOnLineTimeByUid($uid, $time){
        $this->websocket->setOnLineTimeByUid($uid, $time);
    }
    private function getOnLineTimeByUid($uid){
        return $this->websocket->getOnLineTimeByUid($uid);
    }

    /**
     * 针对uid映射fd表 增加自定义字段存储
     * @param $uid
     * @param $field
     * @param $val
     */
    private function setFieldByUid($uid, $field, $val){
        return $this->websocket->setFieldByUid($uid, $field, $val);
    }

    /**
     * 通过某个uid判断对应fd是否在线
     * @param $uid
     * @return array|bool
     */
    private function isOnLineByUid($uid){
        return $this->websocket->isOnLineByUid($uid);
    }
    /**
     * 判断当前某个fd是否在线，默认为当前机器的fd
     * @param int|array $fd
     * @return bool
     */
    private function isOnlineByFd($fd){
        return $this->websocket->isOnlineByFd($fd);
    }
   
    //获取redis用户信息
    protected function getUserInCache($token){
        if(empty($token)) {
            return  [];
        }
        $info = Cache::store('redis')->get(strval($token));
        if(empty($info)) {
            return  [];
        }
        return json_decode($info,true);
    }
    
    //========= uid 和 fd 绑定关系 =========//
    /**
     * 通过用户id找到对应的fd
     * @param $uid
     * @return mixed
     */
    private function getFdByUid($uid){
        return $this->websocket->getFdByUid($uid);
    }
    /**
     * 通过fd找到对应的用户id
     * @param $fd
     * @return mixed
     */
    private function getUidByFd($fd){
        return $this->websocket->getUidByFd($fd);
    }
    /**
     * 通过fd找到对应的用户的简单信息
     */
    private function getSimpleUserByFd($fd){
        return $this->websocket->getTableUser()->getUidInfoByFd($fd);
    }

    /**
     * 通过uid找到对应的用户的简单信息
     * @param $uid
     * @param array $field
     * @return array|bool|mixed
     */
    private function getSimpleUserByUid($uid,$field=[]){
        return $this->websocket->getTableUser()->getFdInfoByUid($uid, $field);
    }

    /**
     * 通过uid 获取当前用户的登录token
     * @param $uid
     * @return bool|mixed
     */
    protected function getLoginTokenByUid($uid){
        $info = $this->getSimpleUserByUid($uid,['login_token']);
        if(empty($info['login_token'])){
            return false;
        }
        return $info['login_token'];
    }
    //========= uid 和 fd 绑定关系 end=========//
    
    //============ 前置操作 ============//
    /**
     * 校验当前 fd 是否登录成功
     * @param $data ['fd']
     * @return array|bool 用户信息|false
     */
    protected function isLogin($data){
        if(empty($data['merch_id'])){
            return false;
        }
        
        $fd = $data['fd']??$this->getCurrentSenderFd();
        if(empty($fd)){  return false; }
        
        $info = $this->getSimpleUserByFd($fd);//fd找得到uid信息为登录
        
        if($info==false||$info['merch_id']!=$data['merch_id']||empty($info['uid'])){
            return false;
        }
        return $info;
    }

    /**
     * 前置打开连接时判断token有效值，绑定fd和uid
     * @param $data
     * @return array|false
     */
    public function onBeforeConnect($data){
        if(empty($data['token'])){
            return false;
        }

        //是否是pc界面
        if($data['pc']==1){
            //判断token是否正确 //pc的token由+加密后的merch_id+商户账号+pc=1md5而成
            $userinfo = encode_pass($data['token'],'decode')??[];
            if(empty($userinfo['merch_id'])||empty($userinfo['pc'])){
                return false;
            }
            $userinfo['merch_id'] = $this->deCodeId($userinfo['merch_id'],'merch_id');
            $userinfo['id'] = 0;//标识是后台
        }else{
            $userinfo = $this->getUserInCache($data['token']);
            if(empty($userinfo['id'])){
                return false;
            }
        }
        if(!empty($userinfo)){
            $uid = $userinfo['id'].'!'.$userinfo['merch_id'];
            $fd = $this->getCurrentSenderFd();
            //绑定fd和uid关系形成登录
            $this->websocket->getTableUser()->uidfdBind([
                'uid' => $uid,
                'login_token' => $data['token'],
                'online_time' => time(),
                'merch_id' => $userinfo['merch_id'],
                'user_id' => $userinfo['id'],
            ],[
                'fd' => $fd,
                'merch_id' => $userinfo['merch_id'],
                'user_id' => $userinfo['id'],
            ]);
        }else{
            return false;
        }

        echo '上线了';
        echo $uid.'----'.$fd;
        echo PHP_EOL;

        return $userinfo;
    }
    /**
     * 前置校验是否可以往下执行监听方法
     * @param $data ['event','data']
     * @return bool true 可以往下执行 false不可往下执行
     *
     */
    public function onCheckCanLoad($data){
        $data = $data['data'];

        $merch_id = $data['merch_id'] ?? '';
        $activity_id = $data['activity_id'] ?? 0;
        if(empty($merch_id)||empty($activity_id)){
           $this->setError('缺失参数')->send();
           return false;
        }
        $data['merch_id'] = $merch_id = $this->deCodeId($merch_id,'merch_id');
        $data['activity_id'] = $activity_id = $this->deCodeId($activity_id,'activity_id');

         // // 绕过验证，模拟测试用
        if(!empty($data['ttt'])){
            Cache::store('redis')->set($data['ttt']['token'],json_encode([
                'merch_id' => $merch_id,
                'activity_id' => $activity_id,
                'id'=> $data['ttt']['user_id'],
                'nickname'=> $data['ttt']['nickname'],
                'headimgurl'=> $data['ttt']['headimgurl'],
            ]));
            $this->onBeforeConnect([
                'token'=> $data['ttt']['token']
            ]);
        }

        
        $data['fd'] = $this->getCurrentSenderFd();
        $loginInfo = $this->isLogin($data);
        if($loginInfo==false||empty($loginInfo['uid'])){
            $this->setError('抱歉，登录失败')->send([],'loginFail');
            return false;
        }

        if($merch_id!=$loginInfo['merch_id']){
            $this->setError('参数错误')->send();
           return false;
        }

        $this->getActivityId($activity_id);

        if(empty($data['event'])) {
            $data['event'] = '';
        }

        //@todo 活动检测
        if(strtolower($data['event'])!='leavepkroom'){
            /*$activity = $this->checkActivity($activity_id);
            if(empty($activity)) {
                return false;
            }*/
        }

        $this->getUid($loginInfo['uid']);
        $this->getMyFdInfo($loginInfo);

        return true;
    }
    //============ 前置操作 end ============//



    //======队列操作 start=======//
    /**
     * 进入房间等待池进行人员队列
     * @param $flag
     * @param $val //小伙伴的uid标识
     * @param $limitLen //队列固定长度，0为不限制长度
     */
    private function waitUidQueue_push($flag,$val,$limitLen=0){
        $key = $this->quekey . $flag;

        //uid-0,标识当前玩家状态，0待准备>0代表玩家准备时间,<0代表已经结束
        $rs =  $this->getRedis()->rpush($key, $val.'_0');
        if(!empty($limitLen)){//设置限制长度时
            $this->getRedis()->lTrim($key,0, intval($limitLen)-1);
        }
        //设置redis队列中房间key的有效时间
        $this->getRedis()->expire($key,intval(3600*3.2));

        echo '加入等待队列'.$key.'int:'.$rs.PHP_EOL;
        return $rs;
    }
    private function waitUidQueueMemberSet($flag,$idx,$val){
        $key = $this->quekey . $flag;
        return $this->getRedis()->lSet($key, $idx, $val);
    }
    /**
     * 当前队列长度
     * @param $flag
     * @return bool|int
     */
    private function waitQueue_len($flag){
        $key = $this->quekey . $flag;
        return $this->getRedis()->lLen($key);
    }
    private function waitUidQueue_del($flag){//在定时器中需新开redis连接
        $key = $this->quekey . $flag;
        return $this->getRedis()->del($key);
    }
    /**
     * 获取队列成员
     * @param $flag
     * @return array
     */
    private function waitUidQueueMembers($flag){
        $key = $this->quekey . $flag;
        $rs = $this->getRedis()->lRange($key, 0, -1 );
        $list = [];
        foreach ($rs as $item){
            $item = explode('_',$item);
            $list[] = [
              'uid'=> $item[0],
              'readyTime'=> $item[1],
            ];
        }

        return $list;
    }
    
    //======队列操作 end=======//

    public function createJoinCode(){
        if($this->getUserId()=='-1'){
            $encode_ownerUid = $this->enCodeId($this->getUid(),'uid');
            $code = '';//生成二维码
            $this->send(['event'=>'joinCodeCall','data'=>$code]);
        }
    }
    /**
     * 加入
     * @param $data
     */
    public function onJoinSinger($data)
    {
        try {
            $merch_id = $this->getMerchantId();
            $user_id = $this->getUserId();
            $uid = $this->getUid();
            $code = $data['join_code'];//二维码
            $encode_ownerUid = $data['owner_uid']; //商家登录的uid
            if(empty($encode_ownerUid)||empty($code)){
                /*var_dump($this->enCodeId('0!2','uid'));
                var_dump($this->deCodeId('JRZTPJ','uid'));*/
                return $this->setError('参数缺失')->send(['event'=>'readyCall']);
            }
            //@todo 二维码验证

            $owner_uid = $this->deCodeId($encode_ownerUid,'uid');

            //判断此次扫码的队列是否已经满了
            $len = $this->waitQueue_len($owner_uid);
            echo PHP_EOL.'房间人数现在:'.$len.PHP_EOL;
            if($len>=$this->limitLen){
                $member_uid = '';
                $members = $this->waitUidQueueMembers($owner_uid);//判断是否是队列中的进行数据重连
                foreach ($members as $item){
                    if($item['uid'] == $uid&&($item['readyTime'] >= 0)){
                        $member_uid = $item['uid'];
                        break;
                    }
                }

                if(empty($member_uid)){
                    return $this->setError('该房间人数已满了')->send(['event'=>'readyCall']);
                }
            }else{
                //进入房间等待池进行队列
                $len = $this->waitUidQueue_push($owner_uid, $this->getUid(), $this->limitLen);
                if($len>$this->limitLen){
                    return $this->setError('该扫码人数已满了')->send(['event'=>'readyCall']);
                }
            }
            echo PHP_EOL.'房间人数现在-:'.$len.PHP_EOL;

            // 改变玩家准备状态
            $this->ready([
                'owner_uid' => $encode_ownerUid,
                'uid' => $uid
            ]);

        }catch (Throwable $t){
            $this->setError('抱歉，数据错误请重新登录')->send([],'loginFail');
        }
    }

    /**
     * 获取房间内双方的头像昵称
     */
    protected function getRoomNameAndImg($uid)
    {
        $info = [];
        // 参与者
        $otherToken = $this->getLoginTokenByUid($uid);
        $otherInfo = $this->getUserInCache($otherToken);
        $info['name'] = $otherInfo['nickname'] ?? "";
        $info['img'] = $otherInfo['headimgurl'] ?? "";
        $info['uid'] = $uid?$this->enCodeId($uid,'uid'):'';
        return $info;
    }

    /**
     * 准备状态
     */
    public function ready($data)
    {
        try {
            $encode_ownerUid = $data['owner_uid'];// 房主UID
            if(empty($encode_ownerUid)) {
                return $this->setError('商户参数缺失')->send(['event'=>'readyCall']);
            }
            $owner_uid = $this->deCodeId($encode_ownerUid,'uid');
            $uid = $data['uid']; //当前访问的uid

            $member_uid = ''; //可以玩的uid
            $members = $this->waitUidQueueMembers($owner_uid);//迭代下一个可以玩的玩家
            foreach ($members as $k=>$item){
                if($item['readyTime'] > 0){
                    $member_uid = $item['uid'];
                    break;
                }
                if($item['readyTime'] == 0){
                    $member_uid = $item['uid'];
                    break;
                }
            }
            if(empty($member_uid)||$uid!=$member_uid){//拦截当前玩家不是可以可以玩的身份
               return false;
            }

            $activityId = $this->getActivityId();// 活动ID
            
            $readyTime = time();
            $this->waitUidQueueMemberSet($owner_uid, $k,$member_uid.'_'.$readyTime);
            $userInfo = $this->getRoomNameAndImg($member_uid);
            $this->setMass([$member_uid, $owner_uid],'uid')->send([
                'other_info'=> $userInfo
            ],'readyCall');

            //@todo 倒计时操作超时
            $after_timerId = Timer::after(($this->singleTime+$this->readyTime)*1000, function ()use($owner_uid,$k,$member_uid){
                //自动放弃结果
                $this->waitUidQueueMemberSet($owner_uid,$k,$member_uid.'_-1');//标识玩家已经结束
            });
            //@todo 标识机器ip、进程与timerid $after_timerId
            //$this->setFieldByUid($owner_uid, 'bobing_store_afterTimerId', $after_timerId);

            return true;
        }catch (Throwable $t){
            $this->setError('抱歉，数据错误请重新登录')->send([],'loginFail');
            return false;
        }
    }

    /**
     * 游戏操作
     * @param $data
     */
    public function onReadyGo($data){
        try {

            $param['user_id'] = $this->getUserId();// 当前用户ID
            $param['merch_id'] = $this->getMerchantId();// 当前用户所属商户ID
            $param['activity_id'] = $this->getActivityId();// 活动ID
            $param['type'] = 3;// 线下类型
            /*$boServer = new BoRes();
            $boRes = $boServer->doBo($param);*/
            $boRes['code'] =1;
            $boRes['num'] =[1,2,3,4,5,6];

            if(isset($boRes['code']) && 0 == $boRes['code']) {
                // 出现异常情况
                $boMsg = $boRes['msg'] ?? "未知异常";
                return $this->setError("博饼出现异常：" . $boMsg)->send(['event'=>'resultCall']);
            }

            $encode_ownerUid = $data['owner_uid'];// 房主UID
            if(empty($encode_ownerUid)) {
                return $this->setError('商户参数缺失')->send(['event'=>'readyCall']);
            }
            $owner_uid = $this->deCodeId($encode_ownerUid,'uid');
            $member_uid = '';
            $members = $this->waitUidQueueMembers($owner_uid);//迭代下一个可以玩的玩家
            foreach ($members as $k=>$item){
                if($item['readyTime'] > 0){
                    $member_uid = $item['uid'];
                    break;
                }
                if($item['readyTime'] == 0){
                    $member_uid = $item['uid'];
                    break;
                }
            }

            if(empty($member_uid)||$member_uid!=$this->getUid()){//判断该用户是否可以操作了
                return $this->setError("抱歉还未到您操作".$member_uid.'@'.$this->getUid() )->send(['event'=>'resultCall']);
            }
            if(($this->singleTime+$this->readyTime)*1000+$item['readyTime']<=time()-1){//临界点时间示为放弃自动结算
                return;
            }
            //清除自动放弃的定时器
            $after_timerId = $this->getSimpleUserByUid($owner_uid,['bobing_store_afterTimerId']);
            if(!empty($after_timerId)){
                //Timer::clear($after_timerId);
            }

            $this->dealPkResult([
                'uid' => $member_uid,
                'index' => $k,
                'owner_uid' => $owner_uid,
                'boRes' => $boRes
            ]);//处理结果

        }catch (Throwable $t){
            $this->setError('抱歉，数据错误请重新登录')->send([],'loginFail');
            return false;
        }
    }

    // PK结果处理
    protected function dealPkResult($data)
    {
        $owner_uid = $data['owner_uid'];
        $uid = $data['uid'];
        $boRes = $data['boRes'];
        $this->waitUidQueueMemberSet($owner_uid, $data['index'],$uid.'_-1');//标识玩家已经结束
        $userInfo = $this->getRoomNameAndImg($uid);
        print_r($boRes);
        $this->setMass([$uid, $owner_uid],'uid')->send([
            'boRes'=> $boRes
        ],'readyCall');

        //@todo 群发活动房间内的人员
        /*$this->setMass($active_room)->send([
            'other_info'=> $userInfo
        ],'readyCall');*/
        return;

        $resKey = $data['res_key'];
        $roomNo = $data['room_no'];
        $activityId = $data['activity_id'];
        $activityEndTime = $data['activity_end_time'];
        $pkPoint = $data['pk_point'];
        $boServer = $data['bo_server'] ?? new BoRes();
        $adImg = $data['adImg'] ?? '';
        $merchId = $data['merch_id'] ?? 0;
        if(empty($adImg)) {
            $adImg = $boServer->getRandImg($merchId);
        }
        

        $redis = $this->getRedis();
        $ownerField = $this->owner;
        $otherField = $this->other;
        $resArr = $redis->hMGet($resKey, [$ownerField, $otherField]);
        if(!empty($resArr[$ownerField]) && !empty($resArr[$otherField])) {// 双方都已经出结果
            if(!empty($resArr)) {
                $dataArr = [];
                foreach ($resArr as $key => $val) {
                    $valArr = json_decode($val, TRUE);
                    $dataArr[] = [
                        'uid' => $valArr['uid'],
                        'info' => $valArr,
                    ];
                }
            } else {
                return $this->setMass($roomNo)->setError('PK出现异常:结果读取失败')->send(['event'=>'resultCall']);
            }

            // 房主  操作日志 ownerArr
            $ownerRes = json_decode($resArr[$ownerField], TRUE);
            if(empty($ownerRes)) {
                return $this->setMass($roomNo)->setError('PK出现异常:结果读取失败')->send(['event'=>'resultCall']);
            }
            $ownerUid = $ownerRes['uid'];
            if(empty($ownerUid)){
                return $this->setMass($roomNo)->setError('PK出现异常:数据错误')->send(['event'=>'resultCall']);
            }
            $ownerInfo = explode('!', $ownerUid);
            $ownerId = $ownerInfo['0'] ?? 0;// 用户ID
            $ownerMerchId = $ownerInfo['1'] ?? 0;// 商户ID
            if(empty($ownerId)||empty($ownerMerchId)){
                return $this->setMass($roomNo)->setError('PK出现异常:数据错误')->send(['event'=>'resultCall']);
            }

            $ownerArr['user_id'] = $ownerId;
            $ownerArr['merch_id'] = $ownerMerchId;
            $ownerArr['activity_id'] = $activityId;
            $ownerArr['end_time'] = $activityEndTime;
            
            // 参与者  操作日志 otherArr
            $otherRes = json_decode($resArr[$otherField], TRUE);
            if(empty($otherRes)) {
                return $this->setMass($roomNo)->setError('PK出现异常:结果读取失败')->send(['event'=>'resultCall']);
            }
            $otherUid = $otherRes['uid'] ?? "0!0";
            $otherInfo = explode('!', $otherUid);
            $otherId = $otherInfo['0'] ?? 0;// 用户ID
            $otherMerchId = $otherInfo['1'] ?? 0;// 商户ID

            $otherArr['user_id'] = $otherId;
            $otherArr['merch_id'] = $otherMerchId;
            $otherArr['activity_id'] = $activityId;
            $otherArr['end_time'] = $activityEndTime;

            // 胜负所得积分
            $pkPoint = json_decode((string)$pkPoint, TRUE);
            if(!isset($pkPoint['win']) || !isset($pkPoint['lose']) || !isset($pkPoint['level'])) {
                // $this->setMass($roomNo)->setError('积分配置异常')->send(['event'=>'resultCall']);
                // return FALSE;
                $pkPoint = $this->pkDefaultPoint;
            }
            $winPoint = $pkPoint['win'] ?? 0;
            $losePoint = $pkPoint['lose'] ?? 0;
            $drawPoint = $pkPoint['level'] ?? 0;

            // 胜负判定
            $ownerScore = $ownerRes['rollVal'] ?? 0;
            $otherScore = $otherRes['rollVal'] ?? 0;
            if($ownerScore == $otherScore) {// 平局
                $ownerArr['type'] = self::$drawIntegral;
                $ownerArr['roll_int'] = $drawPoint;

                $otherArr['type'] = self::$drawIntegral;
                $otherArr['roll_int'] = $drawPoint;

                $infoStatus = $this->drawStr;// 房主状态：平局
            } else {
                if($ownerScore > $otherScore) {// 房主胜
                    // 胜者
                    $ownerArr['type'] = self::$PkGetIntegral;
                    $ownerArr['roll_int'] = $winPoint;
                    // 负者
                    $otherArr['type'] = self::$PkSubIntegral;
                    $otherArr['roll_int'] = -$losePoint;

                    $infoStatus = $this->winStr;// 房主状态：胜利
                } else {// 参与者胜
                    // 胜者
                    $otherArr['type'] = self::$PkGetIntegral;
                    $otherArr['roll_int'] = $winPoint;
                    // 负者
                    $ownerArr['type'] = self::$PkSubIntegral;
                    $ownerArr['roll_int'] = -$losePoint;

                    $infoStatus = $this->loseStr;// 房主状态：失败
                }
            }

            // 获取双方头像昵称
            $nameAndImg = $this->getRoomNameAndImg($ownerUid, $otherUid);

            // info字段存储的信息
            $infoDetail = [
                // 房主结果
                'owner_id' => $ownerId,
                'owner_merch' => $ownerMerchId,
                'owner_roll_str' => $ownerRes['rollStr'] ?? "",
                'owner_roll_num' => $ownerRes['rollNum'] ?? $this->autoNum,
                'owner_name' => $nameAndImg['owner_name'] ?? "",
                'owner_img' => $nameAndImg['owner_img'] ?? "",
                'owner_auto' => !empty($ownerRes['auto']) ? 1 : 0,
                // 参与者结果
                'other_id' => $otherId,
                'other_merch' => $otherMerchId,
                'other_roll_str' => $otherRes['rollStr'] ?? "",
                'other_roll_num' => $otherRes['rollNum'] ?? $this->autoNum,
                'other_name' => $nameAndImg['other_name'] ?? "",
                'other_img' => $nameAndImg['other_img'] ?? "",
                'other_auto' => !empty($otherRes['auto']) ? 1 : 0,
                // 房主是否胜利
                'owner_status' => $infoStatus,
            ];
            $ownerArr['info'] = $otherArr['info'] = $infoDetail;

            $data = [$ownerArr, $otherArr];

            $resRecord = $boServer->boResRecord($data);

            if(isset($resRecord['code']) && 0 == $resRecord['code']) {
                // 出现异常情况
                $resMsg = $resRecord['msg'] ?? "未知异常";
                return $this->setMass($roomNo)->setError('PK出现异常:' . $resMsg)->send(['event'=>'resultCall']);
            }

            // 增加次数
            $this->setPkNum([
                'activity_id' => $activityId,
                'ownerUid' => $ownerUid,
                'otherUid' => $otherUid,
            ]);

            // 记录完日志后，删除定时器和本局结果
            $timerId = (int)$redis->hGet($resKey, $this->timerId);
            if(!empty($timerId)) {
               swoole_timer_clear($timerId);
            }
            // 提取本局开始时间作为版本号传到定时器内
            $beginTime = $redis->hGet($resKey, $this->readyBegin);
            $redis->hDel($resKey, $this->owner, $this->other, $this->ownerReday, $this->otherReday, $this->timerId);

            // 返回时带上积分增减和广告图
            $infoDetail['owner_pk_point'] = (int)$ownerArr['roll_int'];
            $infoDetail['other_pk_point'] = (int)$otherArr['roll_int'];
            $infoDetail['ad_img'] = $adImg;

            // 延时1秒后通知最后的PK结果
            swoole_timer_after(1000, function () use ($roomNo, $infoDetail, $ownerUid, $otherUid, $resKey, $beginTime) {
                $encode_ownerUid = $this->enCodeId($ownerUid,'uid');
                $this->setMass($roomNo)->send(['event'=>'resultCall', 'owner_uid'=>$encode_ownerUid, 'final_result'=>$infoDetail ]);
                // 判断参与者是否在10秒内准备
                swoole_timer_after(1000 * $this->waitTime, function() use ($otherUid, $resKey, $encode_ownerUid, $beginTime) {
                    $redis = $this->getRedis();
                    $otherReady = $redis->hGet($resKey, $this->otherReday);
                    $nowReadyTime = $redis->hGet($resKey, $this->readyBegin);
                    // 判断redis记录的开局时间与设置定时器时传递的时间是否相等，以此判断是否同一局，不同局不执行
                    if($nowReadyTime != $beginTime) {
                        return;
                    }
                    if($otherReady != $otherUid) {
                        self::contextSetData('other_uid',$otherUid);
                        $this->onLeavePkRoom(['owner_uid' => $encode_ownerUid ]);// 10秒内准备，踢出房间
                    }
                });
            });
        }
    }


    /**
     * 当前用户离开单个或者多个房间
     * @param $data
     */
    public function onLeavePkRoom($data)
    {
        $encode_ownerUid = $data['owner_uid'];// 房主UID
        if(empty($encode_ownerUid)) {
            return $this->setError('邀请人参数缺失')->send(['event'=>'leavePkRoomCall']);
        }
        $owner_uid = $this->deCodeId($encode_ownerUid,'uid');
        $info = $this->getRoomByUid($owner_uid);
        if(empty($info['room_no'])){
            return $this->setError('数据错误')->send(['event'=>'leavePkRoomCall']);
        }

        $uid = self::contextGetData('other_uid') ?? $this->getUid();

        $redis = $this->getRedis();

        // 房间结果/准备
        $resKey = $this->pkResKey . $owner_uid;

        // 双方都有准备游戏时
        $readyArr = $redis->hMGet($resKey, [$this->ownerReday, $this->otherReday]);
        if(!empty($readyArr[$this->ownerReday]) && !empty($readyArr[$this->otherReday])) {
            return $this->setError('游戏进行中请勿离开')->send(['event'=>'leavePkRoomCall']);
        }

        //判断是房主还是普通用户
        if($uid==$owner_uid){
            $this->setMass($info['room_no'])->setSuccess('房主已离开房间，游戏没法进行了哦')->send(['event'=>'leavePkRoomCall','which_uid'=>$encode_ownerUid, 'type'=>$data['type']??'']);

            $this->websocket->leaveRoomByUid($uid, $info['room_no']);
            //删除房主的房间
            $this->delRoomByUid($owner_uid,$info['room_no']);
            // 删除整个房间的准备态
            $redis->del($resKey);
        }else{

            $this->setMass($info['room_no'])->setSuccess('您的小伙伴已经离开房间了')->send(['event'=>'leavePkRoomCall','which_uid'=>$this->enCodeId($uid,'uid')]);

            $this->websocket->leaveRoomByUid($uid, $info['room_no']);

            // 删除参与者准备态和上一局的开局时间
            $redis->hDel($resKey, $this->otherReday);
            $redis->hDel($resKey, $this->readyBegin);
            $redis->hDel($resKey, $this->timerId);
        }

        // 清空房间队列
        $key = $this->quekey . $info['room_no'];
        $redis->del($key);
    }


    /**关闭某个链接
     * @param null $fd //关闭指定客户端连接，参数为fd，默认为当前链接
     * @return bool
     */
    protected function close($fd = null)
    {
        if (empty($fd)) {
            $fd = $this->getCurrentSenderFd();
        }
        //获取fd对应的uid
        $uid = $this->getUidByFd($fd);
        echo '用户：'.$uid.'其fd：'.$fd.'离开了'.PHP_EOL;

        $this->setOnLineTimeByUid($uid, '-'.time());//设置下线时间点
        $roomObj = $this->websocket->getTableRoom();

        //5分钟后进行删除用户信息
        swoole_timer_after(1000*60*5,function () use ($roomObj, $uid,$fd){
            //用户离开页面，将 table 的uid和fd 关系删除
            if($uid!=false){
                $online_time =  $this->getOnLineTimeByUid($uid);
                if($online_time<=0){
                    //限制的时间范围过后有重新上线时
                    return;
                }
                echo '删除用户在线数据';
                $rooms = $roomObj->getRooms($uid);//获取uid有加入的房间集合

                // 删除创建房间时创建的PK相关的redis
                $redis = $this->getRedis();
                $resKey = $this->pkResKey . $uid;
                $redis->del($resKey);// 该key以房主UID为后缀，如果不是房主，不会有key，但执行删除不会有影响

                //@todo 是否是房主是的话删除房间
                if($uid){//
                    // $this->delRoomByUid($uid,$rooms);
                }else{
                    $this->websocket->leaveRoomByUid($uid, $rooms);
                }

                $this->websocket->getTableUser()->deleteFdInfoByUid($uid);
            }

            $this->websocket->getTableUser()->deleteUidInfoByFd($fd);
        });

        //关闭指定客户端连接，参数为fd，默认为当前链接
        return $this->websocket->close(intval($fd));
    }

    
}
