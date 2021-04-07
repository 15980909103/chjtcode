<?php
declare (strict_types = 1);

namespace app\websocket;
use app\common\base\WebsocketBase;
use app\common\manage\TaskManage;
use app\common\pool\RedisPool;
use app\common\traits\TraitContext;
use app\server\index\Activity;
use app\server\index\BobRule;
use app\server\index\BoMessageLog;
use app\server\index\BoMessageRoom;
use app\server\index\BoRes;
use app\server\index\PointsMall;
use app\server\user\User;
use app\task\Message;
use Swoole\Server;
use think\App;
use think\Container;
use Exception;
use think\facade\Cache;
use think\swoole\Websocket;
use Throwable;

/**
 * swoole的websocket的订阅事件监听处理
 * Class MyWebsocket
 * @package app\common\listens
 */
class Bobing
{

    protected $quekey     = 'bobpkroomkey';
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

    public $singleTime = 15;// 单局时长
    public $readyTime = 6;// 预备时长
    public $waitTime = 10;// 第二局等待准备时长
    public $ownerReday = "owner_ready";
    public $otherReday = "other_ready";
    public $readyBegin = "ready_begin";
    public $owner = "owner";
    public $other = "other";
    public $timerId = "timer_id";

    protected $autoNum = ['3', '3', '3', '2', '5', '6'];// 未博饼默认结果

    protected $pkDefaultPoint = ['win' => 0, 'lose' => 0, 'level' => 0];// PK默认分数


    use TraitWebSocket;
    use TraitContext;


    public function __construct(Container $container, Server $server)
    {
        $this->initBase($container,$server);
    }
    /**
     * @return \Redis|void
     */
    protected function getRedis($config = []){
        if(empty($config)) {
            $config = [
                'database'=>8,
                'max_active'=> 2
            ];
        }
        $redisObj = RedisPool::getInstance();
        $config = $redisObj->setConfig('swoole.pool.redis', $config);

        return $redisObj->init($config);
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
    //当前登录用户对应的用于标识用户身份 由$userId.'!'.$merchantId组成
    protected function getUid($uid = null){
        if(!is_null($uid)){
            self::contextSetData('uid',$uid);
        }else{
            $uid = self::contextGetData('uid');
        }

        return $uid;
    }
    //当前登录用户对应的fd信息, $fdInfo为此次登录的fd信息
    protected function getMyFdInfo($fdInfo = null){
        if(!is_null($fdInfo)){
            self::contextSetData('fdInfo',$fdInfo);
        }else{
            $fdInfo = self::contextGetData('fdInfo');
        }

        return $fdInfo;
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
     */
    public function onBeforeConnect($data){
        if(empty($data['token'])){
            return false;
        }
        //print_r($data['token']);
        $userinfo = $this->getUserInCache($data['token']);
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
            ],function ($rs){
                // 重置fd和room的关系
                if($rs['store_fdKeyInfo']['isLocal']==1){
                    $rooms = $this->websocket->getTableRoom()->getRooms($rs['uid']);
                    $this->websocket->getTableRoom()->resetFdInRoom($rs['fd'], $rs['store_fdKeyInfo']['fd'], $rooms);
                }
            });
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

    //     // // 绕过验证，模拟测试用
    //    if(!empty($data['ttt'])){
    //        Cache::store('redis')->set($data['ttt']['token'],json_encode([
    //            'merch_id' => $merch_id,
    //            'activity_id' => $activity_id,
    //            'id'=> $data['ttt']['user_id'],
    //            'nickname'=> $data['ttt']['nickname'],
    //            'headimgurl'=> $data['ttt']['headimgurl'],
    //        ]));
    //        $this->onBeforeConnect([
    //            'token'=> $data['ttt']['token']
    //        ]);
    //    }

        
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
        if(strtolower($data['event'])!='leavepkroom'){
            $activity = $this->checkActivity($activity_id);
            if(empty($activity)) {
                return false;
            }
        }

        $this->getUid($loginInfo['uid']);
        $this->getMyFdInfo($loginInfo);
        //$this->setOnLineTimeByUid($loginInfo['uid'], time());//设置上线时间点
        return true;
    }
    //============ 前置操作 end ============//

    /**
     * 获取某个uid创建的房间的信息
     * @param $uid
     * @return array|bool
     */
    private function getRoomByUid($uid){
        $uid = strval($uid);
        $info = $this->websocket->getTableUser()->getFdInfoByUid($uid,['room_no','in_background']);

        if(empty($info['room_no'])){//为空时即销毁了之前创建的房间信息
            return false;
        }

        $info['in_background'] = !isset($info['in_background'])? $info['in_background']:0;
        return $info;
    }
    /**
     * 存储某个uid创建的房间
     * @param $uid
     * @param $data
     */
    private function setRoomByUid($uid,$data){
        $uid = strval($uid);
        if(isset($data['in_background'])){
            $indata['in_background'] = $data['in_background'];
        }
        if(!empty($data['room_no'])){
            $indata['room_no'] = $data['room_no'];
        }

        $this->websocket->getTableUser()->setFdInfoByUid($uid, $indata);
    }

