<?php
// for auto-complete to work
//require_once '../library/Bildelspriser/generated/gen_db_class_carmakes.php'; 
//require_once '/Bildelspriser/generated/gen_db_class_car_makes.php'; 
//require_once 'C:\wamp\www\z18\soap\library/Bildelspriser/generated/gen_db_class_car_makes.php';
require_once 'DbTable/DbtSparePartCategories.php';
require_once 'SparePartCategoriesMapper.php';
require_once 'BaseWithTraceability.php';

// for inspiration to this class - see
// http://framework.zend.com/docs/quickstart/create-a-model-and-database-table

class Default_Model_SparePartCategories extends Default_Model_BaseWithTraceability
{ 	
	private $_spare_part_price_id = null;
	private $_spare_part_price_name = null;
	//private	$_spare_part_price_id = null;
	private $_price_name = null;
	private $_price_url   = null;
	private $_price_product_catalog_url   = null;
	private $_price_admin_user_name  = null;
	private $_price_admin_password   = null;
	private $_price_admin_email   = null;
	private $_state   = null;
	private $_price_secret_token   = null;
	private $_created   = null;
	private $_created_by   = null;
	private $_updated   = null;
	private $_updated_by   = null;
	private $_mapper;

    public function __construct(array $options = null)
    {
        parent::__construct($options,'Default_Model_SparePartCategoriesMapper');
    }
	
     public function __get($name)
    {//$name holds the name of the undefined attributes getting called.
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid Default_Model_SparePartCategories property - "'.$name.'"'.var_export($this,true));
        }
        return $this->$method();
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
        
    
    public function getSpare_part_price_id(){
    	return $this->_spare_part_price_id;
    }
    
    
    public function getPrice_name(){
    	return $this->_price_name;
    }
    
    public function getPrice_admin_user_name(){
    	if(""==$this->_price_admin_user_name){
    		throw new Exception("You tried to fetch an empty username in getPrice_admin_user_name() ");    		
    	}
    	return $this->_price_admin_user_name;
    }
    public function getCreated_by(){
    	return $this->_created_by;
    }
        
    public function getCreated(){
    	return $this->_created; // Data
    }
    
    public function getUpdated_by(){
    	return $this->_updated_by;
    }
        
    public function getUpdated(){
    	return $this->_updated; // Date
    }
    
    
    public function setCreated_by($text){
    	$this->_created_by = (string)$text;
    	return $this;
    }

    public function setUpdated_by($text){
    	$this->_updated_by = (string)$text;
    	return $this;
    }    

        public function setCreated($text){
    	$this->_created_by = $text;
    	return $this;
    }

    public function setUpdated($text){
    	$this->_updated_by = $text;
    	return $this;
    }    
    
    public function setSpare_part_price_id($text){
    	$this->_spare_part_price_id = $text;
    	return $this;
    } 

    public function setPrice_name($text){
    	$this->_price_name = $text;
    	return $this;
    }

    public function setPrice_url($text){
    	$this->_price_url = $text;
    	return $this;
    }
    

    public function setPrice_admin_user_name($text){
    	$this->_price_admin_user_name = $text;
    	return $this;
    }
    
    public function setPrice_admin_password($text){
    	$this->_price_admin_password = $text;
    	return $this;
    }
    
    public function setPrice_admin_email($text){
    	$this->_price_admin_password = $text;
    	return $this;
    }
    
    public function setPrice_secret_token($text){
    	$this->_price_admin_password = $text;
    	return $this;
    }
    
    public function setPrice_product_catalog_url($text){
    	$this->_price_product_catalog_url = $text;
    	return $this;
    }
    
  /*  public function setPrice_url($text){
    	$this->_price_url = $text;
    	return $this;
    }*/    
/*    public function setCar_make_name($text){
    	//throw new exception('Who calls me?');
    	$this->_car_make_name = (string)$text;
    	return $this;
    }*/
    
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
		var_export($car_make);
		
		
    }
    
    
    
/*    public function setComment($text);
    public function getComment();

    public function setEmail($email);
    public function getEmail();

    public function setCreated($ts);
    public function getCreated();

    public function setId($id);
    public function getId();*/

    public function setMapper($mapper)
    {
        $this->_mapper = $mapper;
        return $this;
    }

    /**
     * 
     * @return Default_Model_SparePartCategoriesMapper
     */
    public function getMapper()
    {
        if (null === $this->_mapper) {
            $this->setMapper(new Default_Model_SparePartCategoriesMapper());
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
    }
    

    /*public function fetchAll($select=null)
    {
        $mapper=$this->getMapper();
        if(!isset($mapper)){
        	//return "Mapper not set";
        	throw new exception("mapper not set in fetchAll");
        } 
        else
        	//die("Mapper was set");
    	return $this->getMapper()->fetchAll($select);
    }*/
    
   
}

?>