<?php


namespace app\server\admin;


use app\common\base\ServerBase;
use app\common\MyConst;

class CommentList extends ServerBase
{

    /**
     * 楼盘评论审核列表
     * @param $where
     * @param int $pageSize
     * @return array
     */
    public function propertyReviews($where, $pageSize = 20)
    {
        try {
            $list = $this->db->name('property_reviews')->alias('p')
                ->join('estates_new e', 'e.id = p.estates_new_id')
                ->where($where)
                ->field('p.id,p.user_name,p.content,p.status,p.create_time,e.name,p.img,p.house_inspection')
                ->order(['p.id' => 'desc'])
                ->paginate($pageSize);

            if ($list->isEmpty()) {
                $result['list'] = [];
                $result['total'] = 0;
                $result['last_page'] = 0;
                $result['current_page'] = 0;
            } else {
                $list = $list->toArray();
                foreach ($list['data'] as $key => &$value) {
                    $value['name_title'] = $value['name'] . '-' . $value['title'];
                    $value['create_time'] = date('Y-m-d H:i', $value['create_time']);
                    $value['img'] = explode(',', $value['img']);
                    //类型
                    $value['house_inspection_name'] = $value['house_inspection'] == 1 ? MyConst::HOUSE_INSPECTION_NO : MyConst::HOUSE_INSPECTION_YES;
                }
                $result['list'] = $list['data'];
                $result['total'] = $list['total'];
                $result['last_page'] = $list['last_page'];
                $result['current_page'] = $list['current_page'];
            }
            return $this->responseOk($result);
        } catch (\Exception $exception) {
            return $this->responseFail($exception->getMessage());
        }
    }

    /**
     * 楼盘评论审核
     * @param $id
     * @param $status
     * @return array
     */
    public function propertyReviewsReview($id, $status)
    {
        try {
            $info = $this->db->name('property_reviews')->where('id', $id)->find();
            if (!$info) {
                return $this->responseFail('修改失败');
            }
            $this->db->name('property_reviews')->where('id', $id)->update([
                'status'      => $status,
                'update_time' => time()
            ]);
            return $this->responseOk();
        } catch (\Exception $exception) {
            return $this->responseFail($exception->getMessage());
        }
    }


    /**
     * 咨询评论审核列表
     * @param $where 条件
     * @param $pageSize 页数
     * @return array
     */
    public function consultingComments($where, $pageSize = 20)
    {
        try {

            $list = $this->db->name('consulting_comments')->alias('c')
                ->join('article a', 'a.id = c.article_id')
//                ->rightJoin('article_cloumn ac', 'a.id=ac.article_id')
                ->where($where)
                ->field('c.id,c.user_name,c.content,c.status,c.create_time,a.name,a.title')
                ->order(['c.id' => 'desc'])
//                ->group('a.id')
                ->paginate($pageSize);
            if ($list->isEmpty()) {
                $result['list'] = [];
                $result['total'] = 0;
                $result['last_page'] = 0;
                $result['current_page'] = 0;
            } else {
                $list = $list->toArray();
                foreach ($list['data'] as $key => &$value) {
                    $value['name_title'] = $value['name'] . '-' . $value['title'];
                    $value['create_time'] = date('Y-m-d H:i', $value['create_time']);
                }
                $result['list'] = $list['data'];
                $result['total'] = $list['total'];
                $result['last_page'] = $list['last_page'];
                $result['current_page'] = $list['current_page'];
            }
//            echo $this->db->getLastSql();
            return $this->responseOk($result);
        } catch (\Exception $exception) {
            return $this->responseFail($exception->getMessage());
        }
    }

    /**
     * 审核
     * @param $id
     * @param $status
     * @return array
     */
    public function consultingCommentsReview($id, $status)
    {
        try {
            $info = $this->db->name('consulting_comments')->where('id', $id)->find();
            if (!$info) {
                return $this->responseFail('修改失败');
            }
            $this->db->name('consulting_comments')->where('id', $id)->update([
                'status'      => $status,
                'update_time' => time()
            ]);
            return $this->responseOk();
        } catch (\Exception $exception) {
            return $this->responseFail($exception->getMessage());
        }
    }

