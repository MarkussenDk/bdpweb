<?php
/**
 * @package PriceParser
 */




set_include_path(
	implode(PATH_SEPARATOR, array(
    get_include_path(),
    realpath(APPLICATION_PATH . '/../library'),/*FC*/ 
    realpath(APPLICATION_PATH . '/../library/PEAR')/*FC*/ 
    )/*ar*/  )/*imp*/
);

require 'XML/Parser.php';

$log_level = 1; // only main stuff and errors - 15 is max.

class XmlParserInfo{
	var $_xp;
	public function __construct($xp){
		$this->_xp = $xp;
	}
	static public function FullInfo($xp)	{
		$code_line  = "\nXml File Line  : ".xml_get_current_line_number($xp).':'.xml_get_current_column_number($xp);
		$column = "\nXml File Coloum: ".xml_get_current_column_number($xp);
		$byte_intex = "\nXml File ByteIndex : ".xml_get_current_byte_index($xp);
		//$colum_num  = "\nColumn    : ".;
		return $code_line.$column .$byte_intex;
	}
	
	static public function SimpleInfo($xp)	{
		return "\nLine  : ".xml_get_current_line_number($xp).':'.xml_get_current_column_number($xp);
		//$colum_num  = "\nColumn    : ".;		
	}
	
	static public function LineAndColumn($xp)	{
		return xml_get_current_line_number($xp).':'.xml_get_current_column_number($xp);
	}
	
	public function getInfo(){
		return self::XmlParserInfo($this->_xp);		
	}
}


class XmlException extends Exception{
	public function __construct($message,$xp,$code = null){
		$message.= "<br>Full Info: " .XmlParserInfo::FullInfo($xp);
		parent::__construct($message);		
	}	
}

class XmlParserException extends Exception{
	public 		$message;
	public		$parent_attributes;
	public		$parent_element_name;
	public		$grand_parent_attributes;
	public 		$grand_parent_element_name;
	public 		$code = null;
	
	public function __construct($_message,
						$xp,
						$_parent_attributes,
						$_parent_element_name,
						$_grand_parent_attributes,
						$_grand_parent_element_name,
						$_code = null){
		die('constructio'.$message);
							$this->message = $_message;
		$this->parent_attributes = $_parent_attributes;
		$this->parent_element_name	= $_parent_element_name;
		$this->grand_parent_attributes = $_grand_parent_attributes;
		$this->grand_parent_element_name = $_grand_parent_element_name;
		$this->code = $_code;
		/*$this->message .= XmlParserInfo::FullInfo($xp);
		$this->message .= "<br>Parent Element</b> :<br>&lt;".$_parent_element_name.$this->attribs_to_string($_parent_attributes).'&gt;';
		$this->message .= "<br>GrandParent Element</b> :<br>&lt;".$_grand_parent_element_name.$this->attribs_to_string($_grand_parent_attributes).'&gt;';
		*/
		parent::__construct($this->message);		
	}	

	function attribs_to_string($attribs){
		$str = " ";
		$sep = "";
		foreach($attribs as $key=>$val){
			$str .= '<br>&nbsp; &nbsp;'. $key.'="'.$val.'" ';//.$sep;
			//$sep = "\n ";
		}
		return $str;
	}
	
}

$pp_instance = null;
//todo: Make UnitTest PriceParcerTest
class Bildelspriser_XmlImport_PriceParser extends XML_Parser{
	/** to keep track of the parent object
	 * 
	 * @var array of strings. Push when entering, annd pop when leavng
	 */
	var $stack;
	var $stack_attibs;
	var $cdata;
	var $_spare_part_supplier_id;
	var $_spare_part_supplier_object;
	var $_spare_part_supplier_admin_user;
	var $_xp;
	static $_path;
	static $_instance;
	static $_price_parser_run_id;
	static $_xml_http_request_id;
	var $_start_time;
	var $_start_mktime;
	var $_dbo;
	var $load_success_count;
	var $_spp_count;
	var $_cmo_count;
	var $_c2s_count;
	var $_log_level;
	
	
	/** 
	 * Array of strings containing logging items
	 * @var Array of strings
	 */
	var $_logging_items;
	
	function __construct(Default_Model_SparePartSuppliers $spp){
		$this->load_success_count = array();
		$this->_dbo = new Default_Model_DynPriceParserRun();
		$this->_dbo->xml_http_request_id = self::$_xml_http_request_id;
		//$this->_dbo->file_base_name = "none";
		$this->_dbo->status = 'constructed';
		self::$_price_parser_run_id = $this->_dbo->insert();
		$spare_part_supplier_id = $spp->getSpare_part_supplier_id();
		$this->parse_assert($spare_part_supplier_id>0, "SparePartSupplier must be higher than 0 ($spare_part_supplier_id) ");
// Does not work		$this->parse_assert(null == $spare_part_supplier_id, "SparePartSupplier must be different from null ($spare_part_supplier_id)");
		$this->_spare_part_supplier_id = $spare_part_supplier_id;
		$this->parse_assert(isset($this->_spare_part_supplier_id),"SparePartsupplier must be specified");
		$this->_spare_part_supplier_object = $spp;
		$this->_spare_part_supplier_admin_user = $spp->getSupplier_admin_user_name();
		$this->stack = array();
		$this->stack_attibs = array();
		$this->_logging_items = array();
		
		$this->_log_level = 10;
		//$this->_logging_items = array("Log started - User Name = ".$this->_spare_part_supplier_admin_user);
		$this->log("Log started - User Name = ".$this->_spare_part_supplier_admin_user);
		/**
		 * Initializes the XML_Parser in func mode. 
		 */
		ob_implicit_flush(true);
		ini_set('output_buffering',1024);
		$this->_start_mktime = microtime(true);
		//if(params->log_level is set) set $this->_log_level = $get_log_level
		self::$_instance = $this;
		global $pp_instance;
		$pp_instance = $this;
		parent::__construct('','func');
		try{
			set_time_limit(0);
			echo "<br/>Set time limit(0) was a sucess";
		}
		catch(Exception $e){
			echo "<br/>Set time limit(0) failed - current limit  unknown" . $e;
		}	
	}

