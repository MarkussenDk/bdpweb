<?php
//Zend_Loader::loadClass('ArrayToXML');
//Zend_Loader::loadClass('xmlModelHandler');
//Zend_Loader::loadClass('xmlDomReader','/');
//Zend_Loader::loadClass('Model_CarMakes');
require_once('../application/models/CarMakes.php');
require_once('../application/models/SparePartSuppliers.php');
require_once('../library/Bildelspriser/XmlImport/XmlDomReader.php');
require_once('../library/Bildelspriser/XmlImport/PriceParser.php');
require_once('../application/models/XmlHttpRequest.php');

ini_set('short_open_tags','On');
class XmlController extends Zend_Controller_Action
{
	
 	public function init()
    {
    
    }

    public function indexAction()
    {    	

    }
    
    public function schemaActionFailed_wrong_input(Exception $ex){
    	$str = "<h1>Unable to find an XSD file - Valid entries are</h1>";
    	$entries=array(
    		'car_makes',
    		'car_models',
    		'spare_part_prices',
    		'spare_part_prices_with_make_model_as_text'
    	);
    	$str .="\t\t<ul>\n";
    	$url='/xml/schema/class/';
    	foreach($entries as $entry){
    		$str.="\t\t\t<li><a href ='{$url}{$entry}' >$entry<a/></li>\n";
    	}
    	$str .="\t\t</ul>\n";
    	print $str;
    	print "<pre>".nl2br(var_export($ex,true))."</pre>";
    	phpinfo();
    }
    
    public function schemaAction(){
    	//var_export($this->getRequest());
    	$params = $this->_getAllParams();
		if(empty($params['class'])){
			schemaActionFailed_wrong_input(); 
		}   	
    	
    	//print var_export($params);
    	if(isset($params['class']))
    	{
    		//print 'So you want a class?';
    		$class_name = $params['class'];
    		$raw_xsd;
    		try{
    			$raw_xsd = xmlModelHandler::get_class_xsd_array($class_name);	
    		}
    		catch(FileDoesNotExistException $ex){
    			self::schemaActionFailed_wrong_input($ex);
    			return;
    			//die('FileDoesNotExistException');
    		}
    		
    		catch(Exception $ex){
    			self::schemaActionFailed_wrong_input($ex);
    			return;
    		}
    		
    		
    		unset($raw_xsd['created_by']);
    		//unset($raw_xsd['created']);
    		unset($raw_xsd['updated_by']);
    		//unset($raw_xsd['updated']);
    		//die(var_export($raw_xsd));
    		$xsd_array=array('class@name='.$class_name=>$raw_xsd);
    		$this->view->xml_document = ArrayToXML::toXml($xsd_array,'schema');
    		
    		$doc = new DOMDocument();
			$xsl = new XSLTProcessor();
			$xsl_filename = '..\library\simple_to_excel_schema.xsl';
			$doc->load($xsl_filename);
			//$this->view->xsl_document = $doc;
			$xsl->importStyleSheet($doc);
			
			//$doc->load($xml_filename);
			$doc->loadXML($this->view->xml_document);
			$this->view->xml_document = $xsl->transformToXML($doc);

    		
    		
    		/*
    		 * <?xml version=1.0 ?>
<xsd:schema xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="">
<xsd:element nillable="true" name="carMakes">
<xsd:complexType><xsd:sequence minOccurs="0">
<xsd:element minOccurs="0" maxOccurs="unbounded" nillable="true" name="carMake" form="unqualified">
<xsd:complexType><xsd:sequence minOccurs="0">
<xsd:element minOccurs="0" nillable="true" type="xsd:string" name="name" form="unqualified">
</xsd:element><xsd:element minOccurs="0" nillable="true" type="xsd:integer" name="id" form="unqualified">
</xsd:element>
</xsd:sequence>
</xsd:complexType>
</xsd:element>
</xsd:sequence>
</xsd:complexType>
</xsd:element>
</xsd:schema>
    		 * */    		
    	}
    }