    //用户楼栋评论列表
    public function propertyReviewsList($param, $pageSize)
    {
        try {
            $where = [
                ['estates_new_id', '=', $param['id']],
                ['status', '=', 1],
                ['pid', '=', 0],
            ];

            if (!empty($param['is_img'])) {
                $where[] = ['is_img', '=', $param['is_img']];
            }
            $list = $this->db->name('property_reviews')
                ->where($where)
                ->field('id,user_name,user_avatar,content,create_time,img')
                ->order('create_time desc')
                ->paginate($pageSize);

            if ($list->isEmpty()) {

                $result['list'] = [];
                $result['total'] = 0;
                $result['last_page'] = 0;
                $result['current_page'] = 0;
            } else {
                $list = $list->toArray();
                $idArray = array_column($list['data'], 'id');
                $pidWhere = [
                    ['pid', 'in', $idArray],
                    ['status', '=', 1]
                ];
                $data = $this->db->name('property_reviews')
                    ->where($pidWhere)
                    ->field('id,user_name,img,user_avatar,content,pid,create_time,img')
                    ->order('create_time desc')
                    ->select()->toArray();

                foreach ($list['data'] as $key => &$value) {
                    //图片
                    $value['img'] = empty($value['img']) ? [] : explode(',', $value['img']);
                    foreach ($data as $k => $v) {
                        $v['img'] = empty($v['img']) ? [] : explode(',', $v['img']);
                        if ($value['id'] == $v['pid']) {
                            $value['pid_list'] = $v;
                        }
                    }
                    $value['pid_list'] = $value['pid_list'] ?? [];
                }
                $result['list'] = $list['data'];
                $result['total'] = $list['total'];
                $result['last_page'] = $list['last_page'];
                $result['current_page'] = $list['current_page'];

            }

            return $this->responseOk($result);
        } catch (\Exception $exception) {
            return $this->responseFail($exception->getMessage());
        }
    }

    /**
     * 楼栋评论
     * @param $param
     * @return array
     */
    public function propertyReviewsComment($param)
    {
        try {
            $res = false;
            if (empty($param['img'])) {
                $is_img = MyConst::IMG_NO;
                $param['img'] = '';
            } else {
                $is_img = MyConst::IMG_YES;
                $param['img'] = implode(',', $param['img']);
            }

            $nowtime = time();
            $data = [
                'estates_new_id'   => $param['id'],
                'user_id'          => $param['user_id'],
                'type'             => $param['listings'],
                'house_inspection' => $param['house_inspection'],
                'pid'              => empty($param['pid']) ? 0 : $param['pid'],
                'content'          => $param['comment'],
                'img'              => $param['img'],
                'is_img'           => $is_img,
                'create_time'      => $nowtime,
                'update_time'      => $nowtime,
                'status'      => intval($param['status']),
            ];

            switch ($param['type']) {
                case MyConst::H5:
                    $res = $this->h5propertyReviewsComment($param, $data);
                    break;
                case MyConst::WX_H5:
                    $res = $this->wxH5propertyReviewsComment($param, $data);
                    break;
                default:
                    $res = $this->wxH5propertyReviewsComment($param, $data);
                    break;
            }

            if (!$res) {
                return $this->responseFail('评论失败');
            }
            return $this->responseOk();

        } catch (\Exception $exception) {
            return $this->responseFail($exception->getMessage());
        }
    }

