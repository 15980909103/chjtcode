<?php

namespace app\common\websocket;


use think\swoole\websocket\Pusher;

class MyPusher extends Pusher
{

    /**
     * @param int $fd
     *
     * @return bool
     */
    protected function shouldPushToDescriptor(int $fd): bool
    {
        if (!$this->server->isEstablished($fd)) {
            return false;
        }

        return $this->broadcast ? $this->sender !== (int) $fd : true;
    }

}
