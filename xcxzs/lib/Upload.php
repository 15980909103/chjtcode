<?php
class upload_file
{
   /**声明**/
   var $upfile_type,$upfile_size,$upfile_name,$upfile;
   var $d_alt,$extention_list,$tmp,$arri;
   var $datetime,$date;
   var $filestr,$size,$ext,$check;
   var $flash_directory,$extention,$file_path,$base_directory; 
   var $isurl,$url; //文件上传成功后跳转路径;
   var $isdir; //是否目录以日期的方式命名 1 为 是  0 为否
   var $isfile; //是否以日期的方式重命名文件; 1为是 0为否
   var $overwrite; //是否覆盖原来的文件, 1为覆盖  0 为否
   var $upfilename,$upfiledir;
   
   function upload_file()
   {
    /**构造函数**/
	$this->set_isover(0);
	$this->set_isdir(0);
	$this->set_isfile(0);
	$this->set_isurl(0);                   //设置是否跳转路径
    $this->set_url("index.php");           //初始化上传成功后跳转路径;
    $this->set_extention();              //初始化扩展名列表;
    $this->set_size(50);               //初始化上传文件KB限制;
    $this->set_date();                //设置目录名称;
    $this->set_datetime();              //设置文件名称前缀;
    $this->set_base_directory("UploadFile");   //初始化文件上传根目录名，可修改！;
   }
   
   function set_isover($num){
     $this->overwrite=$num;
   }
   
   function set_isdir($num){
     $this->isdir=$num;
   }
   
   function set_isfile($num){
     $this->isfile=$num;
   }
   
   /**文件类型**/
   function set_file_type($upfile_type)
   {
    $this->upfile_type = $upfile_type;        //取得文件类型;
   }
  
   /**获得文件名**/
   function set_file_name($upfile_name)
   {
    $this->upfile_name = $upfile_name;        //取得文件名称;
   }
  
   /**获得文件**/
   function set_upfile($upfile)
   {
    $this->upfile = $upfile;             //取得文件在服务端储存的临时文件名;
   }
    
   /**获得文件大小**/
   function set_file_size($upfile_size)
   {
    $this->upfile_size = $upfile_size;        //取得文件尺寸;
   }
  
   /**设置文件上传成功后跳转路径**/
   function set_isurl($num)
   {
     $this->isurl=$num;
   }
   function set_url($url)
   {
    $this->url = $url;                //设置成功上传文件后的跳转路径;
   }
  
   /**获得文件扩展名**/
   function get_extention()
   {
     $this->extention = preg_replace('/.*\.(.*[^\.].*)*/iU','\\1',$this->upfile_name); //取得文件扩展名;
   } 
      
   /**设置文件名称**/
   function set_datetime()
   {
    $this->datetime = date("YmdHis").rand(100000,999999);         //按时间生成文件名;
   }
  
   /**设置目录名称**/
   function set_date()
   {
    $this->date = date("Ym").'/'.date('d');           //按日期生成目录名称;
   }
  
   /**初始化允许上传文件类型**/
   function set_extention()
   {
    $this->extention_list = "doc|xls|ppt|avi|txt|gif|jpg|jpeg|bmp|png|swf"; //默认允许上传的扩展名称;
   }  
  
   /**设置最大上传KB限制**/
   function set_size($size)
   {
    $this->size = $size;               //设置最大允许上传的文件大小;
   }
  
   /**初始化文件存储根目录**/
   function set_base_directory($directory)
   {
    $this->base_directory = $directory; //生成文件存储根目录;
   }
  
   /**初始化文件存储子目录**/
   function set_flash_directory()
   {
     if($this->isdir==1){
       $this->flash_directory = $this->base_directory."/".$this->date."/"; //生成文件存储子目录;
	 }else{
	   $this->flash_directory = $this->base_directory."/"; //生成文件存储子目录;
	 } 
   }
  
   /**错误处理**/
   function showerror($errstr="未知错误！"){
   	header('Content-type:text/html;charset=utf-8');
    echo "<script language=javascript>alert('$errstr');location='javascript:history.go(-1);';</script>";
    exit();
   }
  
   /**跳转**/
   function go_to($str,$url)
   {
    echo "<script language='javascript'>alert('$str');location='$url';</script>";
    exit();
   }

   /**如果根目录没有创建则创建文件存储目录**/
   function mk_base_dir()
   {
    if (! file_exists($this->base_directory)){    //检测根目录是否存在;
     @mkdir($this->base_directory,0777);      //不存在则创建;
    }
   } 

