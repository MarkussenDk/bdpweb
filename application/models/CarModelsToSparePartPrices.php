<?php
// for auto-complete to work
//require_once '../library/Bildelspriser/generated/gen_db_class_carmakes.php'; 
//require_once '/Bildelspriser/generated/gen_db_class_car_makes.php'; 
//require_once 'C:\wamp\www\z18\soap\library/Bildelspriser/generated/gen_db_class_car_makes.php';
require_once 'DbTable/CarMakes.php';
require_once 'CarMakesMapper.php';
require_once 'Base.php';

// for inspiration to this class - see
// http://framework.zend.com/docs/quickstart/create-a-model-and-database-table

class Default_Model_CarModelsToSparePartPrices extends Default_Model_BaseWithTraceability
{ 	
	private $_car_model_id=null;
	private $_spare_part_price_id=null;
	private $_year_from=null;
	private $_month_from=null;
	private $_year_to=null;
	private $_month_to=null;
	private $_chassis_no_from=null;
	private $_chassis_no_to=null;
	private $_spare_part_price_guid;
	public $price_parser_run_id;
	
	public function toArray(){
	    return array(
            'car_model_id'   		 => $this->_car_model_id, 
            'spare_part_price_id'    => $this->_spare_part_price_id, 
    	    'year_from'    => $this->_year_from, 
            'month_from'    => $this->_month_from, 
    	    'year_to'    => $this->_year_to, 
            'month_to'	  => $this->_month_to,
    	    'year_to'    => $this->_year_to, 
            'chassis_no_from'	  => $this->_chassis_no_from,
    		'chassis_no_to'	  => $this->_chassis_no_to,
            'created' 		  => date('Y-m-d H:i:s') ,
            'created_by' 		  => $this->getCreated_by(),
    		'price_parser_run_id' => Bildelspriser_XmlImport_PriceParser::$_price_parser_run_id)
    	;
	}

    public function __construct(array $options = null)
    {
        parent::__construct($options,'Default_Model_CarModelsToSparePartPricesMapper');
    }
	
    public function getCar_model_id(){
    	return $this->_car_model_id;
    }    

    public function getSpare_part_price_id(){
    	return $this->_spare_part_price_id;
    }    
    
    public function getYear_from(){
    	return $this->_year_from;
    }    
    
    public function getMonth_from(){
    	return $this->_month_from;
    }    
    public function getYear_to(){
    	return $this->_year_to;
    }    
    public function getMonth_to(){
    	return $this->_month_to;
    }    
    public function getChassis_no_from(){
    	return $this->_chassis_no_from;
    }	
	
    public function getChassis_no_to(){
    	return $this->_chassis_no_to;
    }

 /*   public function getCreated_by(){
    	return $this->_createdby;
    }    

    public function getCreated(){
    	return $this->_created; // Data
    }
    */
/********************************************************
 * Below is the setters
 ******************************************************/
    public function setCar_model_id($text){
    	$this->_car_model_id = (int)$text;
    	return $this;	
    }    
    
    public function setSpare_part_price_id($text){
    	//echo "setting id $text ";
    	if(!(strlen($text) == 13)){
    		//echo "a";
    		$this->_spare_part_price_id = (int)$text;
    	} else {
    		//echo "b";
   			$this->_spare_part_price_guid = $text;
    	}
    	//else die("The spare_part_price was '".var_export($text,true).gettype($text)."' ");
    	return $this;
    }    
    
    public function setYear_from($text){
    	$this->_year_from = (int)$text;
    	return $this;
    }    
    
    public function setMonth_from($text){
    	$this->_month_from = (int)$text;
    	return $this;
    }    
    public function setYear_to($text){
    	$this->_year_to = (int)$text;
    	return $this;
    }    
    public function setMonth_to($text){
    	$this->_month_to = (int)$text;
    	return $this;
    }    
    public function setChassis_no_from($text){
    	$this->_chassis_no_from = (int)$text;
    	return $this;
    }	
	
    public function setChassis_no_to($text){
    	$this->_chassis_no_to = (int)$text;
    	return $this;
    }

    public function setXml_http_request_id($text){
    	$this->_xml_http_request_id = (int)$text;    	
    }
    
 /*   public function setCreated_by($text){
    	$this->_createdby = (string)$text;
    	return $this;
    }   */ 
    
    public function validateForSave(){
    	assertEx(isset($this->_car_model_id),"Car Model id was not defined");
    	assertEx(isset($this->_spare_part_price_id) or 
    			 isset($this->_spare_part_price_guid)				
    			,"Spare Part Id id was not defined");
    }    
}

?>