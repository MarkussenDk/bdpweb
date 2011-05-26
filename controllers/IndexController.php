<?php
//require 'ZEND\form.php';
//Zend_Loader::loadClass('xmlModelHandler');
//Zend_Loader::loadClass('xmlDomReader','/');
//Zend_Loader::loadClass('Model_CarMakes');
//require_once('../application/models/CarMakes.php');
//require_once('../application/models/SparePartSuppliers.php');
//require_once('../library/Bildelspriser/XmlImport/XmlDomReader.php');
//require_once('../library/Bildelspriser/XmlImport/PriceParser.php');
//require_once('../application/models/XmlHttpRequest.php');
 $i = 4;
class IndexController extends Zend_Controller_Action
{
    public function init()
    {
    	setlocale(LC_ALL,'da-DK');
    	Default_Model_XmlHttpRequest::readOrCreateUserCookie();
    	//$this->view->dojo()->disable(); 
    	$this->view->setEncoding('UTF-8'); 
    	$this->view->navigation()
    		->addPage(array( 'label' => 'Find', 'action' => 'index', 'controller'=>'index'))
    		->addPage(array('label' =>  utf8_encode('Alle Bilmærker'),
 //   						'module'     => 'default',
               	'controller' => 'index',
                'action'     => 'brands' 		
    		))
    		->addPage(array(
	    		'action'=>'news',
	    		'controller'=>'index',
	    		'label'=>'Om Bildelspriser.dk'	))
    		;
    	$xh = new Default_Model_XmlHttpRequest();
    	//$xh->setCreatedBy($user_name);
    	$xh->getMapper()->save($xh);
    	
    	
    	$view = $this->view;
		$view->headTitle()->prepend('Bildelspriser.dk - '); 
    	$view->headTitle()->setSeparator(' ');
    	$this->view->headMeta()->appendHttpEquiv('Content-Type','text/html; charset=utf-8');
    	
    	//die(var_dump($view));
    	
    }  
    
