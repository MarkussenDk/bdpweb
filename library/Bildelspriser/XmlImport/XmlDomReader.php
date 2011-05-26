<?php
//Zend_Loader::loadC
include('XmlRequestPersist.php');
/*class XmlStringToXpathHelper{
	public static GetXmlPathObjectFromXmlString($xmlStringInput){		
		
	}	
	public static GetXmlNodesFromQuery($xmlStringInput,$query){		
		
	}	
}*/

function my_assert($expression_bool,$exception_message){
	if(!($expression_bool)){
		throw new Exception('Assertion failed: '.$exception_message);
	}
}

class DataValidator{
	public function Url_IsValidOrNull($value){
		if(empty($value)){
			return;
		}
	} 	
}


abstract class IXmlHandler{
	private $_tagName;
	private static $_userName;
	private $_modelArray;
	public function __construct($tagName){
		$this->_tagName = $tagName;		
	}	
	public function getTagName(){
		return $this->_tagname;		
	}	
	
	//abstract public function GetModelArray();
	

	/**
	 * Saves the username and parses the elements
	 * @param $XmlNodeList
	 * @param $userName
	 * @return unknown_type
	 */
	public function ParseAndSave(DOMNodeList $XmlNodeList,$userName){
		self::$_userName = $userName;
		$this->_modelArray = $this->GetModelArray();
		foreach($XmlNodeList as $DOMElement){
			$this->ParseAndSaveElement($DOMElement);
		}
	}	

	/**
	 * The function looks in the ModelArray to see if the class exists and if not adds it 
	 * with the right status ('Foreslag');
	 * @param $domElement A DOM Element to be saved
	 * @return unknown_type
	 */
	abstract public function ParseAndSaveElement(DOMElement $domElement);	
	
}

class CarMakesHandler extends IXmlHandler{
	public function GetModelArray(){
    	$model_carmakes = new Default_Model_CarMakes();
        $entries[] = $model_carmakes->fetchAll(' 1=1 '); // Only fetch Own and public??
        $ar_car_makes = array();
    	foreach($entries[0] as $entry){
    		$car_make_name = $entry->car_make_name;
    		$car_make_state = $entry->state;
   			$ar_car_makes[$car_make_name] = $car_make_state;
    	}  		
		return $ar_car_makes;
	}
	
	public function ParseAndSaveElement(DOMElement $domElement){
		if(empty(self::$_userName)){
			throw new exception(' self::$_userName was not defined on CarMakesHandler ');			
		}
		$entry_name = $Element->getAttribute('car_make_name');
		if($this->IsKnown('car_make_name',$entry_name)){
			continue;// Dont do anything - skip to next
		}						
  		$model = new Default_Model_CarMakes(array(	'car_make_name'=> $ar_remote['car_make_name'],
  										'created_by'   => self::$_userName,
  										'updated_by'   => self::$_userName
  		));
  		$model->save();		
	}
	
	public function IsKnown($modelUniqueKey,$uniqueValue){
		return array_key_exists($uniqueValue,$this->_modelArray[$modelUniqueKey]);
	}
}

class CarModelsHandler extends IXmlHandler{
	public function GetModelArray(){
    	$model_carmakes = new Default_Model_CarModels();
        $entries[] = $model_carmakes->fetchAll(' 1=1 '); // Only fetch Own and public??
        $ar_car_makes = array();
    	foreach($entries[0] as $entry){
    		$car_model_name = $entry->car_model_name;
    		$car_model_state = $entry->state;
   			$ar_car_makes[$car_model_name] = $car_model_state;
    	}  		
		return $ar_car_makes;
	}
	
	public function ParseAndSaveElement(DOMElement $domElement){
		if(empty(self::$_userName)){
			throw new exception(' self::$_userName was not defined on CarMakesHandler ');			
		}
		//assert(!empty(self::$_userName),)
		$entry_name = $Element->getAttribute('car_make_name');
		if($this->IsKnown('car_make_name',$entry_name)){
			continue;// Dont do anything - skip to next
		}						
  		$model = new Default_Model_CarMakes(array(	'car_make_name'=> $ar_remote['car_make_name'],
  										'created_by'   => self::$_userName,
  										'updated_by'   => self::$_userName
  		));
  		$model->save();		
	}
	
	public function IsKnown($modelUniqueKey,$uniqueValue){
		return array_key_exists($uniqueValue,$this->_modelArray[$modelUniqueKey]);
	}
}

