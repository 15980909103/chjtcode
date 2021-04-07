<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Login
 *
 * @author admin
 */
defined("LOKI") || die("you no have right to access here");

class Login extends Controller {

    function get() {
		if (!isset($_SESSION['username']) || !isset($_SESSION['password']) || empty($_SESSION['username']) || empty($_SESSION['password']) ) {
			Context::Redirect('/xiamenyyhoutai/login/index');
		}else{
        	Context::Redirect('/xiamenyyhoutai/main/index');
		}
    }
	function index(){
		return $this->render("index");
	}
	function go() {
		$username = Context::Post('username');
		$password = Context::Post('password');
		$tblAdmin = Load::Model('admin');
		$rowAdmin = $tblAdmin->getup($username, md5($password));
		$arr = array();
		if($rowAdmin){
			$ip = getIp();
			$arr = array();
			$arr['loginip'] = $ip;
			$arr['logintime'] = time();
			$arr['lastloginip'] = $rowAdmin['loginip'];
			$arr['lastlogintime'] = $rowAdmin['logintime'];
			$arr['logincount'] = $rowAdmin['logincount'] + 1;
			$tblAdmin->update($rowAdmin['id'],$arr);
			$_SESSION['img'] = $rowAdmin['img'];
			$_SESSION['username'] = $rowAdmin['username'];
			$_SESSION['password'] = $rowAdmin['password'];
			$_SESSION['aid'] = $rowAdmin['id'];
			$_SESSION['gid'] = $rowAdmin['gid'];
			$_SESSION['province'] = $rowAdmin['province'];
			$_SESSION['city'] = $rowAdmin['city'];
			Context::Redirect('/xiamenyyhoutai/main/index');
		}else{
			return $this->render("index");
		}
    }
	function logout(){
		session_unset();
		session_destroy();
		return $this->render("index");
	}
	//跳转404页面
	public function error404(){
		return $this->render("error404");
	}
}
