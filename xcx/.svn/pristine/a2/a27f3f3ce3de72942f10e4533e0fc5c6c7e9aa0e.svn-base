<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of main
 *
 * @author ADMIN
 */
include 'AdminController.php';
class main extends AdminController {
	public function index(){
		$data=[];
		if(empty($_SESSION['gid'])){	//超级管理员权限
			$data['is_admin']=1;
			$data['controller']=[];
			$data['action']=[];
		}else{	//普通管理员权限
			$data['is_admin']=0;
			$tblGroup = Load::Model('group');
			$rowGroup = $tblGroup->get($_SESSION['gid']);
			$auths = $rowGroup['auths'];
			if(empty($auths)){
				$auths[]=0;
			}else{
				$auths=explode(',',$auths);
			}
			$authData=$this->db->Name('auth')->select()->where_in('id',$auths)->execute();
            $menu = [];
			if(!empty($authData)){
				$controller=[];
				$action=[];
				foreach($authData as $val){
					$controller[]=$val['controller'];
					$action[]=$val['action'];
				}
                $menu = array_column($authData, 'menu_id');
				$data['menu'] = array_unique($menu);
				$data['controller']=array_unique($controller);
				$data['action']=array_unique($action);
			}else{
				$data['controller']=[];
				$data['action']=[];
			}
		}
		return $this->render("index",$data);
	}
	public function welcome(){
		$tblAdmin = Load::Model('admin');
		$rowAdmin = $tblAdmin->get($_SESSION['aid']);
		$data['arr'] = array(
            'system'=>php_uname(),//'操作系统'
            'environment'=>$_SERVER["SERVER_SOFTWARE"],//'运行环境'
            'port'=>$_SERVER['SERVER_PORT'],//Web端口
            'max_ex_time' => ini_get("max_execution_time")."秒",//脚本最大执行时间
            'operation_mode'=>php_sapi_name(),//'PHP运行方式'
            'gmdate_time'=>date("Y-n-j H:i:s"),//'服务器时间'
            'domain_ip'=>$_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]', //'服务器域名/IP'
            'space'=>round((disk_free_space(".")/(1024*1024)),2).'M',//'剩余空间'
            "ip"=>$rowAdmin['lastloginip'],//IP地址
            "time"=>date('Y-m-d H:i:s',$rowAdmin['lastlogintime']),//登录时间
        );
		return $this->render("welcome",$data);
	}
}