	public static function getInstance(){
		global $pp_instance;		
		//return $pp_instance;
		return self::$_instance;
	}
	
	public function setPath($string){
		$this->_dbo->setFile_base_name($string);
		self::$_path = $string;
	}
	
	public function timeStamp(){
		static $last_time;
		$now = microtime(true);
		$time_spent = $now - $this->_start_mktime;
		$delta = $time_spent - $last_time;  
		$last_time = $time_spent;
		return " $time_spent ($delta)";
	}

	public function incrementCount($type){
		if(!array_key_exists($type,$this->load_success_count)){
			$this->load_success_count[$type] = 0;
		}
		$count = $this->load_success_count[$type]++;
		if($count < 10 || ($count%10) == 0  )
			{
				//$this->log("Element '$type' count is $count ");
			}
	}
	
	public function log($message){
		echo '<br/>Log:'.$this->timeStamp().': '.$message.'stats'.$this->get_stats();	
		//ob_flush();
		//array_push($this->_logging_items,$message);
	}
	
	public function get_stats(){
		return 		 'spp:' . $this->_spp_count . '/ cmo:'.
		   $this->_cmo_count. '/ c2s:'.
		   $this->_c2s_count;
	}
	
	public function inc_spp_count(){
		$this->_spp_count++;
	}
	
	public function inc_cmo_count(){
		$this->_cmo_count++;
	}
	
	public function inc_c2s_count(){
		$this->_c2s_count++;
	}
	
	public function parse_assert($exp,$message){
		if($exp){
			//$this->log("OK: Asserting: ".$message);
			return;
		}
		else $this->parse_error($message);
	}
	
	public function parse_error($message){
		$p_a = $this->get_parent_attribs();
		$p_n = $this->get_parent_elem();
		$gp_a = $this->get_grand_parent_attribs();
		$gp_n = $this->get_grand_parent_elem();
	    echo "<h3>parse_error:<h3/> ".$message;
		throw new XmlParserException($message,
						$this->_xp,
						$p_a,
						$p_n,
						$gp_a,
						$gp_n,
						$_code = null);		
	}

	function get_log_as_xml($list_element_name='ParseLog',$item_element_name = 'LogItem'){
		$xml = "<$list_element_name>\n";
		$li_start = "<$item_element_name>";
		$li_end = "</$item_element_name>";		
		foreach($this->_logging_items as $item){
			$xml.="\t".$li_start.$item.$li_end."\n";
		}
		$xml.="</$list_element_name>\n";
	//	return htmlentities($xml,ENT_QUOTES,"ISO-8859-1");
		return $xml;		
	}
	
	function get_log_as_html(){
//		return "";
		return "<div style='border:red solid 3px;' >"
					.nl2br($this->get_log_as_xml("ol type='1'",'li'))
				."</div>";		
	}
	
	function gen_start($xp,$element,$attribs){
		global $log_level;
		$this->_xp = $xp;
		$this->incrementCount($element);
		$this->stack[] = $element;
		//array_push($this->stack,(string)$element);	
		//$e_element = var_export($element,true);
		//$e_element = trim($element,"'");
		if($log_level>10){
			$line = XmlParserInfo::LineAndColumn($xp);
			$this->log("<Parsing of element &lt;{$element}&gt; started with on Line $line and the following attributes \n "
				.var_export($attribs,true));
		}
		array_push($this->stack_attibs,$attribs);	
	}
	function gen_end(/*$xp=null,$element='element'*/){
		global $log_level;
		$element = end($this->stack);
		$attribs = end($this->stack_attibs);
		array_pop($this->stack);	
		array_pop($this->stack_attibs);
		if($log_level>10){
			$xpath = $this->get_fake_xpath();
			$this->log("Parsing of element <$xpath> finished <br> ".var_export($attribs,true));
		}
 	}
	
	function trim_qoutes($string){
		return trim($string,"'");
	}
	/**
	 * 
	 * @return string
	 */	
	function get_parent_elem(){
		return end($this->stack);
		//return $e;
		//$p=prev($this->stack);
		//return $p;		
	}

	/**
	 * 
	 * @return string
	 */
	function get_grand_parent_elem(){
		return end($this->stack);
		//return $e;
		$p=prev($this->stack);
		return $p;		
	}
	
	
	function get_parent_attribs(){
		return end($this->stack_attibs);
		//var_dump_array($this->stack_attibs,"Stack attribs");
		// return $e;
		//$p=prev($this->stack_attibs);
		//return $p;		
	}
	
	function get_grand_parent_attribs(){
//		$e=end($this->stack_attibs);
	//	return $e;
		return prev($this->stack_attibs);
		//return $p;		
	}
	/**
	 * 
	 * @param $attrib_name Name of attribute to search for
	 * @param $value_out The reference to a variable where the variable could be placed if success
	 * @param $elem_name The element name containing the attribute
	 * @return boolean True if the key is found in the stack 
	 */
	function get_attrib_from_stack($attrib_name,&$value_out,&$elem_name){
		$e=end($this->stack_attibs);
		end($this->stack);
		while($a = prev($this->stack_attibs)){
			$elem_name = prev($this->stack);
			if(array_key_exists($attrib_name,$a)){
				$value_out = $a[$attrib_name];
				//throw new Exception("Found ".$a[$attrib_name]);	
				return true;
			}			
		}
		return false;
	}
	
