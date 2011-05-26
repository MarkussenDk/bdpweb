<?php
require_once realpath(APPLICATION_PATH . '/../library/').'/Soaptest.php';
require_once realpath(APPLICATION_PATH . '/../library/').'/MinService.php';

class SoapController extends Zend_Controller_Action
{
	//$host_name = $_SERVER['HTTP_HOST'];
	private $_WSDL_URI;// = "http://localhost/soap?wsdl";
	private $_CLASS_NAME;
	public function getWSDL_URI(){
		return $_WSDL_URI;
	}
	
 	public function init()
    {	 
    	GLOBAL $_SERVER;
		$host_name = $_SERVER['HTTP_HOST'];
		$this->_WSDL_URI = "http://$host_name/soap?wsdl";
		$this->_CLASS_NAME = 'SoapTest';
		$this->_CLASS_NAME = 'MinService';
		//$this->_CLASS_NAME = 'MinStruct';
		
    }

    public function indexAction()
    {    
    	$this->_helper->viewRenderer->setNoRender();
    		
    	if(isset($_GET['wsdl']) or isset($_GET['WSDL'] )) {
    		//return the WSDL
    		$this->handleWSDL();
		} else {
			//handle SOAP request
    		$this->handleSOAP();
		}
    }

	private function handleWSDL() {
		$autodiscover = new Zend_Soap_AutoDiscover();
    	//$autodiscover->setComplexTypeStrategy($strategy);
    	//setComplexTypeStrategy 
    	// Start here ... 
    	//		 http://framework.zend.com/manual/en/zend.soap.wsdl.html#zend.soap.wsdl.types.add_complex
    	//     	 http://phpdocs.moodle.org/HEAD/Zend_Soap/Wsdl/Zend_Soap_Wsdl_Strategy_Interface.html
    	//
    	//$autodiscover->setComplexTypeStrategy('Zend_Soap_Wsdl_Strategy_ArrayOfTypeSequence');
		//$autodiscover->setClass('MinService');    	
		//$autodiscover->addComplexType('MinStruct');
    	//$autodiscover->setClass('MinStruct');    	
    	$autodiscover->setClass($this->_CLASS_NAME);   
    	//$autodiscover->setClass('MinStructFactory'); 	
		$autodiscover->handle();
	}
    
	private function handleSOAP() {
		$soap = new Zend_Soap_Server($this->_WSDL_URI); 
		$request = $this->getRequest();
		//Zend_Debug::Dump($request,'Req');
		
		$path = 'c:\log.txt';
		$stream = @fopen($path, 'a', false);
		if (! $stream) {
		throw new Exception('Failed to open stream');
		}
		$writer = new Zend_Log_Writer_Stream($stream);
		$logger = new Zend_Log($writer);
		$logger->info('HandleSoap called');
		$logger->info(print_r($request,true));
		//$request->dump('c:\soap_req.xml');
		//$request->
    	//$soap->setClass('MinStruct');
		$soap->setClass($this->_CLASS_NAME);
    	//$soap->setClass('MinService');
    	//$soap->dump('c:\soap_resp.xml');
    	$soap->handle();
	}
    
    public function clientAction() {
    	//die('WSDL URI IS'.$this->_WSDL_URI);
    	$client = new Zend_Soap_Client($this->_WSDL_URI);
    	//$client = null;
    	try{
    		$this->view->add_result = $client->math_add(11, 55);
    	}
    	catch(SoapFault $sf){
    		$msg = "<H2>Soap Fault - ";
    		switch($sf->faultcode){
    			case 'WSDL': 
    				break;
    				$msg .= "WSDL -".$sf->faultstring;
    			default:
    				
    				break;
    		}
    		throw new Zend_Controller_Action_Exception('Soap Fault occured while calling the function <pre>'.var_export($sf,true).'</pre>', 500);
    	}
    	catch(exception $e){
			throw new Zend_Controller_Action_Exception('Exception occured while calling the function <pre>'.var_export($e,true).'</pre>', 500);
    	}
    	//$this->view->logical_not_result = $client->logical_not(true);
    	
    	//$this->view->sort_result = $client->simple_sort( array("d" => "lemon", "a" => "orange", "b" => "banana", "c" => "apple"));
		//$this->view->text_result = $client->add_car_brand('Volvo');	
		
    	
    }

}

