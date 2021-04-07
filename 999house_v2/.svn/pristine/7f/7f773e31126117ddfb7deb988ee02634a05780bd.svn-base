<?php

namespace app\server\index;

use app\common\base\ServerBase;
use app\common\traits\TraitEstates;
use Exception;
use think\facade\Db;

class Search extends ServerBase
{

    use TraitEstates;
   
    public function getSearch($data)
    {
        try {
            if(empty($data['city_no'])) {
                return $this->responseFail('未选择地区');
            }
            if(empty($data['name'])) {
                return $this->responseFail('请输入搜索关键词');
            }
            $name = $data['name'];
            $cityNo = $data['city_no'];

            /**
             * 楼盘
             */
            $whereEstates = [
                ['is_delete', '=', 0],
                ['status', '=', 1],
                ['city', '=', $cityNo],
                ['name|address', 'like', "%{$name}%"],
            ];
            $estateIds = $this->db->name('estates_new')->where($whereEstates)->column('id');
            $estatesCount = sizeof($estateIds);

            /**
             * 资讯
             */
            $whereArticle = [
                ['status', '=', 1],
                ['', 'exp', Db::Raw("FIND_IN_SET({$cityNo}, city_list)")]
            ];
            $articleCount = $this->db->name('article')
                            ->where($whereArticle)
                            ->where(function($query) use ($estateIds, $name) {
                                $where = [
                                    [
                                        ['name', 'like', "%{$name}%"],
                                    ],
                                    [
                                        ['title', 'like', "%{$name}%"],
                                    ]
                                ];
                                // if(!empty($estateIds)) {
                                //     $where[] = [
                                //         ['is_propert_news', '=', 1],
                                //         ['forid', 'in', $estateIds]
                                //     ];
                                // }
                                $query->whereOr($where);
                            })
                            ->count();
            
            /**
             * 视频资讯
             */
            $whereVideo = [
                ['status', '=', 1],
                ['region_no', '=', $cityNo],
            ];
            $videoCount = $this->db->name('article')
                            ->where($whereVideo)
                            ->where(function($query) use ($estateIds, $name) {
                                $where = [
                                    [
                                        ['name', 'like', "%{$name}%"],
                                    ],
                                    [
                                        ['title', 'like', "%{$name}%"],
                                    ]
                                ];
                                // if(!empty($estateIds)) {
                                //     $where[] = [
                                //         ['is_propert_news', '=', 1],
                                //         ['forid', 'in', $estateIds]
                                //     ];
                                // }
                                $query->whereOr($where);
                            })
                            ->count();
                            
            // 结果汇总
            $res = [
                'estates' => $estatesCount,
                'articles' => $articleCount,
                'videos' => $videoCount,
            ];

            return $this->responseOk($res);
        }  catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }
}
