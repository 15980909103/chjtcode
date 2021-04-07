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
class Common extends Controller{
    const MYLIMIT=10;
    public function __construct(){
        if(!Session::get('user_id')){
            echo json_encode(['empty_login'=>true]);exit;
        }
        $this->db = new Query();
    }
    //解析获取的参数
    function getJson($url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    public function input($str){
        $type = substr($str,0,strrpos($str,'.'));
        $input = substr($str,strrpos($str,'.')+1);
        if(substr($type, 0, 1) == '?'){
            $type = substr($type, 1);
            $is = true;
        }else{
            $is = false;
        }
        if(substr($type, 1) == 'get'){
            $regular = Context::Get($input);
        }elseif($type == 'post'){
            $regular = Context::Post($input);
        }else{
            $regular = '';
        }
        if($is && $regular == ''){
            return false;
        }
        if(strpos($input,'id') !== false){
            return intval(Encryption::authcode($regular));
        }else{
            if(Encryption::is_string_regular($regular) || is_array($regular)){
                exit('含有非法字符');
            }else{
                return $regular;
            }
        }
    }
}