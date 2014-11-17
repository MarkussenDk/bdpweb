<?php
require('./cli-utils/include.php');
run('Composer update','composer update');
//run('Downloading Scelenium','wget http://selenium-release.storage.googleapis.com/2.42/selenium-server-standalone-2.42.2.jar');
// echo "\nStarting composer update\n$line\n";
// echo shell_exec('composer update');
// echo "\n$line\nDone calling composer update\n";
php_wget('http://selenium-release.storage.googleapis.com/2.42/selenium-server-standalone-2.42.2.jar','selenium-server-standalone-2.42.2.jar');