class PriceHandler extends IXmlHandler{
	public function GetModelArray(){
    	$model_carmakes = new Default_Model_CarModels();
        $entries[] = $model_carmakes->fetchAll(' 1=1 '); // Only fetch Own and public??
        $ar_car_makes = array();
    	foreach($entries[0] as $entry){
    		$car_model_name = $entry->car_model_name;
    		$car_model_state = $entry->state;
   			$ar_car_makes[$car_model_name] = $car_model_state;
    	}  		
		return $ar_car_makes;
	}
	
/**
 * This function parses and saves Prices and the carMake or Model childs elements below it.
 * @see library/Bildelspriser/XmlImport/IXmlHandler#ParseAndSaveElement()
 */
	public function ParseAndSaveElement(DOMElement $domElement){
		if(empty(self::$_userName)){
			throw new exception(' self::$_userName was not set to a velue in object.');			
		}
		//$ar['spare_part_price_id']=$domElement->getAttributeNode('spare_part_price_id');
		$ar['name']=$domElement->getAttributeNode('name');
		$ar['description']=$domElement->getAttributeNode('description');
		$ar['spare_part_url']=$domElement->getAttributeNode('spare_part_url');
		$ar['spare_part_image_url']=$domElement->getAttributeNode('spare_part_image_url');
		$ar['spare_part_category_id']=$domElement->getAttributeNode('spare_part_category_id');
		$ar['spare_part_category_free_text']=$domElement->getAttributeNode('spare_part_category_free_text');
		if(!is_int($ar['spare_part_category_id'])){
			$ar['spare_part_category_id'] = null;
			// search in categories..
		}
		$ar['part_placement']=$domElement->getAttributeNode('part_placement');
		$ar['part_placement_left_right']=$domElement->getAttributeNode('part_placement_left_right');
		$ar['part_placement_front_back']=$domElement->getAttributeNode('part_placement_front_back');
		$ar['supplier_part_number']=$domElement->getAttributeNode('supplier_part_number');
		$ar['original_part_number']=$domElement->getAttributeNode('original_part_number');
		$ar['price_inc_vat']=$domElement->getAttributeNode('price_inc_vat');
		if(!is_numeric($ar['price_inc_vat'])){
			throw new exception("The price on item ['supplier_part_number'] ". $ar['supplier_part_number'] ." was not a number - '".$ar['price_inc_vat']."'");
		}
		$ar['producer_make_name']=$domElement->getAttributeNode('producer_make_name');
		$ar['producer_part_number']=$domElement->getAttributeNode('producer_part_number');
		$ar['created']=$domElement->getAttributeNode('created');
		$ar['created_by']=$domElement->getAttributeNode('created_by');
		
		$model = new Default_Model_SparePartPrices($ar);
		$model->save();
		
		$parentNodeName = $domElement->parentNode->nodeName;
		//switch($parentNodeName){		}
		 
		if(false === $domElement->hasChildNodes()){
			/**
			 * It is OK for prices, not do have a link to any cars or car models! So just return, you are finished.
			 */
			return;
		}
		
		$dnl_children = $domElement->childNodes;
		
		$car_model_handler = bildelspriser_ClassFactory::getXmlDomHandler('Car_Model');
		$car_make_handler = bildelspriser_ClassFactory::getXmlDomHandler('Car_Make');
		foreach($dnl_children as $dn){
			$name = tolower($dn->nodeName);
			switch($name){ 			
				case 'car_make':
					$car_make_handler->ParseAndSaveElement($dn);
					continue;
				default: 
					throw new Exception ("Unknown element '$name' under PriceList. Must contain Spare_Part_Price, Car_Model or Car_Model to ??");
			}	
			
			
		}
		
  		/*$model = new Default_Model_CarMakes(array(	'car_make_name'=> $ar_remote['car_make_name'],
  										'created_by'   => self::$_userName,
  										'updated_by'   => self::$_userName
  		));*/
  		$model->save();		
	}
	
	public function IsKnown($modelUniqueKey,$uniqueValue){
		return array_key_exists($uniqueName,$this->_modelArray[$modelUniqueKey]);
	}
}


class CarModelsToSparePartPricesHandler extends IXmlHandler{
	public function GetModelArray(){
		return; // you dont need it.
		Zend_Loader_Autoloader::loadClass('Default_Model_CarModelsToSparePartPrices');
    	$model = new Default_Model_CarModelsToSparePartPrices();
    	//retu
        $entries[] = $model->fetchAll(' 1=1 '); // Only fetch Own and public??
        $ar_car_makes = array();
    	foreach($entries[0] as $entry){
    		$car_make_name = $entry->car_make_name;
    		$car_make_state = $entry->state;
   			$ar_car_makes[$car_make_name] = $car_make_state;
    	}  		
		return $ar_car_makes;
	}
	
