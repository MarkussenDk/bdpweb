<?php

function get_server_time(){
    global $_SERVER;
	return $_SERVER["REQUEST_TIME"];	
}

function get_file_name(){
	global $_SERVER;
	$script_name = $_SERVER['SCRIPT_NAME'];
	$script_name = str_replace('/','_',$script_name);
	$filename = "trace.".$script_name.'.'.get_server_time().".html";
	return $filename;
}

function make_tr($key,$value,$tr_class='key_val',$td_class='key_val'){
	return "<tr><td><b>$key</b></td><td>$value</td></tr>";	
}

function microtime_diff( $start, $end=NULL ) { 
        if( !$end ) { 
            $end= microtime(true); 
        } 
        //list($start_usec, $start_sec) = explode(" ", $start); 
        //list($end_usec, $end_sec) = explode(" ", $end); 
        //$diff_sec= intval($end_sec) - intval($start_sec); 
        //$diff_usec= floatval($end_usec) - floatval($start_usec); 
        return  ($end - $start); 
    } 


function get_server_file_info(){
		global $_REQUEST;
    	global $_SERVER;
    	$start = "<table style='border:gray solid 1px;' >";
    	$start .= make_tr('HTTP_USER_AGENT',$_SERVER['HTTP_USER_AGENT']);
    	$start .= make_tr('REQUEST_URI',$_SERVER['REQUEST_URI']);
    	$start .= make_tr('SCRIPT_NAME',$_SERVER['SCRIPT_NAME']);
    	$start .= make_tr('PHP_SELF',$_SERVER['PHP_SELF']);
    	$start .="</table>";
    	return $start;	
}

function get_server_all_info(){
    	$start = '<pre style="border:silver solid 2px;" >'.print_r($GLOBALS,true).'</pre>';  
    	return $start;
}



    function trace_to_file($message){    	
    	$path = get_file_name();
    	$fs = @filesize($path) or 0;
    	$start = ''; 
    	if($fs == 0){
    		$start .= "<h1>TraceFile - ".$path."</H1>";
			$start .= get_server_file_info();  		
    	}
    	$fp = fopen($path,'a+');
    	$start .= '<h3>';
    	$end = '</h3>';
    	fwrite($fp,$start.$message.$end);    	
    	fclose($fp);
    	
    	// write latest_trace.html
    	$latest_trace_path = 'latest_trace.html';
    	copy($path,$latest_trace_path);
    	$fp = fopen($latest_trace_path,'a+');
    	fwrite($fp,"<h3><a href='#all_vars'>All variables</a><h3/>\n".get_server_all_info());
    	fclose($fp);
    }
    
    function trace($string,$print_to_screen = 'off'){
    	if($print_to_screen!='off'){
    		print "\n<BR/>".$string;
    	} 
    	trace_to_file($string);   	
    }
    
    function trace_var($var,$message='<no message>'){
    	//see http://dk2.php.net/manual/en/function.print-r.php
    	trace_to_file('trace_var - '.$message.'<br/>'.print_r($var,true));
    }


function print_var( $var )
{
   if( is_string( $var ) )
       return( '"'.str_replace( array("\x00", "\x0a", "\x0d", "\x1a", "\x09"), array('\0', '\n', '\r', '\Z', '\t'), $var ).'"' );
   else if( is_bool( $var ) )
   {
       if( $var )
           return( 'true' );
       else
           return( 'false' );
   }
   else if( is_array( $var ) )
   {
       $result = 'array( ';
       $comma = '';
       foreach( $var as $key => $val )
       {
           $result .= $comma.print_var( $key ).' => '.print_var( $val );
           $comma = ', ';
       }
       $result .= ' )';
       return( $result );
   }
   
   return( var_export( $var, true ) );    // anything else, just let php try to print it
}

function trace_with_stack( $msg )
{
   $res = '';
   $res.= "<pre>\n";
   
   //var_export( debug_backtrace() ); $res.= "</pre>\n"; return;    // this line shows what is going on underneath
   
   $trace = array_reverse( debug_backtrace() );
   $indent = '';
   $func = '';
   
   $res.= $msg."\n";
   
   foreach( $trace as $val)
   {
       $res.= $indent.$val['file'].' on line '.$val['line'];
       
       if( $func ) $res.= ' </b> in function <b>'.$func.'</b>';
       
       if( $val['function'] == 'include' ||
           $val['function'] == 'require' ||
           $val['function'] == 'include_once' ||
           $val['function'] == 'require_once' )
           $func = '';
       else
       {
           $func = $val['function'].'(';
           
           if( isset( $val['args'][0] ) )
           {
               $func .= '\n<br/><ul class="trace_arguments"> Arguments -  ';
               $comma = '';
               foreach( $val['args'] as $val )
               {
                   $func .= '\n</br><li class="trace_arguments">$comma.print_var( $val )</li>';
                   $comma = ', ';
               }
               $func .= '</ul> ';
           }
           
           $func .= ')';
       }
       
       $res.= "\n";
       
       $indent .= "->";
   }
   
   $res.= "</pre>\n";
   return nl2br($res);
}

