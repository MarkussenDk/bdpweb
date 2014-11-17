<?php
//set_include_path('../');
// Define path to application directory

defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

if(!defined('APPLICATION_PATH')){
    throw new Exception("The constant APPLICATION_PATH is not defined");
}
 elseif(strpos(APPLICATION_PATH,'/')<0) {
    throw new Exception("The constant APPLICATION_PATH does not contain anything '".APPLICATION_PATH."'");
}

if(strpos($host=$_SERVER["HTTP_HOST"],'.dev')){
   define('APPLICATION_ENV','development');    
}

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
if(APPLICATION_ENV == 'production'){
    if(strpos($host=$_SERVER["HTTP_HOST"],'.dev')){
        die("The URL is '$host' - please set the APPLICATION_ENV to somthing other than production. E.g. development ");
    }
}
$first=get_include_path();
// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, 
        [
            realpath(APPLICATION_PATH . '/../php_include'), //php_include moved
            realpath(APPLICATION_PATH . '/../library'),
            get_include_path()
        ]
));
$include_path = get_include_path();
assert(strpos(get_include_path(), 'php_include'),"The include path does not contain php_include ");
assert(strpos(get_include_path(), 'library'),"The include path does not contain php_include ");

/** Zend_Application */
require_once 'Zend/Application.php';  

//die(APPLICATION_ENV);

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV, 
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap()->run();