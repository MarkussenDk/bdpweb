<?php 
//set_error_reporting('E_ALL');
require 'trace.inc.php';
require 'db.inc.php';
require_once "Mail.php";
require_once "Mail/mime.php";
include 'kint/Kint.class.php';
$allow_email = true;

// See - http://dk2.php.net/error_reporting
$error = "";
function error($str){
	global $error;
	$error = $str;
	throw new exception($str);
}
/**
 * 
 * Look to see if the needle can be found in the haystack
 * @param string $haystack
 * @param string $needle
 */
function str_contains($haystack,$needle){
	if(strpos($haystack,$needle) > -1)
		return true;
	return false;	
}
/**
 * 
 * Search the haystack, to see if it begins with the needle
 * @param string $haystack 
 * @param string $needle
 */
function str_begins_with($haystack,$needle){
	if(strpos($haystack,$needle) == 0)
		return true;
	return false;	
}
ini_set("SMTP","smpt1.servage.net");
ini_set("sendmail_from","andreas.markussen@bildelspriser.dk");
function error_function($error_level,$error_message,$error_file,$error_line,$error_context){
	switch ($error_level){
		case 8192:
			if( str_contains($error_file, '/php5/')) return;
			break;
		case 2048: 	
			if( str_contains($error_message, ') should be compatible with that of PEAR::')) return;
			if( str_contains($error_file, '/PEAR/')) return;
			if( str_contains($error_file, '/php5/Mail/')) return;
			if( str_begins_with($error_message, 'is_a()')) return;	
			break;
		case 2: if(strpos($error_message,"Couldn't fetch mysqli_stmt")) return;
		case 8: if(strpos($error_message, 'already started')) return;
	}
	try{
		mail_error_function($error_level, $error_message, $error_file, $error_line, $error_context);
	}
	catch (exception $e){
		echo "Error occured ".nl2br($e);
	}	
}

function exception_handler(){
	
}

function var_format($v) // pretty-print var_export 
{ 
  ob_start();
  Kint::dump($v);
  $var = ob_get_flush();
  return $var;
} 

function getTrace(){
  ob_start();
  Kint::trace();
  //$var = ob_get_flush();
  $var = ob_get_clean();
  return $var;
}

function mail_error_function($error_level,$error_message,$error_file,$error_line,$error_context){
	global $allow_email;
	global $S_SERVER;
	$client_ip = $_SERVER["REMOTE_ADDR"];
	$client_agent=$_SERVER["HTTP_USER_AGENT"];
	$REQUEST_URI=$_SERVER["REQUEST_URI"];
	$host=$_SERVER['HTTP_HOST'];
	$url='http://'.$host.$REQUEST_URI;
	$errortype = array(
  E_ERROR           => 'error',
  E_WARNING         => 'warning',
  E_PARSE           => 'parsing error',
  E_NOTICE          => 'notice',
  E_CORE_ERROR      => 'core error',
  E_CORE_WARNING    => 'core warning',
  E_COMPILE_ERROR   => 'compile error',
  E_COMPILE_WARNING => 'compile warning',
  E_USER_ERROR      => 'user error',
  E_USER_WARNING    => 'user warning',
  E_USER_NOTICE     => 'user notice',
  2048				=> 'strict',
  4096				=> 'notice',
  8192				=> 'informational',
  );
  $silent=false;
  $env = getenv('APPLICATION_ENV');
  if($env && $env=='development'){
		$stack_trace = getTrace();
  }else{// Production
  	if(error_level<E_STRICT)
  		$silent=true;
  }
  
  $error_level_text = $errortype[$error_level];
  	//echo "<hr/>";	
	$style_css_url='http://bildelspriser.dk/media/css/basis.css';
	$html_err_msg =<<<MAIL
	<html>
	<head>
		<link rel="stylesheet" type="text/css" href="$style_css_url" />
	</head>
	<body>
	<div style = 'border:solid 3px red;' >
	<h1>Error on php script</h1>
	<table class='bdp_std_table' >
		<tr class='odd' ><td>ErrorLevel</td><td>$error_level - $error_level_text</td></tr>
		<tr  class='even' ><td>Message</td><td>$error_message : </td></tr>
		<tr class='odd'><td>File</td><td>$error_file</td></tr>
		<tr><td>Line</td><td>$error_line</td></tr>
		<tr><td  class='odd'>Context</td><td>$error_context</td></tr>
		<tr><td  class='even'>client ip</td><td>$client_ip</td></tr>
		<tr><td  class='even'>client agent</td><td>$client_agent</td></tr>
		<tr><td  class='even'>url</td><td>$url</td></tr>
		</table>
	<h2>Stack trace</h2>
	$stack_trace
	</div>
	</body>
	</html>
MAIL;

//echo "<br>Envionment '$env' ";
	if($env && $env=='development' ){
		echo $html_err_msg;	
		return; 
	}
	if(!$allow_email){
		echo "<div style='border:solid 3px red;'><hr>Email not allowed<br>".$html_err_msg.'</div>';		
	}

$headers = 'From:  webmaster@bildelspriser.dk' . "\r\n" .
    'Reply-To:  webmaster@bildelspriser.dk' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
//$headers .= "MIME-Version: 1.0\r\n";
//$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	//mail('andreas.markussen@gmail.com', 'Error on bildelspriser'+$error_message, $html_err_msg,$headers);
 
 $from = "BDP WebServer $env <andreas.markussen@bildelspriser.dk>";
 $to = "WebMaster $env <andreas.markussen@bildelspriser.dk>";
 $subject = 'Error on bildelspriser - '.$error_message;
 $body = $html_err_msg;
 
 $host = "smtp1.servage.net";
 $username = "andreas.markussen@bildelspriser.dk";
 $password = "Volvo480";
 
 $headers = array ('From' => $from,
   //'To' => $to,
   'Subject' => $subject,
  // 'MIME-Version'=>'1.0',
  // 'Content-Type'=>'text/html; charset=UTF-8'
 );
 $crlf="\r\n";
 $mime = new Mail_mime(array('eol' => $crlf ));

//$mime->setTXTBody($text);
 $mime->setHTMLBody($html_err_msg);
 
 $smtp = Mail::factory('smtp',
   array ('host' => $host,
     'port'=>2525,
     'auth' => true,
     'username' => $username,
     'password' => $password));
 //echo $html_err_msg;
 //return;
 $mail = $smtp->send($to, $headers, $body=$mime->get());
 
 
 if (PEAR::isError($mail)) {
   echo("<p>" . $mail->getMessage() ."</p>");
  } elseif(!$silent) {  	
   echo("<p>Der er sket en fejl på siden.</br>Der er sendt en meddelse til Supporten.</br>$error_message.</p>");
  }
}


set_error_handler('error_function');
// for debugging/testing Kint
//echo $unknown/0;

class logger{
	static $log_level = 1;
	static $log_buffer = 100;
	static $msgs = array();
	static $inst=null;
	static function log($string){
		self::$msgs[] = $string;
		if(count(self::$msgs)> self::$log_buffer){
			
		}
	}
	static function empty_buffer(){
		self::log_out(implode("\n", self::$msgs));
		$msgs = array();			
		
	}
	static function log_out($string){
		if(self::$inst){
			self::$inst = new self();
		}
		echo $string;		
	}
	function __destruct(){
		log("Ending logger");
		self::empty_buffer();		
	}
}

function warning($str){
	static $arWarnings;
	$arWarning[]=$str;
}

function info($str){
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

function first_word($string){
	$ar = explode(' ',$string);
	return $ar[0];	
}

 ?>