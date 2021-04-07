<?php

namespace app\common\websocket;

use think\facade\Event;
use think\swoole\websocket\socketio\Packet;
use think\swoole\websocket\socketio\Parser;

class MyParser extends Parser
{
    /**
     * Decode message from websocket client.
     * Define and return payload here.
     *
     * @param \Swoole\Websocket\Frame $frame
     *
     * @return array
     */
    public function decode($frame)
    {
        $payload = MyPacket::getPayload($frame->data);

        return [
            'event' => $payload['event'] ?? '',
            'data'  => $payload['data'] ?? '',
            'module' => $payload['module'] ?? '',
        ];
    }
}
