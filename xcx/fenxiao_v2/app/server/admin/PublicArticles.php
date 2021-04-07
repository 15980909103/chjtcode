<?php


namespace app\server\admin;


use app\common\base\ServerBase;

class PublicArticles extends ServerBase
{
    public function getList($params,$type = 0)
    {
        try {
            $where = [];
            if(!empty($params['status'])){
                $where[] = ['status','=',$params['status']];
            }
            if(!empty($params['title'])){
                $where[] = ['title','=',$params['title']];
            }
            if(empty($type)){
                $list = $this->db->name('article_public')->where($where)->select()->toArray();
                if(!$list){
                    $result = [];
                }else{
                    foreach ($list as $key => &$value){
                        $value['context'] = htmlspecialchars_decode($value['context']);
                        $value['create_time'] = date('Y-m-d H:i',$value['create_time']);
                        $value['update_time'] = date('Y-m-d H:i',$value['update_time']);
                    }
                    $result = $list;
                }
            }else{
                $pageSize = $params['pageSize'] ?? 20;
                $list = $this->db->name('article_public')->where($where)->paginate($pageSize);
                if($list->isEmpty()){
                    $result['list'] = $list['data'];
                    $result['total'] = 0;
                    $result['last_page'] = 0;
                    $result['current_page'] = 0;
                }else{
                    $list = $list->toArray();
                    foreach ($list['data'] as $key => &$value){
                        $value['context'] = htmlspecialchars_decode($value['context']);
                        $value['create_time'] = date('Y-m-d H:i',$value['create_time']);
                        $value['update_time'] = date('Y-m-d H:i',$value['update_time']);
                    }
                    $result['total'] = $list['total'];
                    $result['last_page'] = $list['last_page'];
                    $result['current_page'] = $list['current_page'];
                    $result['list'] = $list['data'];
                }
            }

            return $this->responseOk($result);
        } catch (\Exception $exception) {
            return $this->responseFail($exception->getMessage());
        }
    }


    public function info($id)
    {
        try {
            $info = $this->db->name('article_public')->where('id',$id)->find();
            if(!$info){
                return $this->responseFail('内容不存在');
            }
            return  $this->responseOk($info);
        } catch (\Exception $exception) {
            return $this->responseFail($exception->getMessage());
        }
    }

    public function edit($params)
    {
        try {
            $time = time();
            $data = [
                'update_time' => $time
            ];
            if(!empty($params['status'])){
                $data['status'] = $params['status'];
            }
            if(!empty($params['title'])){
                $data['title'] = $params['title'];
            }
            if(!empty($params['context'])){
                $data['context'] = $params['context'];
            }
            if(empty($params['id'])){
                $data['create_time'] = $time;
                $res = $this->db->name('article_public')->insert($data);
                if(!$res){
                    return $this->responseFail('创建失败');
                }
            }else{
                $info = $this->db->name('article_public')->where('id',$params['id'])->find();
                if(!$info){
                    return $this->responseFail('内容不存在');
                }
                $res = $this->db->name('article_public')->where('id',$params['id'])->update($data);
                if(!$res){
                    return $this->responseFail('修改失败');
                }
            }

            return $this->responseOk();
        } catch (\Exception $exception) {
            return $this->responseFail($exception->getMessage());
        }
    }




    public function delete($id)
    {
        try {
            $info = $this->db->name('article_public')->where('id',$id)->find();
            if(!$info){
                return $this->responseFail('内容不存在');
            }
            $res = $this->db->name('article_public')->where('id',$id)->delete();
            if(!$res){
                return $this->responseFail('删除失败');
            }
            return $this->responseOk();
        } catch (\Exception $exception) {
            return $this->responseFail($exception->getMessage());
        }
    }
}