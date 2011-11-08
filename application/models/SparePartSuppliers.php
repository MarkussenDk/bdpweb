<?php
// for auto-complete to work
//require_once '../library/Bildelspriser/generated/gen_db_class_carmakes.php'; 
//require_once '/Bildelspriser/generated/gen_db_class_car_makes.php'; 
//require_once 'C:\wamp\www\z18\soap\library/Bildelspriser/generated/gen_db_class_car_makes.php';
//require_once 'DbTable/SparePartSuppliers.php';
require_once 'SparePartSuppliersMapper.php';
require_once 'BaseWithTraceability.php';
// for inspiration to this class - see
// http://framework.zend.com/docs/quickstart/create-a-model-and-database-table

class Default_Model_SparePartSuppliers extends Default_Model_BaseWithTraceability
{ 	
	private $_spare_part_supplier_id = null;
	private $_spare_part_supplier_name = null;
	private	$_pare_part_supplier_id = null;
	private $_supplier_name = null;
	private $_xml_http_request_id = null;
	private $_supplier_url   = null;
	public $_supplier_product_catalog_url   = "/";
	private $_supplier_admin_user_name  = null;
	private $_supplier_admin_password   = null;
	private $_supplier_admin_email   = null;
	private $_state   = null;
	private $_supplier_secret_token   = null;
	private $_created   = null;
	private $_created_by   = null;
	private $_updated   = null;
	private $_updated_by   = null;
	private $_mapper;

    public function __construct(array $options = null)
    {
		parent::__construct($options,'Default_Model_SparePartSuppliersMapper');
    	/*if (is_array($options)) {
            $this->setOptions($options);
        }
        parent::__construct($options,'SparePart'
        )*/
    }
	
    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        $vars = get_class_vars(get_class($this));
        if($vars == null)
        	die("vars was null");
        //else die("vars was not nul" . sizeof($vars). "<Hr>VL=$vl<hr>" . var_export($vars,true)."<hr>". implode(" \n\t ",$vars) );
        foreach ($options as $key => $value) {
	       	if($value == null) 	continue; // no need to save things that are null
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }elseif(array_key_exists('_'.$key,$vars)){
            	$this->$key = $value;
            }else{    
            	$methods_list = implode(" ,\n ",$methods);
            	throw new Exception("Method '$method' not implemented - Value used was '$value' "
            		."\n Methods where: $methods_list  and \n $key = $value \n Vars:  ".var_export($vars,true));
            	
            }
        }
        return $this;
    }   
    
	
    //public function __set($name, $value);
    //public function __get($name);

   /* public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid car_makes property"'.$name.'"');
        }
        $this->$method($value);
    }*/

 /*   public function __get($name)
    {//$name holds the name of the undefined attributes getting called.
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid Default_Model_SparePartSuppliers property - "'.$name.'"'.var_export($this,true));
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
        
                   
    public function getSpare_part_supplier_id(){
    	return $this->_spare_part_supplier_id;
    }
    
    
    public function getSupplier_name(){
    	return $this->_supplier_name;
    }
    
    public function getSupplier_admin_user_name(){
    	if(""==$this->_supplier_admin_user_name){
    		throw new Exception("You tried to fetch an empty username in getSupplier_admin_user_name() ");    		
    	}
    	return $this->_supplier_admin_user_name;
    }
 /*   public function getCreated_by(){
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
    }    */
    
    public function setSpare_part_supplier_id($text){
    	$this->_spare_part_supplier_id = $text;
    	return $this;
    } 

    public function setSupplier_name($text){
    	$this->_supplier_name = $text;
    	return $this;
    }

    public function setSupplier_url($text){
    	$this->_supplier_url = $text;
    	return $this;
    }
    
    public function setSupplier_admin_user_name($text){
    	$this->_supplier_admin_user_name = $text;
    	return $this;
    }
    
    public function setSupplier_admin_password($text){
    	$this->_supplier_admin_password = $text;
    	return $this;
    }
    
    public function setSupplier_admin_email($text){
    	$this->_supplier_admin_password = $text;
    	return $this;
    }
    
    public function setSupplier_secret_token($text){
    	$this->_supplier_admin_password = $text;
    	return $this;
    }
    
    public function setSupplier_product_catalog_url($text){
    	$this->_supplier_product_catalog_url = $text;
    	return $this;
    }
    
  /*  public function setSupplier_url($text){
    	$this->_supplier_url = $text;
    	return $this;
    }*/    
/*    public function setCar_make_name($text){
    	//throw new exception('Who calls me?');
    	$this->_car_make_name = (string)$text;
    	return $this;
    }*/

    public function getActiveUserAsObject(){
    	$au = $this->getMapper()->_active_user;
    	assertEx($au,"Active user was not set");
    	assertEx(is_object($au),"Active user was not set to an object");
		//assertEx()    	
        return $au;

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

    /*public function setMapper($mapper)
    {
        $this->_mapper = $mapper;
        return $this;
    }*/

    /**
     * 
     * @return Default_Model_SparePartSuppliersMapper
     */
    /*public function getMapper()
    {
        if (null === $this->_mapper) {
            $this->setMapper(new Default_Model_SparePartSuppliersMapper());
        }
        return $this->_mapper;
    }*/
    
    public function save()
    {
        $this->getMapper()->save($this);
    }

    public function find($id)
    {
        $this->getMapper()->find($id, $this);
        return $this;
    }
    
    public function authenticate($user_name,$password, Default_Model_SparePartSuppliers  $sps){
    	$this->getMapper()->authenticate($user_name,$password, $sps);
    	
    }

   /* public function fetchAll($select)
    {
        $mapper=$this->getMapper();
        if(!isset($mapper)){
        	return "Mapper not set";
        	die("mapper");
        } 
        else
        	//die("Mapper was set");
    	return $this->getMapper()->fetchAll($select);
    }*/
    
   
}

?>