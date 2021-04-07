<?php
namespace app\common\base;
use app\common\pool\RedisPool;
use think\Exception;

class ServerBase
{
    protected $db = null;
    public function __construct( )
    {
        $this->db       = (new HhDb())->init();
    }

    /**
     * //删除文件资料
     * @param $path
     */
    protected function delFile($path){
        $filename = APP_ROOT . 'public' . $path;
        if($path&&file_exists($filename)) {
            unlink($filename);
        }
    }

    /**
     * 获取redis 链接
     */
    protected function  getReids($select=2){
        $reidsObj       = $this->redis    = RedisPool::getInstance();
        $config = [
            'database'=>$select,
            'max_active'=> 5
        ];
        $config  = $reidsObj->setConfig('swoole.pool.redis',$config);
        return $reidsObj->init($config);
    }


    /**
     * 成功时返回
     * @param array $result_data //返回数据
     * @param string $msg //返回信息
     * @return array
     */
    public function responseOk($result=[],$msg='操作成功'){
        return [
            'code' => 1,
            'msg'  => $msg,
            'result' => $result,
        ];
    }
    /**
     * 失败时返回
     * @param array $result_data //返回数据
     * @param string $msg //返回信息
     * @param int $code //错误代码
     * @return array
     *
     */
    public function responseFail($msg='操作失败',$result=[],$code=0){
        if(is_array($msg)){
            $code = $msg['code'];
            $msg = $msg['msg'];
            $result = !empty($msg['result'])?$msg['result']:'';
        }
        return [
            'code' => $code,
            'msg'  => $msg,
            'result' => $result,
        ];
    }

    public  function curlGet($url = '', $options = array())
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }

//        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); //强制协议为1.0
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect: ')); //头部要送出'Expect: '
//        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); //强制使用IPV4协议解析域名

        //https请求 不验证证书和host
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);

        return json_decode($data, true);
    }

    public  function curlPost($url = '', $postData = '', $options = array())
    {
        if (is_array($postData)) {
            $postData = http_build_query($postData);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //设置cURL允许执行的最长秒数
        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }

//        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); //强制协议为1.0
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect: ')); //头部要送出'Expect: '
//        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); //强制使用IPV4协议解析域名

        //https请求 不验证证书和host
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);
        $jsonData = json_decode($data, true);
        if (is_null($jsonData)) {
            return $data;
        } else {
            return $jsonData;
        }
    }
}