   /**如果子目录没有创建则创建文件存储目录**/
   function mk_dir()
   {
    if (! file_exists($this->flash_directory)){    //检测子目录是否存在;
     @mkdir($this->flash_directory,0777);      //不存在则创建;
    }
	!file_exists($this->base_directory."/".date("Ym")."/") &&  mkdir($this->base_directory."/".date("Ym")."/");
	!file_exists($this->base_directory."/".$this->date."/") &&  mkdir($this->base_directory."/".$this->date."/");
   }  
  
   /**以数组的形式获得分解后的允许上传的文件类型**/
   function get_compare_extention()
   {
    $this->ext = explode("|",$this->extention_list);//以"|"来分解默认扩展名;
   }
  
   /**检测扩展名是否违规**/
 function check_extention()
   {
    for($i=0;each($this->ext);$i++)             //遍历数组;
    {
     if($this->ext[$i] == strtolower($this->extention)) //比较文件扩展名是否与默认允许的扩展名相符;
     {
      $this->check = true;                //相符则标记;
      break;
     }
    }
   if(!$this->check){$this->showerror("正确的扩展名必须为".$this->extention_list."其中的一种！");}
    //不符则警告
   }

   /**检测文件大小是否超标**/
   function check_size()
   {
    if($this->upfile_size > round($this->size*1024))      //文件的大小是否超过了默认的尺寸;
    {
     $this->showerror("上传附件不得超过".$this->size."KB"); //超过则警告;
    }
   }

   /**文件完整访问路径**/
   function set_file_path()
   {
     if($this->isfile==1){
        $this->file_path = $this->flash_directory."/".$this->datetime.".".$this->extention; //生成文件完整访问路径;
	 }else{
	    $this->file_path = $this->flash_directory."/".$this->upfile_name; //生成文件完整访问路径;
	 }	
   }
  
   /**上传文件**/
   function copy_file()
   {
    if(file_exists($this->file_path)&&$this->overwrite==0){
	  echo $this->showerror("文件已经存在！");
	  exit;
	}
    if(copy($this->upfile,$this->file_path)){         //上传文件;
	  if($this->isurl!=0){
       print $this->go_to("文件已经成功上传！",$this->url);   //上传成功;
	  } 
    }else {
     print $this->showerror("意外错误，请重试！");      //上传失败;
    }
   }
  
   /**完成保存**/
   function save()
   {
    $this->set_flash_directory();   //初始化文件上传子目录名;
    $this->get_extention();      //获得文件扩展名;
    $this->get_compare_extention(); //以"|"来分解默认扩展名;
    $this->check_extention();     //检测文件扩展名是否违规;
    $this->check_size();       //检测文件大小是否超限;   
    $this->mk_base_dir();       //如果根目录不存在则创建；
    $this->mk_dir();         //如果子目录不存在则创建;
    $this->set_file_path();      //生成文件完整访问路径;
    $this->copy_file();        //上传文件;
   }

   function getFilename(){
     //获取文件名称
     if($this->isfile==1){
	    $upfilename=$this->datetime.".".$this->extention;
	 }else{
	    $upfilename=$this->upfile_name;
	 } 
	 return $upfilename;
   }
   
   function getFilesize(){
     //文件文件大小
      return $this->upfile_size;//B
   }
   
   function getFileDir(){ //获取文件存储子目录，不包括文件名
      if($this->isdir==1){
        $upfiledir = '/'.$this->date; 
	  }else{
	    $upfiledir = ''; 
	  }
	  return $upfiledir;
   }
   
   function getNameDir(){
     //包含目录和文件及/，但不包括上传目录，这样以后在系统应用中就方便多了。
     return $this->getFileDir().'/'.$this->getFilename();
   }
   
