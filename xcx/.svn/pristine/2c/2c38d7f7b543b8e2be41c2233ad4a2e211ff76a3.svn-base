<?php
namespace app\server\estates;


use app\common\base\ServerBase;
use app\common\MyConst;
use think\Exception;

class Tag extends ServerBase
{

    //显示所有
    public function getList($search = []){
        $where = [];
        if(!in_array($search['status'],['0','1'])){
            unset($search['status']);
        }
        if(isset($search['status'])){//状态
            $where[]=  ['status','=', $search['status']];
        }

        if(!empty($search['name'])){
            $where[]=  ['name','like', '%'.$search['name'].'%'];
        }

        if($search['type']<0){
            unset($search['type']);
        }
        if(isset($search['type'])){//类型
            $where[]=  ['type','=', $search['type']];
        }
 
        $order = ['id'=>'desc'];
  
        $list = $this->db->name('estates_tag')->where($where)->order($order)->select()->toArray();
        if(empty($list)){
            $result['list'] = [];
        }else{
            $result['list'] =   $list;
        }
        return $this->responseOk($result);
    }

    //添加操作
    public function add($data)
    {
        try{
            $data['name'] = trim_all($data['name']);
            $has = $this->db->name('estates_tag')->where([
                'name' => $data['name'],
            ])->value('id');
            if(!empty($has)){
                throw new Exception('该标签名称已经存在');
            }

            $id = $this->db->name('estates_tag')->insertGetId([
                'name' => $data['name'],
                'type'=> $data['type'],
                'status'=> $data['status'],
                'cover'=> $data['cover'],
                'update_time'=> 0
            ]);   //将数据存入并返回自增 ID
            if(empty($id)){
                throw new Exception('操作失败');
            }

            $this->updateRedis();

            return $this->responseOk($id);
        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    //修改状态
    public function edit($id,$data){
        try{
            $id = intval($id);
            if(empty($id)){
                throw new Exception('缺少设置参数');
            }
            unset($data['id']);//不可变更id

            if(isset($data['name'])){
                $data['name'] = trim_all($data['name']);
                $has = $this->db->name('estates_tag')->where([
                    'name' => $data['name'],
                ])->value('id');
                if(!empty($has)&&$has!=$id){
                    throw new Exception('该标签名称已经存在');
                }
            }

            if(!empty($data['cover'])){
                $has=$this->db->name('estates_tag')
                    ->field('cover')->where([
                        ['id','=',$id],
                    ])->find();
            }

            $data['update_time'] = time();
            $rs = $this->db->name('estates_tag')->where(['id'=>$id])->update($data);

            if(empty($rs)){
                throw new Exception('操作失败');
            }

            if(!empty($has['cover'])&&!empty($data['cover'])&&$has['cover']!=$data['cover']){
                //删除旧的图片
                $this->delFile($has['cover']);
            }

            $this->updateRedis();

            return $this->responseOk();
        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }


    public function del($id)
    {
        try{
            $fileName = $this->db->name("estates_tag")->where("id",$id)->value("cover");
            
            $res = $this->db->name('estates_tag')->where("id",$id)->delete();
            if($res){
                //删除旧的图片
                $this->delFile($fileName);

                $this->updateRedis();
                
                return $this->responseOk($res);
            }else{
                throw new Exception('操作失败');
            }

        }catch (Exception $e){
            return $this->responseFail(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    /**
     * 获取所有标签
     */
    public function getTagList()
    {
        try {
            $redis = $this->getReids();
            $data = $redis->hGet(MyConst::TAG_REDIS, MyConst::FEATURE_TAG);
            $data = json_decode($data);
            if (empty($data)) {
                /*$data = $this->db->name('estates_tag')->where('type', '=', 1)
                    ->field('id,name')->select()->toArray();
                $redis->hSet(MyConst::TAG_REDIS, MyConst::FEATURE_TAG, json_encode($data));*/
                $data = $this->updateRedis($redis);
                $redis->expire(MyConst::TAG_REDIS,7200); //设置过期时间
            }

            $data = array_column($data,'name','id');

            return $data;
        } catch (Exception $exception) {
            return $this->responseFail($exception->getMessage());
        }
    }

    /**
     * 更新redis中的数据
     * @param null $redis
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function updateRedis($redis=null){
        if(empty($redis)){
            $redis = $this->getReids();
        }

        $redis_indata = $this->db->name('estates_tag')->where('type', '=', 1)
            ->field('id,name')->select()->toArray();
        $redis->hSet(MyConst::TAG_REDIS, MyConst::FEATURE_TAG, json_encode($redis_indata));
        $redis->expire(MyConst::TAG_REDIS,7200);

        return $redis_indata;
    }

    public function tagBatchEdit($data){
        switch ($data['type']){
            case 'display':
                $where = [
                    ['id','In',$data['id']],
                    ['status','=',0]
                ];
                $this->db->name('estates_tag')->where($where)->update([
                    'status' => 1,
                    'update_time' => time()
                ]);
                break;
            case 'hide':
                $where = [
                    ['id','In',$data['id']],
                    ['status','=',1]
                ];
                $this->db->name('estates_tag')->where($where)->update([
                    'status' => 1,
                    'update_time' => time()
                ]);
                break;

            case 'delete':
                $where = [
                    ['id','In',$data['id']],
                ];
                $this->db->name('estates_tag')->where($where)->delete();
                break;
        }

        return $this->responseOk();
    }
}