	function get_fake_xpath(){
//		$e = end($this->stack);
		$s = "";
		foreach($this->stack as $elem ){
			$s.='\\'.$elem;			
		}
		return $s;
	}
	
	/*************************************** PRICE LIST ***********************************************/
	/**
	 * Price List Parser
	 * @param $xp
	 * @param $element
	 * @param $attribs
	 * @return unknown_type
	 */
	function xmltag_price_list($xp, $element, $attribs){
		//We dont write the PriceList to the database. It is implicit.
		$attribs['CREATED_BY'] = $this->_spare_part_supplier_admin_user;
		$attribs['SPARE_PART_SUPPLIER_ID'] = $this->_spare_part_supplier_id;
		$this->gen_start($xp,$element,$attribs);
		//die("Some tag " + $element);
		//throw new exception("error");
	}
	
	function xmltag_price_list_($xp, $element){
		$this->gen_end();
	}
		/*************************************** CAR MODEL ***********************************************/
	/**
	 * Car Model Parser
	 * To be able to save a Car Model, the Car Make must be available first!
	 * If the parent is a SparePartPrice, the Carmodel leads to a cm2spp object
	 * @param $xp
	 * @param $element
	 * @param $attribs
	 * @return unknown_type
	 */
	function xmltag_car_model($xp, $element, $attribs){
		$attribs['CREATED_BY'] = $this->_spare_part_supplier_admin_user;	
		array_change_key_case($attribs);
		$parent_elem = $this->get_parent_elem();
		$parent_attribs = $this->get_parent_attribs();
		$spare_part_price_id = 0; // 0=empty, just for scope.
		$car_model_id = 0; // 0=empty, just for scope.
		
		
		
		$grand_parent_elem = $this->get_grand_parent_elem();
		$grand_parent_attribs = $this->get_grand_parent_attribs();
		/* ensure that there is car model id - create a carmodel if it does not exist*/		
		if(isset($attribs['CAR_MODEL_ID'])){	
			$car_model_id  = $attribs['CAR_MODEL_ID'];			//We are fine, just assign!
		}elseif(isset($attribs['CAR_MODEL_NAME'])){ 		// Check if the model has a value, so we can create it.
			if(empty($attribs['CAR_MAKE_ID'])){					//if the make id is defined
				$has_car_make_name = array_key_exists('CAR_MAKE_NAME',$attribs);
				$car_make_name = "";
				if($has_car_make_name){
					$car_make_name = $attribs['CAR_MAKE_NAME'];
				} /** BASIC approach - IDs are faster than Names since names must be looked up. 
					* And Parents before grand parents*/
				elseif(array_key_exists('CAR_MAKE_ID',$parent_attribs)) {					
					$this->parse_assert(array_key_exists('CAR_MAKE_ID',$parent_attribs)
					,"When CAR_MODEL_ID is not given, but CAR_MODEL_NAME is given, "
					."it must be combined with either a CAR_MAKE_NAME or a CAR_MAKE_ID. <br>Element "
					.var_export($attribs,true)
					.'<br>The Parent Element'.$this->get_fake_xpath() . ' <br>'
					.var_export($parent_attribs,true));
					$attribs['CAR_MAKE_ID'] = $parent_attribs['CAR_MAKE_ID'];
				}// ID was not found in the parents - lets look in the grand parents				
				elseif(array_key_exists('CAR_MAKE_ID',$grand_parent_attribs)) {					
					$this->parse_assert(array_key_exists('CAR_MAKE_ID',$grand_parent_attribs)
					,"When CAR_MODEL_ID is not given, but CAR_MODEL_NAME is given, "
					."it must be combined with either a CAR_MAKE_NAME or a CAR_MAKE_ID. <br>Element "
					.var_export($attribs,true)
					.'<br>The Parent Element'.$this->get_fake_xpath() . ' <br>'
					.var_export($grand_parent_attribs,true));
					$attribs['CAR_MAKE_ID'] = $grand_parent_attribs['CAR_MAKE_ID'];
				} // id was not found at all. Lets look for names. Parents first then grand parents.
				elseif(array_key_exists('CAR_MAKE_NAME',$parent_attribs)){
					$car_make_name = $parent_attribs['CAR_MAKE_NAME'];
					$has_car_make_name = true;
					$attribs['CAR_MAKE_NAME'] = $parent_attribs['CAR_MAKE_NAME'];
				}
				elseif(array_key_exists('CAR_MAKE_NAME',$grand_parent_attribs)){
					$car_make_name = $grand_parent_attribs['CAR_MAKE_NAME'];
					$has_car_make_name = true;
					$attribs['CAR_MAKE_NAME'] = $grand_parent_attribs['CAR_MAKE_NAME'];
				}
				else{
					$this->parse_assert(array_key_exists('CAR_MAKE_NAME',$attribs)
					,"When CAR_MODEL_ID is not given, but CAR_MODEL_NAME is given, "
					."it must be combined with either a CAR_MAKE_NAME or a CAR_MAKE_ID. <br>Element "
					.var_export($attribs,true)
					.'UpperElement '.$this->get_fake_xpath() . ' <br>'
					.var_export($this->stack_attibs,true));
				}
				if($has_car_make_name){	
					$attribs['CAR_MAKE_ID'] = Default_Model_CarMakesMapper::getInstance('Default_Model_CarMakesMappe')->getCarMakeIdByCarMakeName($car_make_name);
					$this->parse_assert(isset($attribs['CAR_MAKE_ID']),"Coding error, Contact support.");
				}
				$this->parse_assert($attribs['CAR_MAKE_ID'],"Still missing a CAR_MAKE_ID!");
			}
			
			$car_model_id = Default_Model_CarModelsMapper::getInstance('Default_Model_CarModelsMapper')->
				getCarModelIdByCarModelNameAndCarMakeId($attribs['CAR_MODEL_NAME'] , $attribs['CAR_MAKE_ID']);
				$attribs['CAR_MODEL_ID'] = 	$car_model_id;	
		}
		else {
			$this->parse_error(
				"Unable to read element Car_Model."
			." Either car_model_id or car_model_name must be defined as attributes on the element.");
		}			

		$this->parse_assert($car_model_id,"Car Model id is still missing!".var_export($attribs,true));		
		/** now we know that tehre is a cor model - checl oif it should be linked to anything*/
		$grand_parent_elem = $this->get_grand_parent_elem();
		switch(trim($parent_elem)){
			case 'SPARE_PART_PRICE': // the quick way to do it.
				//determine the car_make_id is set, then just ignore the car_make_name first if set
				$spare_part_price_id = $parent_attribs['SPARE_PART_PRICE_ID'];
//				var_dump_array(array('car_model_id'=>$car_model_id,'spare_part_price_id'=>$spare_part_price_id));
				$this->parse_assert($parent_attribs['SPARE_PART_PRICE_ID'],"SparePartPriceId was not set. "
					.var_export($parent_attribs,true));
				Bildelspriser_XmlImport_PriceHelper::StripCarModelForCmo2SppDataAndCreate($car_model_id,$spare_part_price_id,$attribs);				
				break;
			case 'CAR_MAKE': // The right way to do it				
				switch($grand_parent_elem){
					case 'SPARE_PART_PRICE':
						$elem_name = "not-set";
						break;
					case 'PRICE_LIST':
						// Just save the brand if it does not exist.
						//$car_make_id = Default_Mode_CarMakesM
				}
				$spp_found = $this->get_attrib_from_stack('SPARE_PART_PRICE_ID',$spare_part_price_id,$elem_name);
				//$spare_part_price_id = $grand_parent_attribs['SPARE_PART_PRICE_ID'];
				//$this->parse_assert("","The element was ($spp_found) $spare_part_price_id $elem_name ");
				if($spp_found){
					//$this->parse_assert($spare_part_price_id<>0,"The spare_part_price_id was 0? ");					
					$this->parse_assert($spare_part_price_id<>0,"Grand_parent_elem=$grand_parent_elem <br> XPATH=".$this->get_fake_xpath());
					Bildelspriser_XmlImport_PriceHelper::StripCarModelForCmo2SppDataAndCreate($car_model_id,$spare_part_price_id,$attribs);	
				}						
				break;
			case 'CAR_MODELS_TO_SPARE_PART_PRICES';
//				$car_model_id = Default_Model_CarMakesMapper::getInstance()->getCarMakeIdByCarMakeName($attribs['CAR_MAKE_NAME']);
				Bildelspriser_XmlImport_PriceHelper::StripCarModelForCmo2SppDataAndCreate($car_model_id,$spare_part_price_id,$attribs);
				break;		
			case 'PRICE_LIST':
				// Just save the brand if it does not exist.
				break;
			default:
				//$this->raiseError("Unexpected error: You wanted to parse $element that whas a child of $parent_elem. That is an unknown combination. Contact support");
				$this->parse_error("Unexpected error: You wanted to parse $element that whas a child of $parent_elem. That is an unknown combination. Contact support.<br/>");
		}
		$attribs['CAR_MODEL_ID'] = $car_model_id;
		$this->parse_assert($car_model_id, "The Car_model_id was not set - coding error!");
		$this->_cmo_count++;
		$this->gen_start($xp,$element,$attribs);
	}
	
