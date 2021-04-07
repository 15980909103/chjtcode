<?php


namespace app\admin\controller;
use app\common\base\AdminBaseController;
use app\server\admin\SearchWord;

class SearchWordController extends AdminBaseController
{
    /**
     * 列表
     */
    public function getList(){
        $data = $this->request->param();

        // 用途
        $where[] = ['purpose_type', '=', 1];
        // 状态
        if(isset($data['status']) && in_array($data['status'], [0, 1])) {
            $where[] = ['status', '=', $data['status']];
        }
        // 城市
        if(!empty($data['region_no'])) {
            if(-1 == $data['region_no']) {// 前搜索当全部城市
                $regionRes = $this->getMyCity();

                $cityIds = !empty($regionRes['data']) ? array_column($regionRes['data'], 'id') : [];

                $where[] = ['city', 'in', $cityIds];
            } else {
                $where[] = ['city', '=', $data['region_no']];
            }
        }
        // 名称
        if(!empty($data['name'])) {
            $where[] = ['name', 'like', "%{$data['name']}%"];
        }

        // 页码
        if(!empty($data['page_size'])) {
            $pageSize = $data['page_size'];
            if($pageSize > 100) {
                $this->error('获取数量超出限制');
            }
        } else {
            $pageSize = 20;
        }

        // 字段
        $fields = '*';

        $res = (new SearchWord())->getList($where, $fields, $pageSize);

        if(empty($res['code']) || 1 != $res['code']) {
            $this->error($res);
        }

        $result = $res['result'];

        // 获取绑定的楼盘或者标签
        $estates = [];
        $tag = [];
        if(!empty($result['list'])) {
            foreach($result['list'] as $v) {
                if(1 == $v['type']) {
                    $estates[] = $v['bind_id'];
                }
                if(2 == $v['type']) {
                    $tag[] = $v['bind_id'];
                }
            }
        }
        if(!empty($estates)) {
            $estatesName = $this->db->name('estates_new')->where([['id', 'in', $estates]])->field('id, name')->select()->toArray();
        }
        if(!empty($tag)) {
            $tagName = $this->db->name('estates_tag')->where([['id', 'in', $tag]])->field('id, name')->select()->toArray();
        }
        if(!empty($estatesName) || !empty($tagName)) {
            foreach($result['list'] as &$item) {
                switch($item['type']) {
                    case 1:
                        if(!empty($estatesName)) {
                            foreach($estatesName as $e) {
                                if($item['bind_id'] == $e['id']) {
                                    $item['bind_name'] = $e['name'];
                                    break;
                                }
                            }
                        }
                        break;
                    case 2:
                        if(!empty($tagName)) {
                            foreach($tagName as $t) {
                                if($item['bind_id'] == $t['id']) {
                                    $item['bind_name'] = $t['name'];
                                    break;
                                }
                            }
                        }
                        break;
                }
            }
        }

        $this->success($result);
    }

    /**
     * 删除
     */
    public function delete()
    {
        $data = $this->request->param();

        if(empty($data['id'])) {
            $this->error('缺少必要参数');
        }
        $id = $data['id'];

        $res = (new SearchWord())->delete($id);

        if(empty($res['code']) || 1 != $res['code']) {
            $this->error($res);
        }

        $this->success($res);
    }

    /**
     * 添加/编辑
     */
    public function edit(){
        $data = $this->request->param();

        $id = !empty($data['id']) ? intval($data['id']) : 0;

        $indata = [
            'name' => $data['name'] ?? "",
            'status' => intval($data["status"]),
            'sort' => intval($data["sort"]),
            'bind_id' => intval($data["bind_id"]),
            'city' => $data['city'] ?? '',
            'type' => (int)$data['type'],
            'purpose_type' => (int)$data['purpose_type'],
        ];

        if($id){
            $where = [
                ['id', '=', $id],
            ];
            $res = (new SearchWord())->edit($where, $indata);
        }else{
            $res = (new SearchWord())->add($indata);
        }

        if(1 == $res['code']){
            $this->success();
        }else{
            $this->error($res);
        }
    }

    /**
     * 排序
     */
    public function changeSort(){
        $data = $this->request->param();

        if(empty($data['id'])) {
            $this->error('缺少必要参数');
        }

        $id = $data['id'];

        $where = [
            ['id', '=', $id],
        ];

        $res = (new SearchWord())->edit($where,['sort' => (int)$data['sort']]);

        if(1 == $res['code']){
            $this->success();
        }else{
            $this->error($res);
        }
    }

    /**
     * 状态变更
     */
    public function changeStatus(){
        $data = $this->request->param();

        if(empty($data['id'])) {
            $this->error('缺少必要参数');
        }

        $id = $data['id'];

        $where = [
            ['id', '=', $id],
        ];

        $res = (new SearchWord())->edit($where,['status' => (int)$data['status']]);

        if(1 == $res['code']){
            $this->success();
        }else{
            $this->error($res);
        }
    }
}