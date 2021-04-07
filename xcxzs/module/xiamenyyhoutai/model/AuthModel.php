<?php
class AuthModel extends Model {
    function __construct() {
        $this->setTable('auth');
    }
	function find($pageRow = 10, $page = 1, $arr = array()) {
        $obj = $this->table->select();
		foreach($arr as $k=>$v){
			if(in_array($k,array('status'))){
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
        return $obj->orderBy('id', 'asc')->execute();
    }
	function total($arr = array()) {
        $obj = $this->table->select('count(*)');
		foreach($arr as $k=>$v){
			if(in_array($k,array('status'))){
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
	function get2($controller,$action,$auths) {
        return $this->table->select()->where_equalTo('controller', $controller)->where_equalTo('action', $action)->where_in('id', $auths)->firstRow();
    }
	function getall() {
        return $this->table->select()->where_equalTo('status', 1)->orderBy('id', 'asc')->execute();
    }
	function getall2() {
		$auths = array();
		$rows = $this->table->select()->where_equalTo('status', 1)->orderBy('controller', 'asc')->orderBy('action', 'asc')->execute();
		if($rows){
            foreach($rows as $val){
                $auths[$val['controller_name']][$val['id']]=$val;
            }
        }
		return $auths;
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

    function joinFind($pageRow = 10, $page = 1, $arr = array(), $fileds = '*', $joinArr = []) {
        $obj = $this->table->select($fileds, 'a');
        if(!empty($joinArr)) {
            foreach ($joinArr as $val) {
                if(isset($val['type']) && isset($val['table']) && isset($val['on']) && isset($val['alias'])) {
                    switch ($val['type']) {
                        case 'left':
                            $obj->leftJoin($val['table'], $val['alias'], $val['on']);
                            break;
                        case 'right':
                            $obj->rightJoin($val['table'], $val['alias'], $val['on']);
                            break;
                        case 'inner':
                        default:
                            $obj->innerJoin($val['table'], $val['alias'], $val['on']);
                            break;
                    }
                }
            }
        }

        foreach($arr as $k=>$v){
            if(in_array($k,array('status'))){
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
        return $obj->orderBy('id', 'asc')->execute();
    }
}
