<?php
// +----------------------------------------------------------------------
// | 会话设置
// +----------------------------------------------------------------------

$app_name='';
if(!empty(app()->request->pathinfo())){
  $app_name = explode('/',app()->request->pathinfo())[0].'_';
}

return [
    // session name
    'name'           => 'PHPSESSID',
    // SESSION_ID的提交变量,解决flash上传跨域
    'var_session_id' => '',
    // 驱动方式 支持file cache
    'type'           => 'file',
    // 存储连接标识 当type使用cache的时候有效
    'store'          => null,
    // 过期时间
    'expire'         => 1440,
    // 前缀
    'prefix'         => $app_name,//根据应用加前缀
];
