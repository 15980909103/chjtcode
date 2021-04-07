<?php

namespace app\server\notify;


use app\common\base\ServerBase;
use app\common\lib\delayQueue\DelayQueue;
use app\common\lib\wxapi\module\WxH5;


class WxNotify extends ServerBase
{

    protected $stepTxt=[
        1=>'报备',
        2=>'带看',
        3=>'认购',
        4=>'业绩确认',
        5=>'开票',
        6=>'结佣',
    ];

    /**
     * 角色与权限
     */
    protected $RoleAuth = [
        /**
         * 与报备单的联系
         * self:自己 subordinate:下级店员 building:绑定的楼盘 subordinate-building：下级绑定的楼盘 create-store:创建的店铺 subordinate-store:下级绑定的店铺 create-building:创建的楼盘 city:城市 subordinate-work 找到下级有审批操作的
         */
        // 店员（经纪人）
        '0' => [
            'name'=> '店员',
            'duplicate'=> [], //抄送
            'examine' => [],//待处理
            'log'=>[
                1 => ['self'],
                2 => ['self'],
                3 => ['self'],
                4 => ['self'],
                5 => ['self'],
                6 => ['self'],
            ],//日志追加
            'add'=> [1],//添加操作 [报备]
        ],
        // 店长（经纪人）
        '1' => [
            'name'=> '店长',
            'duplicate'=> [
                1 => ['subordinate'],
                2 => ['subordinate'],
                3 => ['subordinate'],
                4 => ['subordinate'],
                5 => ['subordinate'],
                6 => ['subordinate'],
            ], //抄送
            'examine' => [],//待处理
            'log'=>[
                1 => ['self'],
                2 => ['self'],
                3 => ['self'],
                4 => ['self'],
                5 => ['self'],
                6 => ['self'],
            ],//日志追加
            'add'=> [1],//添加操作 [报备]
            'store_manager' => true  //标识店长
        ],
        // 项目经理（原项目经理）
        '2' => [
            'name'=> '项目经理',
            'duplicate'=> [], //抄送
            'examine' => [
                1 => ['building'],
                2 => ['building'],
                3 => ['building'],
            ],//待处理
            'log'=>[
                1 => ['building'],
                2 => ['building'],
                3 => ['building'],
            ],//日志追加
            'add'=> [],//添加操作 [报备]
        ],
        // 项目主管（原项目组长）
        '3' => [
            'name'=> '项目主管',
            'duplicate'=> [
                1 => ['subordinate-work'], //'subordinate-building', 下级绑定的店铺抄送和subordinate-work下级的审批抄送是互斥出现
                2 => ['subordinate-work'],
                3 => ['subordinate-work'],
            ], //抄送
            'examine' => [],//待处理
            'log'=>[],//日志追加
            'add'=> [],//添加操作 [报备]
        ],
        // 渠道专员（原渠道组员）
        '5' => [
            'name'=> '渠道专员',
            'duplicate'=> [
                1 => ['create-store'],
                2 => ['create-store'],
                3 => ['create-store'],
                4 => ['create-store'],
                /*5 => ['create-store'],
                6 => ['create-store'],*/ //目前业务流程上抄送的create-store和审批的create-store是同一个信息环节中只能互斥出现
            ], //抄送
            'examine' => [
                5 => ['create-store'],
                6 => ['create-store'],
            ],//待处理
            'log'=>[
                5 => ['create-store'],
                6 => ['create-store'],
            ],//日志追加
            'add'=> [],//添加操作 [报备]
        ],
        // 渠道总监（原渠道组长）
        '6' => [
            'name'=> '渠道总监',
            'duplicate'=> [
                1 => ['create-store'], //'subordinate-store' 下级的店铺抄送和下级审批店铺subordinate-work是页面上互斥出现
                2 => ['create-store'],
                3 => ['create-store'],
                4 => ['create-store'],
                5 => ['subordinate-work'],//subordinate-work 下级有审批操作后的抄送
                6 => ['subordinate-work'],
            ], //抄送
            'examine' => [
                5 => ['create-store'],//目前业务流程上抄送的create-store和审批的create-store是同一个信息环节中只能互斥出现
                6 => ['create-store'],
            ],//待处理
            'log'=>[
                5 => ['create-store'],
                6 => ['create-store'],
            ],//日志追加
            'add'=> [],//添加操作 [报备]
        ],
        // 项目总监（原项目负责人）
        '7' => [
            'name'=> '项目总监',
            'duplicate'=> [], //抄送
            'examine' => [
                4 => ['create-building']
            ],//待处理
            'log'=>[
                4 => ['create-building']
            ],//日志追加
            'add'=> [],//添加操作 [报备]
        ],
        // 总负责人（原区域负责人）
        /*'8' => [
            'name'=> '总负责人',
            'duplicate'=> [
                1 => ['city'],
                2 => ['city'],
                3 => ['city'],
                4 => ['city'],
                5 => ['city'],
                6 => ['city'],
            ], //抄送
            'examine' => [],//待处理
            'log'=>[],//日志追加
            'add'=> [],//添加操作 [报备]
        ],*/
    ];


