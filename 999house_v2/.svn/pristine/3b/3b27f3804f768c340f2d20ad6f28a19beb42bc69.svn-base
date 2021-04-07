<?php


namespace app\admin\controller;
use app\common\base\AdminBaseController;
use app\server\admin\InterestRate;

class InterestRateController extends AdminBaseController
{
    /**
     * 利率列表
     */
    public function getList()
    {
        $params = $this->request->param();

        $search = [
            'type' => $params['type'] ?? 0,
            'status' => $params['status'] ?? 0,
            'start_time' => $params['start_time'] ?? 0,
            'end_time' => $params['end_time'] ?? 0,
        ];

        $pageSize = $params['page_size'] ?? 20;
        if($pageSize > 100) {
            $this->error('超出限制');
        }

        $res = (new InterestRate())->getList($search, '*', $pageSize);

        if(empty($res['code']) || 1 != $res['code']) {
            $this->error($res);
        }
        $res = $res['result'];

        $this->success($res);
    }

    /**
     * 添加/编辑 利率
     */
    public function edit()
    {
        $params = $this->request->param();

        $id = $params['id'] ?? 0;

        if(empty($params['type'])) {
            $this->error('缺少必要参数');
        }
        $type = $params['type'];

        // 利率年限校验
        if(!empty($params['content'])) {
            if(1 == $type) {
                $content = $this->checkLPR($params['content']);
            } else {
                $content = $this->checkRate($params['content']);
            }
            
            $content = json_encode($content);
        } else {
            $content = '';
        }

        $data = [
            'type' => $type,
            'content' => $content,
            'basic_point' => !empty($params['basic_point']) ? json_encode($params['basic_point']) : '',
            'release_time' => !empty($params['release_time']) ? strtotime($params['release_time']) : 0,
        ];

        if($id) {
            $where = [
                ['id', '=', $id]
            ];
            $res = (new InterestRate())->edit($where, $data);
        } else {
            $res = (new InterestRate())->add($data);
        }

        if(empty($res['code']) || 1 != $res['code']) {
            $this->error($res);
        }

        $this->success();
    }

    /**
     * 校验并处理基准利率
     */
    protected function checkRate($content)
    {
        if(empty($content)) {
            return [];
        }
        // 根据年限开始时间排序
        $start = array_column($content, 'start');
        array_multisort($start, SORT_ASC, $content);

        $prevNum = -1;// 上一条利率的结束年限
        $count = sizeof($content);// 总条数
        $i = 1;
        foreach($content as &$v) {
            $v['start'] = (int)$v['start'];
            $v['end'] = (int)$v['end'];
            if($i == $count) {// 循环到最后一条
                if(empty($v['end']) || $v['end'] > 30) {
                    $v['end'] = 30;// 上限设置为30
                }
            }
            $i++;
            // 年限开始大于年限结束
            if($v['start'] > $v['end']) {
                $this->error('年限区间有误');
                break;
            }
            /**
             * 通过开始年限排完序后，对比本条的开始年限和上条的结束年限来对比是否有交集或者空白
             */
            // 年限出现交集
            if($v['start'] <= $prevNum) {
                $this->error('年限区间出现交集');
                break;
            }
            // 年限出现空白
            if($v['start'] > $prevNum+1) {
                $this->error('年限区间出现空白');
                break;
            }
            $prevNum = $v['end'];// 储存上一条的结束年限
        }
        return $content;
    }

    /**
     * 校验LPR
     */
    protected function checkLPR($content)
    {
        if(empty($content)) {
            return [];
        }

        // 排序
        $start = array_column($content, 'year');
        array_multisort($start, SORT_ASC, $content);

        $prevNum = 0;// 上一条记录的年期
        foreach($content as $v) {
            if(empty($v['year'])) {
                $this->error('年期不能为空');
            }
            if($prevNum == $v['year']) {
                $this->error('设置了重复年期');
            }
            $prevNum = $v['year'];
        }

        return $content;
    }

    /**
     * 状态 利率
     */
    public function status()
    {
        $params = $this->request->param();

        if(empty($params['id'])) {
            $this->error('缺少必要参数');
        }

        $status = $params['status'] ?? 0;
        if(!in_array($status, [0, 1])) {
            $this->error('非法状态');
        }

        $id = $params['id'];

        $where = [
            ['id', '=', $id],
        ];

        $update = [
            'status' => $status,
        ];

        $res = (new InterestRate())->edit($where, $update);

        if(empty($res['code']) || 1 != $res['code']) {
            $this->error($res);
        }

        $this->success();
    }

    /**
     * 删除
     */
    public function delete()
    {
        $params = $this->request->param();

        if(!empty($params['id'])) {
            $this->error('缺少必要参数');
        }

        $res = (new InterestRate())->delete($params['id']);

        if(empty($res['code']) || 1 != $res['code']) {
            $this->error($res);
        }

        $this->success();
    }
}