<?php 
//set_error_reporting('E_ALL');
require 'trace.inc.php';
require 'db.inc.php';
// See - http://dk2.php.net/error_reporting
function error($str){
	throw new Exception($str);
}

function warning($str){
	static $arWarnings;
	$arWarning[]=$str;
}

function debug($string){
	return;
	$visibility = 'yes';  
	$start = "\n\t<div style='border:1px solid gray;' >DEBUG: ";
	$end = "\n\t</div>";
	echo $start.$string.$end;	
}

function var_dump_array($array,$array_name = 'Unknown array = '){
	//die(nl2br($array_name."\n".var_export($array,true)));
	error(nl2br($array_name."\n".var_export($array,true)));		
}

function getRequestVar($key){
	global $_GET;
	return String_SQL_Safe($_GET[$key]);
}

function getMandatoryGet($key,$msg=''){
	$val = $_GET[$key];
	if(isset($val) and is_string($val)){
		return $_GET[$key];
	}
	error("The GET variable '$key' was not set");
}

function getMandatoryInt($key,$msg=''){
	if($msg==''){
		$msg = "The GET variable '$key' must be an int";
	}
	$iValue = getMandatoryGet($key);
	if(is_int(iValue)){
		return iValue;
	}
	error($msg." The value was '$iValue'"); 
}

function assertEx($expr,$msg){
	if($expr)
		return;
	error(nl2br("Assertion failed :  $msg \n<br>"));	
}

class Registry{
	private static $instance;
	private $values = array();
	
	static function get($key){
		switch($key){
			case 'vat_rate':	return 0.25;
			default: error("Unknown key used in Registry() - Key was '$key' ");
		}
	}
	
	static function set($key, $value){
		
	}	
}

 ?>