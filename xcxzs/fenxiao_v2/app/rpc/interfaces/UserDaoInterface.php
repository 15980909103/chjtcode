<?php


namespace app\rpc\interfaces;

/**
 * rpc 服务端启动php think swoole start / php think swoole:rpc
 * Interface MyRpcDaoInterface
 * @package app\rpc\interfaces
 */
interface  UserDaoInterface
{
    public function create();
    public function find(int $id);
}