    /**
     * 获取对应步骤中的抄送人和审批人和申请者的用户id
     * @param array $data
     * @param string $keyType //操作的类型 duplicate抄送，examine审批
     * @param array $info 申请单信息
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getSendOpenidsAndSaidsByStep($data=[], $keyType = 'duplicate', $info = []){
        if(empty($data['order_no'])){
            return $this->responseFail('抱歉，缺失参数');
        }
        if(empty($data['step'])){
            return $this->responseFail('抱歉，缺失参数');
        }
        $step = $data['step'];//当前操作的环节

        if(empty($info)){
            $info = $this->db->name('xcx_building_reported')
                ->alias('xr')
                ->field('xr.*,b.aid as building_aid,b.city,b.name as building_name,xa.agent_name,xa.agent_id,xa.agent_img,xa.agent_openid,xu.phone as agent_phone')
                ->join('xcx_building_building b','xr.building_id=b.id') //楼盘信息
                ->join('xcx_store_agent xa','xa.said=xr.said')     //申请者的一些信息
                ->join('xcx_agent_user xu','xa.agent_id=xu.id')
                ->where([
                    'xr.order_no' => $data['order_no']
                ])->find();
        }
        if(empty($info)){
            return $this->responseFail('抱歉，找不到该报备申请单');
        }
        //print_r($info);
        if($step==6&&$info['status_type']==6&&$info['examine_type']==2){//结佣完成了,虚拟出一个新步骤
            $step = 7;
        }

        //找出拥有该环节的角色
        $roles_auths = [
            'detail'=>[],
            'detail_roleIds'=>[],
            'roleIds'=>[],
            'step' => $step,
        ];

        //查找 $step 环节内的角色权限配置，$k为角色标识
        if(!($keyType == 'examine' && $step == 7)) {//非审批操作，排除审批已经完成了无后续审批了
            foreach ($this->RoleAuth as $k=>$item){
                if($step == 7){//流程已经完成了无后续审批了
                    $this->transforRoles( $keyType, $step-1,  $item, $k, $roles_auths); //虚拟的步骤回退
                }else{
                    $this->transforRoles( $keyType, $step,  $item, $k, $roles_auths); //转换出角色环节权限
                }
            }
        }

        switch ($keyType){
            case 'duplicate':
                //通知需要抄送的人员的openid集
                $res_openIds = $this->getDuplicateOpenidsOrSaids($roles_auths, $info)['result'];
                break;
            case 'examine':
                //通知审批权限人员的openid集
                $res_openIds = $this->getExamineOpenidsOrSaids($roles_auths, $info)['result'];
                break;
        }
        
        return $this->responseOk([
            //抄送者 //审批者
            'other'=> $res_openIds,
            //申请人员
            'apply'=>[
                'step' => $step,
                'info' => $info
            ]
        ]);
    }

    private function getAccessToken(){
        $res = doCoHttp([
            'http_type'=>'get',
            'url'=> 'http://mo.999house.com/index/public/getH5AcessToken',
            'data' => [
                'key' => 'hu7iKr5ERmxy7Bqc'
            ]
        ]);
        return !empty($res['body'])?$res['body']:'';
    }

    private function sendWxMsgTpl($data=[], $accessToken){
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$accessToken;
        if(empty($data['touser'])||empty($data['template_id'])||empty($data['url'])||empty($data['data'])){
            return $this->responseFail('参数缺失');
        }

        //$result = $this->curlPost($url,json_encode($data,JSON_UNESCAPED_UNICODE));
        $result = doCoHttp([
            'url' => $url,
            'data' => json_encode($data,JSON_UNESCAPED_UNICODE)
        ]);
        $result = json_decode($result, TRUE);

        $this->db->name('log')->insert([
            'title' => '进行微信推送',
            'content' => json_encode([
                '$result' => $result,
            ],JSON_UNESCAPED_UNICODE),
            'request' => '进行微信推送返回的结果',
            'ctime' => time(),
        ]);

        return $this->responseOk($result);
    }

    /**
     * 以业务类型切分发送者
     * @param $data 
     * @param $dotype //'expire','examine_success'
     * @return array
     */
    public function transforSendTplType($data = [],$dotype = ''){
        if(empty($data['order_no'])){
           return $this->responseFail('抱歉，缺失参数');
        }
        if(empty($data['status_type'])){
            return $this->responseFail('抱歉，缺失参数');
        }
        
        $pageUrl = 'http://fxtest.999house.com/agentside/index.html';//详情跳转地址
        $accessToken = $this->getAccessToken();

        switch ($dotype){
            //过期失效
            case 'expire':
                $res_duplicate = $this->getSendOpenidsAndSaidsByStep([
                    'order_no' => $data['order_no'],
                    'step' => $data['status_type'],
                ],'duplicate');

                if(empty($res_duplicate['result'])){
                    return $this->responseFail('抱歉，找不到该报备申请单');
                }

                $applyInfo = $res_duplicate['result']['apply']['info'];
                $applyStep = $res_duplicate['result']['apply']['step'];
                $res_duplicate = $res_duplicate['result']['other'];

                $openidList = [];$detailOpenIdList = []; $saIdList = []; $detailSaIdList = [];
                //通知抄送者
                foreach ($res_duplicate['detail_openids'] as $k=>$item){
                    //目前只通知店员店长，相应渠道
                    if(in_array($k,['subordinate','create-store'])){
                        $detailOpenIdList[$k] = $item;
                        $openidList = $openidList + $item;
                    }
                }
                foreach ($res_duplicate['detail_saids'] as $k=>$item){
                    //目前只通知店员店长，相应渠道
                    if(in_array($k,['subordinate','create-store'])){
                        $detailSaIdList[$k] = $item;
                        $saIdList = $saIdList + $item;
                    }
                }

                $this->db->name('log')->insert([
                    'title' => '过期失效通知',
                    'content' => json_encode([
                        '$data' => $data,
                        '$detailSaIdList'=>$detailSaIdList,
                        '$detailOpenIdList'=>$detailOpenIdList,
                    ],JSON_UNESCAPED_UNICODE),
                    'request' => '抄送者',
                    'ctime' => time(),
                ]);

                foreach ($openidList as $item){
                    $this->sendWxMsgTpl([
                        'touser' => $item,
                        'template_id' => 'Tm8xwzV5Em7GI48FXN2J9WLih8S_Xarz1seXpPaNAaQ',
                        'url' => $pageUrl . '/agentside/pages/customer/record_detail.html?id=' . $applyInfo['id'],
                        'data' => [
                            'first'    => ['value' => '失效通知', 'color' => '#173177'],
                            'keyword1' => ['value' => $applyInfo['agent_name'], 'color' => '#173177'],//经纪人名称
                            'keyword2' => ['value' => $applyInfo['agent_phone'], 'color' => '#173177'],//经纪人电话
                            'keyword3' => ['value' => $applyInfo['user_name'], 'color' => '#173177'],//客户姓名
                            'keyword4' => ['value' => $applyInfo['user_phone'], 'color' => '#173177'],//客户电话
                            'keyword5' => ['value' => $applyInfo['building_name'], 'color' => '#173177'], //报备楼盘
                            'remark'   => ['value' => '客户'.$this->stepTxt[$applyStep].'已失效，点击查看详情！', 'color' => '#173177']
                        ]
                    ], $accessToken);
                }

                //通知申请人
                $this->sendWxMsgTpl([
                    'touser' => $applyInfo['agent_openid'],
                    'template_id' => 'BY0lvW5_d7DfFIs4gE1O3w9688EQWEL0CY1vMxtohg4',
                    'url' => $pageUrl . '/agentside/pages/customer/record_detail.html?id=' . $applyInfo['id'],
                    'data' => [
                        'first'    => ['value' => '失效通知', 'color' => '#173177'],
                        'customName' => ['value' => $applyInfo['user_name'], 'color' => '#173177'], //客户姓名
                        'customPhone' => ['value' => $applyInfo['user_phone'], 'color' => '#173177'], //客户电话
                        'customBuilding' => ['value' => $applyInfo['building_name'], 'color' => '#173177'], //推荐楼盘
                        'reportTime' => ['value' => date('Y-m-d H:i', $applyInfo['create_time']), 'color' => '#173177'], //推荐时间
                        'invalidTime' => ['value' => date('Y-m-d H:i', $applyInfo['update_time']), 'color' => '#173177'], //失效时间
                        'remark'   => ['value' => '客户'.$this->stepTxt[$applyStep].'已失效，请联系该客户，根据其购买意向重新推荐！', 'color' => '#173177']
                    ]
                ], $accessToken);
                break;
            //审批
            case 'examine':
                $res_examine = $this->getSendOpenidsAndSaidsByStep([
                    'order_no' => $data['order_no'],
                    'step' => $data['status_type'],
                ],'examine');

                $this->db->name('log')->insert([
                    'title' => '审批通知数据库搜索',
                    'content' => json_encode([
                        '$data' => $data,
                        '$res_examine' => $res_examine,
                    ],JSON_UNESCAPED_UNICODE),
                    'request' => '审批者',
                    'ctime' => time(),
                ]);

                if(empty($res_examine['result'])){
                    return $this->responseFail('抱歉，找不到该报备申请单');
                }
                //print_r($res_examine['result']);

                $applyInfo = $res_examine['result']['apply']['info'];
                $applyStep = $res_examine['result']['apply']['step'];
                $res_examine = $res_examine['result']['other'];

                if($applyStep==7){//流程完成了不走该支路
                    return $this->responseFail('抱歉，业务错误');
                }

                $this->db->name('log')->insert([
                    'title' => '审批通知',
                    'content' => json_encode([
                        '$data' => $data,
                        '$res_examine' => $res_examine,
                    ],JSON_UNESCAPED_UNICODE),
                    'request' => '审批者',
                    'ctime' => time(),
                ]);

                //通知审批者
                foreach ($res_examine['detail_openids'] as $k=>$item){
                    $this->sendWxMsgTpl([
                        'touser' => $item,
                        'template_id' => 'Tm8xwzV5Em7GI48FXN2J9WLih8S_Xarz1seXpPaNAaQ',
                        'url' => $pageUrl . '/agentside/pages/customer/record_detail.html?id=' . $applyInfo['id'],
                        'data' => [
                            'first'    => ['value' => '待审核通知', 'color' => '#173177'],
                            'keyword1' => ['value' => $applyInfo['agent_name'], 'color' => '#173177'],//经纪人名称
                            'keyword2' => ['value' => $applyInfo['agent_phone'], 'color' => '#173177'],//经纪人电话
                            'keyword3' => ['value' => $applyInfo['user_name'], 'color' => '#173177'],//客户姓名
                            'keyword4' => ['value' => $applyInfo['user_phone'], 'color' => '#173177'],//客户电话
                            'keyword5' => ['value' => $applyInfo['building_name'], 'color' => '#173177'], //报备楼盘
                            'remark'   => ['value' => '您有个'.$this->stepTxt[$applyStep].'申请待审核，点击查看详情！', 'color' => '#173177']
                        ]
                    ], $accessToken);
                }

                //审核过了第一步再进行操作
                if($applyStep>1){
                    $res_duplicate = $this->getSendOpenidsAndSaidsByStep([
                        'order_no' => $data['order_no'],
                        'step' => $applyStep - 1,
                    ],'duplicate', $applyInfo);
                    //print_r($res_duplicate['result']);

                    if(!empty($res_duplicate['result'])){
                        $res_duplicate = $res_duplicate['result']['other'];

                        $this->db->name('log')->insert([
                            'title' => '审批通知',
                            'content' => json_encode([
                                '$data' => $data,
                                '$res_duplicate' => $res_duplicate,
                            ],JSON_UNESCAPED_UNICODE),
                            'request' => '抄送者',
                            'ctime' => time(),
                        ]);

                        //通知抄送者
                        foreach ($res_duplicate['detail_openids'] as $k=>$item){
                            $this->sendWxMsgTpl([
                                'touser' => $item,
                                'template_id' => 'Tm8xwzV5Em7GI48FXN2J9WLih8S_Xarz1seXpPaNAaQ',
                                'url' => $pageUrl . '/agentside/pages/customer/record_detail.html?id=' . $applyInfo['id'],
                                'data' => [
                                    'first'    => ['value' => '客户状态更新', 'color' => '#173177'],
                                    'keyword1' => ['value' => $applyInfo['agent_name'], 'color' => '#173177'],//经纪人名称
                                    'keyword2' => ['value' => $applyInfo['agent_phone'], 'color' => '#173177'],//经纪人电话
                                    'keyword3' => ['value' => $applyInfo['user_name'], 'color' => '#173177'],//客户姓名
                                    'keyword4' => ['value' => $applyInfo['user_phone'], 'color' => '#173177'],//客户电话
                                    'keyword5' => ['value' => $applyInfo['building_name'], 'color' => '#173177'], //报备楼盘
                                    'remark'   => ['value' => '客户'.$this->stepTxt[$applyStep-1].'成功，点击查看详情！', 'color' => '#173177']
                                ]
                            ], $accessToken);
                        }
                    }

                    //通知申请人
                    $this->sendWxMsgTpl([
                        'touser' => $applyInfo['agent_openid'],
                        'template_id' => '_y3oiGvvRAvJ1wYsDxvzsBqsHx8jYTTXY7CR1j7mBjo',
                        'url' => $pageUrl . '/agentside/pages/customer/record_detail.html?id=' . $applyInfo['id'],
                        'data' => [
                            'first'    => ['value' => '审核通过', 'color' => '#173177'],
                            'applyTitle' => ['value' => '客户'.$this->stepTxt[$applyStep-1].'成功', 'color' => '#173177'], //申请内容
                            'applyTitleTime' => ['value' => date('Y-m-d H:i', $applyInfo['update_time']), 'color' => '#173177'], //申请内容时间
                            'customName' => ['value' => $applyInfo['user_name'], 'color' => '#173177'], //客户姓名
                            'customPhone' => ['value' => $applyInfo['user_phone'], 'color' => '#173177'], //客户电话
                            'customBuilding' => ['value' => $applyInfo['building_name'], 'color' => '#173177'], //推荐楼盘
                            'remark'   => ['value' => '点击查看详情！', 'color' => '#173177']
                        ]
                    ], $accessToken);
                }
                break;
            case 'commission':
                $res_duplicate = $this->getSendOpenidsAndSaidsByStep([
                    'order_no' => $data['order_no'],
                    'step' => $data['status_type'],
                ],'duplicate');
                //print_r($res_duplicate['result']);

                if(empty($res_duplicate['result'])){
                    return $this->responseFail('抱歉，找不到该报备申请单');
                }

                $applyInfo = $res_duplicate['result']['apply']['info'];
                $applyStep = $res_duplicate['result']['apply']['step'];
                $res_duplicate = $res_duplicate['result']['other'];

                if($applyStep!=7){//佣金发放，非流程结束不执行
                    return $this->responseFail('抱歉，业务错误');
                }

                $this->db->name('log')->insert([
                    'title' => '佣金通知',
                    'content' => json_encode([
                        '$data' => $data,
                        '$res_duplicate' => $res_duplicate
                    ],JSON_UNESCAPED_UNICODE),
                    'request' => '抄送者',
                    'ctime' => time(),
                ]);

                //通知抄送者
                foreach ($res_duplicate['detail_openids'] as $k=>$item){
                    $this->sendWxMsgTpl([
                        'touser' => $item,
                        'template_id' => 'Tm8xwzV5Em7GI48FXN2J9WLih8S_Xarz1seXpPaNAaQ',
                        'url' => $pageUrl . '/agentside/pages/customer/record_detail.html?id=' . $applyInfo['id'],
                        'data' => [
                            'first'    => ['value' => '客户状态更新', 'color' => '#173177'],
                            'keyword1' => ['value' => $applyInfo['agent_name'], 'color' => '#173177'],//经纪人名称
                            'keyword2' => ['value' => $applyInfo['agent_phone'], 'color' => '#173177'],//经纪人电话
                            'keyword3' => ['value' => $applyInfo['user_name'], 'color' => '#173177'],//客户姓名
                            'keyword4' => ['value' => $applyInfo['user_phone'], 'color' => '#173177'],//客户电话
                            'keyword5' => ['value' => $applyInfo['building_name'], 'color' => '#173177'], //报备楼盘
                            'remark'   => ['value' => '客户'.$this->stepTxt[$applyStep-1].'成功，点击查看详情！', 'color' => '#173177']
                        ]
                    ], $accessToken);
                }

                //通知申请人
                //查找成交与结佣日志
                $ok_time = $this->db->name('xcx_reported_log')->where([
                    ['report_id', '=', $applyInfo['id']],
                    ['status_type', '=', 3],
                ])->value('created_at');
                $commission_time = $this->db->name('xcx_reported_log')->where([
                    ['report_id', '=', $applyInfo['id']],
                    ['status_type', '=', 6],
                    ['examine_type','=','2']
                ])->value('created_at');

                $this->sendWxMsgTpl([
                     'touser' => $applyInfo['agent_openid'],
                     'template_id' => 'BY0lvW5_d7DfFIs4gE1O3w9688EQWEL0CY1vMxtohg4',
                     'url' => $pageUrl . '/agentside/pages/customer/record_detail.html?id=' . $applyInfo['id'],
                     'data' => [
                         'first'    => ['value' => '佣金发放通知', 'color' => '#173177'],
                         'customName' => ['value' => $applyInfo['user_name'], 'color' => '#173177'], //客户姓名
                         'customPhone' => ['value' => $applyInfo['user_phone'], 'color' => '#173177'], //客户电话
                         'customBuilding' => ['value' => $applyInfo['building_name'], 'color' => '#173177'], //推荐楼盘
                         'reportTime' => ['value' => date('Y-m-d H:i', $applyInfo['create_time']), 'color' => '#173177'], //推荐时间
                         'signAmount' => ['value' => date('Y-m-d H:i', $applyInfo['update_time']), 'color' => '#173177'], //成交金额
                         'signTime' => ['value' => date('Y-m-d H:i',$ok_time), 'color' => '#173177'], //成交时间
                         'commissionAmount' => ['value' => $applyInfo['change_commission'], 'color' => '#173177'], //结佣金额
                         'commissionTime' => ['value' => date('Y-m-d H:i',$commission_time), 'color' => '#173177'], //结佣时间
                         'remark'   => ['value' => "恭喜您，请继续推荐客户！", 'color' => '#173177']
                     ]
                 ], $accessToken);
                break;
            case 'log':
                //通知抄送者
                $res_duplicate = $this->getSendOpenidsAndSaidsByStep([
                    'order_no' => $data['order_no'],
                    'step' => $data['status_type'],
                ],'duplicate');
                //print_r($res_duplicate);

                if(empty($res_duplicate['result'])){
                    return $this->responseFail('抱歉，找不到该报备申请单');
                }

                $applyInfo = $res_duplicate['result']['apply']['info'];
                $applyStep = $res_duplicate['result']['apply']['step'];
                $res_duplicate = $res_duplicate['result']['other'];
                
                $openidList = [];$detailOpenIdList = []; $saIdList = []; $detailSaIdList = [];
                foreach ($res_duplicate['detail_openids'] as $k=>$item){
                    //目前只通知店员店长，相应渠道
                    if(in_array($k,['subordinate','create-store'])){
                        $detailOpenIdList[$k] = $item;
                        $openidList = $openidList + $item;
                    }
                }
                foreach ($res_duplicate['detail_saids'] as $k=>$item){
                    //目前只通知店员店长，相应渠道
                    if(in_array($k,['subordinate','create-store'])){
                        $detailSaIdList[$k] = $item;
                        $saIdList = $saIdList + $item;
                    }
                }

                $this->db->name('log')->insert([
                    'title' => '日志通知',
                    'content' => json_encode([
                        '$data' => $data,
                        '$detailSaIdList' => $detailSaIdList,
                        '$detailOpenIdList' => $detailOpenIdList,
                    ],JSON_UNESCAPED_UNICODE),
                    'request' => '抄送者',
                    'ctime' => time(),
                ]);

                foreach ($openidList as $item){
                    $this->sendWxMsgTpl([
                        'touser' => $item,
                        'template_id' => 'Tm8xwzV5Em7GI48FXN2J9WLih8S_Xarz1seXpPaNAaQ',
                        'url' => $pageUrl . '/agentside/pages/customer/record_detail.html?id=' . $applyInfo['id'],
                        'data' => [
                            'first'    => ['value' => '客户跟进信息通知', 'color' => '#173177'],
                            'keyword1' => ['value' => $applyInfo['agent_name'], 'color' => '#173177'],//经纪人名称
                            'keyword2' => ['value' => $applyInfo['agent_phone'], 'color' => '#173177'],//经纪人电话
                            'keyword3' => ['value' => $applyInfo['user_name'], 'color' => '#173177'],//客户姓名
                            'keyword4' => ['value' => $applyInfo['user_phone'], 'color' => '#173177'],//客户电话
                            'keyword5' => ['value' => $applyInfo['building_name'], 'color' => '#173177'], //报备楼盘
                            'remark'   => ['value' => '客户跟进信息更新，点击查看详情！', 'color' => '#173177']
                        ]
                    ], $accessToken);
                }

                //通知申请人
                //查找申请环节时候的时间
                $applyStep_time = $this->db->name('xcx_reported_log')->where([
                    ['report_id', '=', $applyInfo['id']],
                    ['status_type', '=', $applyStep]
                ])->value('created_at');

                $this->sendWxMsgTpl([
                    'touser' => $applyInfo['agent_openid'],
                    'template_id' => '_y3oiGvvRAvJ1wYsDxvzsBqsHx8jYTTXY7CR1j7mBjo',
                    'url' => $pageUrl . '/agentside/pages/customer/record_detail.html?id=' . $applyInfo['id'],
                    'data' => [
                        'first'    => ['value' => '客户跟进信息通知', 'color' => '#173177'],
                        'applyTitle' => ['value' => '客户'.$this->stepTxt[$applyStep], 'color' => '#173177'], //申请内容
                        'applyTitleTime' => ['value' => date('Y-m-d H:i',$applyStep_time), 'color' => '#173177'], //申请内容时间
                        'customName' => ['value' => $applyInfo['user_name'], 'color' => '#173177'], //客户姓名
                        'customPhone' => ['value' => $applyInfo['user_phone'], 'color' => '#173177'], //客户电话
                        'customBuilding' => ['value' => $applyInfo['building_name'], 'color' => '#173177'], //推荐楼盘
                        'remark'   => ['value' => '您有新的客户跟进信息更新，点击查看详情！', 'color' => '#173177']
                    ]
                ], $accessToken);
                break;
        }

        return $this->responseOk();
    }



