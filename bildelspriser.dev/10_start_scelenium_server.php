<?php
require('./cli-utils/include.php');
//Wget the jar file
// URL: http://selenium-release.storage.googleapis.com/2.42/selenium-server-standalone-2.42.2.jar
$sel_jar_file_name  ='selenium-server-standalone-2.42.2.jar';

if(!file_exists($sel_jar_file_name)){
	php_wget('http://selenium-release.storage.googleapis.com/2.42/selenium-server-standalone-2.42.2.jar'
		,$sel_jar_file_name);
}

//run_other_window("Starting the scelenium driver","java.exe -jar $sel_jar_file_name");
run_other_window("Starting the scelenium driver","_start_scelenium.bat");

//	run_other_window("Starting the scelenium driver","java.exe -jar $sel_jar_file_name");