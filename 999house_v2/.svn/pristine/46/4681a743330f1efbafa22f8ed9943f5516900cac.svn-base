<?php
namespace app\server\admin;

use app\common\traits\TraitInstance;
use app\common\base\ServerBase;
use think\Db;
use think\Exception;

class ArticleTag extends ServerBase
{
    public static  $table = 'article_tag';

    /**
     * @param $data
     * @return bool
     * @throws \think\db\exception\DbException
     */
    public function edit($data){
       if( !$data['id'] ){
           return  false;
       }
       $res =  $this->db->name(self::$table)->where('id','=',$data['id'])->update($data);
       return $res ===false ? false:true;
    }

    /**
     * @param $data
     * @return bool
     */
    public function add($data){
        $res =  $this->db->name(self::$table)->insert($data);
        return $res ===false ? false:true;
    }

    public function getTagList($search){
        $where = [];
        if(!in_array($search['status'],['0','1'])){
            unset($search['status']);
        }
        if(isset($search['status'])){//状态
            $where[]=  ['status','=', $search['status']];
        }
        if($search['pid'] != '-1' && (!empty($search['pid']) || $search['pid'] === 0 ) ){
            $where[]=  ['pid','=', $search['pid']];
        }
        if( !empty($search['name']) ){
            $where[]=  ['name','like', "%{$search['name']}%"];
        }
        $order = ['id'=>'desc'];

        if(!empty($search['id']) &&  is_array($search['id']) ){
            $where[]=  ['id','in',$search['id']];
            $list = $this->db->name(self::$table)
                ->where($where)
                ->whereOr('pid','=',0)
                ->order($order)
                ->select();
        }else{
            $list = $this->db->name(self::$table)
                ->where($where)
                ->order($order)
                ->select();
        }




        $list = empty($list) ? []:$list->toArray();
//        echo $this->db->getLastSql();
//        if($list->isEmpty()){
//            $result['list'] = [];
//        }else{
//            $result['total'] = $list->total();
//            $result['last_page'] = $list->lastPage();
//            $result['current_page'] = $list->currentPage();
//            $result['list'] =$list->items();
//        }
        return $this->responseOk($list);
    }

    /**
     * 修改字段
     * @param $id
     * @param $column_key
     * @param $cloumn_val
     * @return bool
     * @throws \think\db\exception\DbException
     */
    public function setTagColumn($id,$column_key,$cloumn_val){
        $res  = $this->db->name(self::$table)->where('id','=',$id)->update([$column_key=>$cloumn_val]);
        return $res === false ? false:true;
    }

    public function del($id){
        $res  = $this->db->name(self::$table)->where('id','=',$id)->delete();
        return $res === false ? false:true;
    }

    /**
     *  获取标签命
     */

    public function getTagName($ids){
        $list  = $this->db->name('article_tag')->where('id','in',$ids)->column('name');
        return $list?? [];
    }

    public function getParent($field='*'){
         return $this->db->name(self::$table)->field($field)->where('pid','=',0)->select();
    }

    /**
     * 判断数据是否存在
     */
    public function isColumn($coumnVal,$column='id'){
         $info = $this->db->name(self::$table)
            ->where($column,'=',$coumnVal)
            ->where('pid','=',0)
            ->find();

         return !empty($info) ? true:false;
    }
}