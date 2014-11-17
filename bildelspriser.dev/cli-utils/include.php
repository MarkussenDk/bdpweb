<?php
$line='-------------------------------------------------';
function is_windows(){
	return strtoupper(substr($_SERVER['OS'], 0, 3)) === 'WIN';
}
if (is_windows()) {          
    define("DIR_SEP", "\\");
	define("CUR_DIR","");
}
else {
    define("DIR_SEP", "/");
	define("CUR_DIR","./");
}

/**
 * @param string $display_name Displayname
 * @param string $sys_cmd Command or Expression to be executed
 */
function run($display_name,$sys_cmd){
	global $line;
	echo "Starting '$display_name'\n$line\n";
	echo shell_exec($sys_cmd);
	echo "\n$line\nDone calling '$display_name'\n";
}
/**
 * @param string $display_name Displayname
 * @param string $sys_cmd Command or Expression to be executed
 */
function run_other_window($display_name,$sys_cmd){
	global $line;
	//$cmd_post_fix = 'start "" ';

	 //$pid=pcntl_fork();
	 //echo "Fork id $pid ";
	 $args = array();
	 if($pid==0)
	 {
	   //posix_setsid();
	   run($sys_cmd);
	   //pcntl_exec($sys_cmd,$args,$_ENV);
	   // child becomes the standalone detached process
	 }
	 return; 

	if(!is_windows()){	
		throw new Exception("Unix/MacOX drenge .. implementer dette selv :) ");
		$cmd_post_fix = "Some Unix trick to open another window";
	}
	echo "Starting '$display_name'\n$line\n";
	$l = "$cmd_post_fix $sys_cmd ";
	echo "Calling '$l' ";
	echo shell_exec($l);
	echo "\n$line\nDone calling '$display_name'\n";
}
/**
 * @param datatype $paramname description
 */
 
function php_wget($url,$file){
	echo "Downloading $url \n$line\n";
	file_put_contents($file, file_get_contents($url));
	echo "\n$line\nDone downloading to file \n";
}
/**
 * @param array ar_key_vals Array to convert to string
 * @param integer recursive internal parameter 
 * @returns a string representation of the array
*/
function array_to_string($ar_key_vals,$recursive=0)
{
	$text="";
	if($recursive==0){
		$text = "----------------\n";
		echo $text . "\n";
	}	
	if(!is_array($ar_key_vals)){
		return "Variable is not an array";
	}
	foreach ($ar_key_vals as $key => $value) {
		if(is_array($value)){
			$text .= "\n ARRAY START [$key]\n  " . array_to_string($value,1). " ARRAY END [$key]\n" ;
		}
		else{
			$text .=  " [$key] = '$value'\n";
		}
	}
	return $text;	
}