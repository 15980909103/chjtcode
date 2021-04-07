<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Table
 *
 * @author admin
 */
class Query {

    private $tableName;
    private $where;
	private $wherein;
    private $joinStr;
    private $limitStr;
    private $orderBy;
    private $havingStr;
    private $groupStr;
    private $prepare_values;
    private $sql;
    private $action;
    private $db;
    public function setDb( $db){
    	$this->db = $db;
    }
    public function getDb( ){
    	return $this->db;
    }
    public function __construct( $db=null){
    	$this->wherein = 0;
		if( $db !==null){
    		$this->setDb($db);
    	}else{
    		$this->db = new DataBase();
    	}
    }
    public function reset() {
        $this->where = array();
		$this->wherein = 0;
        $this->joinStr = '';
        $this->limitStr = '';
        $this->orderBy = array();
        $this->havingStr = '';
        $this->groupStr = '';
        $this->prepare_values = array();
        $this->sql = '';
        $this->action = 0;
    }

    public function Name($name) {
        $this->tableName = Table_Pre . $name;
        return $this;
    }

    private function where_basic($name, $value, $cond, $type = 'and') {
        $len = count($this->where);
        $paraname = ":where$len";
        $this->prepare_values[$paraname] = $value;
        $this->wherefun($name, $paraname, $cond, $type);
        return $this;
    }

    public function where_equalTo($name, $value, $type = 'and') {
        return $this->where_basic($name, $value, '=', $type);
    }

    public function where_notEqualTo($name, $value, $type = 'and') {
        return $this->where_basic($name, $value, '!=', $type);
    }

    public function where_lessThan($name, $value, $type = 'and') {
        return $this->where_basic($name, $value, '<', $type);
    }

    public function where_lessThanOrEqual($name, $value, $type = 'and') {
        return $this->where_basic($name, $value, '<=', $type);
    }

    public function where_greatThan($name, $value, $type = 'and') {
        return $this->where_basic($name, $value, '>', $type);
    }

    public function where_greatThanOrEqual($name, $value, $type = 'and') {
        return $this->where_basic($name, $value, '>=', $type);
    }

    public function where_like($name, $value, $type = 'and') {
        return $this->where_basic($name, $value, 'like', $type);
    }

    public function where_express($str, $value = [], $type = 'and') {
        if (!empty($this->where)) {
            $this->where[] = $type;
        }
        $this->where[] = $str;
        if (!empty($value)) {
            $this->prepare_values = array_merge($this->prepare_values, $value);
        }
        return $this;
    }

    public function find_in_set($value, $name, $type = 'and') {
        if (!empty($this->where)) {
            $this->where[] = $type;
        }
        $this->where[] = 'find_in_set('.$value.','.$name.')';
        return $this ;
    }
    
    public function where_isNULL($name, $type = 'and'){
         if (!empty($this->where)) {
            $this->where[] = $type;
        }
        $this->where[] = $name .' is null';
        return $this ;
    }
    
     public function where_isNotNULL($name, $type = 'and'){
         if (!empty($this->where)) {
            $this->where[] = $type;
        }
        $this->where[] = $name .' is not null';
        return $this ;
    }

    public function where_in($name, $value, $type = 'and') {
        $in = '';
        if(empty($value)){$value[]=0;}
        foreach ($value as $v) {
            $in.=":where_in_$this->wherein,";
            $this->prepare_values[":where_in_$this->wherein"] = $v;
            $this->wherein++;
        }
        $in = rtrim($in, ',');
        $in = "($in)";

        $this->wherefun($name, $in, 'in', $type);
        return $this;
    }

    public function where_between($name, $a, $b, $type = 'and') {
        $this->prepare_values[':between_a'] = $a;
        $this->prepare_values[':between_b'] = $b;
        $this->wherefun($name, ":between_a and :between_b", 'between', $type);
        return $this;
    }

    public function leftJoin($tableName, $alias, $cond) {
        $this->joinStr.=' left join ' . Table_Pre . "$tableName $alias on $cond";
        return $this;
    }

    public function rightJoin($tableName, $alias, $cond) {
        $this->joinStr.= ' right join ' . Table_Pre . "$tableName $alias on $cond";
        return $this;
    }

    public function innerJoin($tableName, $alias, $cond) {
        $this->joinStr.= ' inner join ' . Table_Pre . "$tableName $alias on $cond";
        return $this;
    }