    function file_get_contents_utf8($fn) { 
	     $content = file_get_contents($fn); 
	      return mb_convert_encoding($content, 'UTF-8', 
	          mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true)); 
	}

    public function indexOldAction()
    {    	
    	Zend_Dojo::enableView($view);
    	$view->dojo()->setDjConfigOption('usePlainJson',true)
    				 ->addLayer('/js/bdp/main.js');
    				 //->addJavascript('bdp.main.init();');*/
    	
		print "<br>In XmlParseUnitTest - #1";
		print "<br>APPLICATION_PATH".APPLICATION_PATH;
		$p=dirname(APPLICATION_PATH).'/tests/data/price_list_file.xml';
		//print "<br> File path ".$p;    	
		//$p = 'C:\wamp\www\z18\soap\tests\data\price_list_file.xml';
    	print "<br> File path: ".$p;    	
    	$xml_source = file_get_contents($p);
    	//$xml_source = mb_convert_encoding($xml_source,'ISO-8859-1');
    	//$xml_source = mb_convert_encoding($xml_source,'UTF-8','auto');
    	//$xml_source = utf8_decode($xml_source);
        $pos = strpos($xml_source,'ï¿½');
    	if($pos>0){
			$str = "<HR>String contained invalid chars : ".substr($xml_source,$pos,2)
				.'HEx Value '
					.dechex(ord(substr($xml_source,$pos,1))).' '
					.dechex(ord(substr($xml_source,$pos+1,2)));
			//die($str);    	
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
    	$spp_m->authenticate($user_name,$some_password,$spp_o);
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
    
    public function newsAction(){
    	
    }
    
    public function dojoAction(){
    	Zend_Dojo::enableView($this->view);
    	$this->view->dojo()->setDjConfigOption('usePlainJson',true)
    				 ->addLayer('/js/bdp/main.js');
    				 //->addJavascript('bdp.main.init();');*/
    	
     	/*$html->$view->contentPane(
			'grid',
			'GridDemo is Loading',
			array('title'=>'GridDemo'
				  'preLoad' => 'true',
				  'href' => 'index/dojogridproces/format/html',
				  'parseOnLoad' => true		
			)
		)*/
    	
    	$form = new Default_Form_DojoSearch();    	
    	$this->view->form = $form;
    }

    public function testAction(){
    	echo "<h1>Test</H1>";
    	$car_mapper = Default_Model_SparePartPricesMapper::getInstance('Default_Model_SparePartPricesMapper');
    	$car_mapper->fillCache(5);
    	$car_mapper->fillCache(6);
    	$car_mapper->fillCache(5);
    	//$car_mapper->fillCacheDirect(6);
    	//$car_mapper->fillCacheAll();
    	die("x");
    }
    
    public function indexAction(){
    	$this->view->headMeta()->appendHttpEquiv('Content-Type','text/html; charset=utf-8');
     	/*$html->$view->contentPane(
			'grid',
			'GridDemo is Loading',
			array('title'=>'GridDemo'
				  'preLoad' => 'true',
				  'href' => 'index/dojogridproces/format/html',
				  'parseOnLoad' => true		
			)
		)*/    	
    	$form = new Default_Form_HtmlSearch();    	
    	//	$this->view->form = " ".str_replace('MÃ¦rke','Mærke',$form);
    	$this->view->form = utf8_decode($form);    	
    }    
    
   public function autocompletecarmakesAction()
    {
    	$sps = new Default_Model_SparePartSuppliers(array('supplier_admin_user_name'=>"WebUser"));
    	Default_Model_SparePartSuppliersMapper::setIdentity($sps);
    	// First, get the model somehow
        $this->view->model = $this->getCarMakesModel();

        // Then get the query, defaulting to an empty string
        $this->view->request = $this->getRequest();
        $this->view->params = $this->_getAllParams();
    }
    
   public function autocompletecarmodelsAction()
    {
    	$sps = new Default_Model_SparePartSuppliers(array('supplier_admin_user_name'=>"WebUser"));
    	Default_Model_SparePartSuppliersMapper::setIdentity($sps);
    	
        // First, get the model somehow
        $this->view->model = $this->getCarModelsModel();

        // Then get the query, defaulting to an empty string
        $this->view->request = $this->getRequest();

        $this->view->params = $this->_getAllParams();
    }
    
    public function getCarMakesModel(){    	
    	return new CarMakesModel();
    }
    
    public function getCarModelsModel(){    	
    	return new CarModelsModel();
    }
    
    public function brandsAction(){
        $this->view->model = $this->getCarMakesModel();
        $this->view->params = $this->_getAllParams();
		$this->view->car_model = $this->getCarModelsModel();
		$searcher;
		if(array_key_exists('car_model_id',$this->view->params)){
			$car_model_id = $this->view->params['car_model_id'];
			$searcher = new Default_Model_SparePartPriceSearcer((integer)$car_model_id);
			$all_names = $searcher->allNames();	
			$this->view->all_names = $all_names;
			//echo "Bildele til $car_model_id ";
			//die(nl2br(var_dump($all_names)));
			
		    if(array_key_exists('q',$this->view->params)){
				$q = $this->view->params['q'];
				$this->view->q = $q;
				$prices = $searcher->runSearch(urldecode($q));
				$this->view->prices = $prices;
				
			}
		}
		

		/*else
			die(print_r($this->view->params));*/
    }

    public function htmlAction(){
        $this->view->model = $this->getCarMakesModel();
        $this->view->params = $this->_getAllParams();
		$this->view->car_model = $this->getCarModelsModel();
    }
    
    
	public function phpinfoAction(){
	
	}
	

	public function dojogridpricesAction(){
		/*$html->$view->contentPane(
			'grid',
			'GridDemo is Loading',
			array('title'=>'GridDemo'
				  'preLoad' => 'true',
				  'href' => 'index/dojogridproces/format/html',
				  'parseOnLoad' => true,
				  'isDebug' => true	
			)
		)*/
	}
	
	public function getDB(){
		$mapper = Default_Model_SparePartPricesMapper::getInstance('Default_Model_SparePartPricesMapper');
		return $mapper->getDbAdapter();
	}
	
	public function getDataSet($q, $car_model_id,$year,$model){
		$db = $this->getDB();
		
		
	}
	
	public function newsearchAction(){
	    $this->view->request = $this->getRequest();
        $this->view->headMeta()->appendHttpEquiv('Content-Type','text/html; charset=utf-8');
        $this->view->params = $this->_getAllParams();	
		$q = "";
		$car_model_id;
		if(array_key_exists('q',$this->view->params)){
			$q = $this->view->params['q'];
		}
		if(array_key_exists('limit',$this->view->params)){
			$limit = (int)$this->view->params['limit'];			
			if(!is_integer($limit)){
				$trace .= "Limit was not an integer '$limit' ";
				$limit = 48;
				//die('Limit must be an integer, it was '.var_dump($car_model_id));	
			}
			if($limit < 1)
				$limit = 49;
			$limit = " LIMIT ".$limit;
		}
		$car_model_name;
		$car_make_name;
		$car_model_row;
		$car_make_row;
		if(array_key_exists('car_model_id',$this->view->params)){
			$car_model_id = (int)$this->view->params['car_model_id'];
			if(is_int($car_model_id) && $car_model_id>0){
				$model_mapper = Default_Model_CarModelsMapper::getInstance('Default_Model_CarModelsMapper');
				$cmo = new Default_Model_CarModels();
				$car_model=$model_mapper->find($car_model_id); 
				//var_dump($car_model);
				if(is_a($car_model,'Zend_Db_Table_Rowset')){
					if(count($car_model)!=1){
						die("Data error - count(car_model) was ".count($car_model));
					}
					$car_model_row = $car_model->current();
					if(!is_a($car_model_row,'Zend_Db_Table_Row')){
						die("Type error car_model_row " . var_export($car_model_row,true));
					}
					$car_model_id = $car_model_row->car_model_id;
					$car_make_id = $car_model_row->car_make_id;			
				}
				if(isset($car_model_name)){
					$this->view->car_model_name = $car_model_name;
				}	
				$cma = Default_Model_CarMakes::getMakeByIdAsObject($car_make_id);
				$car_make_name = $cma->getCar_make_name();
				$this->view->car_make_name = $car_make_name;
				//$this->view->car_model_name = $car_model_name;
				//var_dump($cma);
				//$cm = Default_Model_CarMakesMapper::getMakes();
			}				
		}
		else if(array_key_exists('car_model_name',$this->view->params)
			&& array_key_exists('car_make_id',$this->view->params)){
			$car_model_id = Default_Model_CarModels::getCarModelIdByCarModelNameAndCarMakeId(
					$this->view->params['car_model_name'],
					$this->view->params['car_make_id']); 
			echo "<!-- inNewSearchAction car_model_id was now $car_model_id -->";		
		}
		if(!is_integer($car_model_id)){
			echo "<!-- inNewSearchAction car_model_id was not set -->";
		}
		$searcher = new Default_Model_SparePartPriceSearcer($car_model_id);
		$searcher->searchPrices($q);
		$this->view->suggested_names = $searcher->_suggested_names;
		$this->view->car_model_id = $car_model_id;
		if(isset($searcher->_user_message)){
			$this->view->user_message = $searcher->_user_message;
		}
		if(isset($searcher->_found_spare_part_prices_results)){
			$count = count($searcher->_found_spare_part_prices_results);
			echo "<!--SparePartPricesFound $count -->";
			$this->view->spare_part_prices_results = $searcher->_found_spare_part_prices_results;
		}
		elseif(isset($searcher->_found_spare_part_categories)){
			echo "<!--_found_spare_part_categories-->";
			$this->view->found_spare_part_categories = $searcher->_found_spare_part_categories;
		}
		else {die("Vi kunne ikke finde nogle reservedele til din bil.");}
		if(isset($searcher->_results))
			$this->view->results = $searcher->_results;
		$this->view->sql;
	}
	
	public function jumptoAction(){
        $this->view->params = $this->_getAllParams();
        $spp_id;
        if(array_key_exists('spare_part_price_id',$this->view->params)){
        	$spp_id = (int)$this->view->params['spare_part_price_id'];
        }
        if(!(isset($spp_id) && ($spp_id>0)) ){
        	die("SPP Was not set");
        }
        $spp = new Default_Model_SparePartPrices(array('spare_part_price_id'=>$spp_id));
        $spp->find($spp_id);
        $this->view->spare_part_price = $spp;
	}

	
	public function detailsAction(){
        $this->view->params = $this->_getAllParams();
        $spp_id;
        if(array_key_exists('spare_part_price_id',$this->view->params)){
        	$spp_id = (int)$this->view->params['spare_part_price_id'];
        }
        if(!(isset($spp_id) && ($spp_id>0)) ){
        	die("SPP Was not set");
        }
        $spp = new Default_Model_SparePartPrices(array('spare_part_price_id'=>$spp_id));
        $spp->find($spp_id);
        $this->view->spare_part_price = $spp;
	}
	
	public function datasearchresultsAction(){
		$obs = array('supplier_admin_user_name '=>"WebUser");
    	$sps = new Default_Model_SparePartSuppliers($obs);
    	Default_Model_SparePartSuppliersMapper::setIdentity($sps);
		
        $this->view->request = $this->getRequest();
        $this->view->headMeta()->appendHttpEquiv('Content-Type','text/html; charset=utf-8');
        $this->view->params = $this->_getAllParams();		
		$q = "";
		if(array_key_exists('q',$this->view->params)){
			$q = $this->view->params['q'];
		}
		if(array_key_exists('limit',$this->view->params)){
			$limit = (int)$this->view->params['limit'];			
			if(!is_integer($limit)){
				$trace .= "Limit was not an integer '$limit' ";
				$limit = 48;
				//die('Limit must be an integer, it was '.var_dump($car_model_id));	
			}
			if($limit < 1)
				$limit = 49;
			$limit = " LIMIT ".$limit;
		}
		
		$db = $this->getDB();
		/* 'spare_part_price_id' => 1,
    'name' => 'Udstï¿½dningsrï¿½r',
    'description' => 'Dette er udstï¿½dningsrï¿½ret til en gammel Folkevogn ï¿½ ï¿½ ï¿½ ï¿½ ï¿½ ï¿½',
    'spare_part_url' => 'http://www.biltema.dk/carparts/carpart_info.asp?iSecId=1642&strCarId=Volkswagen&iItemId=132243&iRelItemId=132244',
    'spare_part_image_url' => 'http://www.biltema.dk/Archive/ProductCat/bil/Avgassystem.jpg',
    'spare_part_category_id' => NULL,
    'spare_part_category_free_text' => NULL,
    'part_placement' => 'middle',
    'part_placement_left_right' => NULL,
    'part_placement_front_back' => NULL,
    'supplier_part_number' => '21341234',
    'original_part_number' => '02-3214',
    'price_inc_vat' => '1250.00',
    'producer_make_name' => 'Bosch',
    'producer_part_number' => NULL,
    'spare_part_supplier_id' => 3,
    'created' => '2010-03-06 08:10:09',
    'created_by' => 'ExcelAndreas',
    'updated' => '2010-03-06 08:10:09',/*/
		
		//if(array_key_exists('sort_by',$this->view->params))
		$where_and = " ";
		$table_and = " ";
		$limit = " limit 30 ";
		$view_name = "sps_spp_cm2spp_cmo_v";
		
		// IF(supplier_city IS NULL, 'n/a',supplier_city) 
		$id = " concat(IF(v.car_model_id IS NULL,' ',v.car_model_id),'-', v.spare_part_price_id) "; 
		$trace = "";
		if(array_key_exists('q',$this->view->params)){
			$q = $this->view->params['q'];
			$where_and .= " and `name` LIKE '%$q%' ";
		}
		
		if(array_key_exists('LIMIT',$this->view->params)){
			$limit = (int)$this->view->params['limit'];			
			if(!is_integer($limit)){
				$trace .= "Limit was not an integer '$limit' ";
				$limit = 98;
				//die('Limit must be an integer, it was '.var_dump($car_model_id));	
			}
			if($limit < 1)
				$limit = 99;
			$limit = " LIMIT ".$limit;
		}
		
		if(array_key_exists('car_model_id',$this->view->params)){
			$id = " v.spare_part_price_id ";
			$car_model_id = (int)$this->view->params['car_model_id'];			
			if(!is_integer($car_model_id)){
				$trace .= "'Car_model_id must be an integer, it was '$car_model_id' ";	
				$where_and .= " and `car_model_name` = '$car_model_name' \n ";
			}
			else { // hack but works --  car_model_id is an integer
				$where_and .= " and `car_model_id` = '$car_model_id' \n ";
			}
		//	$where_and.= " and car_models_to_spare_part_prices.spare_part_price_id = spare_part_prices.spare_part_price_id ";
		//	$table_and.= " ,car_models_to_spare_part_prices  \n";
		}
		
//		$sps = '`spare_part_suppliers`';
//		$spp = '`spare_part_prices`';
		//$sql = "SELECT  name, description, spare_part_image_url, price_inc_vat, producer_make_name, part_placement,supplier_part_number from spare_part_prices LIMIT 30;";
		$sql = "SELECT $id as id," 
			. " v.* "
		//	. "spare_part_prices.spare_part_price_id as id, spare_part_prices.name as name "
		//	.", description, spare_part_image_url "
		//	.", spare_part_url, part_placement, supplier_name, car_model_name"
		//	.", price_inc_vat, producer_make_name, part_placement, supplier_part_number, supplier_name\n" 
		    . " FROM $view_name v "
		    . "WHERE 1=1 \n"
//		    . " and spare_part_prices.spare_part_supplier_id = "
//			.  "  and spare_part_prices.spare_part_price_id is not null "	
		    . $where_and
		    . $limit;
		    //die($sql);
		try{    
			$this->view->sql = $sql;			
			$results = $db->fetchAll($sql);
			$this->view->exception = false;			
		}
		catch(exception $e){
			$html = "<h2>sql</h2><br>".nl2br($sql);
			$html.= "<br>Error<br>".nl2br($e);
			//die($html);
			$res = array('id'=>'error','name'=>nl2br($e),'description'=>$sql);
			$results = array($res);
			$this->view->exception = true;
			}
                $count = count($results);
                if($count==0){
                    $res = array('id'=>'info','name'=>"Inden priser fundet, da vi s&oslash;gte efter '".$q."'",'description'=>$sql);
                    $results = array($res);
                }
                if(!is_integer($count)){
                    die("Count was not an integer??" + var_export($count, true));

                }
		if(Default_Model_XmlHttpRequestMapper::$last_object != null){
			$lo = Default_Model_XmlHttpRequestMapper::$last_object;
			$lo->setSqlUsed($sql);
			$q .= "";
			$lo->setQ($q);
                        $lo->setDataRowsReturned($count);
			//$lo->set()
			$lo->save();
		}
		$this->view->results = $results;		
	}
	
}// end of Class IndexController

class AjaxModels{
	public function __construct(){
		$this->debug = false;
	}
	var $debug;
	public function setDebug(){
		$this->debug = true;
		$this->debugOut('setDebug():: Debug is enabled');
	}
	public function debugOut($msg){
		if($this->debug)
			echo "</br>".$msg;
	}	
}

class CarModelsModel extends AjaxModels{
	public function query($params){
		/** this code depends on the following SQL to be processed first to ignore case.
		 * ALTER TABLE  `car_makes` 
		 * CHANGE  `car_make_name`  `car_make_name` VARCHAR( 255 ) 
		 * CHARACTER SET utf8 COLLATE utf8_danish_ci NOT NULL */
		
		$mapper = new Default_Model_CarModelsMapper();
		//$mapper = Default_Model_CarMakesMapper::getInstance();
		$select = $mapper->createSelect();
		$select->from(array('cm'=>'car_models'),
					  array('id'=>'car_model_id','name'=>'car_model_name'));
		//$select->where(" `state` != 'Foreslag' ");
		
		if(is_array($params) && array_key_exists('q',$params)){
			$params['car_model_name'] = $params['q'];
		}
		
		if(is_array($params) && array_key_exists('car_model_name',$params)){
			$this->debugOut('So you now know model to look for?');
			$car_model_name = str_replace("'","",$params['car_model_name']);
			$car_model_name = str_replace("*","",$car_model_name); // dont know why it appends a star?
			$this->debugOut("And that is a good choice  ".$car_model_name." ");
			$select->where("  `car_model_name` like '%$car_model_name%' ");
		}		
		
		if(is_array($params) && array_key_exists('car_make_id',$params)){
			$this->debugOut('So you now know car_make to look for?');
			$car_make_id = str_replace("'","",$params['car_make_id']);
			$this->debugOut("And that is a good choice  ".$car_make_id." ");
			$select->where("  `car_make_id` = '$car_make_id' ");
		}
		$select->order("car_model_name");
		$ar = $mapper->fetchAll_Array($select);
		return $ar;		
	}
}



class CarMakesModel extends AjaxModels{
	public function query($params){
		/** this code depends on the following SQL to be processed first to ignore case.
		 * ALTER TABLE  `car_makes` 
		 * CHANGE  `car_make_name`  `car_make_name` VARCHAR( 255 ) 
		 * CHARACTER SET utf8 COLLATE utf8_danish_ci NOT NULL */
		
		$mapper = new Default_Model_CarMakesMapper();
		//$mapper = Default_Model_CarMakesMapper::getInstance();
		$select = $mapper->createSelect();
		$select->from(array('cm'=>'car_makes'),
					  array('id'=>'car_make_id','name'=>'car_make_name'));
		//$select->where(" `state` != 'Foreslag' ");
		
		if(is_array($params) && array_key_exists('q',$params)){
			$params['car_make_name'] = $params['q'];
		}
		
		if(is_array($params) && array_key_exists('car_make_name',$params)){
			$this->debugOut('So you now know what to look for?');
			$car_make_name = str_replace("'","",$params['car_make_name']);
			$car_make_name = str_replace("*","",$car_make_name); // dont know why it appends a star?
			$this->debugOut("And that is a good choice  ".$car_make_name." ");
			$select->where("  `car_make_name` like '%$car_make_name%' ");
		}
		$select->order("car_make_name");
		$ar = $mapper->fetchAll_Array($select);
		return $ar;		
	}
}

function FixUrl($url,$type){
	global $_SERVER;
	$script_name = trim(' '.$_SERVER["SCRIPT_NAME"].' ');
	$base_path = "/bdp2010/bdpgui/";
	//$base_path = str_ireplace('/public/index.php','',$script_name,1);

	$std_img =  $base_path.'/media/bdp_default_image_200_200.jpg';
	if(empty($url)){
		return $std_img.'?no_url_given_to_fix_url';
	}
	if(strpos(strtolower($url),'autostumper')>-1 ){
		// autostumper is currently down?
		if(strpos(strtolower($type),'img')>-1){
			return $std_img.'?org_url='.urlencode($url);
		}
		return '/index/info/type/supplier_web_down/message/'.urlencode('Webshoppen "Autostumper.dk" virker ikke i øjeblikket.');	
	}
	return $url;
	//die('x'.strpos(strtolower($url),'autostumper').$url);
}
