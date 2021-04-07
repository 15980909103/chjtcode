<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: pl125 <xskjs888@163.com>
// +----------------------------------------------------------------------

namespace api\server\channel;

use api\common\lib\TraitInstance;
use api\server\ServerBase;
use think\Db;
use think\Exception;

/*
 * 好玩操作
 * */
class News extends ServerBase
{
    use TraitInstance;
    //====================类别操作=====================//
    /*public function getCategory(){
        return Db::name('news_category')->where(['status'=>1])->order('sort desc,id desc')->column('*');
    }*/

    /**
     * 分类列表
     */
    public function getCategoryList($search=[],$field='*'){
        $result = array(
            'list'  =>  [],
        );

        $where = [];
        if(isset($search['status'])){
            $where[] = ['status','=',$search['status']];
        }
        if(!empty($search['order'])){
            $order = $search['order'];
        }else{
            $order = 'id desc';
        }

        $list = Db::name('news_category')
            ->field($field)
            ->where($where)
            ->order($order)
            ->select()->toArray();

        if(empty($list)){
            $result['list'] = [];
        }else{
            $result['list'] =$list;
        }

        return $this->responseOk($result);
    }
    /**
     * 分类添加
     * @param $data
     * @return array
     */
    public function categoryAdd($data){
        try{
            if(empty($data['name'])){
                throw new Exception('参数错误');
            }
            $has=Db::name('news_category')->where(['name'=>$data['name']])->value('id');
            if(!empty($has)){
                throw new Exception('该名称已经存在');
            }

            $rs = Db::name('news_category')->insertGetId([
                'name' => $data['name'],
                'status' => $data['status'],
                'sort' => $data['sort'],
                'icon_category' => $data['icon_category'],
                'has_comment' => $data['has_comment']
            ]);

            if($rs){
                //移除相关缓存
                $this->removeCache([
                    'getCategoryList',
                ]);

                return $this->responseOk();
            }else{
                return $this->responseFail();
            }
        }catch (Exception $e){

            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    /**
     * 分类修改
     * @param $id
     * @param $data
     * @return array
     * @throws Exception
     */
    public function categoryEdit($id,$data){
        try{
            if(!is_int($id)|| !($id > 0)){
                throw new Exception('参数错误');
            }
            if(isset($data['status'])&&!in_array($data['status'],['0','1'])){//是否启用
                throw new Exception('参数错误');
            }
            if(!empty($data['name'])){
                $has=Db::name('news_category')->where([
                    ['name','=',$data['name']],
                ])->value('id');
                if(!empty($has)&&$has!=$id){
                    throw new Exception('该名称已经存在');
                }
            }

            if(!empty($data['icon_category'])){
                $has=Db::name('news_category')
                    ->field('icon_category')->where([
                        ['id','=',$id],
                    ])->find();
            }

            unset($data['id']);//不可变更id
            $rs= Db::name('news_category')->where(['id'=>$id])->update($data);
            if($rs){
                if(!empty($has['icon_category'])&&!empty($data['icon_category'])&&$has['icon_category']!=$data['icon_category']){
                    //删除旧的图片
                    $this->delFile($has['icon_category']);
                }

                //移除相关缓存
                $this->removeCache([
                    'getCategoryList',
                ]);

                return $this->responseOk();
            }else{
                return $this->responseFail();
            }
        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }
    /**
     * 分类删除
     * @param $id
     * @param $data
     * @throws Exception
     */
    public function categoryDel($id){
        try{
            if(!is_int($id)|| !($id > 0)){
                throw new Exception('参数错误');
            }
            if($id=='2'){
                throw new Exception('该类别不可删除');
            }

            $haspoint = Db::name('news')->where([
                [ 'category_id','=',$id ]
            ])->value('id');//查找是否含有归属文章
            if(!empty($haspoint)){
                throw new Exception('该信息含有文章内容不可删除');
            }
            $has=Db::name('news_category')
                ->field('icon_category')->where([
                    [ 'id','=',$id ]
                ])->find();

            $rs= Db::name('news_category')->where(['id'=>$id])->delete();
            if($rs){
                //删除旧的图片资料
                $this->delFile($has['icon_category']);

                //移除相关缓存
                $this->removeCache([
                    'getCategoryList',
                ]);

                return $this->responseOk();
            }else{
                return $this->responseFail();
            }
        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }


    //====================文章操作======================//
    /**
     * 列表
     * @param array $search $search['status'] //文章的状态  $search['category_status']//类别的状态
     * @param int $pagesize
     * @return array
     */
    public function getList($search = [], $field = 'i.*', $pagesize = 50){
        $where = [];
        if(!empty($search['name'])){
            $where[]=  ['i.name','like', '%'.$search['name'].'%'];
        }
        if(!in_array($search['status'],['0','1'])){
            unset($search['status']);
        }
        if(isset($search['status'])){//状态
            $where[]=  ['i.status','=', $search['status']];
        }
        if(!in_array($search['category_status'],['0','1'])){
            unset($search['category_status']);
        }
        if(isset($search['category_status'])){//类别的状态
            $where[]=  ['c.status','=', $search['category_status']];
        }
        if(!empty($search['category_id'])){
            $where[]= ['i.category_id','=', $search['category_id']];
        }

        if(!empty($search['order'])){//排序操作
            $order= $search['order'];
        }else{//默认排序
            $order= ['sort'=>'desc','id'=>'desc'];
        }

        $result = array(
            'list'  =>  [],
            'total' =>  0,
            'last_page' =>  0,
            'current_page'  =>  0
        );

        $rslist = Db::name('news i')
            ->field($field.',c.name category_name,c.status category_status')
            ->join('news_category c','c.id=i.category_id','LEFT')
            ->where($where)->order($order);
        if($search['getAll']==1){
            $rslist = $rslist->select();
            $rslist = $rslist->toArray();
            $list['data'] = $rslist;
        }else{
            $rslist = $rslist->paginate($pagesize);
            $list = $rslist->toArray();
        }

        if(empty($list['data'])){
            $result['list'] = [];
        }else{
            if($search['get_statistic']==1){//获取静态统计
                $ids = array_column($list['data'], 'id');
                $statistic= Db::name('news_statistic')
                    ->where([
                        ['news_id','in',$ids]
                    ])->column('*','news_id');

                foreach ($list['data'] as &$item){
                    $item['statistic'] = [];
                    if($statistic[$item['id']]){
                        $item['statistic'] = $statistic[$item['id']];
                    }
                }
            }

            $result['total'] = $list['total'];
            $result['last_page'] = $list['last_page'];
            $result['current_page'] = $list['current_page'];
            $result['list'] =$list['data'];
        }

        return $this->responseOk($result);
    }

    public function getInfo($id,$field='*',$get_statistic=0){
        if(empty($id)){
            return $this->responseFail(['code'=>0,'msg'=>'参数缺失']);
        }

        $info= Db::name('news n')->join("news_category nc","n.category_id=nc.id")
            ->field($field)
            ->where([
                ['n.id','=',$id]
            ])->find();

        if($info['id']){
            if($info['content']){
                $info['content'] = htmlspecialchars_decode($info['content']);
            }
            if($get_statistic==1) {//获取静态统计
                $statistic= Db::name('news_statistic')
                    ->where([
                        ['news_id','=',$id]
                    ])->find();
                $info['statistic'] = [];
                if($statistic['id']){
                    $info['statistic']= $statistic;
                }
            }

            return $this->responseOk($info);
        }else{
            return $this->responseOk();
        }
    }

    public function add($data){
        try{
            $has=Db::name('news')->where([
                ['name','=',$data['name']],
                ['category_id','=',$data['category_id']]
            ])->value('id');
            if(!empty($has)){
                throw new Exception('文章名称已存在');
            }

            Db::startTrans();
            $id=Db::name('news')->insertGetId([
                'category_id' => $data['category_id'],
                'name'=>$data['name'],
                'sort' => $data['sort'],
                'status' => $data['status'],
                'cover' => $data['cover'],
                'content' => $data['content'],
                'keyword' => $data['keyword'],
                'subtitle' => $data['subtitle'],
                'tags' => $data['tags'],
                'create_time' => $data['create_time'],
                'source' => $data['source']
            ]);
            if($id){
                $rs_statistic=Db::name('news_statistic')->insertGetId([
                    'news_id'=>$id,
                    'num_read' => $data['num_read'],
                    'num_collect' => $data['num_collect'],
                    'num_share' => $data['num_share'],
                    'num_thumbup' => $data['num_thumbup'],
                ]);
            }

            if($rs_statistic){
                Db::commit();

                //移除相关缓存
                $this->removeCache([
                    'getList',
                    'getNewAllList'
                ]);

                return $this->responseOk();
            }else{
                throw new Exception('操作失败');
            }
        }catch (Exception $e){
            Db::rollback();
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    public function edit($id,$data){
        try{
            if(empty($id)){
                throw new Exception('缺少设置id');
            }

            if(!empty($data['name'])){
                if(empty($data['category_id'])){
                    throw new Exception('缺少设置category_id');
                }
                $has=Db::name('news')
                    ->field('id')->where([
                        ['name','=',$data['name']],
                        ['category_id','=',$data['category_id']]
                    ])->find();
                if(!empty($has['id'])&&$has['id']!=$id){
                    throw new Exception('文章名称已存在');
                }
            }

            if(!empty($data['cover'])){
                $has=Db::name('news')
                    ->field('cover')->where([
                        ['id','=',$id],
                    ])->find();
            }

            unset($data['id']);//不可变更id
            $data_statistic=[];//更新附属的统计表
            if(isset($data['num_read'])){
                $data_statistic['num_read']=$data['num_read'];
                unset($data['num_read']);
            }
            if(isset($data['num_collect'])){
                $data_statistic['num_collect']=$data['num_collect'];
                unset($data['num_collect']);
            }
            if(isset($data['num_share'])){
                $data_statistic['num_share']=$data['num_share'];
                unset($data['num_share']);
            }
            if(isset($data['num_thumbup'])){
                $data_statistic['num_thumbup']=$data['num_thumbup'];
                unset($data['num_thumbup']);
            }

            $rs= Db::name('news')->where(['id'=>$id])->update($data);
            if(!empty($data_statistic)){
                $rs_statistic=Db::name('news_statistic')->where([
                    'news_id'=>$id,
                ])->update($data_statistic);
            }

            if($rs||$rs_statistic){
                if($rs&&!empty($has['cover'])&&!empty($data['cover'])&&$data['cover']!=$has['cover']){
                    //删除旧的图片资料
                    $this->delFile($has['cover']);
                }

                //移除相关缓存
                $this->removeCache([
                    'getList',
                    'getInfo',
                    'getNewAllList'
                ]);

                return $this->responseOk();
            }else{
                return $this->responseFail();
            }
        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    public function del($id){
        try{
            if(!is_int($id)||empty($id)){
                throw new Exception('id参数错误');
            }

            $has=Db::name('news')
                ->field('cover')->where([
                    [ 'id','=',$id ]
                ])->find();

            $rs = Db::name('news')->where([
                [ 'id','=',$id ]
            ])->delete();

            if($rs){
                ///删除附属的统计表
                Db::name('news_statistic')->where([
                    [ 'news_id','=',$id ]
                ])->delete();

                //删除旧的图片资料
                $this->delFile($has['cover']);

                //移除相关缓存
                $this->removeCache([
                    'getList',
                    'getInfo',
                    'getNewAllList'
                ]);

                return $this->responseOk();
            }else{
                return $this->responseFail();
            }
        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }




    /**
     * 获取文章是否点赞，是否收藏
     * */
    public function getCollectOrThumbup($data,$tableName){
        $where = [
            'user_id' => $data['user_id'],
            'object_id' => $data['object_id'],
            'table_name' => $data['table_name'],
        ];
        $has = Db::name($tableName)->where($where)->find();
        $result = false;
        if($has){
            $result = true;
        }
        return $result;
    }

    /**
     * 更新好玩统计表
     * @param int $news_id //好玩统计表文章id
     * @param int $operateField //好玩统计表对应字段
     * @param int $num //统计记录数
     * @return bool
     * @throws \think\Exception
     */
    public function updataNewsStatistic($news_id,$operateField,$num){
        $rs = Db::name('news_statistic')->where(['news_id'=>$news_id])->setInc($operateField,$num);
        return $rs;
    }

    /**
     * 获取设备接口获取新闻资讯全部  后续待优化改为分页
     * */
    public function getNewAllList($search = [], $field = '*'){
        $where = [];
        if(isset($search['status'])){//状态
            $where[]=  ['status','=', $search['status']];
        }
        if(isset($search['id'])){//景点ID
            $where[]=  ['id','=', $search['id']];
        }
        if(!empty($search['order'])){//排序操作
            $order= $search['order'];
        }else{//默认排序
            $order= $order= 'create_time desc';
        }
        $result = Db::name('news')->where($where)->order($order)->field($field)->select()->toArray();
        return $this->responseOk($result);
    }

    /**
     * //获取相关文章的 阅读数，点赞数
     */
    public function getThumbup($newId,$field='*'){
        $statistic= Db::name('news_statistic')
            ->where([
                ['news_id','in',$newId]
            ])->column($field,'news_id');
        return $this->responseOk($statistic);
    }
}
