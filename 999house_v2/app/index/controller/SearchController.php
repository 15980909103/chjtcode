<?php

namespace app\index\controller;

use app\common\base\UserBaseController;
use app\server\index\Search;

class SearchController extends UserBaseController
{

    /**
     * 综合搜索
     */
    public function getTotalSearch()
    {
        $params = $this->request->param();

        $res = (new Search())->getSearch($params);

        if(empty($res['code']) || 1 != $res['code']) {
            $this->error($res);
        }

        $this->success($res['result']);
    }

}