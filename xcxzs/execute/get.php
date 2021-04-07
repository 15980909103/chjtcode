<?php
$dir = dirname(__DIR__);
require_once $dir . '/base.php';
function exception_error_handler($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

set_error_handler("exception_error_handler");

class get{
	public function __construct() {
		$this->q = new Query();
    }
	
	//获取access_toke
    public function getAccessToken(){
		$access_token_time = $this->q->Name('setting')->where_equalTo('`key`','dAcessTokenTime')->select('value')->firstColumn();
        if(time() > $access_token_time || empty($access_token_time)){
            $appid = $this->q->Name('setting')->where_equalTo('`key`','dAppid')->select('value')->firstColumn();
            $secret = $this->q->Name('setting')->where_equalTo('`key`','dAppsecret')->select('value')->firstColumn();
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";
            $token = $this->getJson($url);
            //打印获得的数据
            $arr=json_decode($token,true);
            $access_token = $arr['access_token'];
            $expires_in = $arr['expires_in'] -3600 + time();
            $this->q->Name('setting')->where_equalTo('`key`','dAccessToken')->update(['value'=>$access_token])->execute();
            $this->q->Name('setting')->where_equalTo('`key`','dAcessTokenTime')->update(['value'=>$expires_in])->execute();
            return $access_token;
        }else{
            return $this->q->Name('setting')->where_equalTo('`key`','dAccessToken')->select('value')->firstColumn();
        }
    }
	//解析获取的参数
    public function getJson($url){
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
	//通过openId获取详细信息，再把详细信息放入到数据库里
    public function getOpenIds($openid){
		$access_token = $this->getAccessToken();
        $url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
        $output = $this->getJson($url);
        //打印获得的数据
        $arr=json_decode($output,true);
		try{
			$addArr=[
				"subscribe" => 1,
				"openid" => $arr['openid'],
				"unionid" => $arr['unionid'],
				'nickname' => $arr['nickname'],
				'headimgurl' => $arr['headimgurl'],
				'sex' => $arr['sex'],
				'city' => $arr['city'],
				'country' => $arr['country'],
				'province' => $arr['province'],
				'language' => $arr['language'],
				'create_time' => $arr['subscribe_time'],
				'update_time' => $arr['subscribe_time'],
			];
			$this->q->Name('user')->insert($addArr)->execute();     //插入后返回ID
		}catch(Exception $ex){
        	try{
				$addArr=[
					"subscribe" => 1,
					"openid" => $arr['openid'],
					"unionid" => $arr['unionid'],
					'nickname' => '',
					'headimgurl' => $arr['headimgurl'],
					'sex' => $arr['sex'],
					'city' => $arr['city'],
					'country' => $arr['country'],
					'province' => $arr['province'],
					'language' => $arr['language'],
					'create_time' => $arr['subscribe_time'],
					'update_time' => $arr['subscribe_time'],
				];
				$this->q->Name('user')->insert($addArr)->execute();     //插入后返回ID
			}catch(Exception $ex){
				DataBase::log(__FILE__.__LINE__,$arr);
				DataBase::log(__FILE__.__LINE__,$ex);
			}
		}
    }
	public function execute() {
		$access_token = $this->getAccessToken();
        $url="https://api.weixin.qq.com/cgi-bin/user/get?access_token=$access_token&next_openid=ogQRvt5uee6x_8dz8vC3SQychrKY";
        $output = $this->getJson($url);
		DataBase::log(__FILE__.__LINE__,$output);
        //打印获得的数据
        $arr=json_decode($output,true);
		foreach($arr['data']['openid'] as $v){
			$user_count = $this->q->Name('user')->where_equalTo('openid',$v)->select('count(*)')->firstColumn();
			if ($user_count <= 0){
				echo $v."\n";
				$this->getOpenIds($v);
			}
		}
		echo "next_openid: ".$arr['next_openid']."\n";
	}
	public function t(){
		$openid = "ogQRvtwrtClcE3EpJ6bZqvM90HMg";
		$access_token = $this->getAccessToken();
        $url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
        $output = $this->getJson($url);
        //打印获得的数据
		echo(urldecode("%E6%9F%A0%E6%AA%AC%F0%9F%8D%8B%E8%8D%89"));
        $arr=json_decode($output,true);
		print_r($arr);
		try{
			$addArr=[
				"subscribe" => $arr['subscribe'],
				'nickname' => urlencode($arr['nickname']),
				'wx_info' =>json_encode($arr),
				'headimgurl' => $arr['headimgurl'],
				'sex' => $arr['sex'],
				'city' => $arr['city'],
				'country' => $arr['country'],
				'province' => $arr['province'],
				'language' => $arr['language'],
				'create_time' => $arr['subscribe_time'],
				'update_time' => $arr['subscribe_time'],
			];
			$this->q->Name('user')->update($addArr)->where_equalTo('openid', $arr['openid'])->execute();     //插入后返回ID
		}catch(Exception $ex){
			DataBase::log(__FILE__.__LINE__,$arr);
			DataBase::log(__FILE__.__LINE__,$ex);
		}
	}
}
echo "get ".date("Y-m-d H:i:s")."\n";
$get = new get();
//$get->execute();
$get->t();
