<?php
class BildelspriserAuthAdapter implements Zend_Auth_Adapter_Interface {
	private $_sps_mapper;
	private $_sps_object;
	private static $_username;
	private static $_password;  
	private $_isAuthenticated;
	private $_no_session;
	private static $_instance;
/**     
* Sets username and password for authentication     
*     
* @return void     
* */    
public function __construct($username, $password)    
{        
 	self::$_password = $password;
    self::$_username = $username;
    self::$_instance = $this;
    $this->_sps_mapper = Default_Model_SparePartSuppliersMapper::getInstance('Default_Model_SparePartSuppliersMapper');
    $this->_sps_object = new Default_Model_SparePartSuppliers();
    $this->_isAuthenticated = false;
    $this->_no_session = false; // by default use sessions
	
}    
	/**     
	 * * Performs an authentication attempt     *     
	 * * @throws Zend_Auth_Adapter_Exception If authentication cannot     
	 * *                                     be performed     
	 * * @return Zend_Auth_Result     */    
public function noSession($bool){
	$this->_no_session = $bool;
}

public function authenticate()    
{        // ...    
	//try
	{
		/*if($this->_isAuthenticated){
				$this->saveInSession();
			$this->setInstance();
			die("in_auth1 . ".var_export($this,true));
			return Zend_Auth_Result::SUCCESS;
		}
		else 
			echo "<br>in Authenticate - \$this->_isAuthenticated was not set ";	*/
		//die(var_export($this,true));
		//die('in function authenticate() iden was '.$iden);
		assertEx(self::$_username,"UserName blank in authenticate()");
		$res = $this->_sps_mapper->authenticate(self::$_username,self::$_password,$this->_sps_object);
		$iden = $this->_sps_mapper->getIdentity(true);
		if($iden == null){
			die("In authenticate() iden was null");	
		}
		else
			$this->_sps_object = $this->_sps_mapper->getIdentity();
		assertEx($this->_sps_object != "","Empty sps_object");
		//var_dump($this);
		if($res){
			//echo "<br>Res ok ";
			$this->_isAuthenticated = true;
			//if(!isset($this->_no_session))
			$this->saveInSession();
			$this->setInstance();
			return Zend_Auth_Result::SUCCESS;
		}
		else
			die("In authenticate() - was not somthing");
	}
	return Zend_Auth_Result::FAILURE;
	//catch ()
}
/**
 * @return Default_Model_SparePartSuppliers
 */
public function getSparePartSupplier(){
	assertEx(isset($this->_sps_object),"NO SPS object in auth class");
	if($this->_sps_object == null){
		$this->authenticate();
	} 
	assertEx($this->_sps_object != null,"SPS object was NULL in auth class");
	return $this->_sps_object;
}

private function saveInSession(){
	@session_start();
	global $_SESSION;
	assertEx(self::$_username,"Username was blank in saveInSession");
	$_SESSION['username'] = self::$_username;
	$_SESSION['password'] = self::$_password;
	$_SESSION['auth_object'] = $this;
	//die( "<br>saving in session");
}

static function checkInSession(&$username,&$password,&$auth_object){
	@session_start();
	global $_SESSION;
	//echo "<br>Checking session vars - ";
	if(array_key_exists('username',$_SESSION) && array_key_exists('password',$_SESSION)){
		$username = $_SESSION['username'];
		$password = $_SESSION['password'];
		$auth_object = $_SESSION['auth_object'];
		//echo " - username found $username "; 
		return true;
	}
	else{
		echo "<br>session  ".var_export($_SESSION,true);
	}
	
	return false;
}

static function checkInRequest(&$username,&$password,&$auth_object){
	//@session_start();
	//global $_SESSION;
	//echo "<br>Checking session vars - ";
	$front = Zend_Controller_Front::getInstance();
	$params = $front->getRequest()->getParams();
	if(array_key_exists('username',$params) && array_key_exists('password',$params)){
		$username = $params['username'];
		$password = $params['password'];
		$auth_object = new BildelspriserAuthAdapter($username,$password);
		//echo " - username found $username "; 
		return true;
	}
	else{
		//echo "<br>Params  ".var_export($params,true);
	}
	
	return false;
}

static function logout(){
	global $_SESSION;
	unset($_SESSION['username']);
	unset($_SESSION['password']);
	unset($_SESSION['auth_object']);
}

function hasIdentity(){
	return isset($this->_isAuthenticated);
}

function getIdentity(){
	return self::$_username;
}

function setInstance(){
	//echo "<br> setting in instance ";
	/*if($this === self::$_instance){
		echo " was set";
	}*/
	if(isset(self::$_instance))		
		//throw new exception("Double inst of ".__CLASS__." type ".get_class(self::$_instance));
	self::$_instance  = $this;
}

/**
 * @return BildelspriserAuthAdapter
 */
	static function getInstance(){
//		echo "<br>InInstance  ".print_r(self::$_instance).' exp '.var_export(self::$_instance,true); 
		if(self::$_instance == null){
			$u = "" ;
			$p = "" ;
			$o = null;
			if(self::checkInSession($u,$p,$o)){
				//echo "<br>after checkinsession";
				assertEx(get_class($o)=="BildelspriserAuthAdapter",'O Was '.get_class($o). 'It should be BildelspriserAuthAdapter ');
				self::$_username = $u;
				self::$_password = $p;
				self::$_instance = $o;
				$o->authenticate($u,$p,$o->_sps_object);
				/*assertEx(get_class($sps)=="",
					'SPS Was '.get_class($sps));*/
				$o->setInstance();
				return $o;
				
			}
			else{
				//echo "<br>user was not in session";
			}
			if(self::checkInRequest($u,$p,$o)){
				//echo "<br>after checkinsession";
				assertEx(get_class($o)=="BildelspriserAuthAdapter",'O Was '.get_class($o). 'It should be BildelspriserAuthAdapter ');
				self::$_username = $u;
				self::$_password = $p;
				self::$_instance = $o;
				$o->authenticate($u,$p,$o->_sps_object);
				/*assertEx(get_class($sps)=="",
					'SPS Was '.get_class($sps));*/
				$o->setInstance();
				return $o;
				
			}
			else{
				echo "<br>user was not in request";
			}
		}
		
		//assertEx(self::$_instance,"No instance of Auth class found");
		return self::$_instance;	
	}

}