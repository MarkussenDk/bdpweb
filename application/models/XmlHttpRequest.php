<?php
/**
 * Filename /application/models/XmlHttpRequest.php
 * for inspiration to this class - see
 * http://framework.zend.com/docs/quickstart/create-a-model-and-database-table
 *
 */

require_once 'DbTable/XmlHttpRequest.php';
require_once 'BaseWithTraceability.php';

class Default_Model_XmlHttpRequest extends Default_Model_BaseWithTraceability
{ 	
	private $_xml_http_request_id=null;
	/*private $_car_make_name=null;
	private $_car_make_main_id=null;*/
/*	private $_created=null;
	private $_created_by=null;*/
	private $_request_payload=null;
	private $_first_response=null; 
	private $_last_response=null; 
	private $_server_request_uri=null;
	private $_user_info = null;
	private $_client_ip = null;
	private $_user_agent = null;
	private $_user_os = null;
	private $_request_time_taken_ms = null;
	private $_data_rows_returned = null;
	private $_sql_used = null;
	private $_q = null;
	private $_car_model_id = null;
	private $_user_agent_instance_id = null; 
	private $_trace = null;
	public $_http_referer = null;
	public $_user_agent_id = null;
	public $_id_addr = null;
/*	private $_updated=null;
	private $_updated_by=null;
	private $_mapper;*/
    public static $new_cookie_guid = null;
    public static $cookie_guid = null;	
	public static $cookie_name = "bdp_cookie_uniqid";
	public static $user_agent_id = null;

    public function __construct(array $options = null)
    {
        parent::__construct(null,'Default_Model_XmlHttpRequestMapper');
    	if (is_array($options)) {
            $this->setOptions($options);
        }
        $this->setUserCookie();
        $this->_user_name_required = false;
    }

    public function addLineToTrace($str){
    	$this->_trace .= "\n".$str;
    	
    }
    
    public static function getCookieName(){
    	return self::$cookie_name;    	
    }
    
    
    public static function readOrCreateUserCookie(){
    	$cn = self::getCookieName();
    	 if(isset($_COOKIE[$cn]))
		 { 
			 self::$cookie_guid = $_COOKIE[$cn]; 
			 //echo "\n<!-- $cookie_guid = $cookie_guid --!>";
		 } 
		 else 
		 { 
		 	self::$new_cookie_guid = uniqid();
		 	setcookie($cn,self::$new_cookie_guid,time()+96000000,'/');
		 } 
    	 
    }
    
