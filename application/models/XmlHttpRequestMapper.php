<?php

// application/models/SparePartPricesMapper.php
//require_once 'SparePartSupplier.php';
//Zend_Loader::loadClass('Model_DbTable_DbtXmlHttpRequest');
include_once 'DbTable/XmlHttpRequest.php';
include_once 'XmlHttpRequest.php';
include_once 'IMapperBase.php';
//die('first load');	
class Default_Model_XmlHttpRequestMapper extends MapperBase
{
	public function __construct(){
		$this->setDbTable('Default_Model_DbTable_XmlHttpRequest');
		parent::__construct();		
	}
	static $last_id;
	static $last_object;
/*	protected $_dbTable;
	protected $_xml_http_request_id;// 	xml_http_request_id
	
	/*public function __construct(){
		$this->_insert_id = -1;
	}*/
	
/*    public function setDbTable($dbTable)
    {
    	assertEx(false,"Test"+$dbTable);
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }*/

    /**
     * 
     * @return Model_DbTable_XmlHttpRequest
     */
/*    public function getDbTable()
    {
        if (null === $this->_dbTable) {            
        	$this->setDbTable('Model_DbTable_XmlHttpRequest');
        }
        return $this->_dbTable;
    }
*/
    public function saveAssocArray($data){
    	//var_dump($data);
    	return;
    	if(empty($data['server_request_uri'])){
    		var_export($data);
    		throw new Exception ('The Array $data does not contain "server_request_uri"  ');
    	}
    	//$data = array();
    	$data['created'] = date('Y-m-d H:i:s');
//    	die('data[server_request_uri] - '.$data['server_request_uri']);    	
    	$this->getDbTable()->insert($data);
    	$db = $this->getDbTable();
    	//die('mysql_insert_id()='.mysql_insert_id());
    	//die(var_export($db,true));
    	//die($db->lastInsertId());
    	return $this->_xml_http_request_id = 0;//$this->getDbTable()->lastInsertId();
    }
    
    
    public function save(Default_Model_XmlHttpRequest $xmlHttpRequest)
    {
	   	global $_COOKIE;
	   	//global $_REQUEST;
	   	$cook = "";
	   	$trace = "";
	   	foreach ($_COOKIE as $key => $value) {
	   		$cook .= "\n '$key' = '$value' ";
	   	}
	   	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	   	if(!is_string($user_agent) || $user_agent == ""){
	   		$user_agent = "UserAgent was empty $user_agent = var_export " + var_export($_REQUEST,true);
	   	}
    	$data = array(
            'request_payload' => $xmlHttpRequest->getRequestPayload(),
    		'created_by'	  => $xmlHttpRequest->getCreated_by(),
    		'first_response'  => $xmlHttpRequest->getFirstResponse(),	
            'latest_response'   => $xmlHttpRequest->getLastResponse(),
    		'server_request_uri' =>$xmlHttpRequest->getServerRequestUri(),
	   		'q'=>$xmlHttpRequest->getQ(),
	   		'http_cookie'=> $cook,
	   		'sql_used' => $xmlHttpRequest->getSqlUsed(),
	   		'car_model_id' => $xmlHttpRequest->getCarModelId(),
    		'user_agent' => $user_agent,
    	    'user_agent_id' => $xmlHttpRequest->getUserAgentId(),
    		'trace' => $xmlHttpRequest->getTrace(),
                'data_rows_returned' => $xmlHttpRequest->getDataRowsReturned(),
    		'http_referer' => $xmlHttpRequest->getHttpReferrer(),

        );
         if (null === ($id = $xmlHttpRequest->getXmlHttpRequestId())) {
           $data['created'] = date('Y-m-d H:i:s');
           if($data['server_request_uri']== ""){
            	throw new Exception('No server_request_uri make name defined in class!');
            }
            $this->getDbTable()->insert($data);
            $xmlHttpRequest->setXmlHttpRequestId($this->getLastInsertId());
            self::$last_id = $xmlHttpRequest->getXmlHttpRequestId();
            self::$last_object  = $xmlHttpRequest;
            return self::$last_id;
        } else {
        	//unset($data['first_response']);
        	$this->getDbTable()->update($data, array('xml_http_request_id = ?' => $id));
        }
    }

    public function getLastInsertId(){
    	$dba = $this->getDbTable()->getAdapter();
    	return $dba->lastInsertId();    	
    }
    
    public function find($id)
    {
        $result = $this->getDbTable()->find($id);
        return $result;
        //$result = car_makes::GetWhere("car_make_id = $id;");
        //$result = car_makes::GetWhere()
        if (0 == count($result)) {
            return;
        }
        //$carmake->setAllFromGenClass($result->First());
        /*$row = $result->current();
        $carmake->setId($row->id)
                  ->setEmail($row->email)
                  ->setComment($row->comment)
                  ->setCreated($row->created);*/
         return $data;        
    }

    public function fetchAll($select)
    {
    	try{
        	$resultSet = $this->getDbTable()->fetchAll($select);
    	}
    	catch(Exception $e){
    		echo "Exception occured '$select' ";
    		var_dump($e,$select); 
    	}
    	$entries   = array();
    	$c= 0;
        foreach ($resultSet as $row) {
            $entry = new Default_Model_XmlHttpRequest();
            /*$entry->setId($row->id)
                  ->setEmail($row->email)
                  ->setComment($row->comment)
                  ->setCreated($row->created)
                  ->setMapper($this);*/
            //$entry->setAllFromGenClass($row);      
            $entries[] = $entry;
//            echo " c="+$c++;
        }
        return $entries;
    }
    
    public function fetch($select)
    {
    	$resultSet = "";//new Zend_db_
    	//$this->getDbTable()->setFetchMode(Zend_Db::FETCH_ASSOC);
    	try{
        	$resultSet = $this->getDbTable()->fetchAll($select);
        	//print_r($resultSet);
    	}
    	catch(Exception $e){
    		echo "Exception occured '$select' ";
    		var_dump($e,$select);
   		 	die("End of story");
    	}
    	/* @var unknown_type */
    	$row = $resultSet[0];
    	$entry = new Default_Model_XmlHttpRequest();
    	//var_dump($row->toArray());
    	$entry->setOptions($row->toArray());
    	 /*foreach ($resultSet as $row) {
            $entry = new Default_Model_CarMakes();
            /* $entry->setId($row->id)
                  ->setEmail($row->email)
                  ->setComment($row->comment)
                  ->setCreated($row->created)
                  ->setMapper($this); * /
            $entry->setAllFromGenClass($row);      
            $entries[] = $entry;
//            echo " c="+$c++;
        }*/
        return $row;
    }
	
    
}

