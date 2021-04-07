<?php

namespace app\common\websocket;

use think\swoole\websocket\socketio\Packet;

/**
 * Class Packet
 */
class MyPacket extends Packet
{


    /**
     * Get data packet from a raw payload.
     *
     * @param string $packet
     *
     * @return array|null
     */
    public static function getPayload(string $packet)
    {
        $packet = trim($packet);
        $start  = strpos($packet, '[');

        if ($start === false || substr($packet, -1) !== ']') {
            return;
        }

        $data = substr($packet, $start, strlen($packet) - $start);
        $data = json_decode($data, true);

        if (is_null($data)) {
            return;
        }

        if(empty($data[2])){
            return;
        }

        return [
            'event' => $data[0],
            'data'  => $data[1] ?? null,
            'module' => $data[2] //用于路由模块分配
        ];
    }
}
