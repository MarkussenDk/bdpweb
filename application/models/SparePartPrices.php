<?php
// for auto-complete to work
//require_once '../library/Bildelspriser/generated/gen_db_class_carmakes.php'; 
//require_once '/Bildelspriser/generated/gen_db_class_car_makes.php'; 
//require_once 'C:\wamp\www\z18\soap\library/Bildelspriser/generated/gen_db_class_car_makes.php';
//require_once 'DbTable/DbtSparePartPrices.php';
//Zend_Loader_Autoloader::autoload('Default_Model_DbTable_SparePartPrices');
//Zend_Loader_Autoloader::autoload('Default_Model_SparePartPricesMapper');
//require_once 'SparePartPricesMapper.php';

// for inspiration to this class - see
// http://framework.zend.com/docs/quickstart/create-a-model-and-database-table

class Default_Model_SparePartPrices extends Default_Model_BaseWithTraceability
{ 	
	private $_spare_part_price_id;
	private $_name;
	private $_description;
	private $_spare_part_url;
	private $_spare_part_image_url;
	private $_spare_part_category_id;
	private $_spare_part_category_free_text;
	private $_part_placement;
	private $_part_placement_left_right;
	private $_part_placement_front_back;
	private $_supplier_part_number;
	private $_original_part_number;
	private $_price_inc_vat;
	private $_producer_make_name;
	private $_producer_part_number;
	private $_spare_part_supplier_id;
	private $_price_parser_run_id;
	private $_xml_http_request_id;
	
	public $Price_parser_run_id;
	public $price_parser_run_id;
/* Moved to Default_Model_BaseWithTraceability
 	private $_created;
	private $_created_by;
	private $_mapper;
	protected $_options;*/