    /**
     * 删除某个uid创建的房间
     * @param $uid
     */
    private function delRoomByUid($uid,$room_no){
        //解散房间里面的人员
        $info = $this->getRoomByUid($uid);
        //置空房间信息
        if($room_no==$info['room_no']){//当操作的是最新的房间信息时
            // $fds = $this->getClientFdsByRoomNo($info['room_no']);

            $this->websocket->getTableUser()->setFdInfoByUid($uid, ['room_no'=>'','in_background'=>0]);
        }else{
            // $fds = $this->getClientFdsByRoomNo($room_no);
        }

        return TRUE;
    }

    /**
     * 获取房间成员的fd
     * @return mixed
     */
    private function getClientFdsByRoomNo($room_no){
       return $this->websocket->getTableRoom()->getClients($room_no);
    }

    /**
     * 获取成员所加入的房间集
     * @return mixed
     */
    private function getRoomsByUid($uid){
        return $this->websocket->getTableRoom()->getRooms($uid);
     }


    //======房间队列操作 start=======//
    /**
     * 进入房间等待池进行人员队列
     * @param $room_no //房间号
     * @param $uid //小伙伴的uid标识
     */
    private function roomWaitUidQueue_push($room_no,$uid){
        $key = $this->quekey . $room_no;
        $rs =  $this->getRedis()->rpush($key, $uid);
        $this->getRedis()->lTrim($key,0, 1);

        //设置redis队列中房间key的有效时间
        $this->getRedis()->expire($key,intval(3600*3.2));

        echo '加入等待房间'.$key.'int:'.$rs.PHP_EOL;
        return $rs;
    }
    private function roomWaitUidQueue_len($room_no){
        $key = $this->quekey . $room_no;
        return $this->getRedis()->lLen($key);
    }
    /**
     * 删除redis中等待的房间
     * @param $room_no
     * @return int
     */
    private function roomWaitUidQueue_del($room_no){//在定时器中需新开redis连接
        $redisObj = RedisPool::getInstance();
        $config = $redisObj->setConfig('swoole.pool.redis',[
            'database'=>8,
            'max_active'=> 2
        ]);
        $redis = $redisObj->init($config,false);

        $key = $this->quekey . $room_no;
        return $redis->del($key);
    }
    private function roomWaitUidQueueList($room_no){
        $key = $this->quekey . $room_no;
        return $this->getRedis()->lRange($key, 0, -1 );
    }
    /**
     * 获取房间内的参与者
     */
    private function getRoomMember($room_no){
        $key = $this->quekey . $room_no;
        return $this->getRedis()->lIndex($key, 0);
    }
    //======房间队列操作 end=======//

    /**
     * 房主挂起当前房间等待
     */
    public function onMyPkRoomInBackGround(){
        $uid = $this->getUid();
        $has = $this->getRoomByUid($uid);
        if($has!==false){
            $this->setRoomByUid($uid,[
                'in_background'=> 1 //挂起等待
            ]);
            $this->send(['in_background'=> 1],'myPkRoomInBackGroundCall');
        }else{
            $this->setError('您没有创建相应房间，不可挂起等待')->send(['event'=>'readyCall']);
        }
    }
    /**
     * pk房间单独处理房间的创建
     * @param $data
     */
    public function onMyPkRoom($data)
    {
        try {
            $uid = $this->getUid();
            $merch_id = $this->getMerchantId();
            $user_id = $this->getUserId();

            //积分检测
            $canScore = $this->checkScoreInActivity([
                'merch_id'=>$merch_id,
                'user_id'=>$user_id
            ]);
            if($canScore==false){
                return;
            }

            $has = $this->getRoomByUid($uid); //是否创建房间了

            $indata['in_background'] = 0;//是否挂起等待
            if($has === false){//未创建
                $mod = ($this->getUserId())%1000;
                $indata['room_no'] = $this->createRoomNo($mod);
            }else{
                $indata['room_no'] = $has['room_no'];
            }

            $this->setRoomByUid($uid,$indata);
            $this->joinRoom($indata['room_no']);

            // $encode_ownerUid = $this->getUid();
            $encode_ownerUid = $this->enCodeId($this->getUid(),'uid');
            // 改变玩家准备状态
            $param = [
                'owner_uid' => $encode_ownerUid,
                'room_no' => $indata['room_no'],
            ];
            $resReady = $this->onReady($param);
            if(!$resReady) {
                return false;
            }

            $this->send(['room_no' => $indata['room_no'], 'msg' => '创建pk房间成功', 'owner_uid'=> $encode_ownerUid,
                'event'=>'myPkRoomCall',
                'test'=>[
                    'room_no' => $indata['room_no'],
                    'fd'=>$this->getCurrentSenderFd(),
                    'encodeuid'=>$encode_ownerUid,
                    'uid'=>$this->deCodeId($encode_ownerUid,'uid'),
                    'pid'=>getMyPid()
                ]
            ]);
        }catch (Throwable $t){
            $this->setError('抱歉，数据错误请重新登录')->send([],'loginFail');
        }
    }

