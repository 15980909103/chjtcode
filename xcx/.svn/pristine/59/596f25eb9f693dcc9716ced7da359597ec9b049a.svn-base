<?php

defined("LOKI") || die("you no have right to access here");

/**
 * @property PDO $conn Description
 */
class DataBase2 {

    public static $conn;

    public static function initConnection() {
        try {
            if (!static::$conn) {
                static::$conn = new PDO('mysql:host=' . MYSQL_HOST2 . ';port=' . MYSQL_PORT2 . ';charset=' . MYSQL_CHARSET2 . ';dbname=' . MYSQL_DB2, MYSQL_USER2, MYSQL_PASSWORD2);
                static::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                static::exec("set NAMES 'UTF8'");
            }
        } catch (Exception $ex) {
            die($ex->getMessage());
			//die("a");
        }
    }

    public static function Select($sql, $para = array()) {
        static::initConnection();
        $query = static::$conn->prepare($sql);
        $query->execute($para);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function Update($sql, $para = array()) {
        static::initConnection();
        $query = static::$conn->prepare($sql);
        $query->execute($para);
        return $query->rowCount();
    }

    public static function Insert($sql, $para = array()) {
        static::initConnection();
        $query = static::$conn->prepare($sql);

        if ($query->execute($para)) {
            return static::$conn->lastInsertId();
        }

        return 0;
    }

    public static function Delete($sql, $para = array()) {
        static::initConnection();
        $query = static::$conn->prepare($sql);

        if($query->execute($para)){
              return $query->rowCount();
        }
        return 0;
    }

    public static function Exec($sql) {
        static::initConnection();
        static::$conn->exec($sql);
    }

    public static function GetRow($sql, $para = array()) {
        static::initConnection();
        $query = static::$conn->prepare($sql);

        $query->execute($para);

        $result = $query->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    public static function GetColumn($sql, $para = array()) {
        static::initConnection();
        $query = static::$conn->prepare($sql);

        $query->execute($para);

        $result = $query->fetchColumn();

        return $result;
    }

    public static function close() {
        if (static::$conn) {
            static::$conn = null;
        }
    }

    public static function beginTransaction() {
        static::initConnection();
		return static::$conn->beginTransaction();
    }

    public static function commit() {
        static::initConnection();
		static::$conn->commit();
    }

    public static function rollBack() {
        static::initConnection();
		static::$conn->rollBack();
    }
    public static function log( $title,$content=''){
    	if( !is_string($title) ){
    		throw new Exception('title must be string');
    	}
    	if( $content instanceof Exception){
    		if( $content->getPrevious()){
    			$rs = array();
    			while ( $content->getPrevious()){
    				$content = $content->getPrevious();
    				$rs[] = $content->getFile().'('.$content->getLine().')'.$content->getMessage();
    			}
    			$rs[] = $content->getTraceAsString();
    			$content = implode("\n", $rs);
    		}else{
    			$content = $content->getMessage()."\n".$content->getTraceAsString();
    		}
    	}
    	if( !is_string($content)){
	    	if( is_array($content)){
	    		$content = json_encode($content);
	    	}else{
	    		$content = var_export($content,true);
	    	}
    	}
    	$sql = "INSERT " . Table_Pre . "log (`title`,`content`) values (?,?) ";
    	return static::Insert($sql,array($title,$content));
    }
}