	function xmltag_car_model_($xp, $element){
		$this->gen_end();
	}
	
	function xmltag_car_make($xp, $element, $attribs){
		$attribs['CREATED_BY'] = $this->_spare_part_supplier_admin_user;	
		array_change_key_case($attribs);
		$parent_elem = $this->get_parent_elem();
		$parent_attribs = $this->get_parent_attribs();
		$spare_part_price_id = 0; // 0=empty, just for scope.
		$car_make_id = 0; // 0=empty, just for scope.

		$grand_parent_elem = $this->get_grand_parent_elem();
		$grand_parent_attribs = $this->get_grand_parent_attribs();
		if(isset($attribs['CAR_MAKE_ID'])){	
			$car_make_id  = $attribs['CAR_MAKE_ID'];			//We are fine, just assign!
		}elseif(isset($attribs['CAR_MAKE_NAME'])){ 		// Check if the model has a value, so we can create it.
			$car_make_id = Default_Model_CarMakesMapper::getInstance('Default_Model_CarMakesMapper')->getCarMakeIdByCarMakeName($attribs['CAR_MAKE_NAME']);
			$this->parse_assert($car_make_id,"Car_make_id $car_make_id ");
		/*	
		 * if(empty($attribs['CAR_MAKE_ID'])){					//if the make id is defined
				$has_car_make_name = array_key_exists('CAR_MAKE_NAME',$attribs);
				$car_make_name = "";
				if($has_car_make_name){
					$car_make_name = $attribs['CAR_MAKE_NAME'];
				}
				elseif(array_key_exists('CAR_MAKE_NAME',$grand_parent_attribs)){
					$car_make_name = $grand_parent_attribs['CAR_MAKE_NAME'];
					$has_car_make_name = true;
					$attribs['CAR_MAKE_NAME'] = $grand_parent_attribs['CAR_MAKE_NAME'];
				}
				elseif(array_key_exists('CAR_MAKE_ID',$parent_attribs)) {
					$this->parse_assert(array_key_exists('CAR_MAKE_NAME',$attribs)
					,"When CAR_MODEL_ID is not given, but CAR_MODEL_NAME is given, "
					."it must be combined with either a CAR_MAKE_NAME or a CAR_MAKE_ID. <br>Element "
					.var_export($attribs,true)
					.'The UpperElement'.$this->get_fake_xpath() . ' <br>'
					.var_export($parent_attribs,true));
				}
				else{
					$this->parse_assert(array_key_exists('CAR_MAKE_NAME',$attribs)
					,"When CAR_MODEL_ID is not given, but CAR_MODEL_NAME is given, "
					."it must be combined with either a CAR_MAKE_NAME or a CAR_MAKE_ID. <br>Element "
					.var_export($attribs,true)
					.'UpperElement '.$this->get_fake_xpath() . ' <br>'
					.var_export($this->stack_attibs,true));
				}
				//var_dump_array($car_make_name);
				//$this->parse_assert(isset($car_make_name),"Car_model_id, Car_Make_Id and Car_Make_name is missing. Cant create new model. Fix or Contact support");
				if($has_car_make_name){						
					$attribs['CAR_MAKE_ID'] = Default_Model_CarMakesMapper::getInstance()->getCarMakeIdByCarMakeName($car_make_name);
					$this->parse_assert(isset($attribs['CAR_MAKE_ID']),"Coding error, Contact support.");
				}
				else{
					$this->parse_assert(false,"Ups was not able to find the car_make_name");	
				}				
			}
			$car_model_id = Default_Model_CarModelsMapper::getInstance()->
				getCarModelIdByCarModelNameAndCarMakeId($attribs['CAR_MODEL_NAME'] , $attribs['CAR_MAKE_ID']);
			$attribs['CAR_MODEL_ID'] = 	$car_model_id;
			*/	
		}else {
			$this->parse_error("Unable to read element Car_Make. Either car_make_id or car_make_name must be defined as attributes on the element.");
		}			
	  				
		$this->parse_assert($car_make_id,"Car make id is still missing!".var_export($attribs,true));		
		$grand_parent_elem = $this->get_grand_parent_elem();

		$attribs['CAR_MAKE_ID'] = $car_make_id;
		$this->gen_start($xp,$element,$attribs);
	}
	