    /**
     * PK次数检测
     * 房主UID（ownerUid）、当前用户UID（myUid）
     * 房主只检测总次数
     * 参与者检测总次数和双方之间的次数
     */
    protected function checkPkNum($param)
    {
        if(empty($param['activity_id']) || empty($param['ownerUid']) || empty($param['myUid'])) {
            $this->setError('PK次数检测异常')->send(['event'=>'checkPkNumCall']);
            return false;
        }

        $activityId = $param['activity_id'];

        $ownerUid = $param['ownerUid'];
        $myUid = $param['myUid'];

        $today = date('ymd');

        $redisKeyTotal = $this->pkNumTotal . "{$today}";// 当前用户的总次数
        $fieldTotal = "{$activityId}-{$myUid}";// 活动ID-当前用户UID

        // 当前用户不是房主，即是参与者
        if($ownerUid != $myUid) {
            $isJoin = TRUE;// 是否要检测双方的次数
            $redisKeyJoin = $this->pkNumJoin . "{$today}";
            // field字段 将传入的两个UID用sort排序，以活动ID为前缀，用横杠将两个排序后的ID依次连接
            $uidArr[] = $ownerUid;
            $uidArr[] = $myUid;
            sort($uidArr);
            $fieldJoin = "{$activityId}-{$uidArr[0]}-{$uidArr[1]}";
        }

        // 获取活动信息
        $activityInfo = $this->checkActivity($activityId);
        if(empty($activityInfo)) {
            return FALSE;
        }

        $redis = $this->getRedis();
        
        // 当前用户总次数
        $pkNumTotal = $redis->hGet($redisKeyTotal, $fieldTotal);
        if(false === $pkNumTotal) {
            $pkNumTotal = 0;
        }

        if($pkNumTotal >= $activityInfo['total_pk_number']) {
            $this->setError('您的PK次数已经用完')->send(['event'=>'checkPkNumCall']);
            return FALSE;
        }
        // 检测双方之间的次数
        if(!empty($isJoin)) {
            $pkNumJoin = $redis->hGet($redisKeyJoin, $fieldJoin);
            if(false === $pkNumJoin) {
                $pkNumJoin = 0;
            }

            if($pkNumJoin >= $activityInfo['pk_number']) {
                $this->setError('您与房主的PK次数已经用完')->send(['event'=>'checkPkNumCall']);
                return FALSE;
            }
        }

        return TRUE;
    }

    /**
     * PK次数累计
     */
    protected function setPkNum($param)
    {
        if(empty($param['activity_id']) || empty($param['ownerUid']) || empty($param['otherUid'])) {
            $this->setError('PK次数计算异常')->send(['event'=>'checkPkNumCall']);
            return false;
        }

        $activityId = $param['activity_id'];

        $ownerUid = $param['ownerUid'];
        $otherUid = $param['otherUid'];

        $today = date('ymd');

        // 总次数
        $redisKeyTotal = $this->pkNumTotal . "{$today}";
        $fieldTotalOwner = "{$activityId}-{$ownerUid}";// 活动ID-房主UID
        $fieldTotalOther = "{$activityId}-{$otherUid}";// 活动ID-参与者UID

        // 双方之间的次数
        $redisKeyJoin = $this->pkNumJoin . "{$today}";
        // field字段 将传入的两个UID用sort排序，以活动ID为前缀，用横杠将两个排序后的ID依次连接
        $uidArr[] = $ownerUid;
        $uidArr[] = $otherUid;
        sort($uidArr);
        $fieldJoin = "{$activityId}-{$uidArr[0]}-{$uidArr[1]}";

        $redis = $this->getRedis();

        // 第二天零点时间戳
        $outTime = strtotime(date('Y-m-d', strtotime("+1 day")));

        // 增加总次数
        $isExistsTotal = $redis->exists($redisKeyTotal);
        $redis->hIncrBy($redisKeyTotal, $fieldTotalOwner, 1);// 房主
        $redis->hIncrBy($redisKeyTotal, $fieldTotalOther, 1);// 参与者
        if(!$isExistsTotal) {
            // 设置过期时间
            $redis->expireAt($redisKeyTotal, $outTime);
        }

        // 增加双方间的次数
        $isExistsJoin = $redis->exists($redisKeyJoin);// key是否存在
        $redis->hIncrBy($redisKeyJoin, $fieldJoin, 1);
        if(!$isExistsJoin) {// 原先不存在key
            // 设置过期时间
            $redis->expireAt($redisKeyJoin, $outTime);
        }
    }

    /**
     * 积分检测
     * @param $data
     * @return bool
     */
    protected function checkScoreInActivity($data){
        // 获取分数
        $pointServer = new PointsMall();
        $activityId = $this->getActivityId();
        $activity = $this->checkActivity($activityId);
        if(empty($activity)) {
            return false;
        }

        // 胜负所得积分
        $pkPoint = json_decode((string)$activity['pk_point'], TRUE);

        if(!isset($pkPoint['lose'])) {
            $this->setError('积分配置异常')->send(['event'=>'readyCall','dd'=>$activityId]);
            return false;
        }
        $losePoint = $pkPoint['lose'];

        $score = $pointServer->userIntegral($activityId, $data['merch_id'], $data['user_id']);
        if($score < $losePoint) {
            $this->setError('抱歉，您的积分不足，不可以参与')->send(['event'=>'readyCall','dd'=>$activityId]);
            return false;
        }
        return true;
    }

