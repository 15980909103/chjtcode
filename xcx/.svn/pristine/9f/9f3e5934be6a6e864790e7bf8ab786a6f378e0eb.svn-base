<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of main
 *
 * @author Goods0
 */
include System . DS . 'Encryption.php';
include System . DS . 'Session.php';
class TaskAjax extends Controller{
    //此控制器不用登陆验证
    public function __construct(){
        $this->db = new Query();
    }

    // 报备单失效
    public function setReportInvaild()
    {
        try {
            $page = 1;
            $pageSize = 200;// 每次搜索的数量
            $limit = 200;// 每次修改的失效单的数量
            $datas = [];// 已搜索的楼盘数据

            // 防止并发，加锁
            if(file_exists('task_report_lock.txt')) {
                $lock = file_get_contents('task_report_lock.txt');
            } else {
                $lock = 0;
            }
            if(1 == $lock) {
                return;// 已有锁，暂不执行
            } else {
                $res = file_put_contents('task_report_lock.txt', 1);// 执行，加锁
                if(!$res) {
                    return;// 加锁失败
                }
            }

            $updateIds = [];// 本次任务中需要改变状态的报备单
            while(1) {
                $searchIds = [];// 本次循环中需要搜索的楼盘
                $reportDatas = $this->db->Name('xcx_building_reported')
                    ->select('id, building_id, status_type, take_time, update_time')
                    ->where_notEqualTo('examine_type', -2)
                    ->where_in('status_type', [1,2,3])
                    ->page($page, $pageSize)
                    ->execute();

                if(!empty($reportDatas)) {
                    // 楼盘数据
                    $buildings = array_column($reportDatas, 'building_id');
                    $buildings = array_unique($buildings);
                    foreach ($buildings as $k => $v) {
                        if(!array_key_exists($v, $datas)) {
                            $searchIds[] = $v;// 之前未搜索过的楼盘
                        }
                    }
                    if(!empty($searchIds)) {
                        $buildingData = $this->db->Name('xcx_building_building')
                            ->select('id, protect_set')
                            ->where_in('id', $searchIds)
                            ->execute();
                        if(!empty($buildingData)) {
                            foreach ($buildingData as $key => $val) {
                                if(!array_key_exists($val['id'], $datas)) {
                                    $datas[$val['id']] = $val;
                                }
                            }
                        }
                    }
                    // 失效判断
                    foreach ($reportDatas as $kk => $vv) {
                        if($vv['status_type']>=1 && $vv['status_type']<=3) {
                            if(isset($datas[$vv['building_id']])) {
                                $protectSet = json_decode($datas[$vv['building_id']]['protect_set'], TRUE);

                                if(2 == $vv['status_type']) {// 如果是报备流程，则从预约带看时间开始算起
                                    $baseTime = $vv['take_time'];
                                } else {//按最后的更新时间-小时
                                    $baseTime = $vv['update_time'];
                                }

                                //每个流程环节保护时间-规范到小时
                                $keySet = 'status' . $vv['status_type'] . '_hours';
                                $protect_set_hours = intval($protectSet[$keySet]);
                                if($protect_set_hours <= 0) {
                                    continue;// 该状态未设置有效保护期，跳过
                                }
                                if(1 == $vv['status_type']) {
                                    $protectTime = $protect_set_hours * 60;// 报备环节以分钟计
                                } else {
                                    $protectTime = $protect_set_hours * 3600;
                                }
                                $protectTimeEnd = $baseTime + $protectTime;
//                                if(67 == $vv['id']) {
//                                    var_dump([$protectTimeEnd, time()]);
//                                }
                                if($protectTimeEnd<=time()){
                                    $updateIds[] = $vv['id'];
                                }
                            }
                        }
                    }
                    if(sizeof($updateIds) >= $limit) {// 要改的失效单达到两百条就退出
                        break;
                    }
                    $page++;
                } else {
                    break;// 退出循环
                }
            }

            // 失效处理
            if(!empty($updateIds)) {
                $this->db->Name('xcx_building_reported')->update(['examine_type' => -2, 'update_time' => time()])->where_in('id', $updateIds)->execute();
            }

            file_put_contents('task_report_lock.txt', 0);// 释放锁
        } catch (\ErrorException $e) {
            $this->db->Name('log')->insert([
                'title'=> '报备单失效定时任务失败',
                'content'=>json_encode($e,JSON_UNESCAPED_UNICODE),
            ])->execute();
        }
    }
}