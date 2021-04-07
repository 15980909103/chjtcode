<?php
namespace app\common\base;


use think\db\Query;
use think\Exception;

//分表操作示例
//$rs=$this->db->setPartition([
//    'merchant_id'=>1,
//    '_num' => 10
//])->name('user')->alias('a')->setPartition([
//    'merchant_id'=>10,
//    '_num' => 10
//])->join('user b','b.id=a.id')->where('a.id',1)->select();
/**
 * 数据库基类
 */
class HhDbQuery extends Query
{
    use TraitPartitionTable;

    public function table($table)
    {
        if(!empty($this->table_seq)){
            $table = $table . '_' . $this->table_seq;
            $this->table_seq = '';
        }

        return parent::table($table);
    }

    public function name(string $name)
    {
        if(!empty($this->table_seq)){
            $name = $name . '_' . $this->table_seq;
            $this->table_seq = '';
        }

        return parent::name($name);
    }
    public function join($join,$condition = null, $type = 'INNER', $bind = [])
    {
        if(!empty($this->table_seq)){
            $tableNameArr = explode(' ',trim($join));
            $length = count($tableNameArr);
            if($length==2){
                $alias = $tableNameArr[1];
            }elseif($length==3){
                $alias = $tableNameArr[1].' '.$tableNameArr[2];
            }

            $join = $tableNameArr[0] . '_' . $this->table_seq .' '.$alias;
            $this->table_seq = '';
        }

        return parent::join( $join, $condition, $type, $bind);
    }
}

/**
 * 切割分表分库算法
 * Trait partition
 * @package app\common\base
 */
trait TraitPartitionTable{
    protected $table_seq; //分表后缀标识
    /**
     * @var array 默认的拆分规则 type='mod'取模，_num=10拆分10张表
     *
     */
    protected $partition_rule = [
        'type' => 'mod',
        '_num' => 10,
    ];

    /**
     * 设置分割
     * @param array $rule
     * @return $this|\think\Db
     * @throws Exception
     */
    public function setPartition($rule = []){
        $rule = array_merge($this->partition_rule,$rule);
        $this->table_seq = $this->doPartition($rule);//分表后缀标识
        return $this;
    }

    /**
     * 得到分表的的数据表的后缀
     * @param array $rule 分表规则
     * @return false|float|int|mixed|string
     * @throws Exception
     */
    public function doPartition($rule = [])
    {
        // 对数据表进行分区
        if(empty($rule['_num'])){
            throw new Exception('缺失分表num参数');
        }
        $num = $rule['_num'];
        unset($rule['_num']);

        $type  = $rule['type']??'mod';
        unset($rule['type']);

        if(empty($rule)){
            throw new Exception('缺失分表字段参数');
        }
        //获取要分表的字段和值
        $rule = each($rule);

        $value = $rule['value'];

        switch ($type) {
            case 'id':
                // 按照id范围分表
                $seq  = floor($value / $num) + 1;
                break;
            case 'year':
                // 按照年份分表
                if (!is_numeric($value)) {
                    $value = strtotime($value);
                }
                $seq = date('Y', $value) - $num + 1;
                break;
            case 'mod':
                // 按照id的模数分表
                $seq = ($value % $num) + 1;
                break;
            case 'md5':
                // 按照md5的序列分表
                $seq = (ord(substr(md5($value), 0, 1)) % $num) + 1;
                break;
            default:
                if (function_exists($type)) {
                    // 支持指定函数哈希
                    $seq = (ord(substr($type($value), 0, 1)) % $num) + 1;
                } else {
                    // 按照字段的首字母的值分表
                    $seq = (ord($value{0}) % $num) + 1;
                }
        }

        return $seq;
        //return $this->db->getTable() . '_' . $seq;

        // 当设置的分表字段不在查询条件或者数据中
        // 进行联合查询，必须设定 partition['num']
        /*$tableName = [];
        for ($i = 0; $i < $rule['num']; $i++) {
            $tableName[] = 'SELECT * FROM ' . $this->db->getTable() . '_' . ($i + 1);
        }
        return ['( ' . implode(" UNION ", $tableName) . ' )' => $this->name];*/
    }
}