	public function ParseAndSaveElement(DOMElement $domElement){
		if(empty(self::$_userName)){
			throw new exception(' self::$_userName was not defined on CarMakesHandler ');			
		}
		$entry_name = $Element->getAttribute('car_make_name');
		$parentNodeName = $domElement->parentNode->nodeName;
		$mandatoryChildNodeName = "";
		switch(tolower($parentNodeName)){
			case 'car_model'		: $mandatoryChildNodeName = 'spare_part_price';
			case 'spare_part_price'	: $mandatoryChildNodeName = 'car_make';
			default: 
				throw new Exception ("You sent me a '$mandatoryChildNodeName', I was expecting a car_model or a spare_part_price?.");
		}
		if(!$domElement->hasChildNodes()){
			throw new Exception('You sent me a CarModelsToSparePartPrices with out any childNodes? If that is on purpose, contact support. ');			
		}
		$car_model_handler = bildelspriser_ClassFactory::getXmlDomHandler()->getXmlHandler('CarModel');
		$spare_part_price_handler = bildelspriser_ClassFactory::getXmlDomHandler()->getXmlHandler('Spare_Part_Price');
		foreach($domElement->getChildNodes() as $cn ){
			my_assert($mandatoryChildNodeName==tolower($cn->nodeName),
				"You gave me a '<$cn->nodeName>', and I was expecting a '<$mandatoryChildNodeName>' element. NodeValue = ".$cn->nodeValue);
			if('car_model'==tolower($cn->nodeName)){
				$car_model_id = (int) $cn->getAttribute('car_model_id');
				if($car_model_id>0) //proberly valid, let it pass
					continue;//finish this
				$car_model_handler->ParseAndSaveElement($cn);			
			}		
		}
		if($this->IsKnown('car_make_name',$entry_name)){
			continue;// Dont do anything - skip to next
		}						
  		$model = new Default_Model_CarMakes(array(	'car_make_name'=> $ar_remote['car_make_name'],
  										'created_by'   => self::$_userName,
  										'updated_by'   => self::$_userName
  		));
  		$model->save();		
	}
	
	public function IsKnown($modelUniqueKey,$uniqueValue){
		$ik = array_key_exists($uniqueValue,$this->_modelArray[$modelUniqueKey]);
		debug("IsKnown ($modelUniqueKey='$modelUniqueKey',$uniqueValue='$uniqueValue') gave $ik");
		return ik;
	}
}

class PriceListHandler extends IXmlHandler{
	public function GetModelArray(){
		throw new Exception("Dont call this function");
	}
	
	public function ParseAndSaveElement(DOMElement $domElement){
		if(empty(self::$_userName)){
			throw new exception(' self::$_userName was not defined ');			
		}
		if(false === $domElement->hasChildNodes()){
			throw new exception('XmlMessage <PriceList> Must contain child elements.');
		}
		$dnl_children = $domElement->childNodes;
		
		$price_handler = bildelspriser_ClassFactory::getXmlDomHandler('Spare_Part_Price');
		foreach($dnl_children as $dn_spp){
			$name = $dn_spp->nodeName;
			switch($name){ 			
				case 'Spare_Part_Price':
					$price_handler->ParseAndSaveElement($dn_spp);
					continue;
				default: 
					throw new Exception ("Unknown element '$name' under PriceList. Must contain Spare_Part_Price, Car_Model or Car_Model to ??");
			}	
			
			
		}
		$entry_name = $Element->getAttribute('car_make_name');
		if($this->IsKnown('car_make_name',$entry_name)){
			continue;// Dont do anything - skip to next
		}						
  		$model = new Default_Model_CarMakes(array(	'car_make_name'=> $ar_remote['car_make_name'],
  										'created_by'   => self::$_userName,
  										'updated_by'   => self::$_userName
  		));
  		$model->save();		
	}
	
	public function IsKnown($modelUniqueKey,$uniqueValue){
		return array_key_exists($uniqueName,$this->_modelArray[$modelUniqueKey]);
	}
}


class bildelspriser_ClassFactory{
	public static $_xmlDomReaderInstance;
	/**
	 * 
	 * @return bildelspriser_xmlDomReader
	 */
	public static function getXmlDomHandler(){
		if(empty(self::$_xmlDomReaderInstance)){
			self::$_xmlDomReaderInstance = new bildelspriser_xmlDomReader();
			self::$_xmlDomReaderInstance->addXmlHandler(new CarMakesHandler('car_make'));
			self::$_xmlDomReaderInstance->addXmlHandler(new CarModelsHandler('car_model'));
			self::$_xmlDomReaderInstance->addXmlHandler(new PriceHandler('spare_part_price'));
			self::$_xmlDomReaderInstance->addXmlHandler(new PriceListHandler('spare_part_price_list'));			
			self::$_xmlDomReaderInstance->addXmlHandler(new CarModelsToSparePartPricesHandler('car_models_to_ppare_part_prices'));			
		}
		return self::$_xmlDomReaderInstance;
	}	
}

/**
 * 
 * @author Andreas Markussen (andreas@markussen.dk)
 * The purpose of this class is to be the facade of receiver of XML 
 * The class will  be able to handle what ever of known XmlElements that is sent in
 */