	function xmltag_car_make_($xp, $element){
		$this->gen_end();
	}	
	
	/*************************************** car_models_to_spare_part_prices ***********************************************/
	/**
	 * car_models_to_spare_part_prices Parser
	 * @param $xp
	 * @param $element
	 * @param $attribs
	 * @return unknown_type
	 */
/*	function xmltag_car_models_to_spare_part_prices($xp, $element, $attribs){
		//$spp = Zend_Loader::loadClass('Default')
		$attribs['CREATED_BY'] = $this->_spare_part_supplier_admin_user;	
		array_change_key_case($attribs);
		$parent_elem = $this->get_parent_elem();
		$parent_attribs = $this->get_parent_attribs();
		$spare_part_price_id = 0; // 0=empty, just for scope.
		$car_model_id = 0; // 0=empty, just for scope.
		//$parent_attribs = $this->get_grand_parent_attribs();
		$attribs['SPARE_PART_SUPPLIER_ID'] = $this->_spare_part_supplier_id;
		
		$spp =  new Default_Model_SparePartPrices(array_change_key_case($attribs));
		$spare_part_price_id = $spp->save();
		$this->parse_assert(isset($spare_part_price_id),"No Spare_part_price_id was found in xmltag_spare_part_price()");
		//$spp_id = $spp->save();
		$attribs['SPARE_PART_PRICE_ID'] = $spare_part_price_id;
		//var_dump_array(array('spare_part_price_id'=>$spare_part_price_id));
		//print "START -spare_part_price_Handler [$element]\n";		
		//var_dump($attribs);
		$this->gen_start($xp,$element,$attribs);
	}
	
	function xmltag_car_models_to_spare_part_prices_($xp, $element){
		$this->gen_end();
	}
	*/
	/*************************************** SPARE PART PRICE ***********************************************/
	/**
	 * Car Model Parser
	 * @param $xp
	 * @param $element
	 * @param $attribs
	 * @return unknown_type
	 */
	function xmltag_spare_part_price($xp, $element, $attribs){
		//$spp = Zend_Loader::loadClass('Default')
		$attribs['CREATED_BY'] = $this->_spare_part_supplier_admin_user;	
		array_change_key_case($attribs);
		/**
		 * Parent and grand Parent elements.
		 */
		$parent_elem = $this->get_parent_elem();
		$parent_attribs = $this->get_parent_attribs();
		$grand_parent_elem = $this->get_grand_parent_elem();
		$grand_parent_attribs = $this->get_grand_parent_attribs();		
		
		$spare_part_price_id; // 0=empty, just for scope.
		$car_model_id = 0; // 0=empty, just for scope.
		//$parent_attribs = $this->get_grand_parent_attribs();
		$attribs['SPARE_PART_SUPPLIER_ID'] = $this->_spare_part_supplier_id;
		//$spp =  new Default_Model_SparePartPrices($attribs);
		$spp = Default_Model_SparePartPricesMapper::getOrCreatePrice($attribs,$spare_part_price_id);
		//$spare_part_price_id = $spp->save();
		//$this->log('<br>SPP Guid was ' . $spare_part_price_id);// . '--' . uniqid(). '--' . uniqid() );
		$this->parse_assert(isset($spare_part_price_id),"No Spare_part_price_id was found in xmltag_spare_part_price()");
		$this->parse_assert($spare_part_price_id>0,"spare_part_price_id was '$spare_part_price_id' - it must be a positive number ");
		//$spp_id = $spp->save();
		$attribs['SPARE_PART_PRICE_ID'] = $spare_part_price_id;
		//var_dump_array(array('spare_part_price_id'=>$spare_part_price_id));
		//print "START -spare_part_price_Handler [$element]\n";		
		//var_dump($attribs);
		$this->log("Creating a Spare_Part_price ".$attribs['SUPPLIER_PART_NUMBER'].' spp.id='.$spare_part_price_id);
		/*Now determine the parent element and see what to do*/
		switch(strtoupper($parent_elem)){
			case 'SPARE_PART_PRICE': $this->parse_error("Error - SPARE_PART_PRICE can not be nested in another SPARE_PART_PRICE element.");
			case 'CAR_MODEL':
/*					$this->parse_assert(isset($parent_attribs['CAR_MODEL_ID'])
						,"Programming Error - this should have been caught earlier - No car_model_id in parent element");*/
					Bildelspriser_XmlImport_PriceHelper::StripCarModelForCmo2SppDataAndCreate($parent_attribs['CAR_MODEL_ID'],$attribs['SPARE_PART_PRICE_ID'], $parent_attribs);
				break;
			case 'CAR_MAKE': $this->parse_error("Please have a car_model between a SPARE_PART_PRICE and A CAR_MAKE!");
			case 'CAR_MODEL_TO_SPARE_PART_PRICE':
				switch(strtoupper($grand_parent_elem)){
					case 'CAR_MODEL':
						/*$this->parse_assert(isset($grand_parent_attribs['CAR_MODEL_ID'])
							,"Programming Error - this should have been caught earlier"
							." - No car_model_id in parent element");*/
						Bildelspriser_XmlImport_PriceHelper::StripCarModelForCmo2SppDataAndCreate(
							$parent_attribs['CAR_MODEL_ID'],$attribs['SPARE_PART_PRICE_ID'], $parent_attribs);
						break;							
					default: $this->parse_error('Unknown element under SPARE_PART_PRICE\CAR_MODEL_TO_SPARE_PART_PRICE\ called '.$grand_parent_elem.". ");
				}
			default: $this->parse_error('Unknown element under SPARE_PART_PRICE\\ called '.$grand_parent_elem.". ");					
		}
		
		$this->_spp_count++;
		$this->gen_start($xp,$element,$attribs);
	}
	
