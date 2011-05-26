<?php
// for auto-complete to work
//require_once '../library/Bildelspriser/generated/gen_db_class_carmakes.php'; 
//require_once '/Bildelspriser/generated/gen_db_class_car_makes.php'; 
//require_once 'C:\wamp\www\z18\soap\library/Bildelspriser/generated/gen_db_class_car_makes.php';
require_once 'DbTable/CarMakes.php';
require_once 'CarMakesMapper.php';
require_once 'BaseWithTraceability.php';

// for inspiration to this class - see
// http://framework.zend.com/docs/quickstart/create-a-model-and-database-table

class Default_Model_CarMakes extends Default_Model_BaseWithTraceability
{ 	
	private $_car_make_id=null;
	private $_car_make_name=null;
	private $_car_make_main_id=null;
	private $_state=null;


    public function __construct(array $options = null)
    {
        parent::__construct($options,'Default_Model_CarMakesMapper');
    }

    public function getState(){
    	return $this->_state;
    }
    
    public function setState($value){
    	//TODO: add check for the right values - and throw exception.
    	//throw new Exception("setState - I was called with value ".$value,25);
    	$this->_state = $value;
    	return $this;
    }
        
    
    public function setState_enum($value){
    	
    	return $this;
    }
    
    public function getCar_make_id(){
    	return $this->_car_make_id;
    }
    
    
    public function setCar_make_id($value){
    	return $this->_car_make_id = (integer)$value;
    }    
    
    public function setCar_make_main_id($value){
    	return $this->_car_make_main_id = (integer)$value;
    }
    
    public function getCarMakeName(){
    	return $this->_car_make_name;
    }
    
    public function getCar_make_name(){
    	return $this->_car_make_name;
    }
 /*   public function getCreated_by(){
    	return $this->_created_by;
    }*/
        
/*    public function getCreated(){
    	return $this->_created; // Data
    }
    
    public function getUpdated_by(){
    	return $this->_updated_by;
    }
        
    public function getupdated(){
    	return $this->_updated; // Date
    }
    
  */	  
    
/*    public function getCar_make_name(){
    	return $this->_car_make_name;
    }*/
    
   /* public function setCreated_by($text){
    	$this->_created_by = (string)$text;
    	return $this;
    }

    public function setUpdated_by($text){
    	$this->_updated_by = (string)$text;
    	return $this;
    }*/    
    
    public function setCar_make_name($text){
    	//throw new exception('Who calls me?');
    	$this->_car_make_name = (string)$text;
    	return $this;
    }
    
    public function setAllFromGenClass($car_make){
    	/*private $_car_make_id=null;
	private $_car_make_name=null;
	private $_car_make_main_id=null;
	private $_created=null;
	private $_created_by=null;
	private $_state=null;
	private $_updated=null;
	private $_updated_by=null;*/
    	//$car_make = $car_make->_data;
    	//die(var_export($car_make));
		$this->_car_make_id = $car_make->car_make_id;
		$this->_car_make_name = $car_make->car_make_name;
		//die($this->_car_make_name);
		$this->_created = $car_make->created;
		$this->_created_by = $car_make->created_by;
		$this->_state = $car_make->state;
		$this->_updated = $car_make->updated;
		$this->_updated_by = $car_make->updated_by;
		//$this->_car_make_id = $car_make->_car_make_id;
		//var_export($car_make);
		
		
    }
    
    
    
/*    public function setComment($text);
    public function getComment();

    public function setEmail($email);
    public function getEmail();

    public function setCreated($ts);
    public function getCreated();

    public function setId($id);
    public function getId();*/

    /*public function getMapper()
    {
        if (null === $this->_mapper) {
            $this->setMapper(new Default_Model_CarMakesMapper());
        }
        return $this->_mapper;
    }*/
    
    public function getModelIdFromModelName($car_model_name){
    	$model_id = -1;
    	$car_make_id = $this->_car_make_id;
    	if(isset($this->_car_make_main_id) && is_integer($this->_car_make_main_id)){
    		$car_make_id = $this->_car_make_main_id;
    	}
    	return Default_Model_CarModelsMapper::getCarModelIdByCarModelNameAndCarMakeId($car_model_name,$car_make_id);
    }

    /**
     * 
     * @param unknown_type $car_make_id
     * @return Zend_Db_Table_Row
     */
    public static function getMakeByIdAsZendRow($car_make_id){
    	$mapper = Default_Model_CarMakesMapper::getInstance('Default_Model_CarMakesMapper');
    	$row = $mapper->fetchRow(array('car_make_id'=>$car_make_id));  
    	return $row;  	
    }
    /**
     * 
     * @param integer $car_make_id
     * @return Default_Model_CarMakes
     */
    public static function getMakeByIdAsObject($car_make_id){
    	$zend_row = self::getMakeByIdAsZendRow($car_make_id);
    	return new Default_Model_CarMakes($zend_row->toArray());    	
    }
    
    public function addAsSimpleXmlChildNode(SimpleXMLElement &$elem){
    	$child = $elem->addChild('car_makes');
    	$child->addChild('car_make_id',$this->getCar_make_id());
    	$child->addChild('car_make_name',$this->getCar_make_name());
    	$main_id = $this->getCar_make_main_id() or $this->getCar_make_id();
    	$child->addChild('car_make_main_id',$m_id);
    	$child->addChild('state',$this->getState());    	
    	$child->addChild('created',$this->getCreated());  
    	$child->addChild('updated',$this->getUpdated());
    	return $child;
    }
}

?>