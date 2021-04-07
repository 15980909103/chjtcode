<?php
/**
 * create by Jerry
 * on 2015-06-10
 */

class WeiXin{
	
	/*
	保存本地微信Token
	*/
	public function setLocationToken($data)
	{
		$path = BasePath . DS . 'json' . DS . 'wx';
		if (!is_dir($path)){
			mkdir($path);
		}
		$file = $path . DS . 'token.json';
		file_put_contents($file, $data);
	}
	
	/*
	获取本地微信Token
	*/
	public function getLocationToken()
	{
		$path = BasePath . DS . 'json' . DS . 'wx';
		$file = $path . DS . 'token.json';
		if(!file_exists($file)){
			$json = '';
		}else{
			$json = file_get_contents($file);
			$d = json_decode($json, true);
			$time = $d['time'];
			if(time() - $time >5){
				$json = '';
			}
		}
		if($json == ''){
			$r = $this->getAccessToken(WX_APPID,WX_APPSECRET);
			$s = array();
			$s = $r;
			$s['time'] = time();
			$str = json_encode($s);
			$this->setLocationToken($str);
		}else{
			$r = json_decode($json, true);
		}
		return $r;
	}
	
	/*
	保存本地微信ticket
	*/
	public function setLocationTicket($data)
	{
		$path = BasePath . DS . 'json' . DS . 'wx';
		if (!is_dir($path)){
			mkdir($path);
		}
		$file = $path . DS . 'ticket.json';
		file_put_contents($file, $data);
	}
	
	/*
	获取本地微信ticket
	*/
	public function getLocationTicket()
	{
		$path = BasePath . DS . 'json' . DS . 'wx';
		$file = $path . DS . 'ticket.json';
		if(!file_exists($file)){
			$json = '';
		}else{
			$json = file_get_contents($file);
			$d = json_decode($json, true);
			$time = $d['time'];
			if(time() - $time >3600){
				$json = '';
			}
		}
		if($json == ''){
			$token = $this->getLocationToken();
			$r = $this->getAccessTicket($token['access_token']);
			$s = array();
			$s = $r;
			$s['time'] = time();
			$str = json_encode($s);
			$this->setLocationTicket($str);
		}else{
			$r = json_decode($json, true);
		}
		return $r;
	}
	
	/**
	 * 微信配置验证
	 */
	public function checkSignature($token,$echoStr,$signature,$timestamp,$nonce)
	{  
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
	
		if( $tmpStr == $signature ){
			return $echoStr;
		}
		
		return false;
	}
	
	
	/**
	 * 通过微信服务器取得普通access_token
	 */
	public function getAccessToken($appId,$appSecret){
		$url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appId.'&secret='.$appSecret;
		$result=$this->curlGet($url);	
		$resultarr=json_decode($result,true);
		
		return $resultarr;
	}
	
	
	/**
	 * 通过微信服务器取得网页授权access_token
	 */
	public function getWebAccessToken($appId,$appSecret,$code){ 
		$url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appId.'&secret='.$appSecret.'&code='.$code.'&grant_type=authorization_code';
		$result=$this->curlGet($url);
		$resultarr=json_decode($result,true);
	
		return $resultarr;
	}
	
	/**
	 * 通过微信服务器取得ticket
	 */
	public function getAccessTicket($token){
		$url='https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$token.'&type=jsapi';
		$result=$this->curlGet($url);	
		$resultarr=json_decode($result,true);
		
		return $resultarr;
	}
	
	/**
	 * 获取公众号带参数的永久二维码
	 */
	public function getCreateQrcode($token,$data){
		$url='https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$token;
		$result=$this->curlPost($url,$data);
		$resultarr=json_decode($result,true);
		
		return $resultarr;
	}
	
	/**
	 * 拉取用户信息(需scope为 snsapi_userinfo)
	 */
	public function getSnsapiUserinfo($webToken,$openId){ 
		$url='https://api.weixin.qq.com/sns/userinfo?access_token='.$webToken.'&openid='.$openId.'&lang=zh_CN';
		$result=$this->curlGet($url);
		$resultarr=json_decode($result,true);
	
		return $resultarr;
	} 
	