	function xmltag_spare_part_price_($xp, $element){
		$this->gen_end();
	}

	
	function endHandler($xp, $element){
		throw new exception("Still used?");
	}
	/*function cdataHandler($xp, $data){
		//Yes this is still called. Lets comment it out.
		< "START [$data]\n";
	}*/
	/**
	 * 
	 * @param $data XML Data
	 * @param $eof
	 * @return unknown_type
	 * @throws XmlParserException
	 */
	function parseString($data,$eof = false){
		  $timeparts = explode(' ',microtime());
		  $starttime = $timeparts[1].substr($timeparts[0],1);
	
		try{
			$this->log("ParseString started.");
			$result = parent::parseString($data,$eof);
			if (PEAR::isError($result)) {
				//$pe = new PEAR_Error();
				//$pe->
	    		$this->log($result->getMessage().'<br>ERROR: '.$result);
	    		return false;
			}
			else{
				$path = self::$_path;
				$this->log("ParseStringng ended without errors");
				/*$sppm = Default_Model_SparePartPricesMapper::getInstance('Default_Model_SparePartPricesMapper');
				$db_load_file = $path.'/spare_part_price_data.dat';
				//$sppm->SavePostponedValuesToFile($db_load_file);
				$cm2sppm = Default_Model_CarModelsToSparePartPricesMapper::getInstance('Default_Model_CarModelsToSparePartPricesMapper');
				$db_load_file = $path.'/car_models_to_spare_part_price_data.dat';				
				//$cm2sppm->SavePostponedValuesToFile($db_load_file);*/
								
				return true;
			}
			//	return $result;
			
		}
/*		catch(Zend_Db_Statement_Mysqli_Exception $zdb_st_ex){
			die($this->get_log_as_html()
//				."<br/>Message".$zdb_st_ex->getMessage();
			
			);
			
		}*/
		catch(Exception $e){
			$t = get_class($e);
			$info = "";
			switch($t){
				case 'Zend_Db_Statement_Mysqli_Exception':
					//$zdb_ex = new Zend_Db_Statement_Mysqli_Exception();
					$info .= "<br>Statement error<br>";
					$zdbe = new Zend_Db_Exception_Helper($e);
					$info .= "<br>SQL Statement = " . $zdbe->getSqlFormatedStatementAsString();
					$info .=$zdbe->getSqlAsInsertTable();
						
					break;
				default:
					break;
			};
			$msg = 'Exception of type '.$t.':<br>Message: '.$e->getMessage()
					.$info
					.'<br> In file: '.$e->getFile().':'.$e->getLine()."\n".$e->getTraceAsString();
			//."Exception dump ".nl2br(var_export($e,true))
			//."Type of ".get_class($e)
			die($this->get_log_as_html().nl2br($msg));		
			//throw $e;
		}// end exception
		$timeparts = explode(' ',microtime());
		$endtime = $timeparts[1].substr($timeparts[0],1);
		$this->log( "Timing was " . bcsub($endtime,$starttime,6));
		echo "Timing was " . bcsub($endtime,$starttime,6);
		
	}// end parseString	
	