    /**
     * 需要审核的人员的openid集
     * @param $roles_examine
     * @param $info
     * @param $key //类型 openid，said
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getExamineOpenidsOrSaids($roles_examine, $info, $key='openid'){
        $openIds = []; $detail_openIds =[]; $saIds =[]; $detail_saIds=[];

        //通知有 我创建的店铺权限的及其上级进行审批
        if(!empty($roles_examine['detail_roleIds']['create-store'])||!empty($roles_examine['detail_roleIds']['subordinate-store'])){
            //找出店铺创建者,渠道专员 //找出创建店铺的后台账号channel_id关联的said
            if(!empty($roles_examine['detail_roleIds']['create-store'])){
                $create_store_agents = $this->db->name('xcx_store_store')
                    ->alias('xs')
                    ->field('xa.said,xa.agent_openid,xa.mgid')
                    ->join('admin a','a.id = xs.aid')
                    ->join('xcx_store_agent xa','xa.said = a.channel_id')
                    ->where([
                        ['xs.id', '=', $info['store_id']],
                        ['type', 'in', $roles_examine['detail_roleIds']['create-store']]
                    ])->find();
                //剔除自己操作的申请，不进行通知
                if(!empty($create_store_agents['said'])&&$info['said']!=$create_store_agents['said']){
                    $create_store_agents['agent_openid']&&$openIds[$create_store_agents['agent_openid']] = $create_store_agents['agent_openid'];
                    $create_store_agents['agent_openid']&&$detail_openIds['create-store'][$create_store_agents['agent_openid']] = $create_store_agents['agent_openid'];
                    $saIds[$create_store_agents['said']] = $create_store_agents['said'];
                    $detail_saIds['create-store'][$create_store_agents['said']] = $create_store_agents['said'];
                }
            }

            //找出店铺创建者的上级，渠道总监
            /*if(!empty($roles_examine['detail_roleIds']['subordinate-store'])&&!empty($create_store_agents['mgid'])){
                $leader_create_store_agents = $this->db->name('xcx_store_agent')
                    ->field('said,agent_openid')
                    ->where(
                        ['mgid', '=', $create_store_agents['mgid']],
                        ['type', 'in', $roles_examine['detail_roleIds']['subordinate-store']]
                    )->find();
                //剔除自己操作的申请，不进行通知
                if(!empty($leader_create_store_agents['said'])&&$info['said']!=$leader_create_store_agents['said']){
                    $openIds[$leader_create_store_agents['agent_openid']] = $leader_create_store_agents['agent_openid'];
                    $detail_openIds['subordinate-store'][$leader_create_store_agents['agent_openid']] = $leader_create_store_agents['agent_openid'];
                    $saIds[$leader_create_store_agents['said']] = $leader_create_store_agents['said'];
                    $detail_saIds['subordinate-store'][$leader_create_store_agents['said']] = $leader_create_store_agents['said'];
                }
            }*/
        }

        //通知有 创建楼盘的权限进行审批
        if(!empty($roles_examine['detail_roleIds']['create-building'])){
            //找出楼盘创建者人员
            $create_building_agents = $this->db->name('xcx_store_agent')
                ->alias('xa')
                ->field('xa.said,xa.agent_openid')
                ->join('admin a','a.charge_id=xa.said')
                ->where([
                    ['a.id', '=', $info['building_aid']],
                    ['xa.type', 'in', $roles_examine['detail_roleIds']['create-building']]
                ])->find();
            //剔除自己操作的申请，不进行抄送
            if(!empty($create_building_agents['said'])&&$info['said']!=$create_building_agents['said']){
                $openIds[$create_building_agents['agent_openid']]&&$openIds[$create_building_agents['agent_openid']] = $create_building_agents['agent_openid'];
                $openIds[$create_building_agents['agent_openid']]&&$detail_openIds['create-building'][$create_building_agents['agent_openid']] = $create_building_agents['agent_openid'];
                $saIds[$create_building_agents['said']] = $create_building_agents['said'];
                $detail_saIds['create-building'][$create_building_agents['said']] = $create_building_agents['said'];
            }
        }

        //通知有 绑定楼盘的权限进行审批
        if(!empty($roles_examine['detail_roleIds']['building'])){
            //找出楼盘绑定人员
            $bind_building_agents = $this->db->name('xcx_store_agent')
                ->alias('xa')
                ->field('xa.said,xa.agent_openid,xa.mgid')
                ->join('xcx_manager_building xb','xb.said=xa.said')
                ->whereRaw('find_in_set(:building_id,xb.building_ids)', ['building_id' => $info['building_id'] ])
                ->where([
                    ['xa.type', 'in', $roles_examine['detail_roleIds']['building']]
                ])->find();
            //剔除自己操作的申请，不进行通知
            if(!empty($bind_building_agents['said'])&&$info['said']!=$bind_building_agents['said']){
                $openIds[$bind_building_agents['agent_openid']]&&$openIds[$bind_building_agents['agent_openid']] = $bind_building_agents['agent_openid'];
                $openIds[$bind_building_agents['agent_openid']]&&$detail_openIds['building'][$bind_building_agents['agent_openid']] = $bind_building_agents['agent_openid'];
                $saIds[$bind_building_agents['said']] = $bind_building_agents['said'];
                $detail_saIds['building'][$bind_building_agents['said']] = $bind_building_agents['said'];
            }
        }

        return $this->responseOk([
            'openids' => $openIds,
            'detail_openids' => $detail_openIds,
            'saids' => $saIds,
            'detail_saids' => $detail_saIds,
        ]);
    }

    /**
     * 需要抄送的人员的openid集
     * @param $roles_duplicate //抄送的权限数据
     * @param $info //申请单信息
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getDuplicateOpenidsOrSaids($roles_duplicate, $info){
        $openIds = []; $saIds = []; $detail_openIds = []; $detail_saIds = [];
        //通知有 城市权限的
        if(!empty($roles_duplicate['detail_roleIds']['city'])){
            $city_agents =  $this->db->name('xcx_store_agent')
                ->field('said,agent_openid')
                ->where([
                    ['city', 'like', '%'.$info['city'].'%'],
                    ['type', 'in', $roles_duplicate['detail_roleIds']['city']]
                ])->select();
            //剔除城市权限自己操作的申请，不进行抄送
            foreach ($city_agents as $item){
                if($info['said']!=$item['said']){
                    $openIds[$item['agent_openid']]&&$openIds[$item['agent_openid']] = $item['agent_openid'];
                    $openIds[$item['agent_openid']]&&$detail_openIds['city'][$item['agent_openid']] = $item['agent_openid'];
                    $saIds[$item['said']] = $item['said'];
                    $detail_saIds['city'][$item['said']] = $item['said'];
                }
            }
        }
        //通知有 我的店员权限的
        if(!empty($roles_duplicate['detail_roleIds']['subordinate'])){
            //查找出店长
            $store_agents = $this->db->name('xcx_store_agent')
                ->field('said,agent_openid')
                ->where([
                    ['store_id', '=', $info['store_id']],
                    ['type', 'in', $roles_duplicate['detail_roleIds']['subordinate']]
                ])->find();
            //剔除店长自己操作的申请，不进行店长抄送
            if(!empty($store_agents['said'])&&$info['said']!=$store_agents['said']){
                $openIds[$store_agents['agent_openid']]&&$openIds[$store_agents['agent_openid']] = $store_agents['agent_openid'];
                $openIds[$store_agents['agent_openid']]&&$detail_openIds['subordinate'][$store_agents['agent_openid']] = $store_agents['agent_openid'];
                $saIds[$store_agents['said']] = $store_agents['said'];
                $detail_saIds['subordinate'][$store_agents['said']] = $store_agents['said'];
            }
        }

        //通知有 我创建的店铺权限的及其上级
        if(!empty($roles_duplicate['detail_roleIds']['create-store'])||!empty($roles_duplicate['detail_roleIds']['subordinate-store'])){
            //找出店铺创建者,渠道专员 //找出创建店铺的后台账号channel_id关联的said
            if(!empty($roles_duplicate['detail_roleIds']['create-store'])){
                $create_store_agents = $this->db->name('xcx_store_store')
                    ->alias('xs')
                    ->field('xa.said,xa.agent_openid,xa.mgid')
                    ->join('admin a','a.id = xs.aid')
                    ->join('xcx_store_agent xa','xa.said = a.channel_id')
                    ->where([
                        ['xs.id', '=', $info['store_id']],
                        ['type', 'in', $roles_duplicate['detail_roleIds']['create-store']]
                    ])->find();
                //剔除自己操作的申请，不进行抄送
                if(!empty($create_store_agents['said'])&&$info['said']!=$create_store_agents['said']){
                    $openIds[$create_store_agents['agent_openid']]&&$openIds[$create_store_agents['agent_openid']] = $create_store_agents['agent_openid'];
                    $openIds[$create_store_agents['agent_openid']]&&$detail_openIds['create-store'][$create_store_agents['agent_openid']] = $create_store_agents['agent_openid'];
                    $saIds[$create_store_agents['said']] = $create_store_agents['said'];
                    $detail_saIds['create-store'][$create_store_agents['said']] = $create_store_agents['said'];
                }
            }

            //找出店铺创建者的上级，渠道总监
            if(!empty($roles_duplicate['detail_roleIds']['subordinate-store'])&&!empty($create_store_agents['mgid'])){
                $leader_create_store_agents = $this->db->name('xcx_store_agent')
                    ->field('said,agent_openid')
                    ->where([
                        ['mgid', '=', $create_store_agents['mgid']],
                        ['type', 'in', $roles_duplicate['detail_roleIds']['subordinate-store']]
                    ])->find();
                //剔除自己操作的申请，不进行抄送
                if(!empty($leader_create_store_agents['said'])&&$info['said']!=$leader_create_store_agents['said']){
                    $openIds[$leader_create_store_agents['agent_openid']]&&$openIds[$leader_create_store_agents['agent_openid']] = $leader_create_store_agents['agent_openid'];
                    $openIds[$leader_create_store_agents['agent_openid']]&&$detail_openIds['subordinate-store'][$leader_create_store_agents['agent_openid']] = $leader_create_store_agents['agent_openid'];
                    $saIds[$leader_create_store_agents['said']] = $leader_create_store_agents['said'];
                    $detail_saIds['subordinate-store'][$leader_create_store_agents['said']] = $leader_create_store_agents['said'];
                }
            }
        }

        //通知有 楼盘创建权限的
        if(!empty($roles_duplicate['detail_roleIds']['create-building'])){
            //找出楼盘创建者人员
            $create_building_agents = $this->db->name('xcx_store_agent')
                ->alias('xa')
                ->field('xa.said,xa.agent_openid')
                ->join('admin a','a.charge_id=xa.said')
                ->where([
                    ['a.id', '=', $info['building_aid']],
                    ['xa.type', 'in', $roles_duplicate['detail_roleIds']['create-building']]
                ])->find();
            //剔除自己操作的申请，不进行抄送
            if(!empty($create_building_agents['said'])&&$info['said']!=$create_building_agents['said']){
                $openIds[$create_building_agents['agent_openid']]&&$openIds[$create_building_agents['agent_openid']] = $create_building_agents['agent_openid'];
                $openIds[$create_building_agents['agent_openid']]&&$detail_openIds['create-building'][$create_building_agents['agent_openid']] = $create_building_agents['agent_openid'];
                $saIds[$create_building_agents['said']] = $create_building_agents['said'];
                $detail_saIds['create-building'][$create_building_agents['said']] = $create_building_agents['said'];
            }
        }

        //通知有 绑定楼盘权限及其上级的
        if(!empty($roles_duplicate['detail_roleIds']['building'])||!empty($roles_duplicate['detail_roleIds']['subordinate-building'])){
            if(!empty($roles_duplicate['detail_roleIds']['building'])){
                //找出楼盘绑定人员
                $bind_building_agents = $this->db->name('xcx_store_agent')
                    ->alias('xa')
                    ->field('xa.said,xa.agent_openid,xa.mgid')
                    ->join('xcx_manager_building xb','xb.said=xa.said')
                    ->whereRaw('find_in_set(:building_id,xb.building_ids)', ['building_id' => $info['building_id'] ])
                    ->where([
                        ['xa.type', 'in', $roles_duplicate['detail_roleIds']['building']]
                    ])->find();
                //剔除自己操作的申请，不进行抄送
                if(!empty($bind_building_agents['said'])&&$info['said']!=$bind_building_agents['said']){
                    $openIds[$bind_building_agents['agent_openid']]&&$openIds[$bind_building_agents['agent_openid']] = $bind_building_agents['agent_openid'];
                    $openIds[$bind_building_agents['agent_openid']]&&$detail_openIds['building'][$bind_building_agents['agent_openid']] = $bind_building_agents['agent_openid'];
                    $saIds[$bind_building_agents['said']] = $bind_building_agents['said'];
                    $detail_saIds['building'][$bind_building_agents['said']] = $bind_building_agents['said'];
                }
            }

            //找出楼盘绑定人员的上级
            if(!empty($roles_duplicate['detail_roleIds']['subordinate-building'])&&!empty($bind_building_agents['mgid'])){
                $leader_bind_building_agents = $this->db->name('xcx_store_agent')
                    ->alias('xa')
                    ->field('xa.said,xa.agent_openid')
                    ->where([
                        ['xa.mgid', '=', $bind_building_agents['mgid']],
                        ['xa.type', 'in', $roles_duplicate['detail_roleIds']['subordinate-building']]
                    ])->find();

                //剔除自己操作的申请，不进行抄送
                if(!empty($leader_bind_building_agents['said'])&&$info['said']!=$leader_bind_building_agents['said']){
                    $openIds[$leader_bind_building_agents['agent_openid']]&&$openIds[$leader_bind_building_agents['agent_openid']] = $leader_bind_building_agents['agent_openid'];
                    $openIds[$leader_bind_building_agents['agent_openid']]&&$detail_openIds['subordinate-building'][$leader_bind_building_agents['agent_openid']] = $leader_bind_building_agents['agent_openid'];
                    $saIds[$leader_bind_building_agents['said']] = $leader_bind_building_agents['said'];
                    $detail_saIds['subordinate-building'][$leader_bind_building_agents['said']] = $leader_bind_building_agents['said'];
                }
            }
        }

        //下级审批后进行上级抄送通知
        if(!empty($roles_duplicate['detail_roleIds']['subordinate-work'])){
            //找出下级审批操作的人
            $sub_agent = $this->db->name('xcx_store_agent')
                ->alias('xa')
                ->field('xa.said,xa.agent_openid,xa.mgid,xa.type')
                ->join('xcx_reported_log xl','xl.examine_said=xa.said')
                ->where([
                    ['xl.report_id', '=', $info['id']],
                ]);
            if($roles_duplicate['step']==7){//流程完成了，为虚拟出的新流程做下转化
                $sub_agent = $sub_agent->where([
                    ['xl.status_type', '=', '6'],
                    ['xl.examine_type', '=', '2']
                ])->find();
            }else{
                $sub_agent = $sub_agent->where([
                    ['xl.status_type', '=', $roles_duplicate['step']],
                    ['xl.examine_type', '=', '2'],
                    ['xl.agent_type', '>', 1]
                ])->find();
            }

            //排除是自己的审批不进行抄送
            if(!empty($sub_agent['type'])&&!in_array($sub_agent['type'],$roles_duplicate['detail_roleIds']['subordinate-work'])){
                //根据操作人员组找出上级
                $leader_examine_work_agents = $this->db->name('xcx_store_agent')
                    ->alias('xa')
                    ->field('xa.said,xa.agent_openid')
                    ->where([
                        ['mgid','=',$sub_agent['mgid']],
                        ['xa.type', 'in', $roles_duplicate['detail_roleIds']['subordinate-work']]
                    ])->find();

                //剔除自己操作的申请和自己的审批，不进行抄送
                if(!empty($leader_examine_work_agents['said'])&&$info['said']!=$leader_examine_work_agents['said']){
                    $openIds[$leader_examine_work_agents['agent_openid']]&&$openIds[$leader_examine_work_agents['agent_openid']] = $leader_examine_work_agents['agent_openid'];
                    $openIds[$leader_examine_work_agents['agent_openid']]&&$detail_openIds['subordinate-work'][$leader_examine_work_agents['agent_openid']] = $leader_examine_work_agents['agent_openid'];
                    $saIds[$leader_examine_work_agents['said']] = $leader_examine_work_agents['said'];
                    $detail_saIds['subordinate-work'][$leader_examine_work_agents['said']] = $leader_examine_work_agents['said'];
                }
            }
        }

        return $this->responseOk([
            'openids' => $openIds,
            'detail_openids' => $detail_openIds,
            'saids' => $saIds,
            'detail_saids' => $detail_saIds,
        ]);
    }



    /**
     * 查找环节内对应可操作模块的角色集合
     * @param $action //duplicate，examine，log
     * @param $step
     * @param $roleInfo
     * @param $roleId  //角色标识
     * @param $res  //返回值
     * @return array[]
     */
    private function transforRoles($action, $step, $roleInfo, $roleId, &$res){
        //self:自己 subordinate:我的店员 building:绑定的楼盘 subordinate-building：下级绑定的楼盘 create-store:创建的店铺 subordinate-store:下级绑定的店铺 create-building:创建的楼盘 city:城市
        //echo $step.PHP_EOL;

        $action_step_auths = $roleInfo[$action][$step];//某一个模块该环节步骤内的权限集合
        /*if($step>1){
            $before_action_step_auths = $roleInfo[$action][$step-1];
        }*/
        /*echo $step.'//////////';
        print_r($roleInfo[$action]);*/

        $auths = [
            'subordinate',//我的店员
            'subordinate-store', //下级绑定的店铺
            'building',//我绑定的楼盘
            'subordinate-work',//下级的审批操作
            'subordinate-building',//下级绑定的楼盘
            'create-store', //我创建的店铺
            'create-building', //我创建的楼盘
            'city' //城市
        ];

        foreach ($auths as $item){
            if(in_array($item, $action_step_auths)){
                $res['detail'][$item][$roleId] = $roleInfo;
                $res['detail_roleIds'][$item][$roleId] = $roleId;
                $res['roleIds'][$roleId] = $roleId;
            }
        }
    }
}
