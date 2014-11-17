<?php
error_reporting( E_ALL | E_STRICT );
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
date_default_timezone_set('Europe/London');

define('BASEPATH',realpath(dirname(__FILE__) . '/..'));
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
define('APPLICATION_ENV', 'unittest');
define('LIBRARY_PATH', realpath(dirname(__FILE__) . '/../library'));
define('TESTS_PATH', realpath(dirname(__FILE__)));
$pear_path = 'C:\wamp\bin\php\php5.3.0\PEAR';

$_SERVER['SERVER_NAME'] = 'http://localhost';

$includePaths = array(LIBRARY_PATH,$pear_path, get_include_path());
set_include_path(implode(PATH_SEPARATOR, $includePaths));

require_once "Zend/Loader.php";
require_once 'Zend/Loader/Autoloader.php';
//$autoloader = Zend_Loader_Autoloader::getInstance();
//$autoloader->registerNamespace('Default');

Zend_Loader::registerAutoload();
//$autoloader = Zend_Loader_Autoloader::getInstance();

        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'Default',
            'basePath'  => APPLICATION_PATH
        ));
        //$autoloader->addResourceType('model', 'models/');
        //$autoloader->addResourceType('Model_', 'models/','Model_');
        //               		->addResourceType('Model', 'models/'); 
		//die('active');
		//$autoloader->autoload('Default_Model_XmlHttpRequestMapper');
		//$autoloader->autoload('Default_Model_DbTable_SparePartSuppliers');

Zend_Session::$_unitTestEnabled = true;
Zend_Session::start();