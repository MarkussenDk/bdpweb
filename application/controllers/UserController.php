<?php

/**
 * {0}
 * 
 * @author
 * @version 
 */

require_once 'Zend/Controller/Action.php';
require_once  APPLICATION_PATH.'/models/SparePartSuppliers.php';
require_once 'BildelspriserAuth.php';
require_once 'Bildelspriser/XmlImport/PriceParser.php';
class UserController extends Zend_Controller_Action {
/**
 * The default action - show the home page
 */
	var $auth_res = "";
	public function init()
    {
    	$xh = new Default_Model_XmlHttpRequest();
    	$params = $this->getRequest()->getParams();
    	if(array_key_exists('username',$params) && array_key_exists('password',$params) ){
    		$user_name = $params['username'];    		
    		$password = $params['password'];
//    		$this->auth_res = $this->doLogin($user_name,$password);
    		//$d = new BildelspriserAuthAdapter($user_name,$password);
    		
    		//$res = $d->authenticate(false);

/*    		if(Zend_Auth_Result::SUCCESS == $this->auth_res){
    			echo "Auth success  - ".$user_name;
    		}
    		else
    			die("Auth Error ".$user_name. + $password);*/
    	}
    	
    	    	
    	//$xh->setCreatedBy($user_name);
    	try{$xhrid = $xh->getMapper()->save($xh);}
    	catch(exception $e){
    		echo "exception while saving XmlHttpRequest ".$e;
    	}
    	assertEx($xhrid, "in User->Init() - xhrid was null '$xhrid' ");
		Bildelspriser_XmlImport_PriceParser::$_xml_http_request_id = $xhrid;
    	//$view = $this->view;
    	//die(var_dump($view));
    	//Zend_Dojo::enableView($view);
        //Zend_Dojo::disableView($view);
        //http://www.zfforums.com/zend-framework-general-discussions-1/general-q-zend-framework-2/plugin-name-dojo-not-found-registry-1879.html 
   // 	$view->dojo()->setDjConfigOption('usePlainJson',true)
    //				 ->addLayer('/js/bdp/main.js');
    				 //->addJavascript('bdp.main.init();');*/
    } 
	var $_form;
	const ERR_USER_OFFSET = 1000000;
	const ERR_NO_AUTHENTICATED_USER = 1000001;
	var $_authenticated_user_name;
	var $_loginform;
	

	public function _init(){
    	Default_Model_XmlHttpRequest::readOrCreateUserCookie();
		$xh = new Default_Model_XmlHttpRequest();
    	//$xh->setCreatedBy($user_name);
		try{$xhrid = $xh->getMapper()->save($xh);}
    	catch(exception $e){
    		warning(echo " exception while saving XmlHttpRequest ".$e);
    	}
    	
    	
		$view = $this->view;
    	//die(var_dump($view));
    	Zend_Dojo::enableView($view);
    	$view->dojo()->setDjConfigOption('usePlainJson',true)
    				 ->addLayer('/js/bdp/main.js');
		$this->view->header_links = $this->getHeadderLinks();
		$action_name = $this->getRequest()->getActionName();
		//echo "Action = $action_name ";
		if('index' <> $action_name && 'login' <> $action_name){
			$user_name = $this->getUserName();
			if(empty($user_name)){
				$this->_forward('index');
			}
		}
		parent::init();
	}
	
	public function initView(){
		//echo "in initview()";
		echo "<html><head>"
		.	'<LINK REL=StyleSheet HREF="/bdp/public/css/b2b_user.css" TYPE="text/css" MEDIA=screen>'
		. "</head>";
		parent::initView();
	}
	
	
	
	private function getHeadderLinks(){
		//$this->
		return " <a href='/user/logout' > Logout </a> |  <a href='/user/filelist'>List Files</a>";
	}	
	
/*public function render($action,$name,$noControler){
		$this->_view->_header_links = $this->getHeadderLinks()." - was set in render()";
		parent::render($action,$name,$noControler);
	}*/
	
	private function createForm(){
		if(empty($this->_loginform)){
			$this->_loginform = new Default_Form_Login();
		}
		$this->_loginform->setAction('/user/login/');
		return $this->_loginform;
	}
	
	private function getDestinationFolder(){		
		$username = strtolower($this->getUserName());
		if(empty($username)){
			throw new Exception("No authenticated use found - Please login",self::ERR_NO_AUTHENTICATED_USER);		
		}		
		$path = dirname(APPLICATION_PATH).'/data/users/'.$username.'/';
		$this->assertFolder($path);
		return $path;
	}
	
