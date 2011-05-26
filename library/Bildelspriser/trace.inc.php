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
		$message = split("\n",$message);
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
