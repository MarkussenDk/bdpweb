<?php

class StringHelper{
	static function substrStartEnd($subject,$start,$end){
		$start_pos = strpos($subject, $start);
		$end_pos = strpos($subject, $end, $start_pos);
		if($start_pos<$end_pos){
			return substr($subject, $start_pos,$end_pos-$start_pos);			
		}
		return null;
	}	
}

/** 
 * @author Andreas
 * 
 * 
 */
class Bildelspriser_3rdParty_BilmodelDk {
	public $brands_by_id;
	public $brands_by_name;
	public $html_select;
	public $models_by_brand_id;
	

	function __construct() {
		$this->_loadBrands();
		//$this->getModelsByBrandId($this->brands_by_name['Alfa Romeo']);		
	}
	/**
	 * 
	 * Loads brands from bilmodel.dk ...
	 */
	public function _loadBrands(){
		$page = file_get_contents('http://bilmodel.dk');
		$select =  StringHelper::substrStartEnd($page, '<select name="brand"','</select>');
		$matches = array();
		//option value="1">Audi</option>
		$count = preg_match_all('/option value="(.+)">([^<]+)/i', $select, $matches);
		$ar_keys = $matches[1];
		$ar_vals = $matches[2];
		$options = array();
		$options_rev = array();
		for($i=0 ; $i<$count ; $i++){
			$options[$ar_keys[$i]] = $ar_vals[$i];
			$options_rev[$ar_vals[$i]] = $ar_keys[$i];
		}
		//Zend_Debug::dump($options,'$options',true);
		//Zend_Debug::dump($options_rev,'$options',true);
		$this->brands_by_id = $options;
		$this->brands_by_name = $options_rev;
		$this->html_select = $select;
	}
	
	public function getModelsByBrandId($bid){
		//echo "URL:".'http://bilmodel.dk/ajax.php?op=brandChange&bid='.$bid;
		$xml_models = file_get_contents('http://bilmodel.dk/ajax.php?op=brandChange&bid='.$bid);
		/**
		 *  split <model id="293">
		 *  		<name>240</name>
		 *  	   </model>
		 */ 
		//echo htmlentities($xml_models);
		$matches = array();
		$count = preg_match_all('/model id="(.+)">[^<]+<name>([^<]+)/i', $xml_models, $matches)	;
		$ar_keys = $matches[1];
		$ar_vals = $matches[2];
		//Zend_Debug::dump($matches,'$matches',true);
		$options = array();
		$options_rev = array();
		for($i=0 ; $i<$count ; $i++){
			$options[$ar_keys[$i]] = $ar_vals[$i];
			$options_rev[$ar_vals[$i]] = $ar_keys[$i];
		}
		return $options;
	}
	
	/**
	 * 
	 */
	function __destruct() {
	
		//TODO - Insert your code here
	}
}


?>