    public function __construct(array $options = null)
    {
    	parent::__construct($options,'Default_Model_SparePartPricesMapper');
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

    public function __get($name)
    {//$name holds the name of the undefined attributes getting called.
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid Default_Model_SparePartPrices property - "'.$name.'"'
            	//.var_export($this,true)
            );
        }
        return $this->$method();
    }

    public function getState(){
    	return $this->_state;
    }
    
    public function setDescription($value){
    	//$detect = array('Ã');
    	/*$str = 'Ã';
    	$part = 30;
    	$pos = strpos($value,$str);
    	//if(is_integer($pos) && $pos > 0){
    		throw new Exception("String contains irlegal chars. Could be caused by wrong encoding? Position $pos<br>".$value);
    	//}*/
    	$this->_description = utf8_decode($value);
    	return $this;
    }

    public function setName($value){
    	$this->_name =  utf8_decode($value);
    	return $this;
    }

    public function dk2usnum($s)
    {
    	$p3 = substr($s,-3,1);
    	//echo "p3 = '$p3'";
    	if($p3==',')
    	{// DK monetary format
    		//Remove all thousand seperators
    		$s = str_replace('.','',$s);
    		//change all commas to dots
    		$s = str_replace(',','.',$s);
    		return $s;
    	}
		return $s;  
    }
    
    public function setPrice_inc_vat($value){
    	$ar = explode(' ',$value);
    	$num_str = "";
    	$currency_str ="";
    	for ($index = 0; $index < count($ar); $index++) {
    		$c=self::dk2usnum($ar[$index]);     		   		
    		if(is_numeric($c)){
    			//print "<br>Nummeric = $c";
    			$num_str .= $c;
    		}
    		else{ // Mostlikely a 
    			//print "<br>Text = $c";
    			$currency_str .= $c;
    		}
    	}
    	switch ($currency_str){
    		case "": // assuming DK if nothing else
    		case "DKK":
    			$this->_price_inc_vat = (float)$num_str;
    			 break;
    		case "EUR":
    			$this->_price_inc_vat = (float)7.5*$num_str;
    			break;
    		default:
    			throw new exception("Unknown currency '$currency_str' or unable to parse price string. '$value'");
    	}
    	//$this->_price_inc_vat = $value;
    	return $this;
    }
    
   
    public function setSpare_part_url($value){
    	$this->_spare_part_url = $value;
    	return $this;
    }    
    public function setSpare_part_image_url($value){
    	$this->_spare_part_image_url = $value;
    	return $this;
    }    
    public function setSpare_part_category_free_text($value){
    	$this->_spare_category_free_text = $value;
    	//TODO:Consider doing a lookup on the category.
    	return $this;
    }    
    
    public function setSpare_part_category_id($value){
    	$this->_spare_category_id = $value;
    	//TODO:Consider doing a lookup on the category.
    	return $this;
    }     
    
    public function setPrice_parser_run_id($value){
    	$this->_price_parser_run_id = $value;
    }
    
    public function setPart_placement($value){
    	$this->_part_placement = $value;
    	return $this;
    }    

    public function setPart_placement_left_right($value){
    	$this->_part_placement = $value;
    	return $this;
    }   
    
    public function setPart_placement_front_back($value){
    	$this->_part_placement = $value;
    	return $this;
    }  
    
    public function setOriginal_part_number($value){
    	$this->_original_part_number = $value;
    	return $this;
    }
    
    public function setProducer_make_name($value){
    	$this->_producer_make_name = $value;
    	return $this;
    }    
    public function setProducer_part_number($value){
    	$this->_producer_part_number = $value;
    	return $this;
    }    
    
    public function setSupplier_part_number($value){
    	$this->_supplier_part_number = $value;
    	return $this;
    }  
    
    public function setShop_item_number($value){
    	$this->_supplier_part_number = $value;
    	return $this;
    } 
    
    public function setState($value){
    	//TODO: add check for the right values - and throw exception.
    	//throw new Exception("setState - I was called with value ".$value,25);
    	$this->_state = $value;
    	return $this;
    }
        
    public function getName(){
    	return $this->_name; 
	}
    
	
    public function getDescription(){
        return $this->_description; 
    }		
    
    public function getSpare_part_url(){
           return $this->_spare_part_url; 
    }
    public function getSpare_part_image_url(){
           return $this->_spare_part_image_url; 
    }
    public function getSpare_part_category_id(){
           return $this->_spare_part_category_id; 
    }
    public function getSpare_part_category_free_text(){
           return $this->_spare_part_category_free_text; 
    }
    public function getPart_placement(){
           return $this->_part_placement; 
    }
    public function getPart_placement_left_right(){
           return $this->_part_placement_left_right; 
    }
    public function getPart_placement_front_back(){
           return $this->_part_placement_front_back; 
    }
    public function getsupplier_part_number(){
           return $this->_supplier_part_number; 
    }
    public function getOriginal_part_number(){
           return $this->_original_part_number; 
    }
    public function getPrice_inc_vat(){
           return $this->_price_inc_vat; 
    }
    public function getProducer_make_name(){
           return $this->_producer_make_name; 
    }
    public function getProducer_part_number(){
           return $this->_producer_part_number; 
    }
    public function getSpare_part_supplier_id(){
           return $this->_spare_part_supplier_id; 
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
        
    
    
    
 /*   public function setCreated_by($text){
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
    */
    public function setSpare_part_price_id($text){
    	$this->_spare_part_price_id = $text;
    	return $this;
    } 

    public function setPrice_name($text){
    	$this->_price_name =  utf8_decode($text);
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
    
    public function setSpare_part_supplier_id($text){
    	$this->_spare_part_supplier_id = $text;
    	return $this;
    }
    
    public function setXml_http_request_id($text){
    	$this->_xml_http_request_id = $text;
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
    
/*    public function setAllFromGenClass($car_make){
    	/*private $_car_make_id=null;
	private $_car_make_name=null;
	private $_car_make_main_id=null;
	private $_created=null;
	private $_created_by=null;
	private $_state=null;
	private $_updated=null;
	private $_updated_by=null;* /
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
		
		
    }*/
    
    
    
/*    public function setComment($text);
    public function getComment();

    public function setEmail($email);
    public function getEmail();

    public function setCreated($ts);
    public function getCreated();

    public function setId($id);
    public function getId();*/

    /**
     * 
     * @return Default_Model_SparePartPricesMapper
     */
 /*   public function getMapper()
    {
        if (null === $this->_mapper) {
            $this->setMapper(new Default_Model_SparePartPricesMapper());
        }
        return $this->_mapper;
    }*/
    
    public function save()
    {
    	$this->validate_before_safe();
        return $this->getMapper()->save($this);
    }

    public function validate_before_safe(){
    	assertEx(isset($this->_spare_part_supplier_id),"Spare_Part_Supplier_id must be set, before an element can be saved.");
    	assertEx(isset($this->_name),"Name must be defined");
    	//assertEx(isset($this->_description),"Description must be defined<br>");    	
    }
    
  /*  public function find($id)
    {
        $this->getMapper()->find($id, $this);
        return $this;
    }*/
    
   
 /*   public function addAsSimpleXmlChildNode(SimpleXMLElement &$elem){
    	$child = $elem->addChild('car_makes');
    	$child->addChild('car_make_id',$this->getCar_make_id());
    	$child->addChild('car_make_name',$this->getCar_make_name());
    	$main_id = $this->getCar_make_main_id() or $this->getCar_make_id();
    	$child->addChild('car_make_main_id',$m_id);
    	$child->addChild('state',$this->getState());    	
    	$child->addChild('created',$this->getCreated());  
    	$child->addChild('updated',$this->getUpdated());
    	return $child;
    }*/

  /*  public function fetchAll($select)
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
    
    /**
     * 
     * Enter description here ...
     * @return array of Default_Model_CarModels2SparePartPrices
     */
	public function getCmo2Spp(){
   		$ar = array();
   		$cm2spp_mapper = Default_Model_CarModelsToSparePartPricesMapper::getInstance('Default_Model_CarModelsToSparePartPricesMapper');
   		$spp_id = (int)$this->getSpare_part_price_id();
   		assertEx( is_int($spp_id), "Spare Part Price id was not set in getModels() ".var_export(get_object_vars($this),true));
   		$cmm2spp_rowset = $cm2spp_mapper->fetchAll(' spare_part_price_id = '.$spp_id);
   		is_array($cmm2spp_rowset) or error('$cmm2spp_rowset must be a rowset');
   		return $cmm2spp_rowset;
   }
}