	function parseFileName($fileName,$eof = false){
		  $timeparts = explode(' ',microtime());
		  $dbo = $this->_dbo;
		  $starttime = $timeparts[1].substr($timeparts[0],1);
		  $dbo->file_base_name = basename($fileName);
		  $dbo->file_create_time  = phpDateToDbDate(filectime($fileName));		  
		  $dbo->file_size = filesize($fileName);
		  $dbo->status = 'parsing file';
		  $this->log("before update");
		  try{$dbo->update();}
		  catch(exception $e){
		  	$this->log("Update throw an exception $e ");
		  }
		   $this->log("after update"); 	
		   $this->_spp_count=0;
		   $this->_cmo_count=0;
		   $this->_c2s_count=0;
		try{
			$this->log("Parsefilename started. $fileName ");
			parent::setInputFile($fileName);
			$result = parent::parse();
			$this->log("parse() ended");
			if (PEAR::isError($result)) {
				//$pe = new PEAR_Error();
				//$pe->
	    		$this->log($result->getMessage().'<br>ERROR: '.$result);
	    		$this->_dbo->{'status'} = 'failed';
	    		$this->_dbo->update();
	    		return false;
			}
			else{
				$path = self::$_path;
				$this->log("ParseStringng ended without errors");
				/*$sppm = Default_Model_SparePartPricesMapper::getInstance('Default_Model_SparePartPricesMapper');
				$db_load_file = $path.'/spare_part_price_data.dat';
				$sppm->SavePostponedValuesToFile($db_load_file);
				$cm2sppm = Default_Model_CarModelsToSparePartPricesMapper::getInstance('Default_Model_CarModelsToSparePartPricesMapper');
				$db_load_file = $path.'/car_models_to_spare_part_price_data.dat';				
				$cm2sppm->SavePostponedValuesToFile($db_load_file);
				$this->_dbo->{'status'} = 'success';
	    		$this->_dbo->update();				
				return true;*/
			}
			//	return $result;
			
		}
/*		catch(Zend_Db_Statement_Mysqli_Exception $zdb_st_ex){
			die($this->get_log_as_html()
//				."<br/>Message".$zdb_st_ex->getMessage();
			
			);
			
		}*/
		catch(Exception $e){
			$t = get_class($e);
			$info = "";
			switch($t){
				case 'Zend_Db_Statement_Mysqli_Exception':
					//$zdb_ex = new Zend_Db_Statement_Mysqli_Exception();
					$info .= "<br>Statement error<br>";
					$zdbe = new Zend_Db_Exception_Helper($e);
					$info .= "<br>SQL Statement = " . $zdbe->getSqlFormatedStatementAsString();
					$info .=$zdbe->getSqlAsInsertTable();
						
					break;
				default:
					break;
			};
			$msg = 'Exception of type '.$t.':<br>Message: '.$e->getMessage()
					.$info
					.'<br> In file: '.$e->getFile().':'.$e->getLine()."\n".$e->getTraceAsString();
			//."Exception dump ".nl2br(var_export($e,true))
			//."Type of ".get_class($e)
			die($this->get_log_as_html().nl2br($msg));		
			//throw $e;
		}// end exception
		$timeparts = explode(' ',microtime());
		$endtime = $timeparts[1].substr($timeparts[0],1);
		$this->log( "Timing was " . bcsub($endtime,$starttime,6));
		echo "Timing was " . bcsub($endtime,$starttime,6);
		
	}// end parseString	
}

class Zend_Db_Exception_Helper{
	var $_ex;
	var $_sql_stmt;
	public function __construct(Zend_Db_Exception $ex){
		$this->_ex = $ex;
	}
	public function getSqlStatementAsString(){
		if(isset($this->_sql_stmt))
			return $this->_sql_stmt;
		$zdbaa = TraceHelper::search_for_class($this->_ex->getTrace()
							,'Zend_Db_Adapter_Abstract');
		$stmt = $zdbaa['args'][0];
		foreach($zdbaa['args'][1] as $arg=>$val){
			/*echo "Value found<br>";
			echo var_dump($val);
			echo "<hr>";
			echo "val was $val ";
//			die('val2'.var_dump($zdbaa['args']).'val2 - end');*/
			$stmt = self::str_replace_first($stmt,'?',$val);
			//echo "Stmt[$arg]".$stmt;
		}
		if(empty($stmt)){
			throw new exception("Stmt was not found".var_dump($zdbaa));
		}
		//die('STMT'.var_dump($stmt));	
		$this->_sql_stmt = $stmt;
		return $stmt;
	}	
	
	public function str_replace_first($string,$find,$replace){
		$pos = strpos($string,$find);
		//echo "you want to str_replace_first($string,$find,$replace) ".'<hr>';
		//return substr($string,0,$pos-1).$replace.substr($string,$pos);
		//echo "and you get ".substr($string,0,$pos).'"'.$replace.'"'.substr($string,$pos+1);
		return substr($string,0,$pos).'`'.$replace.'`'.substr($string,$pos+1);
		//die("End");
	}
	
	public function formatSqlString($sql){
		$ret = $sql;
		//$ret = str_replace($sql,'(',"(\n");
//		$ret = str_replace($sql,'(',"(\n");
		//die("Format = ".$ret	);
		return $sql;	
	}
	
	public function getSqlFormatedStatementAsString(){
		return $this->formatSqlString($this->getSqlStatementAsString());
	}
	
