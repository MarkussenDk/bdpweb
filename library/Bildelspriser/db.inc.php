<?php
require_once 'Zend/Db/Adapter/Pdo/Mysql.php';
//require("../../include/adodb5/adodb.inc.php");
//$dsn = "mysql://root@localhost/bdp_dev";
/*
 * MySQL:
Host:                   localhost
Database:               bildelspriser_d
Brugernavn (User):      bildelspriser_d
Kodeord (Password):     iQpH3VmJ
 */
//$query = "select * from testtable";
$db_schema = 'bdp_dev';
$conn = null;


/**
 * 
 * @param $fetch_mode
 * @return Zend_Db_Adapter
 */
function getConn($fetch_mode=''){
	global $conn,$application;
	if($conn != null){
		return $conn;		
	}
	global $db_schema;
//	$cfg_elem = new Zend_Config();
	$application->getOptions();
// Automatically load class Zend_Db_Adap ter_Pdo_Mysql and create an instance of it.
	$conf_arr = array(
    'host'     => '127.0.0.1',
    'username' => 'root',
    'password' => '',
    'dbname'   => 'bdp_dev');
	$db = new Zend_Db_Adapter_Pdo_Mysql($conf_arr);
	return $db;
	if($fetch_mode!=''){
		$conn->SetFetchMode($fetch_mode);
	}
	$host = 'localhost';
	$db = 'bdp_dev';
	$user = 'root';
	$pwd = '';
	if($_ENV["HTTP_HOST"]=='www.bildelspriser.dk'){
		$user = 'bildelspriser_d';
		$pwd = 'iQpH3VmJ';
		$db = 'bildelspriser_d';		
	}
	$db_schema = $db;
	//$argHostname = "", $argUsername = "", $argPassword = "", $argDatabaseName = "", $forceNew = false) 
	//return $conn->connect('localhost','root','','bildelspriser_d');
	$conn->connect($host,$user,$pwd,$db);
	return $conn;
}

function getConn_ado($fetch_mode=''){
	global $conn;
	if($conn != null){
		return $conn;		
	}
	global $db_schema;
	$conn = &ADONewConnection('mysql');
	if($fetch_mode!=''){
		$conn->SetFetchMode($fetch_mode);
	}
	$host = 'localhost';
	$db = 'bdp_dev';
	$user = 'root';
	$pwd = '';
	if($_ENV["HTTP_HOST"]=='www.bildelspriser.dk'){
		$user = 'bildelspriser_d';
		$pwd = 'iQpH3VmJ';
		$db = 'bildelspriser_d';		
	}
	$db_schema = $db;
	//$argHostname = "", $argUsername = "", $argPassword = "", $argDatabaseName = "", $forceNew = false) 
	//return $conn->connect('localhost','root','','bildelspriser_d');
	$conn->connect($host,$user,$pwd,$db);
	return $conn;
}

function where_clause_formater($array){
	$col_sep = '`';
	$val_sep = "'";
	$eq_op = "=";
	$and="";
	$where_clause = ' ';
	foreach($array as $col_name => $col_value){
		$where_clause .= $and.$col_sep.$col_name.$col_sep.$eq_op.$val_sep.$col_value.$val_sep;
		 $and = ' and ';		
	}
	return $where_clause;	
}
//$rs = $conn->execute($query);
//if($rs->RecordCount()>0){
	//print "ADO Rows";	
//}

	//$con = getConn();
	function db_query_ado($sql){
		//global $con;
		//$con = getConn();
		//die($con->ErrorMsg());
		trace('db_query called with SQL = '.$sql);
		$res = getConn()->execute($sql) or report_error('Fejl ved kald til databasen - '.getConn()->ErrorMsg()."\n SQL:\n\t".$sql);
		return $res;
		
	}

	function db_query($sql){ //using Zend_Db
		//global $con;
		//$con = getConn();
		//die($con->ErrorMsg());
		trace('db_query called with SQL = '.$sql);
		
		$res  = getConn()->query($sql)
		//$res = getConn()->execute($sql) 
			or report_error('Fejl ved kald til databasen - '
			.getConn()->ErrorMsg()."\n SQL:\n\t".$sql);
		return $res;
		
	}
	
   function phpDateToDbDate($phpdate){
  	return date( 'Y-m-d H:i:s', $phpdate );
  }
  
  function DkFloatToMySQL($value){
  	$comma_pos = strrpos($value, ',');
  	$dot_pos = strrpos($value, '.');
  	if($comma_pos){
  		if($dot_pos){//both 10.000,30 and 10,000.00
  			if($comma_pos==2){ // only 10.000,30
  				$r = str_replace('.','',$value); // now 10000,30
  				return str_replace('.','',$r);  // now 10000.30 = ok for mysql			
  			}else{// only 10,000.00
  				//return $value;
  				return str_replace(',','',$value);
  			}
  		}else{ // either // 10,000 and 10000,12
		  	if($comma_pos==2){ //10000,12
		 		return str_replace(',','.',$value);  // now 10000.30 = ok for mysql 		
		  	}else{ // 10,000
		 		return str_replace(',','',$value); 		
		  	}
  		}		  			
  	}
  	if($dot_pos){//both 10.000 and 10.30
  		if($dot_pos==2){// only 10.30
  			return $value;
  		}else{
  			return str_replace('.','',$value); //now only 10000 = ok for mysql;
  		}  		
  	}
  }
  
  function DbDateToPhpDate($dbdate){
  	return strtotime( $mysqldate );
  }
	
function get_affected_rows(){
	//http://phplens.com/adodb/reference.functions.affected_rows.html
	return getConn()->Affected_Rows();
}

function get_db_schema(){
	global $db_schema;
	return $db_schema;
	
}

function get_timestampformat(){
	$iso_format = 'Y-m-d H:i:s';
	//"25-01-2009 00:00:00"
	//'y'
	return $iso_format;
}

function get_time_now_as_string(){
	return date(get_timestampformat());
}

function Unittest_getConn(){
	$rs = getConn()->Execute('SELECT * FROM test');
	print rs2html(rs);
}



/*function String_SQL_Safe($str){
	return str_replace("';\","-",$str);	
}
*/
?>