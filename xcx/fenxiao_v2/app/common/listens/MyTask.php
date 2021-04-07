<?php
declare (strict_types = 1);

namespace app\common\listens;
use Opis\Closure\SerializableClosure;
//use think\queue\Job;
//https://github.com/top-think/think-queue
use Swoole\Server;
use think\App;


/**
 * swoole 任务投递监听
 */
class MyTask
{

    // swoole的任务投递的事件监听处理
    /**
     * @param $swoole_taskObj //任务的数据对象
     * @param Server $server //swoole Server对象
     */
    public function handle($swoole_taskObj, Server $server)
    {
        if(empty($swoole_taskObj->data)||empty($swoole_taskObj->data['_$action'])){
            return;
        }
        $result ='';

        try {
            $swoole_taskObj->data = $this->unCompressData($swoole_taskObj->data['_$action']);//数据解压
            //$unserializeObj = (unserialize($swoole_taskObj->data['_$action']))->getClosure();
            if(!empty($swoole_taskObj->data['_$action'])){
                $unserializeObj = \Opis\Closure\unserialize($swoole_taskObj->data['_$action']);
            }

            unset($swoole_taskObj->data['_$action']);
            $postData = $swoole_taskObj->data['_$post_data'];
            unset($swoole_taskObj->data['_$post_data']);


            if(!empty($unserializeObj)){
                if(is_callable($unserializeObj)===true){
                    $result = call_user_func($unserializeObj, $postData, $server)??'';
                }elseif(method_exists($unserializeObj,'run')===true){
                    $unserializeObj = new $unserializeObj();
                    $result = call_user_func([$unserializeObj, 'run'], $postData, $server)??'';
                }
            }

        } catch (\ErrorException $errorException) {
            // 错误异常 //最常用的就是将那几个非致命的错误捕获后 ErrorException 回抛到 try ... catch 中

            echo 'ErrorException';
            //echo 'ErrorException: ' . $errorException . PHP_EOL;
        } catch (\Exception $exception) {
            // 异常
            echo 'Exception';
            //echo 'Exception: ' . $exception . PHP_EOL;
        } catch (\ParseError $parseError) {
            // 解析错误 语法错误
            echo 'Parse Error';
            //echo 'Parse Error: ' . $parseError . PHP_EOL;
        } catch (\ArgumentCountError $argumentCountError ) {
            // 传参非法错误 php >= 7.1.0
            echo 'Argument Count Error';
            //echo 'Argument Count Error: ' . $argumentCountError . PHP_EOL;
        } catch (\TypeError $typeError) {
            // 类型错误 返回值
            echo 'Type Error';
            //echo 'Type Error: ' . $typeError . PHP_EOL;
        } catch (\DivisionByZeroError $divisionByZeroError) {
            // x / 0 不抛出  x % 0 可以抛出 // x / 0 可以用 intdiv(x, 0) 代替 会抛出
            echo 'Division By Zero Error';
            //echo 'Division By Zero Error: ' . $divisionByZeroError . PHP_EOL;
        } catch (\ArithmeticError $arithmeticError) {
            // 算数运算错误 intdiv(PHP_INT_MIN, -1) 触发
            echo 'Arithmetic Error';
            //echo 'Arithmetic Error: ' . $arithmeticError . PHP_EOL;
        } catch (\AssertionError $assertionError) {
            // 断言错误
            echo 'Assertion Error';
            //echo 'Assertion Error: ' . $assertionError . PHP_EOL;
        } catch (\Error $error) {
            // 基本错误
            echo 'Error';
            //echo 'Error: ' . $error . PHP_EOL;
        } finally {
            $swoole_taskObj->finish($result);
        }
    }


    /**
     * 压缩字符串内容
     * @param string|array $data
     * @return false|string
     */
    public function compressData($data){
        return gzcompress(json_encode($data,JSON_UNESCAPED_UNICODE));
    }
    /**
     * 解压字符串内容
     * @param string $data
     * @return false|string
     */
    public function unCompressData($data = ''){
        return json_decode(gzuncompress($data),true);
    }
}
