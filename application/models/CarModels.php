<?php
// for auto-complete to work
//require_once '../library/Bildelspriser/generated/gen_db_class_carmakes.php'; 
//require_once '/Bildelspriser/generated/gen_db_class_car_makes.php'; 
require_once 'DbTable/CarModels.php';
require_once 'CarModelsMapper.php';
require_once 'BaseWithTraceability.php';

// for inspiration to this class - see
// http://framework.zend.com/docs/quickstart/create-a-model-and-database-table

class Default_Model_CarModels extends Default_Model_BaseWithTraceability
{ 	
	public $_car_model_id = null;
	public $_car_model_name = null;
	public $_car_make_id = null;	
	public $_car_model_main_id = null;
	public $_model_cleansed_name = null;	
	private $_state;
	public $state_enum;
	private $_car_make_obj = null;

    public function __construct(array $options = null)
    {
        parent::__construct($options,'Default_Model_CarModelsMapper');
    }
	
    /*public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }
	
    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
            else{
            	throw new Exception("Method '$method' not implemented - Value used was '$value' ");
            	
            }
        }
        return $this;
    } */  
    
	
    //public function __set($name, $value);
    //public function __get($name);

   /* public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid car_models property"'.$name.'"');
        }
        $this->$method($value);
    }*/

    /*public function __get($name)
    {//$name holds the name of the undefined attributes getting called.
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid car_models property - "'.$name.'"');//.var_export($this,true));
        }
        return $this->$method();
    }*/

    public function getState(){
    	return $this->_state;
    }
    
    public function setState($value){
    	//TODO: add check for the right values - and throw exception.
    	//throw new Exception("setState - I was called with value ".$value,25);
    	$this->_state = $value;
    	return $this;
    }
        
    
    public function getCar_model_id(){
    	return $this->_car_model_id;
    }

    public function getCar_make_id(){
    	return (integer)$this->_car_make_id;
    }
    
    public function getCar_model_main_id(){
    	return $this->_car_model_main_id;
    }
    
    public function getCar_model_name(){
    	return $this->_car_model_name;
    }
    
    public function getMakeAndModelName(){
    	return $this->getCar_make_name().' '.$this->_car_model_name; 
    }
    
    public function getCar_make_name(){
    	if($this->_car_make_obj==null){
    		$this->_car_make_obj= new Default_Model_CarMakes();    		
    	}    	 
    	$this_make = '';
    	$this_make = $this->_car_make_obj->getCachedById($this->_car_make_id);
    	return $this_make->car_make_name;
    	return;
    	static  $make;
    	if($make==""){
    		$make = Default_Model_CarMakes::getMakeByIdAsObject($this->_car_make_id)->getCar_make_name();     		
    	}
    	return $make;   	
    }
    /*public function getCreated_by(){
    	return $this->_created_by;
    }
        
    public function getCreated(){
    	return $this->_created; // Data
    }
    
    public function getUpdated_by(){
    	return $this->_updated_by;
    }
        
    public function getupdated(){
    	return $this->_updated; // Date
    }*/
    
    
    
/*    public function getCar_Model_name(){
    	return $this->_car_model_name;
    }*/
    
  /* public function setCreated_by($text){
    	$this->_created_by = (string)$text;
    	return $this;
    }

    public function setUpdated_by($text){
    	$this->_updated_by = (string)$text;
    	return $this;
    }    */
    
    public function setCar_make_id($value){
    	//throw new exception('Who calls me?');
    	$this->_car_make_id	 = (integer)$value;
    	return $this;
    }    
    
    public function setCar_model_name($text){
    	//throw new exception('Who calls me?');
    	$this->_car_model_name = (string)$text;
    	return $this;
    }
    
    public function setCar_make_name($text){
    	throw new exception('Who calls me?');
    	$this->_car_make_name = (string)$text;
    	return $this;
    }
    
    public function validate_before_safe(){
    	!empty($this->_car_model_name)
    		or error("Cant save without a car make");
//		assertEx("","The object was ".var_dump_array($this));
    }
    
    public function save()
    {
    	$this->validate_before_safe();
        return $this->getMapper()->save($this);
    }
    
    
    public function setAllFromGenClass($car_model){
		throw new Exception("setAllFromGenClass this should not be used");
    	/*private $_car_model_id=null;
	private $_car_model_name=null;
	private $_car_model_main_id=null;
	private $_created=null;
	private $_created_by=null;
	private $_state=null;
	private $_updated=null;
	private $_updated_by=null;*/
    	//$car_model = $car_model->_data;
    	//die(var_export($car_model));
		$this->_car_model_id = $car_model->car_model_id;
		$this->_car_model_name = $car_model->car_model_name;
		//die($this->_car_model_name);
		$this->_created = $car_model->created;
		$this->_created_by = $car_model->created_by;
		$this->_state = $car_model->state;
		$this->_updated = $car_model->updated;
		$this->_updated_by = $car_model->updated_by;
		//$this->_car_model_id = $car_model->_car_model_id;
		//var_export($car_model);
		
		
    }
    
    
    
/*    public function setComment($text);
    public function getComment();

    public function setEmail($email);
    public function getEmail();

    public function setCreated($ts);
    public function getCreated();

    public function setId($id);
    public function getId();*/

    /*public function setMapper($mapper)
    {
        $this->_mapper = $mapper;
        return $this;
    }

    public function getMapper()
    {
        if (null === $this->_mapper) {
            $this->setMapper(new Default_Model_CarModelsMapper());
        }
        return $this->_mapper;
    }
    
    public function save()
    {
        $this->getMapper()->save($this);
    }

    public function find($id)
    {
        $this->getMapper()->find($id, $this);
        return $this;
    }*/
    
   /* public function setMapper($mapper)
    {
        $this->_mapper = $mapper;
        return $this;
    }*/
    
    
    public function addAsSimpleXmlChildNode(SimpleXMLElement &$elem){
    	throw new exeception("addAsSimpleXmlChildNode this should not be used");
    	$child = $elem->addChild('car_models');
    	$child->addChild('car_model_id',$this->getCar_Model_id());
    	$child->addChild('car_model_name',$this->getCar_Model_name());
    	$main_id = $this->getCar_Model_main_id() or $this->getCar_Model_id();
    	$child->addChild('car_model_main_id',$m_id);
    	$child->addChild('state_enum',$this->state_enum);    	
    	$child->addChild('created',$this->getCreated());  
    	$child->addChild('updated',$this->getUpdated());
    	return $child;
    }


}

?>