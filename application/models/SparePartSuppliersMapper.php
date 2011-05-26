<?php
require_once 'IMapperBase.php';
// application/models/SparePartSuppliersMapper.php
//require_once 'SparePartSupplier.php';
//require_once 'application/models/DbTable/DbtSparePartSuppliers.php';


class Default_Model_SparePartSuppliersMapper extends MapperBase
{
	public  $_active_user;
	public function __construct(){
		$this->setDbTable('Default_Model_DbTable_SparePartSuppliers');
		parent::__construct();
	}
/*	protected $_dbTable;

    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }

    public function getDbTable()
    {
        if (null === $this->_dbTable) {            
        	$this->setDbTable('Model_DbTable_DbtSparePartSuppliers');
        }
        return $this->_dbTable;
    }*/

    public function save(Default_Model_SparePartSuppliers $carmake) 
    {
    	global $_SERVER;
         /*	From AutoGeneratedClass
		  * private $_car_make_id=null;
			private $_car_make_name=null;
			private $_car_make_main_id=null;
			private $_created=null;
			private $_created_by=null;
			private $_state=null;
			private $_updated=null;
			private $_updated_by=null;*/
    	$data = array(
            'car_make_name'   => $carmake->getCar_make_name(),// + $carmake->getCreated_by(),
    		'created_by'	  => $carmake->getCreated_by(),//.'a = '.$_SERVER["PHP_AUTH_USER"]. 'as_html_en='.trim(htmlentities($_SERVER["PHP_AUTH_USER"])),
    		'updated_by'	  => $carmake->getCreated_by(),//.'a'.$_SERVER["PHP_AUTH_USER"],
    		'state'			  => $carmake->getState(),	
            //'created_by' => , - ADD USERNAME
            'created' 		  => date('Y-m-d H:i:s'),
            'updated' 		  => date('Y-m-d H:i:s')    	
        );
        //die("It there . ".$data['created_by']);
        //var_export($data);
        if (null === ($id = $carmake->getCar_make_id())) {
            unset($data['car_make_id']);
            //$car_make_id = $carmake->InsertIntoDb(xmlModelHandler::get_DB_SCHEMA());
            if($data['car_make_name']== ""){
            	//var_dump($data);
            	throw new Exception('No car make name defined in class!');
            }
            $data['state']= "Foreslag";
            $data['state_enum']= "Foreslag";
            //echo "<H3>Saving - ".$data['car_make_name']." <H3/>";
            //die(ArrayToXML::toXml($data,'Test'));
            $this->getDbTable()->insert($data);
        } else {
        	//unset($data['created_by']);		// 
        	//unset($data['created']);        //	
        	//unset($data['car_make_name']);  // it is never possible to change a car_make_name - only its state      	
        	$data['updated']=date('Y-m-d H:i:s');
        	//$data['updated_by']=date('Y-m-d H:i:s');
        	//$carmake->UpdateToDb(xmlModelHandler::get_DB_SCHEMA());
        	die('Finally an update - id = ' .$id);
        	$this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }

    static function setIdentity(Default_Model_SparePartSuppliers  $sps){
    	//die("In set identity ".var_Export($sps,true));
    	$obj = Default_Model_SparePartSuppliersMapper::getInstance('Default_Model_SparePartSuppliersMapper');
    	assertEx($obj,"Object was not defined");
    	$obj->_active_user = $sps;
    	assertEx($obj->_active_user,'Active user was not defined in setIdentity');
    }
    /**
     * 
     * @return Default_Model_SparePartSuppliers
     */
    static function getIdentity(){
    	$obj = Default_Model_SparePartSuppliersMapper::getInstance('Default_Model_SparePartSuppliersMapper');
    	assertEx($obj,"Object was not defined");    	
    	assertEx($obj->_active_user,'Active user was not defined in getIdentity.'.get_class($obj).'<br/>Active user should be set with setIdentity()');
    	return $obj->_active_user;
    }
    
    /**
     * Returns the username of an authenticated user.
     * @return String
     */
    static function getIdentityAsString($authenticated_user_required = true){
    		//Zend_Loader_Autoloader::autoload('BildelspriserAuthAdapter');
    	//$sps = BildelspriserAuthAdapter::getInstance('BildelspriserAuthAdapter')->getSparePartSupplier();
    	$user_name = Default_Model_SparePartSuppliersMapper::getIdentity($authenticated_user_required)->getSupplier_admin_user_name();
    	assertEx(!$authenticated_user_required or $user_name,"UserName not defined?");
    	return $user_name;
    }
    /**
     * 
     * @param unknown_type $user_name
     * @param unknown_type $password
     * @param Default_Model_SparePartSuppliers $sps
     * @return true for success or false for faliure
     * @throws Exception
     */
    public function authenticate($user_name,$password, Default_Model_SparePartSuppliers &$sps){
        //echo ("\n<!-- In  authenticate($user_name,hidden-password, sps  -->  ");
        $safe_user_name = mysql_escape_string($user_name);
        //mysql_real_escape_string($user_name);
        //$safe_password = mysql_real_escape_string($user_name);
        $safe_password = mysql_escape_string($password);
        assertEx( empty($safe_user_name) == false, "In sps_mapper->authenticate - user_name was empty ??");
        assertEx( empty($safe_password) == false, "In sps_mapper->authenticate - safe_password was empty ??");
        $stmt = $this->getDbTable()->select()
								  ->where('supplier_admin_user_name = ?', $safe_user_name)
								  ->where('supplier_admin_password = ? ',$safe_password);
		//echo "stmp = ";
								  $result = @$this->fetchRow($stmt);
		//echo "Fetch";								 
		/*if(count($result)==0){
			return false;
		}*/								   
        if ( 1 <> count($result)) {
            //echo "<!-- left due to wrong error count -->";
            Throw new Exception("Invalid username and password combination. ".count($result));
        	return false;
        }
        $sps->setOptions($result->toArray());
        //var_dump($sps);
        $sps_user_name = $sps->getSupplier_admin_user_name();
        assert(""<>$sps_user_name && $sps_user_name == $safe_user_name);
        //die();
        //$carmake->setAllFromGenClass($result->First());
        //$row = $result->current();
        /*$sps->setId($row->id)
                  ->setEmail($row->email)
                  ->setComment($row->comment)
                  ->setCreated($row->created);*/
        //echo "<H1>sps_user_name =  '" . $sps_user_name . "'</H1>";//$sps = $result;
        //echo "n\<!-- sps_user_name =  '" . $sps_user_name . "' -->";
        self::setIdentity($sps);
        $sps2 = self::getIdentity();
        //assertEx(isset($this->_active_user),"The Active user was not defined");
        assertEx(isset($sps2),"The sps2 Active user was not defined");
        //echo "<!-- active user =  '" . $this->_active_user->getSupplier_admin_user_name() . "' -->";
        return true;    	
    }    

    public function getActiveUser(){
        return $this->_active_user;

    }
  
    /*public function find($id, Default_Model_CarMakes $carmake=null)
    {
        //$result = $this->getDbTable()->find($id);
        $result = car_makes::GetWhere("car_make_id = $id;");
        //$result = car_makes::GetWhere()
        if (0 == count($result)) {
            return;
        }
        $carmake->setAllFromGenClass($result->First());
        /*$row = $result->current();
        $carmake->setId($row->id)
                  ->setEmail($row->email)
                  ->setComment($row->comment)
                  ->setCreated($row->created);
                 
    }*/

    public function fetchAll($select)
    {
    	try{
        	$resultSet = $this->getDbTable()->fetchAll($select);
    	}
    	catch(Exception $e){
    		echo "Exception occured '$select' ";
    		var_dump($e,$select); 
    	}
    	$entries  = array();
    	$c= 0;
        foreach ($resultSet as $row) {
            $entry = new Default_Model_SparePartSuppliers($row);
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
    
    public function fetchRow($select)
    {
    	$resultSet = "";//new Zend_db_
    	//$this->getDbTable()->setFetchMode(Zend_Db::FETCH_ASSOC);
    	$resultSet = $this->getDbTable()->fetchAll($select);
    	/*try{
        	$resultSet = $this->getDbTable()->fetchAll($select);
        	//print_r($resultSet);
    	}
    	catch(Exception $e){
     		echo "Exception occured '$select' ";
    		var_dump($e,$select);
   		 	die("End of story");
    	}*/
    	/* @var unknown_type */
    	$row = $resultSet[0];
    	//$entry = new Default_Model_SparePartSuppliers();
    	//var_dump($row->toArray());
    	//$entry->setOptions($row->toArray());
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
	

    /**
     * 
     * @param $select
     * @return Default_Model_SparePartSuppliers
     */
    public function fetchObject($select)
    {
    	$row = $this->fetchRow($select);
    	$entry = new Default_Model_SparePartSuppliers($row->toArray());
    	//var_dump($row->toArray());
    	//$entry->setOptions($row->toArray());
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
        return $entry;
    }
	
    
}

