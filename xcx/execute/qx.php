<?php
$dir = dirname(__DIR__);
require_once $dir . '/base.php';
function exception_error_handler($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

set_error_handler("exception_error_handler");

class qx{
	public function __construct() {
		$this->q = new Query();
    }
	
	public function execute() {
		try{
			//24小时未支付订单自动取消
			$time = time() - 60*60*24;
			$rows = $this->q->Name('order')->select()
				->where_lessThanOrEqual('atime', $time)
				->where_equalTo('status', 1)
				->where_equalTo('pflag', 0)
				->execute();
			foreach ($rows as $row) {
				DataBase::beginTran();
				$this->q->Name('order')->update(array('status'=>7))->where_equalTo('id', $row['id'])->execute();
				$rowMember = $this->q->Name('user')->select()->where_equalTo('id', $row['userid'])->firstRow();
				//微信发送模版信息开始
				require_once Lib . DS . 'WeiXin.php';
				$wechat = new WeiXin();
				if(!empty($rowMember['openid'])){
					$d = array();
					$d['touser'] = $rowMember['openid'];
					$d['template_id'] = 'KnVSofIt5tekhmBYdIUNJF96OJOoOBnbbt1HZTwm59g';
					$d['url'] = '';
					$d['data']['first']['value'] = '24小时未支付订单自动取消';
					$d['data']['first']['color'] = '#173177';
					$d['data']['keyword1']['value'] = $row['orderid'];
					$d['data']['keyword1']['color'] = '#173177';
					$d['data']['keyword2']['value'] = date('Y-m-d H:i:s',$row['atime']);
					$d['data']['keyword2']['color'] = '#173177';
					$d['data']['keyword3']['value'] = date('Y-m-d H:i:s');
					$d['data']['keyword3']['color'] = '#173177';
					$d['data']['remark']['value'] = '感谢您的使用';
					$d['data']['remark']['color'] = '#173177';
					$djson = json_encode($d);
					$token = $wechat->getLocationToken();
					$wx_r = $wechat->sendTemplateMessage($token['access_token'],$djson);
					if($wx_r['errmsg']!='ok'){
						DataBase::log(__FILE__.__LINE__,$wx_r);
					}
				}
				DataBase::commit();
			}
		}catch(Exception $ex){
			DataBase::rollBack();
			DataBase::log(__FILE__.__LINE__,$ex);
		}
	}
}
echo "qx ".date("Y-m-d H:i:s")."\n";
$qx = new qx();
$qx->execute();
