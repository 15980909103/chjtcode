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
}