	private function getUserName(){
		if(empty($this->_authenticated_user_name)){
			//echo "<br/> no authed user_name - this is OK - will get it from";
			//$auth = Zend_Auth::getInstance()
			$auth = BildelspriserAuthAdapter::getInstance();
			//$msg = implode('<br>',get_class_methods(get_class($auth)));
			//die('class was ' .get_class($auth).get_parent_class(get_class($auth)).$msg);
			if($auth==null){
				echo "HasIdentity was null in  ".__FILE__.' on line '.__LINE__;
				global $_SERVER;
				$this->_redirect('/user/index/reason/HasIdentityWasNull/?from='.$_SERVER['REDIRECT_URL']);
			}
			if ($auth->hasIdentity()) {				
				// Identity exists; get it
				//echo = ""    
				$identity = $auth->getIdentity();
				$this->_authenticated_user_name = $identity;
				assertEx(isset($identity),"Identity error  ");
			}
		}
		//echo "<br/> authed user_name = ".$this->_authenticated_user_name;
		$u="";
		$p="";
		//$this->_authenticated_user_name = "autostumper";
		$obj=null;
		if(BildelspriserAuthAdapter::checkInSession($u,$p,$obj)){
			//assertEx($u<>"","User was not found in getUserName");
			$this->_authenticated_user_name = $u;
		}
		//if(isset($this->_authenticated_user_name))
        $this->view->username = $this->_authenticated_user_name;
		return $this->_authenticated_user_name;
	}
	
	private function createOrUseFolder(){
		$dest = $this->getDestinationFolder();
		$this->assertFolder($dest);
	}
	
	private function assertFolder($path){
		//echo "<br/>Checking ".dirname($path).' - '.$path;
		if(is_dir($path)){
			//echo " - was found";			
			return true;
		}
		else{
			//echo " - was NOT found";
			//echo "<br/> - Lookin for ".dirname($path);
			//$this->assertFolder(dirname($path));
			// Since the parent is found, create the child
			$res = mkdir($path,0750,true);
			if($res)
				return true;
			else
				throw new exception("Unable to create directory '$path' <br/>$php_errormsg.");
			
		}
	}
	
	private function createUploadForm(){
		// creating object for Zend_Form_Element_File
	 	 
		 $file_dest = $this->getDestinationFolder();
		 $username = $this->getUserName();
		 if(empty($username)){
		 	echo "No authenticated user - please login.";
		 	return $this->_forward('index');
		 }
		 
		 
		 $form = new Zend_Form();
		 $form->setAction('/user/fileuploaded')
		 	   ->setMethod('post')
		 	   ->setAttrib('enctype', 'multipart/form-data');
		 		
		 	
		 $doc_file = new Zend_Form_Element_File('doc_path');
		 $doc_file->setLabel('XML Pricelist File Path')
				  ->setRequired(true)
				  ->setDescription($file_dest);
				  
		 $username = new Zend_Form_Element_Text('authed_username');
		 $username->setLabel('Authenticated Username')
		 		  ->setRequired(true);

		 	 // creating object for submit button
		 $submit = new Zend_Form_Element_Submit('submit');
		 $submit->setLabel('Upload Xml File')
				 ->setAttrib('id', 'submitbutton');
		
		$form->addElement($doc_file)
			 ->addElement($submit);	
		return $form;	
	}
	
	
	public function getForm()    
	{        // create form as above
      if(empty($this->_form))
      	$this->_form = $this->createForm();
	  return $this->_form;
	}    
	  
	  
	public function indexAction()    
	{        
		// get idears from this article - http://robertbasic.com/blog/login-example-with-zend_auth/
		
		// render user/form.phtml     
		$this->view->form = $this->getForm();
        //$this->view->username = $this->_authenticated_user_name;

		//assertEx() 		
		$this->render('form'); 
	} 

	public function phpinfoAction(){
		
	}
	
