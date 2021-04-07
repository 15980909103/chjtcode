<?php
class AdminModel extends Model {
    function __construct() {
        $this->setTable('admin');
    }
    function getup($username, $password) {
		return $this->table->select()
                        ->where_equalTo('username', $username)
                        ->where_equalTo('password', $password)
						->where_equalTo('status', 1)
                        ->firstRow();
    }
	function find($pageRow = 10, $page = 1, $arr = array()) {
        $obj = $this->table->select();
		foreach($arr as $k=>$v){
			if(in_array($k,array('status'))){
				if (isset($v) && $v!='' && $v>=0) {
					$obj->where_equalTo($k, $v);
				}
			}elseif(in_array($k,array('gid'))){
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
			if(in_array($k,array('status'))){
				if (isset($v) && $v!='' && $v>=0) {
					$obj->where_equalTo($k, $v);
				}
			}elseif(in_array($k,array('gid'))){
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
	function get2($username) {
        return $this->table->select()->where_equalTo('username', $username)->firstRow();
    }
	function get3($unionId) {
        return $this->table->select()->where_equalTo('unionId', $unionId)->where_equalTo('status', 1)->firstRow();
    }
	function getall() {
        return $this->table->select()->orderBy('id', 'asc')->execute();
    }
	function getall2() {
        return $this->table->select()->where_equalTo('wx_flag', 1)->orderBy('id', 'asc')->execute();
    }
	function add($data) {
        $id = $this->table->insert($data)->execute();
    }
	function update($id,$data) {
		$this->table->update($data)->where_equalTo('id', $id)->execute();
    }
	function update2($username,$data) {
		$this->table->update($data)->where_equalTo('username', $username)->execute();
    }
	function delete($id) {
        $this->table->delete()->where_equalTo('id', $id)->where_notEqualTo('id', 1)->execute();
    }
}
