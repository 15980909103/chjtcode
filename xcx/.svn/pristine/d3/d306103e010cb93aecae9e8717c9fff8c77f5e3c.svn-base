<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of context
 *
 * @author admin
 */
include Extend . DS . 'img_compress/Image.php';

class UploadFiles{
    private $maxsize = '2097152'; //允许上传文件最大长度
    private $allowtype = array('jpg','png','gif','jpeg');//允许上传文件类型
    private $israndfile = true;//是否随机文件名
    private $filepath;//上传路径
    private $originName;//上传的源文件
    private $tmpfileName;//临时文件名
    private $newfileName;//新文件名
    private $fileSize;//文件大小
    private $fileType;//文件类型
    private $errorNum = 0;//错误号
    private $errorMessg = array();//错误消息
    //对成员初始化
    function __construct($options = array()){
        foreach($options as $key=>$val){
            $key = strtolower($key);
            //查看传进来的数组里下标是否与成员属性相同
            //print_r(array_keys(get_class_vars(get_class($this))));
            if(!in_array($key,array_keys(get_class_vars(get_class($this))))){
                continue;
            }else{
                $this->setOption($key,$val);
            }
        }
    }
    private function setOption($key,$val){
        $this->$key = $val;
    }
    //检查文件上传路径
    private function checkfilePath(){
        //echo $this->filepath;
        if(empty($this->filepath)){
            $this->setOption('errorNum',"-5");
            return false;
        }
        if(!file_exists($this->filepath) || !is_writable($this->filepath)){
            if(!@mkdir($this->filepath,0755)){
                $this->setOption('errorNum','-4');
                return false;
            }
        }
        return true;
    }
    //获取错误信息
    private function getError(){
        $str = "上传文件{$this->originName}出错---";
        switch($this->errorNum){
            case 4; $str .= "没有文件被上传";break;
            case 3; $str .= "文件只被部分上传";break;
            case 2; $str .= "超过文件表单允许大小";break;
            case 1; $str .= "超过php.ini中允许大小";break;
            case -1; $str .= "未允许的类型";break;
            case -2; $str .= "文件过大，不能超过".$this->maxsize."个字节";break;
            case -3; $str .= "上传失败";break;
            case -4; $str .= "建立文件上传目录失败";break;
            case -5; $str .= "必须指定上传路径";break;
            default; $str .= "未知错误";
        }
        return $str;
    }
    //检查文件类型
    private function checkfileType(){
        if(!in_array(strtolower($this->fileType),$this->allowtype)){
            $this->setOption('errorNum','-1');
            return false;
        }else{
            return true;
        }
    }
    //检查文件大小
    private function checkfileSize(){
        if($this->fileSize > $this->maxsize){
            $this->setOption('errorNum','-2');
            return false;
        }else{
            return true;
        }
    }
    //
    private function setFiles($name="",$tmp_name="",$size="",$error=""){
        //检查上传路径
        if(!$this->checkfilePath()){
            return false;
        }
        if($error){
            $this->setOption('errorNum',$error);
            return false;
        }
        $arrstr  = explode('.',$name);
        $type   = end($arrstr);
        $this->setOption('originName',$name);
        $this->setOption('fileSize',$size);
        $this->setOption('fileType',$type);
        $this->setOption('tmpfileName',$tmp_name);
        return true;
    }
    //检查是否有文件上传
    function checkFile($formname){
        if(!@$_FILES[$formname]){
            $this->setOption('errorNum',4);
            return false;
        }else{
            return true;
        }
    }
    //上传文件
    function uploadeFile($formname){
        if(!$this->checkFile($formname)){
            $this->errorMessg = $this->getError();
            return false;
        }
        $return  = true;
        $name   = @$_FILES[$formname]['name'];
        $tmp_name = @$_FILES[$formname]['tmp_name'];
        $size   = @$_FILES[$formname]['size'];
        $error  = @$_FILES[$formname]['error'];
        if(is_array($name)){
            $errors = array();
            for($i=0; $i<count($name); $i++){
                if($this->setFiles($name[$i],$tmp_name[$i],$size[$i],$error[$i])){
//                    if(!$this->checkfileSize() || !$this->checkfileType()){
                    if(!$this->checkfileType()){
                        $errors[] = $this->getError();
                        $return = false;
                    }
                }else{
                    $errors[] = $this->getError();
                    $return = false;
                }
                if(!$return) $this->setFiles();
            }
            if($return){
                $newfileN = array();
                for($i=0; $i<count($name); $i++){
                    if($this->setFiles($name[$i],$tmp_name[$i],$size[$i],$error[$i])){
                        if(!$this->copyFile()){
                            $errors[] = $this->getError();
                            $return = false;
                        }else{
                            $newfileN[] = $this->newfileName;
                        }
                    }
                    $this->newfileName = $newfileN;
                }
            }
            $this->errorMessg = $errors;
            return $return;
        }else{
            if($this->setFiles($name,$tmp_name,$size,$error)){
                $return = true;
                if($error) var_dump($error);
//                if($this->checkfileSize() && $this->checkfileType()){
                if($this->checkfileType()){
                    if(!$this->copyFile()){
                        $return = false;
                    }
                }else{
                    $return = false;
                }
            }else{
                $return = false;
            }
            if(!$return){
                $this->errorMessg = $this->getError();
            }
            return $return;
        }
    }
    //获取上传后的文件名
    function getnewFile(){
        return $this->newfileName;
    }
    //把文件拷贝到指定的路径
    function copyFile(){
        // 图片压缩
        $image = Image::open($this->tmpfileName);
        $image->thumb(750, 750);

        $filepath = rtrim($this->filepath,'/')."/";
        if(!$this->errorNum){
            if($this->israndfile){
                $this->newfileName = date('Ymdhis').rand(1000,9999).".".$this->fileType;
            }else{
                $this->newfileName = $this->originName;
            }
//            if(!move_uploaded_file($this->tmpfileName,$filepath.$this->newfileName)){
            if(!$image->save($filepath.$this->newfileName)){
                $this->setOption('errorNum',-3);
                return false;
            }else{
                return true;
            }
        }else{
            return false;
        }
    }
    //上传错误后返回的消息
    function gteerror(){
        $err = $this->errorMessg;
        return $err;
    }
}

?>
