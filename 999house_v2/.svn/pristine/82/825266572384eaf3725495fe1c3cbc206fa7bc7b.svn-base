<?php


namespace app\admin\controller;


use app\common\base\AdminBaseController;
use app\server\admin\CommentList;

class CommentListController extends AdminBaseController
{
    //房源评论列表

    public function propertyReviews()
    {
        $pageSize = $this->request->param('pageSize', 20);
        $status = $this->request->param('status', '-1');
        $name = $this->request->param('name');
        $user_name = $this->request->param('user_name');
        $startdate = $this->request->param('startdate');
        $enddate = $this->request->param('enddate');
        $where = [];

        if ($status != -1) {
            $where[] = ['p.status', '=', $status];
        }
        if (!empty($name)) {
            $where[] = ['e.name', 'like', '%' . $name . '%'];
        }
        if (!empty($user_name)) {
            $where[] = ['p.user_name', 'like', '%' . $user_name . '%'];
        }
        if (!empty($startdate) && !empty($enddate)) {
            $where[] = ['p.create_time', '>=', strtotime($startdate)];
            $where[] = ['p.create_time', '<=', strtotime($enddate)];
        }

        $server = new CommentList();
        $res = $server->propertyReviews($where, $pageSize);

        if ($res['code'] == 0) {
            return $this->error($res['msg']);
        }

        return $this->success($res['result']);
    }


    //楼盘评论审核
    public function propertyReviewsReview()
    {
        $params = $this->request->param();

        $server = new CommentList();
        $res = $server->propertyReviewsReview($params['id'], $params['status']);

        if ($res['code'] == 0) {
            return $this->error($res['msg']);
        }
        return $this->success($res['result']);
    }

    //咨询评论列表
    public function consultingComments()
    {
        $status = $this->request->param('status', '-1');
        $name = $this->request->param('name');
        $user_name = $this->request->param('user_name');
        $startdate = $this->request->param('startdate');
        $enddate = $this->request->param('enddate');
        $region_no = $this->request->param('city');
        $cate_id = $this->request->param('cate_id');
        $pageSize = $this->request->param('pageSize', 20);
        $where = [];

        if ($status != -1) {
            $where[] = ['c.status', '=', $status];
        }
        if (!empty($name)) {
            $where[] = ['a.name', 'like', '%' . $name . '%'];
        }
        if (!empty($user_name)) {
            $where[] = ['c.user_name', 'like', '%' . $user_name . '%'];
        }
        if (!empty($startdate) && !empty($enddate)) {
            $where[] = ['c.create_time', '>=', strtotime($startdate)];
            $where[] = ['c.create_time', '<=', strtotime($enddate)];
        }

//        if (empty($region_no) || $region_no == -1) {
//            $regionRes = $this->getMyCity();
//            $cityIds = !empty($regionRes['data']) ? array_column($regionRes['data'], 'id') : [];
//            $where[] = ['a.region_no', 'in', $cityIds];
//        } else {
//            $where[] = ['a.region_no', '=', $region_no];
//        }

//        if (!empty($cate_id)) {
//            $cate_id = end($cate_id);
//            if (isset($cate_id) && $cate_id !== 'all') {//状态
//                $where[] = ['ac.column_id', '=', $cate_id];
//            }
//        }
        $server = new CommentList();
        $res = $server->consultingComments($where, $pageSize);

        if ($res['code'] == 0) {
            return $this->error($res['msg']);
        }

        return $this->success($res['result']);
    }

    //咨询评论审核
    public function consultingCommentsReview()
    {
        $params = $this->request->param();

        $server = new CommentList();
        $res = $server->consultingCommentsReview($params['id'], $params['status']);

        if ($res['code'] == 0) {
            return $this->error($res['msg']);
        }
        return $this->success($res['result']);
    }

}