function get_stack_trace(){
	return "<h3>Stack Trace</h3>\n<pre>".print_r(debug_backtrace(),true).'</pre>';	
}

function print_with_line_numbers($message,$print_to_screen = 'off'){
	//is array
	if(!is_array($message)){
		$message = explode("\n",$message);
	}
	$line_no = 1;
	foreach($message as $line){
		$html .= $line_no++.' : '.$line;
	}
	if($print_to_screen != 'off'){
		print $html;
	}
	return $html;
}

interface bdp_logger{
	public static function log($msg);
}

class bdp{
	static $_log_obj;
	
	public static function l($msg){
		self::info($msg);
	}
	public static function log($msg){
		//if($msg=='1') 			Error("Please be a bit more specifit than just 1");
		if(self::$_log_obj){
			self::$_log_obj->log($msg);
		}	
	}

	public static function info($msg){
		//if($msg=='1') 			Error("Please be a bit more specifit than just 1");
		if(self::$_log_obj){
			self::$_log_obj->log($msg);
		}	
	}
	
	public static function set_logger(bdp_logger $_logger){
		self::$_log_obj = $_logger;
	}
	
	public static function set_logger_type($type='direct'){
		ini_set('output_buffering',1);
		ob_start();
		ob_flush();
		self::set_logger(new screen_logger());
		echo "setting logger to screen_logger";
	}	
}

function human_readable_seconds($secs_as_float) {
	$log10 = log10($secs_as_float);
	$iv=intval($log10/3,10);
	$s = "  ";
	switch($iv){
		case -0: // milli secs
			$s .= round($secs_as_float*1000,1) . ' mS ';
			break;
		case -1: // milli secs
			$s .= round($secs_as_float*1000*1000,1) . ' uS ';
			break;
		case -2: // milli secs
			$s .= rount($secs_as_float*1000*1000*1000,1) . ' nS ';
			break;
		default:
			$s .='Unknown value  IV: $iv ';
	}
	return $s;
}

class screen_logger implements bdp_logger{
	public static function log($msg){
		$style = ' style="border:solid silver 1px" ';
		$i=++table_logger::$i;
		$mt = microtime(true);
		$t = $mt - table_logger::$start_microtime;
		$dt = $mt - table_logger::$last_microtime;
		$hdt = human_readable_seconds($dt); 
		table_logger::$last_microtime = $mt;
		$t = ' '.$t.' </td><td width="10pt" >'.$hdt ;
		$ob_lvl = ob_get_level();
		$tbody= "<table width='100%' ><tr  $style > <td width='50pt'  $style >$i</td><td width='60pt' >$t</td> <td dwidth='10pt'  $style >$msg</td> <td  $style ></td> </tr><table>";
		echo "<div style=' '><!-- (OB level: $ob_lvl)-->$tbody<div>";
		$i=0;
		//$s='<hr>';
		while($ob_lvl=ob_get_level()){
			//echo "($lvl) - flushing;";
			//$s .= '<br>'.$i++.' - '.$ob_lvl;
			if($ob_lvl>0){
				ob_flush();
				flush();
				ob_end_flush();
			}
			if($i>8){
				//$s .= ' breaking '; 
				break;
			}
		}
		//ob_start();
		//echo $s;			
	}
}

class table_logger implements  bdp_logger{
	static $_logger_items = array();
	static $i = 0;		
	public static $last_microtime = null;
	public static $start_microtime = null;
	public static function log($msg){
		self::$_logger_items[] = array( 'message' => $msg, 'microtime'=>microtime(true));
	}

	public static function formatHtmlTable(){
		$style = ' style="border:solid silver 1px" ';
		$tbody = "<tr><th >Id</th><th >Time</th> <th>Message</th> </tr>";
		foreach (self::$_logger_items as $item) {
			$m = $item['message'];
			$mt = $item['microtime'];
			$t = $mt - self::$start_microtime;
			$dt = $mt - self::$last_microtime;
			self::$last_microtime = $mt;
			$t = ' '.$t.' d ='.$dt ;
			$i=++self::$i;
			$tbody.= "<tr  $style > <td  $style width='50pt' >$i</td><td width='300pt' >$t</td> <td  $style >$m</td> <td  $style ></td> </tr>";
		}
		return "<table width='100%' $style >$tbody</table>";
	}	
	
	public static function toString(){
		return self::formatHtmlTable();
	}
}

table_logger::$start_microtime = microtime(true);
table_logger::$last_microtime = table_logger::$start_microtime;

class buffered_logger implements bdp_logger{
	public static function log($msg){
			
	}
}


