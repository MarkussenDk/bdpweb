<?php
require_once 'IMapperBase.php';
// application/models/CarModelsMapper.php

class Default_Model_CarModelsMapper extends MapperBase
{
	public function __construct(){
		$this->setDbTable('Default_Model_DbTable_CarModels');
		parent::__construct();
	}

	/**
	 * 
	 * @param $car_model_name	The name of the Model e.g. 480
	 * @param $car_make_id		The unique refence number for a car make (car brand) e.g. 3 for Volvo
	 * @return Car_model_id		A valid car model id
	 */
	public function getCarModelIdByCarModelNameAndCarMakeId($car_model_name,$car_make_id){
		//assertEx(false,"Car_model_id is empty".var_export("Car_model_name  ".$car_model_name." & car_make_id = ".$car_make_id,true) );
		isset($car_make_id)
			or error('Car_make_id was missing while trying to get a new car model.');
		isset($car_model_name)
			or error('Car_model_name was missing while trying to get a new car model.');
		// first lookup the car makes
		$db = $this->getDbAdapter();
		$qoute_car_make_id = $db->quote($car_make_id);
		$qoute_car_model_name = $db->quote($car_model_name);
		//$car_obj = $this->fetchAll_Array(' `car_make_name`= $qoute_car_make_name ');
		//die($qoute_car_model_name);
		$fetch_where = " `car_model_name`= $qoute_car_model_name and `car_make_id`= $qoute_car_make_id ";
		//die($fetch_where);
		$car_model_row = $this->getDbTable()->fetchRow($fetch_where);
		if(isset($car_model_row)){			
			//if(isset($car_model_row['car_model_main_id'])){
			if(is_int($car_model_row['car_model_main_id'])){
				//die("Row found -3 ".$car_model_row['car_model_main_id'].var_export($car_model_row,true));
				return $car_model_row['car_model_main_id'];
			}
			//die("Car_model_id = ".$car_model_row['car_model_id']);
			return $car_model_row['car_model_id'];
		}
		// if it does not exist, create it.
		$new_cmo_obj = new Default_Model_CarModels(array('car_model_name'=>$car_model_name,'car_make_id'=>$car_make_id));
		$new_cmo_id = $new_cmo_obj->save();
		isset($new_cmo_id)
			or error('The new car_model was perhaps saved, but I dont know the ID?');
		return $new_cmo_id;		
	}
	
	
	/*protected $_dbTable;

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
        	$this->setDbTable('Model_DbTable_CarModels');
        }
        return $this->_dbTable;
    }*/

    public function save(Default_Model_CarModels  $carmake)
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
            'car_model_name'   => $carmake->getCar_model_name(),// + $carmake->getCreated_by(),
    		'car_model_id'	  => $carmake->getCar_model_id(),
    		'car_model_main_id' => $carmake->getCar_model_main_id(),
    		'car_make_id'	  => $carmake->getCar_make_id(),
    		'created_by'	  => $carmake->getCreated_by(),//.'a = '.trim(htmlentities($_SERVER["PHP_AUTH_USER"])),
    		'updated_by'	  => $carmake->getCurrent_user_name(),//.'a'.$_SERVER["PHP_AUTH_USER"],
    		'state_enum'			  => $carmake->state_enum,	
            //'created_by' => , - ADD USERNAME
            'updated' 		  => date('Y-m-d H:i:s')    	
        );
        assertEx(isset($data['car_make_id']),"Car Make Id missing while trying to save");
        //die("It there . ".$data['created_by']);
        //var_export($data) and die();
        if (null === ($id = $carmake->getCar_model_id())) {
            unset($data['car_model_id']);
            $data['created'] = date('Y-m-d H:i:s');            

            if($data['car_model_name']== ""){
            	//var_dump($data);
            	throw new Exception('No car model name defined in class!');
            }
            $data['created_by'] = $carmake->getCurrent_user_name();
            assertEx($data['updated_by'],"Username was empty");
            $data['state_enum']= state_enum::_public;
            //echo "<H3>Saving - ".$data['car_make_name']." <H3/>";
            //die(ArrayToXML::toXml($data,'Test'));
            $insertId = $this->getDbTable()->insert($data);
            assertEx($insertId > 0,"The InsertId was empty '$insertId' ?");
            //die("The InsertId was $insertId");
            return $insertId;
        } else {
        	//unset($data['created_by']);		// 
        	//unset($data['created']);        //	
        	//unset($data['car_make_name']);  // it is never possible to change a car_make_name - only its state      	
        	$data['updated']=date('Y-m-d H:i:s');
        	//$data['updated_by']=date('Y-m-d H:i:s');
        	//$carmake->UpdateToDb(xmlModelHandler::get_DB_SCHEMA());
        	//die('Finally an update - id = ' .$id);
			$data['updated_by']=$carmake->getCurrentUserName();
        	$this->getDbTable()->update($data, array('car_model_id = ?' => $data['car_model_id']));
        	return $data['car_model_id'];
        }
    }

   
    /*public function find($id, Default_Model_CarModels $carmake)
    {
        $result = $this->getDbTable()->find($id);
        //$result = car_makes::GetWhere("car_make_id = $id;");
        //$result = car_makes::GetWhere()
        if (0 == count($result)) {
            return;
        }
        //$carmake->setAllFromGenClass($result->First());
        $row = $result->current();
        $carmake->setId($row->car_model_id)
        //          ->setEmail($row->email)
                  ->setComment($row->comment)
                  ->setCreated($row->created);
                 
    }*/

    /*public function fetchAll($select)
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
            $entry = new Default_Model_CarModels();
            /*$entry->setId($row->id)
                  ->setEmail($row->email)
                  ->setComment($row->comment)
                  ->setCreated($row->created)
                  ->setMapper($this);* /
            $entry->setAllFromGenClass($row);      
            $entries[] = $entry;
//            echo " c="+$c++;
        }
        return $entries;
    }*/
	
    
}