class bildelspriser_xmlDomReader{
	 const E_AUTHENTICATED_USER_MISSING  = 00101;
	 const E_XML_MESSAGE_MISSING 		= 00201; 
	 const E_XML_UNKNOWN_ROOT_ELEMENT_NAME 		= 00301; 
	
	private $_handlers;
	public function __construct(){
			
	}
	
	public function addXmlHandler(IXmlHandler $newHandler){
		$_handlers[$newHandler->getTagName()]=$newHandler;		
	}
	
	
	/**
	 * 
	 * @param $HandlerName
	 * @return IXmlHandler The appropriate handler
	 */
	public function getXmlHandler($HandlerName){
		if(empty($this->_handlers[$HandlerName])){
			throw new Exception('Unknown root element in the request XML source. '.$HandlerName
				,self::E_XML_UNKNOWN_ROOT_ELEMENT_NAME); 
		}
		return $this->_handlers[$HandlerName];
	}
	
	public function SaveXmlContent($xmlString,$userName){
		if(empty($userName)){
			throw new Exception('UserName not defined while calling SaveXmlContent',self::E_AUTHENTICATED_USER_MISSING);
		}
		if(empty($xmlString)){
			throw new Exception('XmlString was empty while calling SaveXmlContent',self::E_XML_MESSAGE_MISSING);
		}
		$sim_xml = simplexml_load_string($xmlString);
		//$sim_xml->
		$sim_it = new SimpleXMLIterator($xmlString);
		$xdf = new DOMDocument($xmlString);
		$handler = $this->getXmlHandler(toupper($sim_it->getName()));
		$handler->ParseAndSaveElement($xdf);
		/*$dom_doc = new DOMDocument();
		$dom_doc->loadXML($xmlString);
		//$dom_doc->get
		foreach($this->_handlers as $tagName=>$handler){
			$handler->ParseAndSave($dom_doc->getElementsByTagName($tagName),$userName);	
		}	*/	
	}
	
	
	
	public function SaveXmlContent2($xmlString,$userName){
		if(empty($userName)){
			throw new Exception('UserName not defined while calling SaveXmlContent');
		}
		if(empty($xmlString)){
			throw new Exception('XmlString was empty while calling SaveXmlContent');
		}
		$dom_doc = new DOMDocument();
		$dom_doc->loadXML($xmlString);
		foreach($_handlers as $tagName=>$handler){
			$handler->ParseAndSave($dom_doc->getElementsByTagName($tagName),$userName);	
		}		
	}
	
	/**
	 * 
	 * 
	 * @param $xmlStringInput Xml String that the user has been sendt. 
	 * @param user_name Name of the AUTHENTICATED user.
	 * @return unknown_type
	 * @todo This function needs to be made generic - eg. use Chain of command and a facade pattern.
	 */	
	public function SaveCarMakes($xmlStringInput,$userName){
		$req = new DOMDocument();
    	$req->loadXML($xmlStringInput);
    	$xpath = new DOMXPath($req);
    	$query = "//CarMake[car_make_name]";
    	//$query = "//CarMake/*";
    	$elements = $xpath->query($query);
    	//$elements = $xpath->query("//CarMake/*");
    	
		if (!is_null($elements) /*&& sizeof($elements)>0*/) {
		  foreach ($elements as $element) {	// for each CarMake DOMNode
		  	if($element->nodeType == XML_ELEMENT_NODE){ //XML_ELEMENT_NODE = 1; // treat as DOMElement
		  		//echo "<br/>[". $element->nodeName.  "]"; // Check if dublicate if
			    $nodes = $element->childNodes;
			    foreach ($nodes as $node) {
			      $ar_remote[$node->nodeName] = $node->nodeValue;
			    }
		  		if(array_key_exists($ar_remote['car_make_name'],$ar_car_makes)){
		  			Die("Array key existed '$ar_car_makes'");
					//$db_state = $ar_car_makes[$user_car_make_name];
		  			/*if($db_state <> 'Offentlig' && $db_state <> 'Foreslag'){
		  				
		  			}*/			  			
		  		}
		  		else{ // This is actually a new an unknown brand name
		  			//$new_brand_names[] = $user_car_make_name; // cache these before we save them to the db.
		  			//die("\n<br/>".'UserName is = "'.$user_name.'" and p_user = '.$p_user);
		  			if(""==$userName){
		  				die(" Username is missing? ");
		  			}
		  			$model = new Default_Model_CarMakes(array(	'car_make_name'=> $ar_remote['car_make_name'],
		  														'created_by'   => $userName,
		  														'updated_by'   => $userName
		  			));
                	$model->save();			  			
		  		} // end else			  		
		  	}// end if Dom Node type is XML_ELEMENT_NODE	
		  }// End for loop that iterates over list of CarMakes
		}// End if elements is empty check			

	} 
	
}


?>