    public static function getClientIp(){
    	global $_SERVER;
    	$ip=null;
		if ( isset($_SERVER["REMOTE_ADDR"]) )    { 
		    $ip=$_SERVER["REMOTE_ADDR"] . ' '; 
		} else if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]) )    { 
		    $ip=$_SERVER["HTTP_X_FORWARDED_FOR"] . ' '; 
		} else if ( isset($_SERVER["HTTP_CLIENT_IP"]) )    { 
		    $ip=$_SERVER["HTTP_CLIENT_IP"] . ' '; 
		} 
		if($ip==null)
			error("Client ip $ip could not be found".Zend_Debug::dump($_SERVER));
    	return $ip;
    	
    }
    
    public function setUserCookie(){
    	/*$sql = "SELECT `user_agent_id`, `user_agent_info`, `cookie_guid`, `created`, `ip_addr_first_visit`, `is_bot`
    	, `end_user_id`, `internal_note`, `next_user_greting`
    	, `allow_remember_user_name`, `allow_automatic_login` FROM `user_agents` WHERE 1 LIMIT 0, 30 ";*/  	
    	$ua_info = $this->getUserAgent();
    	if(strpos($ua_info,'Googlebo')>-1){
    		$this->_user_agent_id = 2; // 2 is google bot in production;
    		return;
    	}
    	$dbt_ua = new Default_Model_DbTable_UserAgents();
    	$dbr_ua = null;    
    	if(isset(self::$user_agent_id)){
    		$this->_user_agent_id = self::$user_agent_id;
    		if(isset($this->_user_agent_id))
    			warning('setting the useragent and it was already '.$this->_user_agent_id);
    		warning("setting this->_user_agent_id to ".self::$user_agent_id);
    		return $this->_user_agent_id;
    	}
    	if(isset(self::$new_cookie_guid)){
    		if(!is_integer(self::$user_agent_id)){
	    		$data = array(
	    			'user_agent_info' => $ua_info,
	    			'cookie_guid'	  => self::$new_cookie_guid,
	    			'ip_addr_first_visit' => self::getClientIp()    			
	    		);    		
	    		$dbr_ua = $dbt_ua->createRow($data);
	    		//Zend_Debug::dump($this,'self',true);
	    		try{
	    			self::$user_agent_id = $dbr_ua->save();
	    		}
	    		catch(exception $e){
	    			error(" - ".self::$user_agent_id." - Error while saving the user_agent?<br>".$e);
	    		}
    		}
    		else {
    			//echo "<br> self::\$user_agent_id ".self::$user_agent_id;
    			
    		}
    		//echo "\n-- first time visitor".self::$new_cookie_guid." & user agent id = self::$user_agent_id -";
    		    		
    	}
    	if(isset(self::$cookie_guid)){
    		$dirty=false;
    		$dbr_ua = $dbt_ua->fetchRow($dbt_ua->select()->where('cookie_guid = ?', self::$cookie_guid));
    		$data = null;
    		if(isset($dbr_ua)){
    			$data = $dbr_ua->toArray(); 
    			//echo " - data found". var_export($data,true);
    			if(!array_key_exists('user_agent_id',$data)){ // data exists -
		    		$dirty=true;
		    	}
		    	else {
		    		self::$user_agent_id = $data['user_agent_id'];
		    	}
		    	$info = $data['user_agent_info'];
		    	if(empty($info)){
	    			$dirty=true;    			
	    		}   			
    		}
    		else{
    			echo "unable to find data";  
    			$dirty = true; 			
    		}	
    		if($dirty){
    			if(is_int(self::$user_agent_id)){// just update
    				echo "would like to update ". $this->getUserAgent();
    				$data['user_agent_info'] = $this->getUserAgent();
    				$dbr_ua->user_agent_info = $this->getUserAgent();
    				$dbr_ua->save();
    			}
    			else{// insert as new row
		    		$data = array(
		    			'user_agent_info' => $this->getUserAgent(),
		    			'cookie_guid'	  => self::$cookie_guid,
		    			'ip_addr_first_visit' => self::getClientIp()    			
		    		);    		
    				$dbr_ua = $dbt_ua->createRow($data);
    				try{
    					self::$user_agent_id = $dbr_ua->save();
    				}
    				catch(exception $e){
    					error("error while saving second time".$e);    					
    				}
    				echo "<br>data saved".self::$user_agent_id ;
    			}
    			    			
    		}
    		//echo "\n-- welcome back " .self::$cookie_guid.' $user_agent_id --';      		  		
    	}
    	$this->_user_agent_id = self::$user_agent_id;
    }
    
    public function getUserAgentId(){
    	if(!isset($this->_user_agent_id)){
    		$this->setUserCookie();
    	} 	
    	return $this->_user_agent_id;
    	
    }
    
    public function setTrace($str){
    	$this->_trace = $str;    	
    }

    public function setDataRowsReturned($count){
         $this->_data_rows_returned = $count;

    }

    public function getDataRowsReturned(){
        return $this->_data_rows_returned ;
    }

    public function getState(){
    	return $this->_state;
    }
    
    public function getTrace(){
    	return $this->_trace;
    	
    }
    
    public function setState($value){
    	//TODO: add check for the right values - and throw exception.
    	//throw new Exception("setState - I was called with value ".$value,25);
    	$this->_state = $value;
    	return $this;
    }        
    
    public function setXmlHttpRequestId($xml_http_request_id){
    	$this->_xml_http_request_id = $xml_http_request_id;
    }
    
    public function getXmlHttpRequestId(){
    	return $this->_xml_http_request_id;
    }    

    public function getRequestPayload(){
    	return $this->_request_payload;
    }
    
    public function getFirstResponse(){
    	return $this->_first_response;
    }
        
    public function getLastResponse(){
    	return $this->_last_response;
    }
    
    public function setFirstResponse($value){
    	$this->_first_response=$value;
    }
        
    public function setLastResponse($value){
    	$this->_last_response=$value;
    }

    public function setServerRequestUri($value){
    	$this->_server_request_uri=$value;
    }

    public function getServerRequestUri(){
    	if(!isset($this->_server_request_uri)){
    		global $_SERVER;
    		$this->_server_request_uri = $_SERVER['REQUEST_URI'];    		
    	}    	
    	return $this->_server_request_uri;
    }

    public function getHttpReferrer(){
    	if(!isset($this->_http_referer)){
    		$this->_http_referer = getenv('HTTP_REFERER');
    	}
    	return $this->_http_referer;
    }
    
    public function getStaticUserAgent(){
    	return getenv('HTTP_USER_AGENT');     
    	
    }
    
    public function getUserAgent(){
    	if(!isset($this->_user_agent)){
    		$this->_user_agent = getenv('HTTP_USER_AGENT');     	
    	}
    	return $this->_user_agent;
    }
    
    public function setWhatYouKnow(){    	
    	assert($this->getUserAgent() == ""," this->_user_agent was empty");
     	$this->setServerRequestUri($this->getServerRequestUri());
    	
    }
    
     public function setRequestPayload($text){
    	//throw new exception('Who calls me?');
    	$this->_request_payload = (string)$text;
    	return $this;
    }
    
    public function setQ($txt){
    	$this->_q = $txt;    	
    }    
    public function setSqlUsed($txt){
    	$this->_sql_used = $txt;    	
    }
    
    public function getSqlUsed(){
    	return $this->_sql_used;    	
    }
    
    public function setCarModelId($car_model_id){
    	$this->_car_model_id = $car_model_id;    	
    }
    
    public function getQ(){
    	return $this->_q;    	
    }
    
    public function getCarModelId(){
    	return $this->_car_model_id ;	
    }   
}

?>