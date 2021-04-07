<?php
class WechatmenuModel extends Model {
    function __construct() {
        $this->setTable('wechat_menu');
    }
	function find($pageRow = 10, $page = 1, $arr = array()) {
        $obj = $this->table->select();
		foreach($arr as $k=>$v){
			if(in_array($k,array('status','public_id'))){
				if (isset($v) && $v!='' && $v>=0) {
					$obj->where_equalTo($k, $v);
				}
			}else{
				if (!empty($v)) {
					$obj->where_like($k, '%'.$v.'%');
				}
			}
		}
        $obj->limit(($page - 1) * $pageRow, $pageRow);
        return $obj->orderBy('id', 'desc')->execute();
    }
	function total($arr = array()) {
        $obj = $this->table->select('count(*)');
		foreach($arr as $k=>$v){
			if(in_array($k,array('status','public_id'))){
				if (isset($v) && $v!='' && $v>=0) {
					$obj->where_equalTo($k, $v);
				}
			}else{
				if (!empty($v)) {
					$obj->where_like($k, '%'.$v.'%');
				}
			}
		}
        return $obj->firstColumn();
    }
	function get($id) {
        return $this->table->select()->where_equalTo('id', $id)->firstRow();
    }
	function getall() {
        return $this->table->select()->where_equalTo('parent_id', 0)->orderBy('sort', 'asc')->execute();
    }
	function getall2($public_id,$parent_id) {
        return $this->table->select()->where_equalTo('public_id', $public_id)->where_equalTo('parent_id', $parent_id)->where_equalTo('status', 1)->orderBy('sort', 'asc')->execute();
    }
	function add($data) {
        $id = $this->table->insert($data)->execute();
    }
	function update($id,$data) {
		$this->table->update($data)->where_equalTo('id', $id)->execute();
    }
	function delete($id) {
        $this->table->delete()->where_equalTo('id', $id)->execute();
    }
}
