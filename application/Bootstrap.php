<?php
require_once 'Bildelspriser/all.inc.php';
setlocale ( LC_ALL, 'da_DK' );

$ts = 'Europe/Copenhagen';
date_default_timezone_set ( $ts );


class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	var $zfdebug_enabled = false;
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
      $webhost =$_SERVER["SERVER_NAME"];
      if(stripos($webhost,'zfd')>-1){
      	$this->zfdebug_enabled = true;
      }
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
	    
    public static function ZFDebug_Enabled(){
    	$autoloader = Zend_Loader_Autoloader::getInstance();
    	$rn = $autoloader->getRegisteredNamespaces();
    	//Zend_Debug::dump($rn,'RegisteredNameSpaces',true);
    	return array_search('ZFDebug', $rn);
    }
   
   
	protected function _initZFDebug()
	{
	    if (!$this->zfdebug_enabled) { 
	    	return;
	    }
		$autoloader = Zend_Loader_Autoloader::getInstance();
	    $autoloader->registerNamespace('ZFDebug');
	    
	    $options = array(
	        'plugins' => array('Variables', 
	                           'File' => array('base_path' => 'APPLICATION_PATH'),
	                           'Memory', 
	                           'Time', 
	                           'Registry', 
	                           'Exception')
	    );
	    
	    # Instantiate the database adapter and setup the plugin.
	    # Alternatively just add the plugin like above and rely on the autodiscovery feature.
	    if ($this->hasPluginResource('db')) {
	        $this->bootstrap('db');
	        $db = $this->getPluginResource('db')->getDbAdapter();
	        $options['plugins']['Database']['adapter'] = $db;
	    }
	
	    
	    # Setup the cache plugin
	    if ($this->hasPluginResource('cache')) {
	    	//throw new Exception('This has cahce', 123,NULL);
	        $this->bootstrap('cache');
	        $cache = $this->getPluginResource('cache')->getDbAdapter();
	        $options['plugins']['Cache']['backend'] = $cache->getBackend();
	    }
	
	    $debug = new ZFDebug_Controller_Plugin_Debug($options);
	    
	    $this->bootstrap('frontController');
	    $frontController = $this->getResource('frontController');
	    $frontController->registerPlugin($debug);
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

