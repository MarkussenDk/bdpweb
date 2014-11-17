<?php
//require_once 'XmlHttpRequestMapper.php';
//Zend_Loader::loadClass('XmlHttpRequestMapper');
/*$dirs=array(
	//'c:\wamp\www\z18\soap\application',
	//'c:\\wamp\\www\\z18\\soap\\application\\models\\',
	//'../../application/models/',	
	//'z18/soap/application/models/',
	'/customers/bildelspriser.dk/bildelspriser.dk/httpd.www/z18/soap/application/models/',	
	'/customers/bildelspriser.dk/bildelspriser.dk/httpd.www/z18/soap/application/'	
	);
var_export($dirs);
Zend_Loader_Autoloader::loadClass('XmlHttpRequestMapper',$dirs);*/

require_once "Zend/Loader/Autoloader.php";
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallbackAutoloader(true);


//Zend_Loader_Autoloader::loadClass('Default_Model_XmlHttpRequestMapper');

/**
 * 
 * 
 * @author tester
 * `xml_http_request_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_by` varchar(50) NOT NULL,
  `request_payload` longtext NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `first_response` longtext NOT NULL,
 */
 
class XmlRequestPersist{
	private $_persister;
	private $_data;
	public function __construct($type='ALL'){
		$this->_persister[] = new XmlRequestMySqlPersist($this);		
		$this->_persister[] = new XmlRequestXmlFilePersist($this);	
	}
	private $xml_http_request_id;
	
	/**
	 * 
	 * @return IPersister
	 */
	public function getPersister(){
		return $this->_persister;		
	}
	
	/**
	 * 
	 * @param $userName
	 * @param $request_payload
	 */
	public function SaveAuthenticatedRequest($created_by,$request_payload){
		GLOBAL $_SERVER;
		$this->_data = array(
    		'created_by'	  => $created_by,
    		'server_request_uri' => $_SERVER["REQUEST_URI"],
    		'request_payload' => $request_payload		
        );
        if(empty($this->_persister)){
        	die('No persister');        	
        }
        foreach($this->getPersister() as $p){
        	//var_dump($data);
        	$p->Save($this->_data);
        }
	}
	
	//public function Save($)
	
	public function SaveResponse($response){
		$this->_data['response']= $response;
	    foreach($this->getPersister() as $p){
        	$p->Save($this->_data);
        }
		
	}
}

interface IPersister{
	function Save($AssocArray);
	function SaveResponse($response);
	function Load($userName,$timeStamp);	
}

class XmlRequestXmlFilePersist implements IPersister{
	private $object;
	public function __construct($XmlRequestPersistObject){
		$this->object = $XmlRequestPersistObject;		
	}

	public function SaveResponse($response){
		return;
	}
	
	public function Save($AssocArray){
		$main_elem_name = 'Xmlhttp_Request';
		$dir_name = realpath(APPLICATION_PATH . '/../Logs/');
		$file_name=$dir_name.DIRECTORY_SEPARATOR.$main_elem_name.'_'.$AssocArray['created_by'].'_'.date('Y-m-d His').'.xml';  
		$xw = new XMLWriter();
		$xw->openMemory();
		$xw->startDocument();
		$xw->startElement($main_elem_name);
		foreach($AssocArray as $key=>$val){
			$xw->writeElement($key,$val);
		}
		$xw->endElement();
		$fp = fopen($file_name,'w');
		fwrite($fp,$xw->outputMemory());
		fclose($fp);		
	}
	
	public function Load($userName,$timeStamp){
		throw new Exception('E_NOT_IMPL');
		
	}
	
}

class XmlRequestMySqlPersist implements IPersister{
	private $object;
	protected $_xml_http_request_id;
	private $_mapper;
	public function __construct($XmlRequestPersistObject){
		$this->object = $XmlRequestPersistObject;
//		$table_name='xml_request_'		
	}

	/**
	 * @returns Default_Model_XmlHttpRequestMapper
	 */
	private function getMapper(){
		if(empty($this->_mapper)){
			$this->_mapper = new Default_Model_XmlHttpRequestMapper();
		}
		return $this->_mapper;
	}
	
	/**
	 * 
	 * 
	 */
	public function Save($AssocArray){
		return $this->_xml_http_request_id = $this->getMapper()->saveAssocArray($AssocArray);		
	}	
	
	public function SaveResponse($response){
		if(empty($this->_xml_http_request_id)){
			throw new Exception("A SaveResponse was called before a Save(AssocArray)");			
		}
		$array = array('latest_response'=>$response);
		$this->getMapper()->getDbTable()->update($array,'xml_http_request_id = '.$this->_xml_http_request_id);
		//$db->update('bugs', $data, 'bug_id = 2');
	}
	
	public function Load($userName,$timeStamp){
		$t = new Default_Model_XmlHttpRequestMapper();
		throw new Exception('E_NOT_IMPL');
		
	}
	
}

?>