	public function doLogin($username,$password){
		//echo "<br>doLogin($username,$password)";
		assertEx($username,"Username was not set in doLogin");
		assertEx($password,"Password was not set in doLogin");
		$adapter = new BildelspriserAuthAdapter($username,$password);
		//assertEx($this->_auth,"Auth module was not loaded?");
		//$result = $this->_auth->authenticate($adapter);
		$res = $adapter->authenticate();
		if(Zend_Auth_Result::SUCCESS == $res){
			$this->_authenticated_user_name = $username;
		}
		else{
			die("Wrong username and password");
		}
		return $res;
	}
	
	        
	public function loginAction()    
	{ 
		//echo "Welcome to the login action";
		if (!$this->getRequest()->isPost()) {
			return $this->_forward('index');	
		}
		$form = $this->getForm ();
		if (! $form->isValid ( $_POST )) {
	
		 // Failed validation; redisplay form   
			$this->view->form = $form;
			return $this->render ( 'form' );  
		} 
	    $values = $form->getValues();  
	    $result = null; 
		// inside of AuthController / loginAction
		try{
	    	$result = $this->doLogin($values['username'],$values['password']);
		}
		catch(Exception $e)
		{
			//if(strpos($e,'Invalid username and password')>=0){
				$this->view->error_message = $e->getMessage();
				return $this->_forward('index');	 	
			//}	
		}
		switch ($result/*->getCode()*/) {    
			//case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:        
			/** do stuff for nonexistent identity **/        
			//	break;    
			//case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:        
			/** do stuff for invalid credential **/        
			//	break;    
			case Zend_Auth_Result::SUCCESS:
			 /** do stuff for successful authentication **/ 
				$this->_authenticated_user_name = $values['username'];    
				break;    
				default:        /** do stuff for other failure **/      
					return $this->_forward('index');  
					break;
		}
		
		 $this->view->username = $this->_authenticated_user_name ;

		
		}
	
	public function fileuploadAction(){
		//echo "Looking for";
		$this->view->form = $this->createUploadForm();
		assertEx(isset($this->view->form),"No form in action");		
		$this->render('fileupload'); 
	}	

	public function fileuploadedAction(){
		if (!$form->isValid) {    
			print "Uh oh... validation error";
		}
		if (!$form->foo->receive()) {    
			print "Error receiving the file";}
		$location = $form->foo->getFileName();
		
		// check referer - later
		// confirm username
		// Validate that the user directorty exist, else create it
	}
	
	public function filelistAction(){
		$dest = $this->getDestinationFolder();
		$this->view->db = $this->getDbAdapter();
		// move the file to the location		
		//$di = new DirectoryIterator($dest.PATH_SEPARATOR.'*.xml');
		//$this->_user_file_directory_iterator = new DirectoryIterator($dest.'*.xml');		
		$this->view->user_file_directory_iterator = new DirectoryIterator($dest);		
		//$this->view->form = "hello";
		$this->view->user_name = $this->_authenticated_user_name; 
		assertEx(isset($this->view->user_file_directory_iterator),"No Directory indicator in filelistAction()");
		$this->render('filelist-json');
		
	}

	public function getSmallestFileObject(){
		$dest = $this->getDestinationFolder();
		// move the file to the location		
		//$di = new DirectoryIterator($dest.PATH_SEPARATOR.'*.xml');
		//$this->_user_file_directory_iterator = new DirectoryIterator($dest.'*.xml');		
		$di = new DirectoryIterator($dest);		
		//$this->view->form = "hello";
		$smallest_file = null;
		$smallest_file_size = 10000000000000000000;
		$name= "nothing";
		
		foreach ($di as $file){
	
			$fn = $file->getFilename();
			if($file->isDir()){
				continue; // to ignore . and ..			
			}
			if(substr_count($fn,'parsed')>0){
				continue; // ignore parsed files;
			}
			$bn = basename($fn);
//			$unix_timestamp = $file->getCTime();
			$raw_size = $file->getSize();
			if($raw_size < $smallest_file_size){
				//echo "\n<br> Small file = ".$raw_size. " " . $bn;
				//$smallest_file = $file;
				$smallest_file_size = $raw_size;
				$name = $fn;
			}
			//else 
				//echo "\n<br> Big file = ".$raw_size. " " . $bn;
			//$fs = round($file->getSize()/1024,1);
			//$date = date($date_format, $unix_timestamp);
		}
		//echo $name;		
		return $name;
		
	}
	
