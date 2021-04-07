<?php

class Wechat extends Controller{
    public function valid(){
        DataBase::log(__FILE__.__LINE__,'valid');
		//配置URL
        if (Context::Get('echostr')) {
            $this->wecha();
        }else{
            $this->responseMsg();
        }
    }

    public function wecha(){
        $echoStr = Context::Get('echostr');
		DataBase::log(__FILE__.__LINE__,$echoStr);
        if($this->checkSignature()){
            echo $echoStr;
        }
    }

    private function checkSignature(){
        $q = new Query();
		$signature = Context::Get('signature');
        $timestamp = Context::Get('timestamp');
        $nonce = Context::Get('nonce');
        $token =  $integral = $q->Name('setting')->where_equalTo('`key`','TOKEN')->select('value')->firstColumn();
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
			return true;
        }else{
			return false;
        }
    }

    public function responseMsg(){
        $q = new Query();
		//扫描二维码动作
        //get post data, May be due to the different environments
        $postStr = file_get_contents("php://input");
		DataBase::log(__FILE__.__LINE__,$postStr);
        //extract post data
        if (!empty($postStr)) {
            /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
               the best way is to check the validity of xml by yourself */
            libxml_disable_entity_loader(true);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $postObj = json_encode($postObj);
            $postObj = json_decode($postObj,true);
            $fromUsername = $postObj['FromUserName'];//请求的用户
            $CreateTime = trim($postObj['CreateTime']);//文本消息内容
            $Event = empty($postObj['Event']) ? '' : $postObj['Event'];//关注状态
            if (!empty($fromUsername)) {
                if ($Event == "subscribe") {
                    //判断操作是关注
                    $user_count = $q->Name('user')->where_equalTo('openid',$fromUsername)->select('count(*)')->firstColumn();
                    if ($user_count > 0) {
                        //修改关注状态
                        $q->Name('user')->where_equalTo('openid',$fromUsername)->update(['subscribe' => 1,'update_time' => $CreateTime])->execute();
                    } else {
                        //获取用户信息
                        $this->getOpenIds($fromUsername);
                    }
                }else  if ($Event == "SCAN") {
                    //用户已关注时的事件推送
                    $user_count = $q->Name('user')->where_equalTo('openid',$fromUsername)->select('count(*)')->firstColumn();//判断Openid是否有重复，针对管理员
                    if ($user_count > 0) {
                        //修改关注状态
                        $q->Name('user')->where_equalTo('openid',$fromUsername)->update(['subscribe' => 1,'update_time' => $CreateTime])->execute();
                    } else {
                        //获取用户信息
                        $this->getOpenIds($fromUsername);
                    }
                }else if ($Event == "unsubscribe") {//取消关注触发
                    //修改关注状态
                    $q->Name('user')->where_equalTo('openid',$fromUsername)->update(['subscribe' => 0,'update_time' => $CreateTime])->execute();
                }else  if ($Event == "VIEW") {//点击菜单跳转链接时触发
                    //用户已关注时的事件推送
                    $user_count = $q->Name('user')->where_equalTo('openid',$fromUsername)->select('count(*)')->firstColumn();//判断Openid是否有重复，针对管理员
                    if ($user_count > 0) {
                        //修改关注状态
                        $q->Name('user')->where_equalTo('openid',$fromUsername)->update(['subscribe' => 1,'update_time' => $CreateTime])->execute();
                    } else {
                        //获取用户信息
                        $this->getOpenIds($fromUsername);
                    }
                }
            }
        }
    }

    //通过openId获取详细信息，再把详细信息放入到数据库里
    public function getOpenIds($openid){
        $q = new Query();
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
			$q->Name('user')->insert($addArr)->execute();     //插入后返回ID
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
				$q->Name('user')->insert($addArr)->execute();     //插入后返回ID
			}catch(Exception $ex){
				DataBase::log(__FILE__.__LINE__,$arr);
				DataBase::log(__FILE__.__LINE__,$ex);
			}
		}
    }
	
	public function getAllOpenids(){
		$q = new Query();
		$access_token = $this->getAccessToken();
        $url="https://api.weixin.qq.com/cgi-bin/user/get?access_token=$access_token&next_openid=ogQRvtwYwEsDg5OkrxeKCL9UWIco";
        $output = $this->getJson($url);
		DataBase::log(__FILE__.__LINE__,$output);
        //打印获得的数据
        $arr=json_decode($output,true);
		foreach($arr['data']['openid'] as $v){
			$user_count = $q->Name('user')->where_equalTo('openid',$v)->select('count(*)')->firstColumn();
			if ($user_count <= 0){
				echo $v."</br>";
				$this->getOpenIds($v);
			}
		}
		echo $arr['next_openid']."</br>";
	}

    //获取access_toke
    public function getAccessToken(){
        $q = new Query();
		$access_token_time = $q->Name('setting')->where_equalTo('`key`','dAcessTokenTime')->select('value')->firstColumn();
        if(time() > $access_token_time || empty($access_token_time)){
            $appid = $q->Name('setting')->where_equalTo('`key`','dAppid')->select('value')->firstColumn();
            $secret = $q->Name('setting')->where_equalTo('`key`','dAppsecret')->select('value')->firstColumn();
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";
            $token = $this->getJson($url);
            //打印获得的数据
            $arr=json_decode($token,true);
            $access_token = $arr['access_token'];
            $expires_in = $arr['expires_in'] -3600 + time();
            $q->Name('setting')->where_equalTo('`key`','dAccessToken')->update(['value'=>$access_token])->execute();
            $q->Name('setting')->where_equalTo('`key`','dAcessTokenTime')->update(['value'=>$expires_in])->execute();
            return $access_token;
        }else{
            return $q->Name('setting')->where_equalTo('`key`','dAccessToken')->select('value')->firstColumn();
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
    //最后抽奖api
    public function prize(){
        $prize =(new Query())->Name('prize')->where_equalTo('status',1)->select()->execute();
        $user = (new Query())->Name('user')->where_express("subscribe=1 AND (integral>1000 OR phone!='0')",array())->select('id,nickname,headimgurl,phone')->execute();
        foreach($user as &$return){
            $return['hide_phone'] = empty($return['phone'])?'':substr_replace($return['phone'],'****',3,4);
            $return['name'] = $return['nickname'];
            $return['image'] = $return['headimgurl'];
            $return['thumb_image'] = $return['headimgurl'];
        }
        $count = (new Query())->Name('setting')->where_equalTo('`key`','count')->select('value')->firstColumn();
        if($count == 0){
            $count = count((new Query())->Name('user')->select()->execute());
            $count=intval($count)+22000;
        }
        echo json_encode(['success'=>true,'prize'=>$prize,'user'=>$user,'count'=>"$count"]);
    }
    public function prizeAdd(){
        $user_id = intval(Context::Post('id'));
        $phone = Context::Post('phone');
        (new Query())->Name('setting')->where_equalTo('`key`','user_id')->update(['value'=>$user_id])->execute();
        (new Query())->Name('setting')->where_equalTo('`key`','phone')->update(['value'=>$phone])->execute();
        //添加开奖记录
        (new Query())->Name('prize_log')->insert(['user_id'=>$user_id,'create_time'=>time()])->execute();
        echo json_encode(['success'=>true]);
    }
}
