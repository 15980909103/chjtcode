<?php
function getIp(){
	$realip = '';
	if( isset($_SERVER) ){
		if( isset($_SERVER['HTTP_X_FORWARDED_FOR']) ){
			$realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}elseif( isset($_SERVER['HTTP_CLIENT_IP']) ){
			$realip = $_SERVER['HTTP_CLIENT_IP'];
		}elseif( isset($_SERVER['REMOTE_ADDR']) ){
			$realip = $_SERVER['REMOTE_ADDR'];
		}elseif( ! empty($_SERVER['argv']) ){
			$realip = '127.0.0.1';
		}
	}else{
		if( getenv("HTTP_X_FORWARDED_FOR") ){
			$realip = getenv("HTTP_X_FORWARDED_FOR");
		}elseif( getenv("HTTP_CLIENT_IP") ){
			$realip = getenv("HTTP_CLIENT_IP");
		}else{
			$realip = getenv("REMOTE_ADDR");
		}
	}
	$realip=preg_replace('/[^0-9,\.]/', '', $realip);
	if( strpos($realip, ',') !== false ){
		$realip = strstr($realip, ',', true);
	}
	return $realip;
}
function t2t($times){
	$i = (int)($times/(24*60*60));
	$j = (int)($times%(24*60*60));
	$k = (int)($j/(60*60));
	$l = (int)($j%(60*60));
	$m = (int)($l/60);
	$n = (int)($l%60);
	return ($i>0?$i.'天':'').($k>0?$k.'小时':'').($m>0?$m.'分':'').($n>0?$n.'秒':'');
}
/**
* 发起HTTPS请求
*/
function curl_post($url,$data,$header,$post=1)
{
	//初始化curl
	$ch = curl_init();
	//参数设置  
	$res= curl_setopt ($ch, CURLOPT_URL,$url);  
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt ($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_POST, $post);
	if($post)
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
	$result = curl_exec ($ch);
	curl_close($ch);
	return $result;
}
function unescape($source){ 
	$decodedStr = ""; 
	$pos = 0; 
	$len = strlen ($source); 
	while ($pos < $len){ 
		$charAt = substr ($source, $pos, 1); 
		if ($charAt == '%'){ 
			$pos++; 
			$charAt = substr ($source, $pos, 1); 
			if ($charAt == 'u'){ 
				$pos++; 
				$unicodeHexVal = substr ($source, $pos, 4); 
				$unicode = hexdec ($unicodeHexVal); 
				$decodedStr .=u2utf82gb($unicode); 
				$pos += 4; 
			}else{ 
				$hexVal = substr ($source, $pos, 2); 
				$decodedStr .= chr (hexdec ($hexVal)); 
				$pos += 2; 
			}
		}else{
			$decodedStr .= $charAt; 
			$pos++; 
		} 
	} 
	return $decodedStr; 
}
function u2utf82gb($c){
	$strphp = "";
	if($c < 0x80){
		$strphp .= $c;
	}elseif($c < 0x800){
		$strphp .= chr(0xC0 | $c>>6);
		$strphp .= chr(0x80 | $c & 0x3F);
	}elseif($c < 0x10000){
		$strphp .= chr(0xE0 | $c>>12);
		$strphp .= chr(0x80 | $c>>6 & 0x3F);
		$strphp .= chr(0x80 | $c & 0x3F);
	}elseif($c < 0x200000){
		$strphp .= chr(0xF0 | $c>>18);
		$strphp .= chr(0x80 | $c>>12 & 0x3F);
		$strphp .= chr(0x80 | $c>>6 & 0x3F);
		$strphp .= chr(0x80 | $c & 0x3F);
	}
	return $strphp;
}
function uploadFile($obj,$path=''){
	if(!empty($obj['error'])){ 
		return false; 
	} 
	require_once Lib . DS . 'Upload.php';

	if(!empty($path)){
		$UploadDir=$path;
	}else{
		$UploadDir='upload';
	}
	
	$upload = new upload_file;              # 创建对象
	$upload->set_file_type($obj['type']);    # 获得文件类型
	$upload->set_file_name($obj['name']);    # 获得文件名称
	$upload->set_file_size($obj['size']);    # 获得文件尺寸
	$upload->set_upfile($obj['tmp_name']);   # 服务端储存的临时文件名
	$upload->set_size(10240);                          # 设置最大上传KB数
	$upload->set_isdir(1); 
	$upload->set_isfile(1); 
	$upload->set_isover(1); 
	$upload->set_base_directory(BasePath.DS.$UploadDir);     # 文件存储根目录名称
	$upload->set_url("");                              # 文件上传成功后跳转的文件
	$upload->save(); 
	$upname=$upload->getNameDir();
	$newimg = $oldimg=$UploadDir.$upname;		
	/*if($s==1){  //1表示是否缩放图片
	 $news=$ns; //是否等比例缩放,1为按设置的高度
	 $newimg=$upload->thumb($oldimg,$swidth,$sheight,$news);
	}*/
	return $newimg;
}
/**
 * 异步将远程链接上的内容(图片或内容)写到本地
 *
 * @param  $url
 *            远程地址
 * @param  $saveName
 *            保存在服务器上的文件名
 * @param  $path
 *            保存路径
 * @return boolean
 */