    /**
     * 
     * @param $spare_part_supplier
     * @return Default_Model_SparePartSuppliers
     */
    private function authenticate(){
    	global $_SERVER;
/*        if(!isset($_SERVER['REDIRECT_REMOTE_USER'])){
        	header('WWW-Authenticate: Basic realm="bildelspriser.dk.Redirect"');
    		header('HTTP/1.0 401 Unauthorized');
    		echo "InAuth first " . $_SERVER['REDIRECT_REMOTE_USER'];
    		//die();
    	}    	
    	
    	if(isset($_SERVER['REDIRECT_REMOTE_USER'])){
    		echo "InAuth second " . $_SERVER['REDIRECT_REMOTE_USER'];
    		die();
    	} 	
    	else
    		echo "No_Server_Redirect_Remote_user<br/>";
    	//	die();
    	phpinfo();
    		return;
*/
    	if( !isset($_SERVER["PHP_AUTH_USER"]) ||
    		!isset($_SERVER["PHP_AUTH_PW"])){
    		header('WWW-Authenticate: Basic realm="Bildelspriser.dk"');
    		header('HTTP/1.0 401 Unauthorized');    				
    		echo("Please give a username and a password. See the XMLHTTP help for more details.</br>");
    		echo("<H2>User  = ".$_SERVER["PHP_AUTH_USER"].'pwd = '.$_SERVER["PHP_AUTH_PW"]."<H2/>");
    		//phpinfo();
    		}
    	//echo("<H2>".$_SERVER["PHP_AUTH_USER"].'pwd = '.$_SERVER["PHP_AUTH_PW"]."<H2/>");
   		$model_spare_part_supplier = new Default_Model_SparePartSuppliers();
   		$sps = new Default_Model_SparePartSuppliers(); 
   		$model_spare_part_supplier->authenticate($_SERVER["PHP_AUTH_USER"],$_SERVER["PHP_AUTH_PW"],$sps);
   		return $sps;
    }
    
    public function getIdentity(){
    	$identity = Default_Model_SparePartSuppliersMapper::getIdentity();
    	assertEx(isset($identity),"No identity found in XmlControler::getIdentity");
    }
    