	/**
	 * 拉取用户基本信息
	 */
	public function getUserinfo($webToken,$openId){ 
		$url='https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$webToken.'&openid='.$openId.'&lang=zh_CN';
		$result=$this->curlGet($url);
		$resultarr=json_decode($result,true);
	
		return $resultarr;
	}
	
	/**
	 * 通过微信服务器取得jsapi_ticket
	 */
	public function getJsapiTicket($accessToken){
		$url='https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$accessToken.'&type=jsapi';
		$result=$this->curlGet($url);
		$resultarr=json_decode($result,true);
	
		return $resultarr;
	}
		
	
	/**
	 * 发布微信菜单
	 */
	public function createMenu($accessToken,$data){	
		$url="https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$accessToken;
		$result=$this->curlPost($url,$data);
		$resultarr=json_decode($result,true);
	
		return $resultarr;
	}

	
	/**
	 * 客服接口-发消息
	 */
	public function sendMessage($accessToken,$data){
		$url="https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$accessToken;
		$result=$this->curlPost($url,$data);
		$resultarr=json_decode($result,true);
	
		return $resultarr;
	}
	 
	
	/**
	 * 回复消息模板
	 */
	public function responseTpl($tplType){
		$textTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[text]]></MsgType>
						<Content><![CDATA[%s]]></Content>
						<FuncFlag>0</FuncFlag>
						</xml>";
	
		$picTpl  = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[image]]></MsgType>
						<Image>
						<MediaId><![CDATA[%s]]></MediaId>
						</Image>
						</xml>";
			
		$voiceTpl= "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[voice]]></MsgType>
						<Voice>
						<MediaId><![CDATA[%s]]></MediaId>
						</Voice>
						</xml>";
	
		$videoTpl= "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[video]]></MsgType>
						<Video>
						<MediaId><![CDATA[%s]]></MediaId>
						<Title><![CDATA[%s]]></Title>
						<Description><![CDATA[%s]]></Description>
						</Video>
						</xml>";
	
		$newsTpl= "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[news]]></MsgType>
						%s
						</xml>";
	
		switch ($tplType)
		{
			case "1":
				$tpl= $textTpl;
				break;
			case "2":
				$tpl= $picTpl;
				break;
			case "3":
				$tpl= $voiceTpl;
				break;
			case "4":
				$tpl= $videoTpl;
				break;
			case "5":
				$tpl= $newsTpl;
				break;
		}
	
		return $tpl;
	}
	
	/**
	 * 发送模版消息
	 */
	public function sendTemplateMessage($accessToken,$data){
		$url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$accessToken;
		$result=$this->curlPost($url,$data);
		$resultarr=json_decode($result,true);
		return $resultarr;
	}
	
	
	
	/**
	 * 获取素材列表
	 * 
	 * $type:素材的类型，图片（image）、视频（video）、语音 （voice）、图文（news） 
	 * $offset:从全部素材的该偏移位置开始返回，0表示从第一个素材返回 
	 * $count:返回素材的数量，取值在1到20之间 
	 */
	public function materialList($accessToken,$type,$offset,$count){
		$url="https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=".$accessToken;
		
		$data=array('type'=>$type,'offset'=>$offset,'count'=>$count);
		$result=$this->curlPost($url,$data);
		$resultarr=json_decode($result,true);
	
		return $resultarr;
	}
	
	
	/**
	 * 获取素材总数
	 */
	public function materialCount($accessToken){
		$url="https://api.weixin.qq.com/cgi-bin/material/get_materialcount?access_token=".$accessToken;
		$result=$this->curlGet($url);
		$resultarr=json_decode($result,true);
	
		return $resultarr;
	}
	
	
	/**
	 * CURL GET方法
	 */
	private function curlGet($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$result = curl_exec($ch);
		curl_close($ch);
	
		return $result;
	}
	
	
	/**
	 * CURL POST方法
	 */
	private function curlPost($url,$data){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$result = curl_exec($ch);
		curl_close($ch);
	
		return $result;
	}
}
?>