function saveImage($url, $saveName, $path) {
	// 设置运行时间为无限制
	set_time_limit ( 0 );

	$url = trim ( $url );
	$curl = curl_init ();
	// 设置你需要抓取的URL
	curl_setopt ( $curl, CURLOPT_URL, $url );
	// 设置header
	curl_setopt ( $curl, CURLOPT_HEADER, 0 );
	// 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
	curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
	// 运行cURL，请求网页
	$file = curl_exec ( $curl );
	// 关闭URL请求
	curl_close ( $curl );
	// 将文件写入获得的数据
	$filename = $path . $saveName;
	// 检测目录
	if (false === checkPath(dirname($filename))) {
		return false;
	}
	$write = @fopen ( $filename, "w" );
	if ($write == false) {
		return false;
	}
	if (fwrite ( $write, $file ) == false) {
		return false;
	}
	if (fclose ( $write ) == false) {
		return false;
	}
}

/**
 * 检查目录是否可写
 * @param  string   $path    目录
 * @return boolean
 */
function checkPath($path)
{
	if (is_dir($path)) {
		return true;
	}

	if (mkdir($path, 0755, true)) {
		return true;
	} else {
		$error = "目录 {$path} 创建失败！";
		return false;
	}
}
function showTip($errstr="未知错误！",$isback=0,$url=''){
	header('Content-type:text/html;charset=utf-8');
	echo "<script language=javascript>alert('$errstr');</script>";
	
	if($isback==1){
		echo "<script language=javascript>history.back();</script>";
		return ;
	}	
	
	if(!empty($url)){
		echo "<script language=javascript>location.href='$url';</script>";
	}
}
function write_xls($file){
	if (!file_exists($file)) {
		exit("not found file.\n");
	}
	require_once Lib . DS . 'PHPExcel' . DS . 'PHPExcel.php';
	require_once Lib . DS . 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php';
	
	$reader = PHPExcel_IOFactory::createReader('Excel5'); //设置以Excel5格式(Excel97-2003工作簿)
	$PHPExcel = $reader->load($file); // 载入excel文件
	$sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
	$highestRow = $sheet->getHighestRow(); // 取得总行数
	$highestColumm = $sheet->getHighestColumn(); // 取得总列数
	$highestColumm= PHPExcel_Cell::columnIndexFromString($colsNum); //字母列转换为数字列 如:AA变为27
	 
	/** 循环读取每个单元格的数据 */
	$str = '';
	for ($row = 1; $row <= $highestRow; $row++){//行数是以第1行开始
		for ($column = 0; $column < $highestColumm; $column++) {//列数是以第0列开始
			$columnName = PHPExcel_Cell::stringFromColumnIndex($column);
			$str .= $columnName.$row.":".$sheet->getCellByColumnAndRow($column, $row)->getValue()."<br />";
		}
	}
	return $str;
}
function redirectTo($url){
	header('Location:'.$url);
}
function is_mobile($text){
	$search = '/^0?1[3|4|5|6|7|8][0-9]\d{8}$/';
	if(preg_match($search,$text)){
		return (true);
	}else{
		return (false);
	}
}


/**
 * 生成随机字符串
 * @param int $length 生成随机字符串的长度
 * @param string $char 组成随机字符串的字符串
 * @return string $string 生成的随机字符串
 */
function str_rand($length = 32, $char = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
{
    if (!is_int($length) || $length < 0) {
        return false;
    }

    $string = '';
    for ($i = $length; $i > 0; $i--) {
        $string .= $char[mt_rand(0, strlen($char) - 1)];
    }

    return $string;
}

function create_code_key($type='',$salt = ''){
    if(empty($salt)){
        $salt = str_rand(8);
    }
    return $type.md5($salt.'_'.md5(uniqid(mt_rand(10000, 99999), true)).'&'.$salt);
}

function create_order_no($type='')
{
    return $type.date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
}

/**
 * 用于扫码激活调跳转的地址
 */
function getActive_code_url($code_key){
    $proto = 'http://';
    if(isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))){
        return $proto = 'https://';
    }elseif(isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'] )) {
        return $proto = 'https://';
    }
    return $proto.$_SERVER['HTTP_HOST'].'/agentside/index.html?code_key='.$code_key;
}