   function water($dst_path,$src_path,$postion){
   	$dst_path=$_SERVER['DOCUMENT_ROOT'].'/'.$dst_path;
   	$src_path=$_SERVER['DOCUMENT_ROOT'].$src_path;
   	//创建图片的实例
   	$dst = imagecreatefromstring(file_get_contents($dst_path));
   	$src = imagecreatefromstring(file_get_contents($src_path));
   	 
   	list($dst_w, $dst_h, $dst_type) = getimagesize($dst_path);
   	 
   	//获取水印图片的宽高
   	list($src_w, $src_h) = getimagesize($src_path);
   	 
   	//将水印图片复制到目标图片上，最后个参数50是设置透明度，这里实现半透明效果
   	//imagecopymerge($dst, $src, 10, 10, 0, 0, $src_w, $src_h, 50);
   	//如果水印图片本身带透明色，则使用imagecopy方法

   	
   	if($dst_w>200 && $dst_h>100){

   		if($postion==1){
   			$left=($dst_w-$src_w)/2;
   			$top=($dst_h-$src_h)/2;
   		}
   		elseif($postion==2){
   			$left=$dst_w-$src_w-10;
   			$top=$dst_h-$src_h-10;
   		}
   		
   		imagecopy($dst, $src, $left, $top, 0, 0, $src_w, $src_h);
   	}
   	 
   	 
   	//输出图片
   	switch ($dst_type) {
   		case 1://GIF
   			imagegif($dst,$dst_path);
   			break;
   		case 2://JPG
   			imagejpeg($dst,$dst_path);
   			break;
   		case 3://PNG
   			imagepng($dst,$dst_path);
   			break;
   		default:
   			break;
   	}
   	 
   	imagedestroy($dst);
   	imagedestroy($src);
   	 
   	//return $dst_path;
   }
   
   function thumb($oldImg='',$new_w=0,$new_h=0,$ns='0',$pre='',$new_n='',$path='',$method='gd2'){
     $falg=false;
     
     $new_file=empty($path)?str_replace(basename($oldImg),'',$oldImg):$path;
     $new_file1=empty($new_n)?$pre.basename($oldImg):$pre.$new_n;
     $returnfile=$new_file.$new_file1;
     
     $oldImg=$_SERVER['DOCUMENT_ROOT'].'/'.$oldImg;
     
     $new_file=empty($path)?str_replace(basename($oldImg),'',$oldImg):$path;
     $new_file1=empty($new_n)?$pre.basename($oldImg):$pre.$new_n;
     $new_file=$new_file.$new_file1;
     
     if(file_exists($oldImg)){     	
         //$oldImg=basename($oldImg);//返回路径中的文件名部分
         $imgInfo=getimagesize($oldImg);//得到图片相关信息
     if(!@empty($imgInfo)){
      $old_w=$imgInfo[0];//原图片的宽度
      $old_h=$imgInfo[1];//原图片的高度
      
      if($old_w<$new_w && $old_h<$new_h){
      	return $returnfile;
      }
      
      $radio=max($old_w/$new_w,$old_h/$new_h);
      $des_w=intval($old_w/$radio);//计算新宽---按比例缩放的
      $des_h=intval($old_h/$radio);//计算新高---按比例缩放的
      
	  if($ns==1){
	     $des_w=$new_w;
		 $des_h=$new_h;
	  }

      if($imgInfo[2]==2){
       $source=imagecreatefromjpeg($oldImg);//从jpg文件新建一副图片
      }elseif($imgInfo[2]==3){
       $source=imagecreatefrompng($oldImg);//从png文件新建一副图片
      }else{
       $source=imagecreatefromgif($oldImg);//从gif文件新建一副图片
      }
      
      
      
      
      if(!@empty($source) && @is_resource($source)){
        switch($method){
        case 'gd1':
           $img=imagecreate($des_w,$des_h);//创建画布---新建一个基于调色板的图像,属于GD1库
           imagecopyresized($img,$source,0,0,0,0,$des_w,$des_h,$old_w,$old_h);//图片的拷贝--拷贝部分图像并调整大小,属于GD1库
           if($imgInfo[2]==2){
               imagejpeg($img,$new_file,'100');//保存图片到文件--80为透明度，透明度只jpg有
           }elseif($imgInfo[2]==3){
               imagepng($img,$new_file);//保存图片到文件
           }else{
               imagegif($img,$new_file);//保存图片到文件
           }
           imagedestroy($img);//销毁资源
           $falg=$new_file;
           break;
        case 'gd2':
           $img=imagecreatetruecolor($des_w,$des_h);//创建画布---新建一个真彩色图像,属于GD2库
           imagecopyresampled($img,$source,0,0,0,0,$des_w,$des_h,$old_w,$old_h);//图片的拷贝-- 重采样拷贝部分图像并调整大小,属于GD2库
           if($imgInfo[2]==2){
               imagejpeg($img,$new_file,'100');//保存图片到文件--80为透明度，透明度只jpg有
           }elseif($imgInfo[2]==3){
               imagepng($img,$new_file);//保存图片到文件
           }else{
               imagegif($img,$new_file);//保存图片到文件
           }
           imagedestroy($img);//销毁资源
           $falg=$new_file;
           break;
        default:
        break;
        }
      }
     }}//结束顶部2个判断
    return $returnfile;
    }
}

?>