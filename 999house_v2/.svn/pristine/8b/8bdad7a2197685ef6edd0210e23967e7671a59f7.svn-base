<?php

namespace app\server\admin;

use app\common\traits\TraitInstance;
use app\common\base\ServerBase;
use think\Db;
use think\Exception;

/*
 *
 * */

class Sys extends ServerBase
{

    private $toHtmlDecodeList = [
        'book_instruction'
    ];

    public function sysInfo($parms)
    {
        if (empty($parms['key'])) {
            return $this->responseFail('', '缺失参数');
        }
        if (is_array($parms['key'])) {
            $where[] = ['key', $parms['key'][0], $parms['key'][1]];
            $result['list'] = $this->db->name('sysset')->where($where)->select()->toArray();
            if (!empty($result['list'])) {
                foreach ($result['list'] as &$item) {
                    if (in_array($item['key'], $this->toHtmlDecodeList) && !empty($item['val'])) {
                        $item['val'] = htmlspecialchars_decode($item['val']);
                    }
                    if ($item['key'] == 'wxh5' || $item['key'] == 'estates_browse') {
                        $item['val'] = json_decode($item['val'], true);
                    }
                    if ($item['key'] == 'kfmobilecode') {
                        $item['val'] = !empty($item['val']) ? $this->getFormatImgs($item['val']) : [];
                    }
                }
            }
        } else {
            $where[] = ['key', '=', $parms['key']];
            $result['info'] = $this->db->name('sysset')->where($where)->find();

            if (!empty($result['info']) && in_array($result['info']['key'], $this->toHtmlDecodeList) && !empty($result['info']['val'])) {
                $result['info']['val'] = htmlspecialchars_decode($result['info']['val']);
            }
        }

        return $this->responseOk($result);
    }

    public function sysEdit($data)
    {
        $where[] = ['key', '=', $data['key']];
        $has = $this->db->name('sysset')->where($where)->find();

        if (empty($has)) {
            switch ($data['key']) {
                case 'kfmobile';
                    $data['describe'] = '客服电话';
                    break;
                case 'poster_img';
                    $data['describe'] = '分享海报上传';
                    break;
                case 'wxh5';
                    $data['describe'] = '服务号配置';
                case 'kfmobilecode':
                    $data['describe'] = '客服二维吗';
                    break;
            }
            $result = $this->db->name('sysset')->insert($data);
        } else {
            unset($data['key']);
            $this->db->name('sysset')->where($where)->update($data);
        }
        if ($result) {
            return $this->responseOk();
        } else {
            return $this->responseFail();
        }
    }

    public function getFormatImgs($urls)
    {
        $imgs = [];
        if (!is_array($urls)) {
            $urls = explode(',', $urls);
        }
        foreach ($urls as $item) {
            $imgs[] = [
                'name' => basename($item),
                'url'  => $item,
            ];
        }

        return $imgs;
    }

    public function serverCode()
    {
        try {
            $info = $this->db->name('sysset')->where('key','kfmobilecode')->field('val')->find();
            $img = '';
            if($info){
                $img = $info['val'];
            }
            return $this->responseOk($img);
        } catch (\Exception $exception) {
            return $this->responseFail($exception->getMessage());
        }
    }
}