    /**
     * 朋友加入pk 房间
     * @param $data
     */
    public function onJoinPK($data)
    {
        try {
            $merch_id = $this->getMerchantId();
            $user_id = $this->getUserId();
            $uid = $this->getUid();
            $encode_ownerUid = $data['owner_uid'];
            if(empty($encode_ownerUid)) {
                return $this->setError('邀请人参数缺失')->send(['event'=>'readyCall']);
            }
            $owner_uid = $this->deCodeId($encode_ownerUid,'uid');
            //防止自己加入自己的房间
            if($owner_uid==$uid){
                return $this->setError('不可以加入自己的房间')->send(['event'=>'readyCall']);
            }

            //判断邀请人是否在线
            $is_online = $this->isOnLineByUid($owner_uid);
            if($is_online===false){
                return $this->setError('邀请人目前不在线不可加入')->send(['event'=>'readyCall']);
            }
            //房间信息是否存在
            $roominfo = $this->getRoomByUid($owner_uid);
            if(empty($roominfo['room_no'])){
                return $this->setError('房间已解散')->send(['event'=>'readyCall']);
            }

            //是否自己也创建了房间在等待他人，先退出自己创建的房间
            $mine = $this->getRoomByUid($uid);
            if($mine!==false){
                return $this->setError('请先退出自己创建的房间')->send(['event'=>'joinpkleaveCall']);
                //    $leaveParam['owner_uid'] = $this->enCodeId($uid, 'uid');
                //     $this->onLeavePkRoom($leaveParam);
            }
            //积分是否足够
            $canScore = $this->checkScoreInActivity([
                'merch_id'=>$merch_id,
                'user_id'=>$user_id
            ]);
            if($canScore==false){
                return;
            }

            //判断要加入的pk房间是否已经满了
            $len = $this->roomWaitUidQueue_len($roominfo['room_no']);
            echo PHP_EOL.'房间人数现在:'.$len.PHP_EOL;
            if($len>=1){
                // $roomMember = $this->getClientFdsByRoomNo($roominfo['room_no']);
                $rooms = $this->getRoomsByUid($roominfo['room_no']);
                if(in_array($roominfo['room_no'], $rooms)) {
                    $param = [
                        'owner_uid' => $encode_ownerUid,
                        'room_no' => $roominfo['room_no'],
                    ];
                    $resReady = $this->onReady($param);
                    return;
                } else {
                    return $this->setError('该房间人数已满了')->send(['event'=>'readyCall',/* '$roomMember'=>$roomMember, */'room_no'=>$roominfo['room_no']]);
                }
            }

            //进入房间等待池进行队列
            $rs = $this->roomWaitUidQueue_push($roominfo['room_no'],$this->getUid());
            if($rs>1){
                return $this->setError('该房间人数已满了')->send(['event'=>'readyCall']);
            }
            //将小伙伴加入房间
            $this->joinRoom($roominfo['room_no']);

            // 改变玩家准备状态
            $param = [
                'owner_uid' => $encode_ownerUid,
                'room_no' => $roominfo['room_no'],
            ];
            $resReady = $this->onReady($param);
            if(!$resReady) {
                return false;
            }
        }catch (Throwable $t){
            $this->setError('抱歉，数据错误请重新登录')->send([],'loginFail');
        }
    }

    /**
     * 获取房间内双方的头像昵称
     */
    protected function getRoomNameAndImg($ownerUid, $otherUid)
    {
        $info = [];
        // 房主
        $ownerToken = $this->getLoginTokenByUid($ownerUid);
        $ownerInfo = $this->getUserInCache($ownerToken);
        $info['owner_name'] = $ownerInfo['nickname'] ?? "";
        $info['owner_img'] = $ownerInfo['headimgurl'] ?? "";
        $info['owner_uid'] = $ownerUid?$this->enCodeId($ownerUid,'uid'):'';

        // 参与者
        $otherToken = $this->getLoginTokenByUid($otherUid);
        $otherInfo = $this->getUserInCache($otherToken);
        $info['other_name'] = $otherInfo['nickname'] ?? "";
        $info['other_img'] = $otherInfo['headimgurl'] ?? "";
        $info['other_uid'] = $otherUid?$this->enCodeId($otherUid,'uid'):'';
        return $info;
    }