    //楼栋wxh5评论
    public function wxH5propertyReviewsComment($param, $data)
    {
        try {
            $info = $this->db->name('user')
                ->where(['id' => $param['user_id']])
                ->field('id,unionid,nickname,headimgurl')
                ->find();
            if (empty($info)) {
                $data['user_name'] = '匿名用户';
                $data['user_avatar'] = '';
            } else {
                $data['user_name'] = $info['nickname'];
                $data['user_avatar'] = $info['headimgurl'];
            }

            $re = $this->db->name('property_reviews')->insert($data);
            if ($re) {
                return true;
            }
            return false;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    //楼栋h5评论
    public function h5propertyReviewsComment($param, $data)
    {
        try {
            //判断手机用户是否有union
            $info = $this->db->name('user')
                ->where(['id' => $param['user_id']])
                ->field('id,unionid,nickname,headimgurl')
                ->find();

            if (empty($info['unionid'])) {
                $data['user_name'] = '匿名用户';
                $data['user_avatar'] = '';

            } else {
                $data['user_name'] = $info['nickname'];
                $data['user_avatar'] = $info['headimgurl'];
            }
            $re = $this->db->name('property_reviews')->insert($data);

            if ($re) {
                return true;
            }
            return false;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

    }

    /**
     * 资讯评论列表
     * @param $param
     * @param $pageSize
     * @return array
     */
    public function newsCommentList($param, $pageSize)
    {
        try {
            $where = [
                ['article_id', '=', $param['id']],
                ['status', '=', 1],
                ['pid', '=', 0],
            ];
            $list = $this->db->name('consulting_comments')
                ->where($where)
                ->field('id,user_name,user_avatar,content,create_time')
                ->paginate($pageSize);

            if ($list->isEmpty()) {
                $result['list'] = [];
                $result['total'] = 0;
                $result['last_page'] = 0;
                $result['current_page'] = 0;
            } else {
                $list = $list->toArray();
                $idArray = array_column($list['data'], 'id');
                $pidWhere = [
                    ['pid', 'in', $idArray],
                    ['status', '=', 1]
                ];

                $data = $this->db->name('consulting_comments')
                    ->where($pidWhere)
                    ->field('id,user_name,user_avatar,content,pid,create_time')
                    ->select()->toArray();

                foreach ($list['data'] as $key => &$value) {
                    foreach ($data as $k => $v) {
                        if ($value['id'] == $v['pid']) {
                            $value['pid_list'] = $v;
                        }
                    }
                    $value['pid_list'] = $value['pid_list'] ?? [];
                }
                $result['list'] = $list['data'];
                $result['total'] = $list['total'];
                $result['last_page'] = $list['last_page'];
                $result['current_page'] = $list['current_page'];

            }

            return $this->responseOk($result);
        } catch (\Exception $exception) {
            return $this->responseFail($exception->getMessage());
        }
    }

    /**
     * 资讯评论
     * @param $param
     */
    public function newsComment($param)
    {
        try {
            $res = false;
            $data = [
                'article_id'  => $param['id'],
                'user_id'     => $param['user_id'],
                'city_no'     => $param['city_no'],
                'pid'         => empty($param['pid']) ? 0 : $param['pid'],
                'content'     => $param['comment'],
                'cate_pid'    => $param['cate_pid'],
                'status'      => 1,
                'create_time' => time(),
                'update_time' => time()
            ];

            $res = $this->h5NewsComment($param, $data);

            if (!$res) {
                return $this->responseFail('评论失败');
            }
            return $this->responseOk(['id'=> $res]);

        } catch (\Exception $exception) {
            return $this->responseFail($exception->getMessage());
        }
    }

    public function h5NewsComment($param, $data)
    {
        try {
            //判断手机用户是否有union
            $info = $this->db->name('user')->where(['id' => $param['user_id']])->find();
            $data['user_name']   =  empty($info['user_name']) ? ($info['nickname'] ?? '手机用户') : $info['user_name'];
            $data['user_avatar'] =  empty($info['user_avatar']) ? ($info['headimgurl'] ?? '/9house/static/my/touxiang.png') : $info['user_avatar'];;
//            var_dump($data);return ;
            $res = $this->db->name('consulting_comments')->insert($data,true);
            return $res;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function wxH5NewComment($param, $data)
    {
        try {
            $info = $this->db->name('user')
                ->where(['id' => $param['user_id']])
                ->field('id,unionid,nickname,headimgurl')
                ->find();
            if (empty($info)) {
                $data['user_name'] = '匿名用户';
                $data['user_avatar'] = '';
            } else {
                $data['user_name'] = $info['nickname'];
                $data['user_avatar'] = $info['headimgurl'];
            }
            $this->db->name('consulting_comments')->insert($data);
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    //点赞
    public function newsCommentLike($param)
    {
        try {
            $this->db->name('consulting_comments')
                ->where(['id' => $param['id']])->inc('like_number')->update();
            return $this->responseOk();
        } catch (\Exception $exception) {
            return $this->responseFail($exception->getMessage());
        }
    }

    //点赞
    public function propertyReviewsLike($param)
    {
        try {
            $this->db->name('property_reviews')->where(['id' => $param['id']])->inc('like_number')->update();
            return $this->responseOk();
        } catch (\Exception $exception) {
            return $this->responseFail($exception->getMessage());
        }
    }

    public function commentCountByStatus($data){
        if(in_array($data['status'],[0,1,2])){
            return  0;
        }
        $count = $this->db->name('consulting_comments')
            ->where('stuats','=',$data['status'])
            ->where('city_no','in' ,$data['city_no'])
            ->count();
        return $count;
    }
}