	public function analysefileAction(){
		$xpath_spp_count = "//spare_part_price[@name]";
	}
	
	
	public function procesfileAction(){
		bdp::set_logger(new table_logger());
		$path = $this->getDestinationFolder();
		$request = $this->getRequest();
		$form = new Zend_Form();
		// disable layouts for this action:
		if(Bootstrap::ZFDebug_Enabled()){
			//echo "<hr>ZFDebug enabled";	
			
		}	
		else{ 
			//echo "<hr>ZFDebug disabled - therefore we are allowed to disable the layout</br>";
        	$this->_helper->layout->disableLayout();
		}
		//$this->view()->layout->disable();
		$this->view->message="initial";
		Zend_Loader_Autoloader::autoload('Bildelspriser_XmlImport_PriceParser');
		$obj = BildelspriserAuthAdapter::getInstance('BildelspriserAuthAdapter');		
		assertEx(isset($obj),"BildelspriserAuth was not set");
		$spp = BildelspriserAuthAdapter::getInstance('BildelspriserAuthAdapter')->getSparePartSupplier();
		assertEx($spp instanceof Default_Model_SparePartSuppliers,"SPP Must be a Default_Model_SparePartSuppliers".gettype($spp));
		
		$values = $request->getParams();
		$price_parser_run_id=null;
		$this->view->reset_status = $values['reset_status'];
		if(array_key_exists('price_parser_run_id',$values)){
			$price_parser_run_id=$values['price_parser_run_id'];
			$pp = new Bildelspriser_XmlImport_PriceParser($spp,$price_parser_run_id);
			$this->view->pp = $pp;
			$this->view->price_parser_run_id = $price_parser_run_id;
			$this->view->path = $path;
			$this->view->full = $values['full'];
			//$this->view->filename = $filename;				
			//$this->render('procesfile');				
		}
		elseif(array_key_exists('filename',$values))
		{			
			assertEx(is_array($values),"Values was not an array");
			if(array_key_exists('filename',$values)){
				$filename = utf8_decode($path.$values['filename']);
				assertEx(file_exists($filename),utf8_encode("File does not exist? - $filename"));
				$pp = new Bildelspriser_XmlImport_PriceParser($spp,null,$filename);
				assertEx($pp,"No price parser");
				$result = "";
				$this->view->pp = $pp;
				$this->view->filename = $filename;				
				$this->render('procesfile');				
			}
			else
			{
				echo " filename does not exist ";
			}		
		}
		else
			//$this->dispatch('procesfile');	
			$this->render('procesfile');		
	}
	
	public function smallestfileAction()
	{
		$file = $this->getSmallestFileObject();
		die($file);
	}
	
public function processmallestfileAction(){
		die("Dead code?");
		/*
		$path = $this->getDestinationFolder();
		$request = $this->getRequest();
		$form = new Zend_Form();
		$this->view->message="initial";
		if($request->isPost()){
			$values = $request->getParams();
			assertEx(is_array($values),"Values was not an array");
			if(array_key_exists('filename',$values)){
				$filename = $path.$values['filename'];
				assertEx(file_exists($filename),"File does not exist? - $filename");
				//$xml_source = file_get_contents($filename);
				//$this->view->file_size = strlen($xml_source);
				Zend_Loader_Autoloader::autoload('Bildelspriser_XmlImport_PriceParser');
				$sps = BildelspriserAuthAdapter::getInstance()->getSparePartSupplier();				
				assertEx($sps instanceof Default_Model_SparePartSuppliers,"SPP Must be a Default_Model_SparePartSuppliers".gettype($sps));
				$pp = new Bildelspriser_XmlImport_PriceParser($sps);
				assertEx($pp,"No price parser");
				try{
					//$result = $pp->parseString($xml_source);
					$result = $pp->parseFileName($fileName);
				}
				catch(XmlParserException $xpe){
					$result.="ExPE"; 
					$this->view->xml_parser_exception = $xpe;					
				}
				catch(Exception $e){
					$this->view->result = $e->getMessage();
				}
				$log = $pp->get_log_as_html();
				//$this->view->file_size;
				$this->view->parseResult = $result;
				$this->view->message = "Parsed xml file.<br/>".$log;
				if($result){//SUCCESS
					// if no errors rename the file to 'Parsed_yymmdd_'.filename.xml
					$date = date('Y-m-d');
					$ext_start = strrpos($filename,'.');
					$raw_file_name = substr($filename,0,$ext_start+1);
					$file_ext = substr($filename,$ext_start);
					if($file_ext=='parsed'){
						$date_start = $ext_start-len($date);
						$raw_file_name = substr($filename,0,$date_start+1);
						$new_file_name = $raw_file_name.$date.'.parsed';						
					}
					else
					{
						$new_file_name = $raw_file_name.$date.'.parsed';
					}
					echo "New filename = ".$new_file_name;
					//rename($filename,$new_file_name);
					$this->render('procesfile');
					//$this->dispatch('filelist');
					return;
				}	
				$this->render('procesfile');				
			}
			else
			{
				echo " filename does not exist ";
			}		
		}
		else
			$this->dispatch('procesfile');	
			$this->render('procesfile');	*/	
	}
	
	public function logoutAction(){
		BildelspriserAuthAdapter::logout();
		$this->dispatch('indexAction');
	}
	/**
	 * 
	 * Enter description here ...
	 * @return Zend_Db_Adapter_Mysqli
	 */
	public static function getDbAdapter(){
		$dbAdapter = Zend_Registry::get ( "db" );
		if($dbAdapter instanceof Zend_Db_Adapter_Mysqli)
			return $dbAdapter;
		error('The registry did not contain an adapter - '.Zend_Debug::dump($dbAdapter,'Adapter',true));
		return null;
	} 
	
}                                             