    /**
     * 双方准备状态
     */
    public function onReady($data)
    {
        try {
            $encode_ownerUid = $data['owner_uid'];// 房主UID
            if(empty($encode_ownerUid)) {
                return $this->setError('邀请人参数缺失')->send(['event'=>'readyCall']);
            }
            $ownerUid = $this->deCodeId($encode_ownerUid,'uid');

            $currentUid = $this->getUid();// 调用者UID
            $userId = $this->getUserId();// 调用者ID
            $merchId = $this->getMerchantId();// 商户ID
            $activityId = $this->getActivityId();// 活动ID

            $roominfo = $this->getRoomByUid($ownerUid);
            if(empty($roominfo['room_no'])){
                $this->setError('房间数据错误2')->send(['event'=>'readyCall' ]);
                return false;
            }
            $roomNo = $roominfo['room_no'];

            // 活动配置
            $activity = $this->checkActivity($activityId);
            if(empty($activity)) {
                return false;
            }

            // 判断次数
            $canNum = $this->checkPkNum([
                'activity_id' => $activityId,
                'ownerUid' => $ownerUid,
                'myUid' => $currentUid,
            ]);
            if($canNum==false){
                return;
            }

            $ownerReday = $this->ownerReday;
            $otherReday = $this->otherReday;

            // $roomMember = $this->getClientFdsByRoomNo($roomNo);// 获取房间内的成员
            $rooms = $this->getRoomsByUid($currentUid);// 获取加入的房间集

            $redis = $this->getRedis();
            $resKey = $this->pkResKey . $ownerUid;

            if($ownerUid == $currentUid) {
                $field = $ownerReday;// 房主是否准备
            } else {
                // 不是房主时，房间里要有两个人才能准备
                $roomLen = $this->roomWaitUidQueue_len($roomNo);
                // $roomMember = $this->getClientFdsByRoomNo($roomNo);
                if($roomLen < 1) {
                    $this->setError('房间信息有误')->send(['event'=>'readyCall']);
                    return false;
                }
                // 判断当前玩家是否在房间内
                if(!in_array($roomNo, $rooms)) {
                    $this->setError('房间信息有误')->send(['event'=>'readyCall']);
                    return false;
                }

                // 超出准备时间不允许继续准备
                $readyBegin = $redis->hGet($resKey, $this->readyBegin);
                if(!empty($readyBegin)) {
                    $outTime =  $readyBegin + $this->readyTime + $this->singleTime + $this->waitTime - 1;
                    if($outTime<=time()) {
                        $this->setError('已超过准备时间')->send(['event'=>'readyCall']);
                        return false;
                    }
                }

                $field = $otherReday;// 参与者是否准备
            }


            // $redis = $this->getRedis();
            // $resKey = $this->pkResKey . $ownerUid;

            // 玩家准备
            $redis->hSet($resKey, $field, $currentUid);// 记录UID，作为准备态

            // 获取双方头像昵称
            // $otherUid = '0!0';// 参与者UID
            // if(!empty($roomMember)) {
            //     foreach($roomMember as $v) {
            //         if($ownerUid != $v) {
            //             $otherUid = $v;
            //         }
            //     }
            // }
            $otherUid = $this->getRoomMember($roomNo);
            if(empty($otherUid)) {
                $otherUid = '';
            }

            // 查看双方是否都准备
            $readyArr = $redis->hGetAll($resKey);
            if(!empty($readyArr[$ownerReday]) && !empty($readyArr[$otherReday])) {// 双方已准备
                $userInfo = $this->getRoomNameAndImg($ownerUid, $otherUid);

                if(!empty($readyArr[$this->timerId])) {// 存在之前的游戏自动结算延时器，返回之前的数据状态
                    if(!empty($readyArr[$this->owner])){
                        $userInfo['owner_result'] = json_decode($readyArr[$this->owner], true) ?? '';
                    }
                    if(!empty($readyArr[$this->other])){
                        $userInfo['other_result'] = json_decode($readyArr[$this->other], true) ?? '';
                    }

                    $this->setSuccess('游戏开始时间')->send(['event'=>'readyCall', 'owner_uid'=>$encode_ownerUid, 'readyBegin' => $readyArr[$this->readyBegin], 'singleTime'=>$this->singleTime, 'user_info' => $userInfo]);
                    return true;
                }
                // 写入游戏开始时间
                $nowTime = time();
                $redis->hSet($resKey, $this->readyBegin, $nowTime);
                // // 设置key的过期时间
                // $timeOut = $this->readyTime + $this->singleTime + $this->waitTime + 2;// 预备时间 + 单局时间 + 等待再来一局时间 + 延迟时间（避免定时器获取不到）
                // $redis->expire($resKey, $timeOut);
                // 游戏自动结算延时器
                $timeAfter = ($this->readyTime + $this->singleTime) * 1000;
                $timerId = swoole_timer_after($timeAfter, function() use ($redis, $resKey, $roomNo, $activity, $nowTime, $merchId) {
                    $owner = $this->owner;// 房主结果
                    $other = $this->other;// 参与者结果
                    $ownerReady = $this->ownerReday;// 房主UID
                    $otherReday = $this->otherReday;// 参与者UID
                    $resArr = $redis->hMGet($resKey, [$owner, $other, $ownerReady, $otherReday, $this->readyBegin]);
                    // 判断redis记录的开局时间与设置定时器时传递的时间是否相等，以此判断是否同一局，不同局不执行
                    if(!empty($resArr[$this->readyBegin]) && $nowTime != $resArr[$this->readyBegin]) {
                        return;
                    }
                    // 双方都已经准备，且至少有一方没有结果
                    if((empty($resArr[$owner]) || empty($resArr[$other])) && (!empty($resArr[$ownerReady]) && !empty($resArr[$otherReday]))) {
                        // 模拟结果
                        $resMoni = [
                            'rollVal' => 0,
                            'rollNum' => $this->autoNum,
                            'rollStr' => "啥都没有",
                            'auto' => 1,// 未博饼标记
                        ];
                        if(empty($resArr[$owner])) {
                            $resMoni['uid'] = $resArr[$ownerReady];// 玩家准备好时，存入的是UID，此时可直接取用
                            $redis->hSet($resKey, $owner, json_encode($resMoni));
                        }
                        if(empty($resArr[$other])) {
                            $resMoni['uid'] = $resArr[$otherReday];// 玩家准备好时，存入的是UID，此时可直接取用
                            $redis->hSet($resKey, $other, json_encode($resMoni));
                        }
                        // 活动配置
                        $data = [
                            'res_key' => $resKey,
                            'room_no' => $roomNo,
                            'activity_id' => $activity['id'],
                            'pk_point' => $activity['pk_point'],
                            'activity_end_time' => $activity['activity_end_time'] ?? 0,
                            'bo_server' => null,
                            'merch_id' => $merchId,
                        ];
                        $this->dealPkResult($data);// 处理结果
                    }
                });

                $this->setMass($roomNo)->setSuccess('开始pk吧老铁')->send(['event'=>'readyCall', '$timerId'=>$timerId, 'owner_uid'=>$encode_ownerUid, 'singleTime'=>$this->singleTime, 'readyBegin' => $nowTime ,'user_info' => $userInfo]);
                // 写入定时器ID
                //$redis->hSet($resKey, $this->timerId, $timerId);
            }

            return true;
        }catch (Throwable $t){
            $this->setError('抱歉，数据错误请重新登录')->send([],'loginFail');
            return false;
        }
    }

