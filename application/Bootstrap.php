<?php
require_once 'Bildelspriser/all.inc.php';
setlocale(LC_ALL,'da_DK');

$ts = 'Europe/Copenhagen';
date_default_timezone_set($ts);

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
// Add this method to the Bootstrap class:
// Reason - http://framework.zend.com/docs/quickstart/create-a-model-and-database-table

	protected function _setupMyDbCache(){
	       //Caching 
		$cache_dir =  APPLICATION_PATH . '/data/cache';
		$cache_dir = 'cache';
		$frontendOptions = array( 
                          'lifetime'                  => 25200, 
                          'automatic_serialization'   => true 
                          ); 
      $backendOptions  = array( 
                           'cache_dir'                => $cache_dir  
                          ); 
     if(!file_exists($cache_dir))
      {
      	ECHO "DIR was missing $cache_dir ";
      	
		$r = mkdir($cache_dir);
      	echo " -$r- now created\n<br>";
      }
      $db_cache = Zend_Cache::factory( 
                          'Core', 
                          'File', 
                          $frontendOptions, 
                          $backendOptions 
                          ); 
      //$d = dir(cache_dir);
      //Cache table metadata 
      Zend_Db_Table_Abstract::setDefaultMetadataCache($db_cache);  
        
	}
	
    protected function _initAutoload()
    {
    	//die('Dirname = '.dirname(__FILE__));
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'Default',
            'basePath'  => dirname(__FILE__)
        ));
        //$autoloader->addResourceType('model', 'models/');
        //$autoloader->addResourceType('Model_', 'models/','Model_');
        //               		->addResourceType('Model', 'models/'); 

		$this->_setupMyDbCache();
        
        //die('active');
		$autoloader->autoload('Default_Model_XmlHttpRequestMapper');
		//$autoloader->autoload('Default_Model_DbTable_SparePartSuppliers');
        return $autoloader;
    }
    
    /*protected function _initView(){
    	$view = $this->bootstrap('view')->getResource('view');
    	Zend_Dojo::enableView($view);
    	$view->dojo()->setDjConfigOption('usePlainJson',true)
    				 ->addLayer('/js/bdp/main.js')
    				 ->disable();
    				 
    				 //->addJavascript('bdp.main.init();');* /
	 	return $view;
    }*/   

}