    public function xmlhttpAction(){
    	/****** Finding the user **********/
    	$sps = $this->authenticate(); 
    	$user_name = $sps->getSupplier_admin_user_name();
    	assert(""<>$user_name);
    	$sps_id = $sps->getSpare_part_supplier_id();
    	
     	
    	$isXMLHTTP = "false";
    	$raw_body = "";
    	$method = "";
    	$req = $this->_request;
    	$header = $this->_getAllParams();
    	if($req->isXmlHttpRequest())
		{
		  //The request was made with JS XmlHttpRequest
		  $isXMLHTTP = "true";
		  //$rq = new Zend_Controller_Request_Http();
		  $raw_body = $this->_request->getRawBody();
		  //$isXMLHTTP.= $this->
		}    	
		$method = $req->getMethod();
    	/*
    	 *   'controller' => 'xml',
  			 'action' => 'add',
  			 'car' => 'Volvo',
             'module' => 'default',
             http://localhost/xml/add/car/Volvo/Model/V40/year/1999/engine/2.0/
             	                     /Key3/Val3/Key4/val4/Key5/Val5/
             array ( 'controller' => 'xml', 'action' => 'add',
              'car' => 'Volvo', 'Model' => 'V40', 'year' => '1999', 
              'engine' => '2.0', 'module' => 'default', )
    	 * 
    	 */
    	$params = $this->_getAllParams();
    	$xml="";
    	$xmlOutput="";

    	/****** Logging the request **********/
    	$xdr = new XmlRequestPersist();
    	global $_SERVER;
    	$xh = new Default_Model_XmlHttpRequest();
    	$xh->setCreatedBy($user_name);
    	$xh->setRequestPayload($raw_body);
    	$xh->getMapper()->save($xh);
    	
    	/******************************************
    	 * Run through existing Car Makes - from database
    	 *********************************************/
/*    	$model_carmakes = new Default_Model_CarMakes();
        $entries[] = $model_carmakes->fetchAll(' 1=1 '); // Only fetch Own and public??
        $xmlOutput = simplexml_load_string('<Root/>');
        $ar_car_makes = array();
    	foreach($entries[0] as $entry){
    		$car_make_name = $entry->car_make_name;
    		$car_make_state = $entry->state;
/*    		if(array_key_exists($car_make_name,$ar_car_makes)){
    			$org_val = $ar_car_makes[$car_make_name];
    			$car_make_state;
    			die('Dublicate - $car_make_name $org_val $car_make_state ');
    		}*--/
    		//else
    		{
  //  			$ar_car_makes[$car_make_name] = $car_make_state;
    		}
    	}   
*/
    	/************************************************
    	 * Check the incomming request content for tings to be added to the database
    	 ************************************************/
   	
    	$dr = new bildelspriser_xmlDomReader();
    	$xmlResponse="";
    	try{
			$xmlResponse = $dr->SaveXmlContent($raw_body,$user_name);
    	}
    	catch(Exception $ex){
    		$exCode = $ex->getCode();
    		
    		switch($exCode){
    			case 129: 
    				$xmlResponse = "No Request XML was received";
    				break;
    			case bildelspriser_xmlDomReader::E_AUTHENTICATED_USER_MISSING: 
    				$xmlResponse = "Invalid username and password";
    				break;	
    			default: 
    				$xmlResponse = "Uknown error - " . $ex->getMessage()."(".$exCode.")";
    		}	
    		//die(var_dump($ex));
    	}
		//$dr->SaveCarMakes($raw_body);
    	
    	$array=array(
    		//'request'=>$rq,
    		'arguments'=>$params,
    		'response'=>$xmlResponse,
    		//'raw_request'=>$raw_body,
    		'http_method'=>$method,
    		'is_XmlHttp'=>$isXMLHTTP
    	);
    	$xml = ArrayToXML::toXml($array,"message");
    	//phpinfo();
    	$xh->setFirstResponse($xml);
    	$xh->getMapper()->save($xh);
    	die($xml);
    }
    