    // PK结果处理
    protected function dealPkResult($data)
    {

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
            /*$timerId = (int)$redis->hGet($resKey, $this->timerId);
            if(!empty($timerId)) {
               swoole_timer_clear($timerId);
            }*/
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
     * 游戏操作
     * @param $data
     */
    public function onReadyGo($data){
        try {
            $encode_ownerUid = $data['owner_uid'];// 房主UID
            if(empty($encode_ownerUid)) {
                return $this->setError('邀请人参数缺失')->send(['event'=>'readyCall']);
            }
            $ownerUid = $this->deCodeId($encode_ownerUid,'uid');

            $currentUid = $this->getUid();// 调用者UID
            $param['user_id'] = $this->getUserId();// 当前用户ID
            $param['merch_id'] = $this->getMerchantId();// 当前用户所属商户ID
            $param['activity_id'] = $this->getActivityId();// 活动ID
            $param['type'] = 2;// PK类型

            // 通过房主找房间
            $roominfo = $this->getRoomByUid($ownerUid);
            if(empty($roominfo['room_no'])){
                return $this->setError('房间数据错误1')->send(['event'=>'resultCall']);
            }

            // 通过房间队列确认是否已有两人
            $roomLen = $this->roomWaitUidQueue_len($roominfo['room_no']);
            // $roomMember = $this->getClientFdsByRoomNo($roominfo['room_no']);
            if($roomLen < 1) {
                return $this->setError('房间人数不足')->send(['event'=>'resultCall']);
            }

            $resKey = $this->pkResKey . $ownerUid;// 结果存储的RedisKey
            $redis = $this->getRedis();

            // 查看游戏开始时间，超出本局游戏时间不予执行
            $beginTime = $redis->hGet($resKey, $this->readyBegin);
            if(empty($beginTime)) {
                return $this->setError('游戏异常')->send(['event'=>'resultCall']);
            }
            $timeAllow = $beginTime + $this->readyTime + $this->singleTime - 1;
            if(time() > $timeAllow) {
                return;
            }

            // 判断双方玩家是否都已经准备好
            $ownerReday = $this->ownerReday;
            $otherReday = $this->otherReday;
            $readyArr = $redis->hMGet($resKey, [$ownerReday, $otherReday]);
            if(empty($readyArr[$ownerReday]) || empty($readyArr[$otherReday])) {
                return $this->setError('对方尚未准备')->send(['event'=>'resultCall']);
            }

            // 判断当前博饼玩家是否为准备态
            if($ownerUid == $currentUid) {
                $readyMember = $readyArr[$ownerReday];
            } else {
                $readyMember = $readyArr[$otherReday];
            }
            if($readyMember != $currentUid) {// 当记录的准备玩家的UID和当前博饼玩家的UID不符
                return $this->setError('您的准备状态异常')->send(['event'=>'resultCall']);
            }

            // 博饼结果使用哪个field记录
            if($ownerUid == $currentUid) {
                $field = $this->owner;
            } else {
                $field = $this->other;
            }
            $resCurrent = $redis->hExists($resKey, $field);
            if(false !== $resCurrent) {
                // 当局已有结果，不可重复请求
                return $this->setError('您已经博饼过，请等待PK结果')->send(['event'=>'resultCall']);
            }

            $boServer = new BoRes();
            $boRes = $boServer->doBo($param);
            if(isset($boRes['code']) && 0 == $boRes['code']) {
                // 出现异常情况
                $boMsg = $boRes['msg'] ?? "未知异常";
                return $this->setError("博饼出现异常：" . $boMsg)->send(['event'=>'resultCall']);
            }

            // 通知双方结果
            $boRes = $boRes['result'];
            $boRes['owner_uid'] = $ownerUid;
            $this->setMass($roominfo['room_no'])->send(['event'=>'resultCall', 'which_uid'=>$this->enCodeId($currentUid,'uid'), '$currentUid'=>$currentUid, 'result'=>$boRes ]);


            // 整理要存入Redis的数据
            $boSave = [
                'uid' => $currentUid,
                'rollVal' => $boRes['rollVal'],
                'rollNum' => $boRes['rollNum'],
                'rollStr' => $boRes['rollStr'],
            ];
            $redis->hSet($resKey, $field, json_encode($boSave));// 将结果存入Redis

            // 结果处理
            $param = [
                'res_key' => $resKey,
                'room_no' => $roominfo['room_no'],
                'activity_id' => $param['activity_id'],
                'activity_end_time' => $boRes['activity_end_time'],
                'pk_point' => $boRes['pk_point'],
                'bo_server' => $boServer,
                'ad_img' => $boRes['adImg'] ?? '',
                'merch_id' => $param['merch_id'] ?? 0,
            ];
            $this->dealPkResult($param);
        }catch (Throwable $t){
            $this->setError('抱歉，数据错误请重新登录')->send([],'loginFail');
            return false;
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


    /*
       * 加入聊天房间
       */
    public function onJoinMsg($data){
        $mch_id         =  $this->getMerchantId();
        $token          =  $this->getLoginTokenByUid($this->getUid());
        $info           =  $this->getUserInCache($token);
        if(!$mch_id && !$token){
            return false;
        }
        $mgs_room_no = 'system_msgroom'.$mch_id.$this->getActivityId();
        $this->joinRoom($mgs_room_no);
        $this->setMass($mgs_room_no)
            ->setSuccess('欢迎'.$info['nickname'].'加入群聊')
            ->send([
                    'id'            =>$this->getOnlyId($mgs_room_no,false),
                    'user_name'     =>$info['nickname'],
                    'headimgurl'    =>$info['headimgurl'],
                    'time'          =>date('m:s',time()),
                    'event'         =>'joinmsg'
                ]
            );

    }

    /**
     * 聊天房间聊天
     * @param $data
     */
    public function onSendMsg($data)
    {
        try {
            $this->setSuccess('1发起聊天')->send();

            $mch_id         = $this->getMerchantId();
            $token          =  $this->getLoginTokenByUid($this->getUid());
            $info           =  $this->getUserInCache($token);
            $type           = $data['type'];
            if(!$mch_id){
                return false;
            }
            $mgs_room_no = 'system_msgroom'.$mch_id.$this->getActivityId();
            $user_id = $this->getUserId() ;
            $context = $data['context'] ;
            if (empty($context)) {
                return $this->setError('聊天内容不能为空')->send();
            }
            //检测是否过长 //@todo 检测是否有非法内容对接微信
            if(mb_strlen($context)>150){
               return $this->setError('发送的内容过长 ')->send();
            };

//        $modle      =  new User((int)$mch_id);
//        $user_inof  =  $modle->getUserByid(['user_id'=>$user_id,'mch_id'=>$mch_id]);
            $order      =  $this->getOnlyId($mgs_room_no);
            $msg_arr = [
                'user_id'       => $user_id,
                'content'       => $context,
                'talker'        => $mgs_room_no,
                'activities_id' => $this->getActivityId(),
                'mch_id'        =>  $mch_id,
                'user_name'     =>  $info['nickname'],
                'headimgurl'     => $info['headimgurl'],
                'order'         => $order
            ];

            // echo "sendMsgEnd";
            $this->setMass($mgs_room_no)->send([
                'user_name'     =>$info['nickname'],
                'headimgurl'    =>$info['headimgurl'],
                'time'          =>date('H:i',time()),
                'event'         =>'SendMsg',
                'type'          => $type ??  1,
                'text'          => $context,
                'id'            => $order,
                'owner_uid'     => $this->enCodeId($this->getUid(),'uid')
            ]);

            //将记录信息 放到task 执行
            TaskManage::getInstance()->asyncPost($msg_arr, Message::class, function () {
                echo '3聊天记录已保存'.PHP_EOL;
            });
        }catch (Throwable $t){
            $this->setError('抱歉，数据错误请重新登录')->send([],'loginFail');
        }
    }

    /**
     * 测试卡死时是否还可以发送
     * @param $data
     */
    public function onSendTest($data)
    {
        try {
            $this->setSuccess('1发起聊天')->send();
            if($data['zz']!='asdqweasdzxc123123'){
                return;
            };
            $mch_id         = $this->getMerchantId();
            $mgs_room_no = 'system_msgroom'.$mch_id.$this->getActivityId();
            $order      =  $this->getOnlyId($mgs_room_no);
            $token          =  $this->getLoginTokenByUid($this->getUid());
            $info           =  $this->getUserInCache($token);
            if(mb_strlen($data['context'])>150){
                return $this->setError('发送的内容过长 ')->send();
            };

            $this->setMass($mgs_room_no)->send([
                'user_name'     =>$info['nickname'],
                'headimgurl'    =>$info['headimgurl'],
                'time'          =>date('H:i',time()),
                'event'         =>'SendMsg',
                'type'          => $type ??  1,
                'text'          => $data['context'],
                'id'            => $order,
                'owner_uid'     => $this->enCodeId($this->getUid(),'uid')
            ]);

        }catch (Throwable $t){
            $this->setError('抱歉，数据错误请重新登录')->send([],'loginFail');
        }
    }

    private function getOnlyId($keys,$type =true){
        $reids  = $this->getRedis();
        $key    = 'msg_only_id:'.$keys;
        if($type){
            $value  = $reids->incr($key);
        }else{
            $value  = $reids->get($key);
        }


        //redis 聊天记录修改为不存数据库，
//        if($value ==1){
//            $model = new  Activity();
//            $max_order = $model->getMsgOrderMax();
//            if(!empty($max_order) && $max_order>1){
//                $reids->set($key,$max_order);
//                $value = $max_order+1;
//            }
//
//
//        }
        return  $value;
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

trait TraitWebSocket{
    /**
     * @var WebsocketBase
     */
    public $websocket = null;
    /**
     * @var Server
     */
    public $server = null;

    public $table = null;


    /**
     * MyWebsocket constructor.
     * @param Container $container
     * @param Server $server
     */
    public function initBase(Container $container, Server $server){
        $this->server    = $server;

        $this->websocket = $container->make(WebsocketBase::class);
        $container->bind(Websocket::class,$this->websocket);//将框架原来的Websocket类替换为我们的

    }

    /**
     * id解密
     * @param string $val
     * @param string $field
     * @return array|int|mixed
     */
    protected function deCodeId($val = '',$field =''){
        // return $val;
        if($field == 'uid'){
            $val = hashids_decode($val,1);
            $val = implode('!',$val);
        }else{
            $val = hashids_decode($val);
        }
        return $val;
    }

    /**
     * id加密
     * @param string $val
     * @param string $field
     * @return int|string
     */
    protected function enCodeId($val = '',$field=''){
        // return $val;
        if($field=='uid'){
           $val = explode('!',$val);
           return $val = hashids_encode($val[0],$val[1]);
        }

        return hashids_encode($val);
    }

    /**
     * 生产房间号
     * @param string|int $mod 混进的取模值
     * @return string
     */
    protected function createRoomNo($mod = '')
    {
        if($mod!==''){
            str_pad(strval($mod),3,'0',STR_PAD_LEFT );
        }
        $no = 'pk' . date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).str_pad(strval(mt_rand(1, 99999)), 5, '0', STR_PAD_LEFT).$mod;
        return $no;
    }

    /**
     * 获取所有的Websocket链接
     * @return array
     */
    protected function getWebsocketConnections()
    {
        //echo "当前服务器共有 " . count($this->server->connections) . " 个连接\n";
        //echo PHP_EOL;

        //过滤非状态的 websocket_status 0不是，1连接等待握手，2正在握手，3握手成功连接
        return array_filter(iterator_to_array($this->server->connections), function ($fd) {
            return (bool)$this->server->getClientInfo($fd)['websocket_status'] ?? false;
        });
    }


    /**
     * 设置错误状态给客户端
     */
    protected function setError($msg ='')
    {
        self::contextSetData('status_msg',[
            'code' => '-1',
            'msg'=> $msg
        ]);
        return $this;
    }
    /**
     * 设置正确状态给客户端
     */
    protected function setSuccess($msg = 'success'){
        self::contextSetData('status_msg',[
            'code' => '1',
            'msg'=> $msg
        ]);
        return $this;
    }
    /**
     * 执行消息发送
     * @param array $data //要发送的数据
     * @param string $flagForWeb //前端识别的名称
     */
    protected function send($data = [], $flagForWeb = 'msgCallback')
    {
        $status_msg = self::contextGetData('status_msg');
        $status_msg = $status_msg??[
            'code' => '1',
            'msg'=> 'success'
        ];
        $status_msg['data'] = $data;
        $status_msg['fd'] = $this->getCurrentSenderFd();
        //重置为默认正确状态信息
        $this->setSuccess();

        $this->websocket->emit($flagForWeb, $status_msg);
    }

    /**
     * 设置广播群发，如果当时是由当前某个fd (getCurrentSenderFd) 触发广播则不会广播发送该人，如果需所有人员发送就在广播发送后在补上该fd的发送
     * $this->setBroadcast()->send(['msg'=>'广播信息-'.$this->getCurrentSenderFd().'上线了']);
     * @param bool|integer $flag //是否进行广播群发
     * @return $this
     */
    protected function setBroadcast($flag = true)
    {
        if ($flag) {
            $this->websocket->broadcast();
        }
        return $this;
    }

    /**
     * 设置指向进行群发
     * $this->setMass($target)->send(['msg'=>'欢迎来到']);
     * @param null $target //要发送的目标 integer, string, array //fd or room names
     * @param string $type
     * @return $this
     */
    protected function setMass($target = null,$type='room')
    {
        switch ($type){
            case 'room':
                $this->websocket->setToRoom($target);
                break;
            case 'uid':
                $this->websocket->setToUid($target);
                break;
            case 'fd':
                $this->websocket->setToFd($target);
                break;
        }

        return $this;
    }

    /**
     * 获取当前操作的客户端fd
     * @return mixed
     */
    protected function getCurrentSenderFd()
    {
        return $this->websocket->getSender();
    }

    /**
     * 设置当前要操作的客户端fd //用于指定人员对应的相关操作
     * 指定客户端发送，假设已知某一客户端连接fd为1 $this->setCurrentSenderFd(1)->send(['guangbo' => 1, 'getdata' => $event['asd']]);
     * @param int $val
     * @return $this
     */
    protected function setCurrentSenderFd(int $val)
    {
        $this->websocket->setSender($val);
        return $this;
    }

    /**
     * 加入房间
     * Join sender to multiple rooms.
     * @param string, array $rooms
     *
     * @return $this
     */
    protected function joinRoom($rooms){
        $uid = $this->getUid();

        $type = 1;
        if(strpos($rooms,'pk')!==false){
            $type = 0;
        }

        $this->websocket->joinRoomByUid($rooms, $uid, $type);
        return $this;
    }

    /**
     *  用户断开链接的回调
     */
    public function onClose()
    {
        $this->close($this->getCurrentSenderFd());
    }
    /**
     * 压缩字符串内容
     * @param string|array $data
     * @return false|string
     */
    protected function compressData($data){
        return gzcompress(json_encode($data,JSON_UNESCAPED_UNICODE));
    }
    /**
     * 解压字符串内容
     * @param string $data
     * @return false|string
     */
    protected function unCompressData($data = ''){
        return json_decode(gzuncompress($data),true);
    }

    /**
     * fd 关闭时的操作进行资源回收
     * @return mixed
     */
    abstract protected function close();
    /**
     * 前置校验是否可以往下执行监听方法
     * @param $data ['event','data']
     * @return bool true 可以往下执行 false不可往下执行
     */
    abstract function onCheckCanLoad($data);

}