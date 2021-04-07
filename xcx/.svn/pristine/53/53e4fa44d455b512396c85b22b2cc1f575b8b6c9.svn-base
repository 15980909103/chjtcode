<?php
namespace app\common\traits;

/**
 * 压缩字符串与解压缩字符串
 * Trait TraitInstance
 * @package app\common\traits
 */
trait TraitCompressData{
    /**
     * 压缩字符串内容
     * @param string|array $data
     * @return false|string
     */
    protected function compressData($data){
        $data['doTask'] = \Opis\Closure\serialize($data['doTask']);
        return gzcompress(json_encode($data,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 解压字符串内容
     * @param string $data
     * @return false|string
     */
    protected function unCompressData($data = ''){
        $data = json_decode(gzuncompress($data),true);
        $data['doTask'] = \Opis\Closure\unserialize($data['doTask']);
        return $data;
    }
}