    function file_get_contents_utf8($fn) { 
	     $content = file_get_contents($fn); 
	      return mb_convert_encoding($content, 'UTF-8', 
	          mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true)); 
	} 
    
    
    public function  xmlparseunittestAction(){    	
		print "<br>In XmlParseUnitTest - #1";
		print "<br>APPLICATION_PATH".APPLICATION_PATH;
		$p=dirname(APPLICATION_PATH).'/tests/data/price_list_file.xml';
		//print "<br> File path ".$p;    	
		//$p = 'C:\wamp\www\z18\soap\tests\data\price_list_file.xml';
    	print "<br> File path: ".$p;   
    	//stream_default_encoding('UTF-8'); 	
    	//stream_default_encoding('ISO-8859-1');
    	$xml_source = file_get_contents($p,FILE_TEXT);
    	$pos = strpos($xml_source,'Ø');
    	if($pos>0){
			$str = "String contained invalid chars : ".substr($xml_source,$pos,2);
			die($str);    	
    	}
    	print "<br> File Lenght: " . strlen($xml_source). " Bytes";
    	//$fp = fopen($p,'r');
    	//$xml_source = fread($fp,10000000);
		
    	//phpinfo();
    	print "<br/><hr/>".htmlentities($xml_source,ENT_QUOTES);    	
//    	die("");
		
    	//print "<pre>".$xml_source."</PRE>";
    	//assertEx($xml_source,"XML Source must not be empty".$xml_source);
		print "<hr/>";	

    	$spp_m = new Default_Model_SparePartSuppliersMapper();
		$spp_o = new Default_Model_SparePartSuppliers();
    	$spp_m->authenticate('ExcelAndreas','ExcelAndreas',$spp_o);
		//$spp = $spp_m->fetchRow($select = " `supplier_admin_user_name` = 'ExcelAndreas' ");
		//$spp_o = $spp_m->fetchObject($select);
//    	die();    	
    	//$spp_id = $spp['spare_part_supplier_id'];
    	$spp_id = $spp_o->getSpare_part_supplier_id();
		assertEx($spp_id,"No SparePartSupplier_id found");
    	$pp = new Bildelspriser_XmlImport_PriceParser($spp_o); 
    	echo 'ParseString Result: '.$pp->parseString($xml_source).'<hr/>';
    	
   		die($pp->get_log_as_html());
    }

    public function  xmlsaveunittestAction(){    	
		print "<br>In SaveUnitTest - #1";
		print "<br>APPLICATION_PATH is set to ".APPLICATION_PATH;
		//$p=dirname(APPLICATION_PATH).'/tests/data/price_list_file.xml';
		//print "<br> File path ".$p;    	
		//$p = 'C:\wamp\www\z18\soap\tests\data\price_list_file.xml';
    	//print "<br> File path: ".$p;   
    	//stream_default_encoding('UTF-8'); 	
    	//stream_default_encoding('ISO-8859-1');
    	/*$xml_source = file_get_contents($p,FILE_TEXT);
    	*
    	$pos = strpos($xml_source,'Ø');
    	if($pos>0){
			$str = "String contained invalid chars : ".substr($xml_source,$pos,2);
			die($str);    	
    	}
    	print "<br> File Lenght: " . strlen($xml_source). " Bytes";
    	//$fp = fopen($p,'r');
    	//$xml_source = fread($fp,10000000);
		
    	//phpinfo();
    	print "<br/><hr/>".htmlentities($xml_source,ENT_QUOTES);    	
//    	die("");
		
    	//print "<pre>".$xml_source."</PRE>";
    	//assertEx($xml_source,"XML Source must not be empty".$xml_source);
		print "<hr/>";	
		*/
    	echo "<b><br>Testing SparePartSuppliersMapper::authenticate()</b>";
		$spp_m = new Default_Model_SparePartSuppliersMapper();
		$spp_o = new Default_Model_SparePartSuppliers();
    	$spp_m->authenticate('ExcelAndreas','ExcelAndreas',$spp_o);
		//$spp = $spp_m->fetchRow($select = " `supplier_admin_user_name` = 'ExcelAndreas' ");
		//$spp_o = $spp_m->fetchObject($select);
//    	die();    	
    	//$spp_id = $spp['spare_part_supplier_id'];
    	$spp_id = $spp_o->getSpare_part_supplier_id();
		assertEx($spp_id,"No SparePartSupplier_id found");
		echo "<br>SUCCESS: SparePartSupplierId was found - and authenticated";
    	//$pp = new Bildelspriser_XmlImport_PriceParser($spp_o); 
    	//echo 'ParseString Result: '.$pp->parseString($xml_source).'<hr/>';
    	echo "<b><br>Testing CarMakesMapper::getCarMakeIdByCarMakeName()</b>";
		$car_make_name = 'Volkswagen';
		$cmi = Default_Model_CarMakesMapper::getInstance()->getCarMakeIdByCarMakeName($car_make_name);
		assertEx(isset($cmi),"ERROR car_make_id must not be empty - it was empty.");
		assertEx(is_int($cmi),"ERROR car_make_id must be an integer - it was ".gettype($cmi));
		assertEx($cmi>0,"ERROR car_make_id must be larger than 0 - primary keys must be a positive number");
		echo "<br>SUCCESS: Car Model I for '$car_make_name' was was $cmi";
		   		//   		die($pp->get_log_as_html());
   		die("<br>");//To advoid a script file - xmlsaveunittest.phtml
    }
    
    public function implicitflushtestAction(){
    	$count = 100;
    	while($count-->0){
    		print "<br>Count was ".$count;
    		flush();
    		sleep(2);
    	}
    	die("");// No script file
    }
    
    public function showAction()
    {
    	$sps = $this->authenticate(); 
    	$user_name = $sps->getSupplier_admin_user_name();
        //$this->xml_document;
    // the XSD file is http://localhost/xml/schema/class/spare_part_prices
    //mySQL version - Serverversion: 5.0.32-Debian_7etch10-log
    /* tasks
     * Fetch the class name
     * instansiate the class
     * call ->getWhere(row_user_id = current_user_id or status = approved)
     * in the for loop - do the following magic - using strings? Or using simpleXML and ArrayToXML.
     *  - <className primary_key_name ='primar_key_value' >
     *     <row1>value1</row1>
     *     <row2>value2</row2>
     *    </className>
     * add the right schema
     * validate the document agains the schema
     * send it to excel or what ever the client is. 
     * http://office.microsoft.com/en-us/excel/HP010419331033.aspx
     */
    $xml = "<info>No data yet</info>";
    $class = $this->getRequest()->getParam('class');
    if( !isset($class) or strlen($class)<1 )
    {
    	$xml =  "<a href='help_link.php?id=class_is_not_set&ex=Class_is_not_set' >Help Link</a>";
    }
    else
	    //try
	    {
	    	//xmlModelHandler::Load_Generated_Class($class);
	    	$model_name = 'Default_Model_'.$class;
	    	$model = new $model_name;
	    	//$own_user = 'Andreas';
	    	$status_approved = 'Offentlig';
	    	$where_txt = "`created_by` ='$user_name' or `state` = '$status_approved' ";
	    	$where_ar = array('created_by = ? '=>$user_name,'state = ?'=>$status_approved);
	    	//$select = $model->getMapper()->getDbTable()>select()->where($where_ar);	    	
	    	$entries = $model->fetchAll_Array($where_txt);
	    	$xml1 = '<'.$class.'List/>';
	    	//var_export($entries);
	    	//$xml = ArrayToXML::toXml($entries,$class.'List',$xml1);
	    	$sxe = New SimpleXMLElement($xml1);
	    	/*  xmlns="http://www.w3schools.com"
xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
xsi:schemaLocation="http://www.w3schools.com note.xsd">*/
//	    	$sxe->addAttribute('xmlns:xsi','http://www.w3.org/2001/XMLSchema-instance');
	    	global $_SERVER;
	    	$xsd_location = 'http://'.$_SERVER['SERVER_NAME'].'/xml/schema/class/'.$class;
	    	$xsd_location = 'http://'.$_SERVER['SERVER_NAME'].'/xml/schema/class/'.'car_makes';
//	    	$sxe->addAttribute('xsi:schemaLocation',$xsd_location);
	    	//$sxe_List = $sxe->addChild('List'); 
	    	foreach($entries as $item){
	    		$class_item = $sxe->addChild(trim($class,'s'),'');
	    		//var_export($item);
	    		foreach($item as $element=>$value){
	    			
	    			if(// $own_user!='Andreas' and 
	    				strpos($element,'_by')>1  
	    				){
	    				continue; //we dont want to tell who did what.
	    				//Consider md5($value)
	    			}
	    			$class_item->addChild(trim('Car_make_name','_'),$item->getCar_make_name());
	    		}
	    		//$class_item->addChild('car_make_name',$item->_car_make_name);
	    		//$class_item->addChild('car_make_name',$item['_car_make_name']);
	    		$class_item->addChild(trim('Car_make_name','_'),$item->getCar_make_name());
	    	}
	    	$xml = $sxe->asXML();
	    }
	    flush();
	    die(trim($xml));
    }
}

