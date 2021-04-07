<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of session
 *
 * @author admin
 */
//class Session {
$SESS_HANDLE = "";

if( defined('MODULE') ){
	if( MODULE =='line'){
		define('SESSION_TBL_NAME', 'tbl_session_line');
	}elseif(  MODULE =='admin'){
		define('SESSION_TBL_NAME', 'tbl_session_admin');
	}else{
		define('SESSION_TBL_NAME', 'tbl_session');
	}
}else{
	define('SESSION_TBL_NAME', 'tbl_session');
}
function sess_open($save_path, $session_name) {
    global $SESS_HANDLE;
    if( defined('MODULE') && in_array(MODULE, array('line','admin')) ){
    	if (!($SESS_HANDLE = mysql_connect(MYSQL_HOST_A . ':' . MYSQL_PORT_A, MYSQL_USER_A, MYSQL_PASSWORD_A))) {
    		//echo "<li>MySql Error:" . mysql_error() . "<li>";
			echo 'S';
    		die();
    	}
    	if (!mysql_select_db(MYSQL_DB_A, $SESS_HANDLE)) {
    		//echo "<li>MySql Error:" . mysql_error() . "<li>";
			echo 'S';
    		die();
    	}
    }else{
	    if (!($SESS_HANDLE = mysql_connect(MYSQL_HOST_B . ':' . MYSQL_PORT_B, MYSQL_USER_B, MYSQL_PASSWORD_B))) {
	        //echo "<li>MySql Error:" . mysql_error() . "<li>";
			echo 'S';
	        die();
	    }
	    if (!mysql_select_db(MYSQL_DB_B, $SESS_HANDLE)) {
	        //echo "<li>MySql Error:" . mysql_error() . "<li>";
			echo 'S';
	        die();
	    }
    }
    return true;
}

function sess_close() {
    global $SESS_HANDLE;
	mysql_close($SESS_HANDLE);
	return true;
}

function sess_read($key) {
    global $SESS_HANDLE;
    $query = "select caipiao from ".SESSION_TBL_NAME." where id='$key' and unix_timestamp()-modified<lifetime";

    $qid = mysql_query($query, $SESS_HANDLE);
    $result = "";
    list($result) = mysql_fetch_row($qid);
    return $result;
}

function sess_write($key, $val) {
    global $SESS_HANDLE;
//     $sess_time = ini_get("session.gc_maxlifetime");
    //$query = "insert into tbl_session(id,name,modified,caipiao,lifetime) values('$key','global',unix_timestamp(),'$val','$sess_time')";
	$query = "select caipiao from ".SESSION_TBL_NAME." where id='$key' ";
    $qid = mysql_query($query, $SESS_HANDLE);
    if ($qid) {
        $query = "update ".SESSION_TBL_NAME." set modified=unix_timestamp(),caipiao='$val' where id='$key'";
        $qid = mysql_query($query, $SESS_HANDLE);
    }
    return $qid;
}

function sess_destory($key) {
    global $SESS_HANDLE;
    $query = "delete from ".SESSION_TBL_NAME." where id='$key'";
    return mysql_query($query, $SESS_HANDLE);
}

function sess_gc($maxlifetime) {
    //global $SESS_HANDLE;
    //$query = "delete from tbl_session where current_timestamp()-modified>lifetime";
    //mysql_query($query, $SESS_HANDLE);
    //return mysql_affected_rows($SESS_HANDLE);
}


session_set_save_handler("sess_open", "sess_close", "sess_read", "sess_write", "sess_destory", "sess_gc");