	public function getSqlAsTable(){
		$stmt = $this->getSqlStatementAsString();
		$cmd = substr($stmt,0,6);
		switch($cmd){
			case 'INSERT': //UPDATE // SELECT
				return $this->getSqlAsInsertTable();
				break;
			default:
				throw new exception ("Not implemented yet cmd = $cmd ");
		}
	}
	
	public function getSqlAsInsertTable(){
		$stmt = $this->getSqlStatementAsString();
		//var_dump(array('statement'=>$stmt));
		$words = explode(' ',$stmt);
		$parts = explode('(',$stmt);
		$ins = explode(')',$parts[1]);
		$val = explode(')',$parts[2]);
		$c =  ' class="sql_info_table" ';
		$trtd = "\n\t\t<tr $c ><td $c >";		
		//var_dump($words);
		$table = '<table border=1px'.$c.' >'.$trtd.'TableName</th><th '.$c.' >'.$words[2].'</th></tr>';
		$table .= $trtd.'Insert</td><td>'.$ins[0].'<td>';
		$table .= $trtd.'td>Values</td><td>'.$val[0].'<td>';
		$ins_ar = explode(',',$ins[0]);
		$val_ar = explode(',',$val[0]);
		for($i=0;$i<sizeof($ins_ar);$i++){
			$table .= $trtd.$ins_ar[$i].'</td><td>';
			if(array_key_exists($i,$val_ar)){
				$table.=$val_ar[$i];
			}
			else
				$table.="NULL";
			$table.="</td></tr>\n";
		}
		return $table.'</table>';		
	}	
}



class TraceHelper{
	public static function search_for_class($trace,$class_name){
		if(empty($trace)){
			die("TraceHelper : trace was empty?");
		}
		//die("TraceHelper".var_dump_array($trace,'trace_name'));
		foreach($trace as $trace_line){
			if($trace_line['class']==$class_name)
				return $trace_line;
		}
		throw new exception('Trace Class '.$class_name.' was not found '.var_dump($trace));
	}
	public static function search_for_value_on_class($trace,$class_name,$argument_name){
		$class = self::search_for_class($trace,$class_name);
		$args = $class['args'];
		//var_dump($args);
		$all_args = "";
		foreach($args as $key=>$val){
			if($key == $argument_name){
				return $val;
			}
			$all_args .= " \n $key = $val ";
		}	
		throw new exception (" $class_name:: $argument_name was not found. Here is everything = ".$all_args);	
	}
}


class Bildelspriser_XmlImport_PriceHelper{
	static $priceParser;
	/**
	 * Returns a CarModelToSparePartPrice object with a valid SparePartPrice, CarMake & CarModel 
	 * attached to it. it is not saved, and that needs to be fone by the caller. 
	 * @param $car_model_id
	 * @param $spare_part_price_id
	 * @param $attribs
	 * @return Integer The insertId of the CarModelToSparePartPrice id created.
	 */
	static function StripCarModelForCmo2SppDataAndCreate($car_model_id,$spare_part_price_id,$attribs){
		self::parse_assert(isset($spare_part_price_id),"Spare Part price id was not set? ");
		self::parse_assert($spare_part_price_id<>0,"Spare Part price id was ".$spare_part_price_id."? ".$spare_part_price_id);
		self::parse_assert(isset($car_model_id),"Car_Model_id was not set? ");
		self::parse_assert($car_model_id<>0,"Car_Model_id was 0? ");
		$cmm2spp_array = array('CAR_MODEL_ID'=>$car_model_id,
					   'SPARE_PART_PRICE_ID'=>$spare_part_price_id);
		$log = " car_model_id=$car_model_id spare_part_price_id = $spare_part_price_id ";
		foreach($attribs as $key=>$value){
			$log.= "$key= '$value' ";
			switch($key){
				case 'CAR_MAKE_ID':  break; // skip to next
				case 'CAR_MODEL_NAME': break; // 
				case 'CAR_MAKE_NAME':  break; // skip to next
				case 'CAR_MODEL_ID': break;				
				case 'YEAR_TO': 
				case 'YEAR_FROM':
				case 'MONTH_FROM':
				case 'MONTH_TO': 
				case 'CHASSIS_NO_FROM': 
				case 'CHASSIS_NO_TO': 
				case 'CREATED_BY':
					$cmm2spp_array[$key]=$value;
					break;
				default:
			}
		}
		//var_dump_array($cmm2spp_array,"Cmm2spp array");
		//Bildelspriser_XmlImport_PriceParser::getInstance()->log("Saving a relationship between a Car Model and a Spare Part <br>$log ");
		
		$cmm2spp = new Default_Model_CarModelsToSparePartPrices($cmm2spp_array);
		//$cmi = $cmm2spp->getCar_model_id();
		//var_dump_array($cmm2spp_array,"CMI");
		//Bildelspriser_XmlImport_PriceParser::getInstance()->log("Saving model=$car_model_id & spp = $spare_part_price_id  ");
		//return $cmm2spp;
		$id = $cmm2spp->save();
		//Bildelspriser_XmlImport_PriceParser::getInstance()->log("Saving saved with Id = $id  ");
		self::getParser()->inc_c2s_count();
		return $id;
		//return $cmm2spp->save();		
	} 	
	/**
	 * @return Bildelspriser_XmlImport_PriceParser
	 */
	static private function getParser(){
		if(empty($priceParser)){
			self::$priceParser = Bildelspriser_XmlImport_PriceParser::getInstance('Bildelspriser_XmlImport_PriceParser');
		}
		return self::$priceParser;
	}
	
	static public function parse_assert($exp,$message){
		if($exp)
			return;
		self::getParser()->parse_error($message);
	}
}



//$parser = new demo_parser;
//$xml = "<PriceList><SomeContent</myElem>";
//$parser->parseString($xml);