    private function wherefun($name, $value, $cond, $type) {
        if (!empty($this->where)) {
            $this->where[] = $type;
        }
        $this->where[] = $name;
        $this->where[] = $cond;
        $this->where[] = $value;
    }

    public function orderBy($name, $order = 'asc') {
        $this->orderBy[] = "$name $order";
        return $this;
    }

    public function groupBy($str) {
        $this->groupStr = "$str";
        return $this;
    }

    public function having($str) {
        $this->havingStr = $str;
        return $this;
    }

    public function limit($a, $b = 0) {
        if ($b == 0) {
            $this->limitStr = "limit $a";
        } else {
            $this->limitStr = "limit $a,$b";
        }
        return $this;
    }

    /**
     * @param $page页数
     * @param $listRows每页数量
     * @return $this
     */
    public function page($page, $listRows){
        $page=($page - 1) * $listRows;
        $this->limitStr = "limit $page,$listRows";
        return $this;
    }

    public function select($str = '*', $alias = '') {

        $this->sql = "select $str from $this->tableName $alias";
        $this->action = 1;
        return $this;
    }

    public function update($arr) {
        $this->action = 2;
        $index = 0;
        $sql = '';
        foreach ($arr as $key => $value) {
            $para = ":update$index";
            $sql.="$key= $para,";
            $this->prepare_values[$para] = $value;
            $index++;
        }
        $sql = rtrim($sql, ',');
        $this->sql = "update $this->tableName set $sql";
        return $this;
    }

    public function insert($arr) {
        $this->action = 3;
        $index = 0;
        $sql1 = '';
        $sql2 = '';
        foreach ($arr as $key => $value) {
            $sql1.="$key,";
            $para = ":insert$index";
            $sql2 .= "$para,";
            $this->prepare_values[$para] = $value;
            $index++;
        }
        $sql1 = rtrim($sql1, ',');
        $sql2 = rtrim($sql2, ',');

        $this->sql = "insert into $this->tableName ($sql1) values($sql2)";

        return $this;
    }

    public function delete() {
        $this->sql = "delete from $this->tableName";
        $this->action = 4;
        return $this;
    }

    public function firstRow() {
        $sql = $this->toString();
        $database= $this->db;
        //$database::Log(basename(__FILE__).__LINE__,$database);
        $obj = $database::GetRow($sql, $this->prepare_values);
        $this->reset();
        return $obj;
    }

    public function firstColumn($debug = FALSE) {
        $sql = $this->toString();
        if ($debug) {

            echo $sql, "<br>";
            print_r($this->prepare_values);
        }
        $database= get_class($this->db);
        $obj = $database::GetColumn($sql, $this->prepare_values);
        $this->reset();
        return $obj;
    }

    public function toString() {
        $arr = array();
        $arr[] = $this->sql;
        $arr[] = $this->joinStr;
        if (!empty($this->where)) {
            $arr[] = 'where';
            $arr[] = join(' ', $this->where);
        }
        if (!empty($this->groupStr)) {
            $arr[] = 'group by';
            $arr[] = $this->groupStr;
        }

        if (!empty($this->havingStr)) {
            $arr[] = 'having';
            $arr[] = $this->havingStr;
        }

        if (!empty($this->orderBy)) {
            $arr[] = 'order by';
            $arr[] = join(',', $this->orderBy);
        }
        if (!empty($this->limitStr)) {
            $arr[] = $this->limitStr;
        }

        $sql = join(' ', $arr);
        return $sql;
    }

    public function execute($debug = FALSE) {
        $sql = $this->toString();
        if ($debug) {

            echo $sql, "<br>";
            print_r($this->prepare_values);
        }
        $ret = '';
        $database= get_class($this->db);
		//DataBase::log(basename(__FILE__).__LINE__,$sql);
        //DataBase::log(basename(__FILE__).__LINE__,$this->prepare_values);
        switch ($this->action) {
            case 1:
                $ret = $database::Select($sql, $this->prepare_values);
                break;
            case 2:
                $ret = $database::Update($sql, $this->prepare_values);
                break;
            case 3:
                $ret = $database::Insert($sql, $this->prepare_values);
                break;
            case 4:
                $ret = $database::Delete($sql, $this->prepare_values);
                break;
        }
        $this->reset();
        return $ret;
    }

}

?>
