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
include_once ('../application/views/helpers/SparePartPricePrinter.php');
$i = 4;
class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        setlocale ( LC_ALL, 'da-DK.utf8' );
        		Default_Model_XmlHttpRequest::readOrCreateUserCookie ();
        		//$this->view->dojo()->disable(); 
        		$this->view->setEncoding ( 'UTF-8' );
        		$this->view->headMeta ()->appendHttpEquiv ( 'Content-Type', 'text/html; charset=utf-8' );
        		$this->view->navigation ()
        			->addPage ( array ('label' => 'Find', 'action' => 'index', 'controller' => 'index' ) )
        			->addPage ( array ('label' => utf8_encode ( 'Alle Bilmærker' ),'controller' => 'index', 'action' => 'brands' ) )
        			->addPage ( array ('action' => 'news', 'controller' => 'index', 'label' => 'Nyheder/Opdateringer' ) )
        			->addPage ( array ('action' => 'about', 'controller' => 'index', 'label' => 'Om Bildelspriser.dk' ) );
        			$xh = new Default_Model_XmlHttpRequest ();
        		//$xh->setCreatedBy($user_name);
        		try {
        			$xhrid = $xh->getMapper ()->save ( $xh );
        		} catch ( exception $e ) {
        			echo " exception while saving XmlHttpRequest " . $e;
        		}
        		$view = $this->view;
        		$view->headTitle ()->prepend ( 'Bildelspriser.dk - ' );
        		$view->headTitle ()->setSeparator ( ' ' );
    }

    public function file_get_contents_utf8($fn)
    {
        $content = file_get_contents ( $fn );
        		return mb_convert_encoding ( $content, 'UTF-8', mb_detect_encoding ( $content, 'UTF-8, ISO-8859-1', true ) );
    }

    public function indexOldAction()
    {
        Zend_Dojo::enableView ( $view );
        		$view->dojo ()->setDjConfigOption ( 'usePlainJson', true )->addLayer ( '/js/bdp/main.js' );
        		//->addJavascript('bdp.main.init();');*/
        		
        
        		print "<br>In XmlParseUnitTest - #1";
        		print "<br>APPLICATION_PATH" . APPLICATION_PATH;
        		$p = dirname ( APPLICATION_PATH ) . '/tests/data/price_list_file.xml';
        		//print "<br> File path ".$p;    	
        		//$p = 'C:\wamp\www\z18\soap\tests\data\price_list_file.xml';
        		print "<br> File path: " . $p;
        		$xml_source = file_get_contents ( $p );
        		//$xml_source = mb_convert_encoding($xml_source,'ISO-8859-1');
        		//$xml_source = mb_convert_encoding($xml_source,'UTF-8','auto');
        		//$xml_source = utf8_decode($xml_source);
        		$pos = strpos ( $xml_source, '�' );
        		if ($pos > 0) {
        			$str = "<HR>String contained invalid chars : " . substr ( $xml_source, $pos, 2 ) . 'HEx Value ' . dechex ( ord ( substr ( $xml_source, $pos, 1 ) ) ) . ' ' . dechex ( ord ( substr ( $xml_source, $pos + 1, 2 ) ) );
        		
        		//die($str);    	
        		}
        		print "<br> File Lenght: " . strlen ( $xml_source ) . " Bytes";
        		//$fp = fopen($p,'r');
        		//$xml_source = fread($fp,10000000);
        		
        
        		//phpinfo();
        		print "<br/><hr/>" . htmlentities ( $xml_source, ENT_QUOTES );
        		//    	die("");
        		
        
        		//print "<pre>".$xml_source."</PRE>";
        		//assertEx($xml_source,"XML Source must not be empty".$xml_source);
        		print "<hr/>";
        		
        		$spp_m = new Default_Model_SparePartSuppliersMapper ();
        		$spp_o = new Default_Model_SparePartSuppliers ();
        		$spp_m->authenticate ( $user_name, $some_password, $spp_o );
        		//$spp = $spp_m->fetchRow($select = " `supplier_admin_user_name` = 'ExcelAndreas' ");
        		//$spp_o = $spp_m->fetchObject($select);
        		//    	die();    	
        		//$spp_id = $spp['spare_part_supplier_id'];
        		$spp_id = $spp_o->getSpare_part_supplier_id ();
        		assertEx ( $spp_id, "No SparePartSupplier_id found" );
        		$pp = new Bildelspriser_XmlImport_PriceParser ( $spp_o );
        		echo 'ParseString Result: ' . $pp->parseString ( $xml_source ) . '<hr/>';
        		
        		die ( $pp->get_log_as_html () );
    }

    public function newsAction()
    {
        
    }

    public function aboutAction()
    {
        
    }

    public function dojoAction()
    {
        Zend_Dojo::enableView ( $this->view );
        		$this->view->dojo ()->setDjConfigOption ( 'usePlainJson', true )->addLayer ( '/js/bdp/main.js' );
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
        		
        		$form = new Default_Form_DojoSearch ();
        		$this->view->form = $form;
    }

    public function testAction()
    {
        echo "<h1>Test</H1>";
        		$car_mapper = Default_Model_SparePartPricesMapper::getInstance ( 'Default_Model_SparePartPricesMapper' );
        		$car_mapper->fillCache ( 5 );
        		$car_mapper->fillCache ( 6 );
        		$car_mapper->fillCache ( 5 );
        		//$car_mapper->fillCacheDirect(6);
        		//$car_mapper->fillCacheAll();
        		die ( "x" );
    }

    public function indexAction()
    {
        /*$html->$view->contentPane(
                                                			'grid',
                                                			'GridDemo is Loading',
                                                			array('title'=>'GridDemo'
                                                				  'preLoad' => 'true',
                                                				  'href' => 'index/dojogridproces/format/html',
                                                				  'parseOnLoad' => true		
                                                			)
                                                		)*/
        		$form = new Default_Form_HtmlSearch ();
        		//	$this->view->form = " ".str_replace('Mærke','Mærke',$form);
        		$this->view->form = utf8_decode ( $form );
        		if (! array_key_exists ( 'bdp_cookie_uniqid', $_COOKIE )) {
        			$this->view->list_of_cars = "Velkommen første gang!";
        			return;
        		}
        		$bdp_cookie_uniqid = $_COOKIE ['bdp_cookie_uniqid'];
        		$db = $this->_db = Zend_Registry::get ( "db" );
        		/* @var db Zend_Db_Adapter_Mysqli */
        		if (! isset ( $bdp_cookie_uniqid )) {
        		
        		}
        		$select = $db->select ( 'server_request_uri' )
        						->from ( array ('x' => 'xml_http_request' ), array ('server_request_uri' ) )
        						->join ( array ('ua' => 'user_agents' )
        									, 'x.user_agent_id = ua.user_agent_id', array () )
        						->where ( 'ua.cookie_guid = ? ', $bdp_cookie_uniqid )
        						->where ( 'server_request_uri like ?', '%/car_model_id/%' )
        						->where ( 'server_request_uri not like ?', '%/car_model_id/undef%' )
        						->where ( 'server_request_uri not like ?', '%selected-prices-pr-model%' )
        						->order ( 'x.created DESC' )->limit ( 10 );
        		$transactions = $db->fetchAll ( $select );
        		$html = "Trans<br>";
        		$models = array ();
        		$models_list = "";
        		foreach ( $transactions as $t ) {
        			$url = $t ['server_request_uri'];
        			$p = explode ( '/', $url );
        			$key = array_search ( 'car_model_id', $p );
        			$value = ((int)($p[$key + 1]));
        			if(is_integer($value) && $value < 0)
        			{
        				//Zend_Debug::dump($p,'Value must be a positive number '.$key.' url is '.$url,true);
        				continue;
        			}
        			$car_model_id = $value;
        			$models [$car_model_id] = $car_model_id;
        		
        		//$html.=' car_model_id = '.$car_model_id.' - '.Zend_Debug::dump($p,'p','false').'<hr/>';  			
        		//$html.=Zend_Debug::dump($t,'T',false);
        		}
        		$imodels = array ();
        		foreach ( $models as $model ) {
        			//Zend_Debug::dump($model,'m&$model',true);
        			$imodel = ( integer ) $model;
        			if ($imodel > 0) {
        				$imodels [] = $imodel;
        			}
        		}
        		if (count ( $imodels ) > 0) {
        			$models_list = implode ( ',', $imodels );
        			//Zend_Debug::dump($models_list,'m&$$models_list',true);
        			$q = $db->select ()->from ( 'car_models_v' )->where ( ' car_model_id in (' . $models_list . ')' );
        			global $make_and_models;
        			$make_and_models = $db->fetchAll ( $q );
        			//Zend_Debug::dump($make_and_models,'m&models',true);
        			$table = $this->view->HtmlTable ();
        			/* @var $table Zend_View_Helper_HtmlTable */
        			$table->setCaption ( 'Sidst valgte modeller' );
        			//$table->
        			// set table tag attributes
        			$table->setAttribs ( array ('summary' => '', 'class' => 'bdp_std_table' ) );
        			
        			function getId() {
        				return 'getId called' . Zend_Debug::dump ( func_get_args (), 'arg', false );
        			}
        			
        			function getAJavaScript() {
        				global $make_and_models;
        				$args = func_get_args ();
        				$car_model_id = $args [0];
        				//Zend_Debug::dump($make_and_models,'$make_and_models '.$car_model_id,true) ;							
        				//$row = array_search( array("car_model_id"=>$car_model_id),$make_and_models);
        				$row = null;
        				foreach ( $make_and_models as $make_and_model ) {
        					if ($make_and_model ['car_model_id'] == $car_model_id) {
        						$row = $make_and_model;					
        						//echo "Row found";
        					}				
        					//Zend_Debug::dump($make_and_model,'$make_and_model id',true);
        					//echo "<br> $model_id =>$model_name ";
        				}
        				if(is_null($row)){
        					echo("row was null '$car_model_id' ");
        				}
        				$car_make_id = $row ['car_make_id'];
        				$car_model_name = $row ['model_cleansed_name'];
        				$car_make_name = $row ['car_make_name'];
        				//return 'getId called' .Zend_Debug::dump(func_get_args(),'arg',false) ;
        				return "<a href='javascript:setMakeAndModel($car_make_id,$car_model_id)' " . ' title="Vælg ' . $car_make_name . " " . $car_model_name . '" ' . // .' targer="_AnotherWindow" '
        				' >' . $car_make_name . " " . $car_model_name . '</a>';
        				return 'getId called' . Zend_Debug::dump ( func_get_args (), 'arg', false );
        			}
        			
        			function getSaveButton() {
        				return '[Save]';
        			}
        			
        			$table->setNotFoundText ( 'No records found.' );
        			
        			$table->setColumns ( array ('car_model_id' => array ('text' => 'Dine Biler', 'callback' => 'getAJavaScript' )//  'class' => 'idClass',
        			/*,
                							    'name' => array(
                							        'text' => 'car_model_id',
                							    	'callback'=> 'getSaveButton'
                							    ),
                							    'price' => array(
                							        'text' => '1',
                							    )*/
                							) );
        			
        			$table->setAttrib ( 'style', 'width: 100%;' );
        			$table->setData ( $models );
        			$this->view->list_of_cars = $table;
        		}
        	
        	//$car_models_from_cookies = 
        	//$last_5_cars =  new Car_List_From_Cookies();
    }

    public function selectedPricesPrModelAction()
    {
        $this->_helper->layout ()->disableLayout ();
        		if(!array_key_exists('bdp_cookie_uniqid', $_COOKIE))
        		{
        			$this->_helper->layout ()->enableLayout();            
        			$this->_forward('brands');
        			return;
        		}
        		$bdp_cookie_uniqid = $_COOKIE ['bdp_cookie_uniqid'];
        		$db = $this->_db = Zend_Registry::get ( "db" );
        		$car_model_id = $this->getRequest ()->getParam ( 'car_model_id', - 1 );
        		if ($car_model_id < 0)
        			die ( '<!-- car_model_id must above 1 -->' );
        			/* @var db Zend_Db_Adapter_Mysqli */
        		$select = $db->select ()->from ( array ('s' => 'sps_spp_cm2spp_cmo_v' ), array ('supplier_name', 'name', 'description', 'price_inc_vat', 'spare_part_price_id' ) )->where ( 'car_model_id = ?', $car_model_id )->//order('s.created DESC')->
        		limit ( 10 );
        		$transactions = $db->fetchAll ( $select );
        		if (sizeof ( $transactions ) == 0) {
        			$this->view->prices = utf8_decode('Der findes ingen priser til den valgte model. Desværre!');
        			return;
        		}
        		$html = "Trans<br>";
        		$table = $this->view->HtmlTable ();
        		/* @var $table Zend_View_Helper_HtmlTable */
        		$table->setCaption ( 'Udvalgte bildele' );
        		//$table->
        		// set table tag attributes
        		$table->setAttribs ( array ('summary' => '', 'class' => 'bdp_std_table' ) );
        		
        		function getA() {
        			$args = func_get_args ();
        			$row = $args [0];
        			return '<a href="/index/details/spare_part_price_id/' . $row ['spare_part_price_id'] . '" ' . ' title="' . $row ['description'] . "\n" . $row ['supplier_name'] . implode ( "\n", $row ) . '" ' . ' targer="_AnotherWindow" ' . ' >' . $row ['name'] . '</a>';
        			return 'getId called' . Zend_Debug::dump ( func_get_args (), 'arg', false );
        		}
        		function getSaveButton() {
        			return '[Save]';
        		}
        		
        		$table->setNotFoundText ( 'No records found.' );
        		/*[0] => array(5) {
                						    ["supplier_name"] => string(7) "biltema"
                						    ["name"] => string(25) "ELEKTRISK BRÃ†NDSTOFPUMPE"
                						    ["description"] => string(3) "8mm"
                						    ["price_inc_vat"] => string(6) "299.00"
                						    ["spare_part_price_id"] => int(8268)*/
        		$table->setColumns ( array ('name' => array ('text' => 'Varenavn', 'callback' => 'getA' )// get the link with title and everyting
        
        
        		, 'price_inc_vat' => array ('text' => 'Pris <span style="font-size:9px"><br/>inkl. moms</span>', 'td_class' => 'price_inc_vat' ) ) );
        		$table->setAttrib ( 'style', 'width: 100%;' );
        		$table->setData ( $transactions );
        		$this->view->prices = utf8_decode ( $table );
        	
        	//$car_models_from_cookies = 
        	//$last_5_cars =  new Car_List_From_Cookies();
    }

    public function autocompletecarmakesAction()
    {
        $sps = new Default_Model_SparePartSuppliers ( array ('supplier_admin_user_name' => "WebUser" ) );
        		Default_Model_SparePartSuppliersMapper::setIdentity ( $sps );
        		// First, get the model somehow
        		$this->view->model = $this->getCarMakesModel ();
        		
        		// Then get the query, defaulting to an empty string
        		$this->view->request = $this->getRequest ();
        		$this->view->params = $this->_getAllParams ();
    }

    public function autocompletecarmodelsAction()
    {
        $sps = new Default_Model_SparePartSuppliers ( array ('supplier_admin_user_name' => "WebUser" ) );
        		Default_Model_SparePartSuppliersMapper::setIdentity ( $sps );
        		
        		// First, get the model somehow
        		$this->view->model = $this->getCarModelsModel ();
        		
        		// Then get the query, defaulting to an empty string
        		$this->view->request = $this->getRequest ();
        		
        		$this->view->params = $this->_getAllParams ();
    }

    public function getCarMakesModel()
    {
        return new CarMakesModel ();
    }

    public function getCarModelsModel()
    {
        return new CarModelsModel ();
    }

    public function brandsAction()
    {
        $this->view->model = $this->getCarMakesModel ();
        		$this->view->params = $this->_getAllParams ();
        		$this->view->car_model = $this->getCarModelsModel ();
        		$searcher = null;
        		if (array_key_exists ( 'car_model_id', $this->view->params )) {
        			$car_model_id = $this->view->params ['car_model_id'];
        			$searcher = new Default_Model_SparePartPriceSearcer ( ( integer ) $car_model_id );
        			$all_names = $searcher->allNames ();
        			$this->view->all_names = $all_names;
        			//echo "Bildele til $car_model_id ";
        			//die(nl2br(var_dump($all_names)));
        			if (array_key_exists ( 'q', $this->view->params )) {
        				$q = $this->view->params ['q'];
        				$this->view->q = $q;
        				if(is_string($q))
        					$prices = $searcher->runSearch ( urldecode ( $q ) );
        				else	
        					$prices = $searcher->runSearch ( urldecode ( $q[0] ) );        				
        				$this->view->prices = $prices;			
        			}
        		}
    }

    public function htmlAction()
    {
        $this->view->model = $this->getCarMakesModel ();
                                		$this->view->params = $this->_getAllParams ();
                                		$this->view->car_model = $this->getCarModelsModel ();
    }

    public function phpinfoAction()
    {
        
    }

    public function dojogridpricesAction()
    {
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

    /**
     * @return Zend_Db_Adapter_Mysqli
     * 
     */
    public static function getDB()
    {
        return Zend_Registry::get ( "db" );
    }

    public function getDataSet($q, $car_model_id, $year, $model)
    {
        $db = $this->getDB ();
    }

    public function newsearchAction()
    {
        $this->view->request = $this->getRequest ();
                                		$this->view->headMeta ()->appendHttpEquiv ( 'Content-Type', 'text/html; charset=utf-8' );
                                		$this->view->params = $this->_getAllParams ();
                                		$q = "";
                                		$car_model_id;
                                		if (array_key_exists ( 'q', $this->view->params )) {
                                			$q = $this->view->params ['q'];
                                		}
                                		if (array_key_exists ( 'limit', $this->view->params )) {
                                			$limit = ( int ) $this->view->params ['limit'];
                                			if (! is_integer ( $limit )) {
                                				$trace .= "Limit was not an integer '$limit' ";
                                				$limit = 48;
                                			
                                		//die('Limit must be an integer, it was '.var_dump($car_model_id));	
                                			}
                                			if ($limit < 1)
                                				$limit = 49;
                                			$limit = " LIMIT " . $limit;
                                		}
                                		$car_model_name = null;
                                		$car_make_name = null;
                                		$car_model_row = null;
                                		$car_make_row = null;
                                		if (array_key_exists ( 'car_model_id', $this->view->params )) {
                                			$car_model_id = ( int ) $this->view->params ['car_model_id'];
                                			if (is_int ( $car_model_id ) && $car_model_id > 0) {
                                				$model_mapper = Default_Model_CarModelsMapper::getInstance ( 'Default_Model_CarModelsMapper' );
                                				$cmo = new Default_Model_CarModels ();
                                				$car_model = $model_mapper->find ( $car_model_id );
                                				//var_dump($car_model);
                                				if ($car_model instanceof Zend_Db_Table_Rowset) {
                                					if (count ( $car_model ) != 1) {
                                						die ( "Data error - count(car_model) was " . count ( $car_model ) );
                                					}
                                					$car_model_row = $car_model->current ();
                                					if (!  $car_model_row instanceof  Zend_Db_Table_Row) {
                                						die ( "Type error car_model_row " . var_export ( $car_model_row, true ) );
                                					}
                                					$car_model_id = $car_model_row->car_model_id;
                                					$car_make_id = $car_model_row->car_make_id;
                                				}
                                				if (isset ( $car_model_name )) {
                                					$this->view->car_model_name = $car_model_name;
                                				}
                                				$cma = Default_Model_CarMakes::getMakeByIdAsObject ( $car_make_id );
                                				$car_make_name = $cma->getCar_make_name ();
                                				$this->view->car_make_name = $car_make_name;
                                			
                                		//$this->view->car_model_name = $car_model_name;
                                			//var_dump($cma);
                                			//$cm = Default_Model_CarMakesMapper::getMakes();
                                			}
                                		} else if (array_key_exists ( 'car_model_name', $this->view->params ) && array_key_exists ( 'car_make_id', $this->view->params )) {
                                			$car_model_id = Default_Model_CarModels::getCarModelIdByCarModelNameAndCarMakeId ( $this->view->params ['car_model_name'], $this->view->params ['car_make_id'] );
                                			echo "<!-- inNewSearchAction car_model_id was now $car_model_id -->";
                                		}
                                		if (! is_integer ( $car_model_id )) {
                                			echo "<!-- inNewSearchAction car_model_id was not set -->";
                                		}
                                		$searcher = new Default_Model_SparePartPriceSearcer ( $car_model_id );
                                		$searcher->searchPrices ( $q );
                                		$this->view->suggested_names = $searcher->_suggested_names;
                                		$this->view->car_model_id = $car_model_id;
                                		if (isset ( $searcher->_user_message )) {
                                			$this->view->user_message = $searcher->_user_message;
                                		}
                                		if (isset ( $searcher->_found_spare_part_prices_results )) {
                                			$count = count ( $searcher->_found_spare_part_prices_results );
                                			echo "<!--SparePartPricesFound $count -->";
                                			$this->view->spare_part_prices_results = $searcher->_found_spare_part_prices_results;
                                		} elseif (isset ( $searcher->_found_spare_part_categories )) {
                                			echo "<!--_found_spare_part_categories-->";
                                			$this->view->found_spare_part_categories = $searcher->_found_spare_part_categories;
                                		} else {
                                			die ( "Vi kunne ikke finde nogle bilddele til din bil.<br>Vi udbygger løbende vores database." );
                                		}
                                		if (isset ( $searcher->_results ))
                                			$this->view->results = $searcher->_results;
                                		$this->view->sql;
    }
	
	public function jumptoAction() {
		$this->view->params = $this->_getAllParams();
		$spp_id=null;
		if (array_key_exists ( 'spare_part_price_id', $this->view->params )) {
			$spp_id = ( int ) $this->view->params ['spare_part_price_id'];
		}
		if (! (isset ( $spp_id ) && ($spp_id > 0))) {
			die ( "SPP Was not set" );
		}
		$spp_m = MapperFactory::getSppMapper ();
		$spp = $spp_m->findObject ( $spp_id );
		if ($spp == null) {
			//$this->_forward('brands','index','default',array('forward_message'=>'Ukendt Spare Part Price id '.$spp_id));
			$url = '/index/brands/';
			if (array_key_exists ( 'car_model_id', $this->view->params )) {
				$url .= '/car_model_id/' . car_model_id;
			}
			$this->view->no_show_message = 'Ukendt Spare Part Price id ' . $spp_id . '<br><a href="' . $url . '">Gå til Alle Bilmærker for at finde reservedele til aktive dele.</a>' . '<br><br>Den reservedel du søgte findes ikke mere i vores database.';
			
			warning ( 'Ukendt Spare Part Price id ' . $spp_id . ' in ' . __FUNCTION__ . ' at' . __FILE__ . ':' . __LINE__ );
			return;
		}
		$this->view->spare_part_price = $spp;
		
		$jt = new Default_Model_DynJumpTo ();
		$jt->spare_part_price_id = $spp->getSpare_part_price_id ();
		$jt->spare_part_supplier_id = $spp->getSpare_part_supplier_id ();
		$jt->price_inc_vat = $spp->getPrice_inc_vat ();
		$jt_id = $jt->insert ();
		$this->view->jump_to_record = $jt;
		$this->view->jump_to_id = $jt_id;
	}

    public function detailsAction()
    {
        $this->view->params = $this->_getAllParams ();
                                    		$spp_id = null;
                                		if (array_key_exists ( 'spare_part_price_id', $this->view->params )) {
                                			$spp_id = ( int ) $this->view->params ['spare_part_price_id'];
                                		}                                		
                                		if (! (isset ( $spp_id ) && ($spp_id > 0))) {
                                			error ( "SPP Was not set" );
                                		}
                                		$spp_m = MapperFactory::getSppMapper();
                                		$spp = $spp_m->findObject($spp_id);
                                		//Kint::dump($spp);
                                		//die();
                                		if($spp == null){
                                			//$this->_forward('brands','index','default',array('forward_message'=>'Ukendt Spare Part Price id '.$spp_id));
                                			$url = '/index/brands/';
                                			if (array_key_exists ( 'car_model_id', $this->view->params )) 
	                                		{
	                                			$url .= '/car_model_id/'.car_model_id;
	                                		}
                                			$this->view->no_show_message='Ukendt Spare Part Price id '.$spp_id
                                				.'<br><a href="'.$url.'">Gå til Alle Bilmærker for at finde reservedele til aktive dele.</a>'
                                				.'<br><br>Den reservedel du søgte findes ikke mere i vores database.'
                                			;			
	                                		
                                			warning('Ukendt Spare Part Price id '.$spp_id.' in '.__FUNCTION__.' at'.__FILE__.':'.__LINE__);
                                			return;                                			
                                		}
                                		$this->view->spare_part_price = $spp;
                                		$this->view->car_model_id = null;
                                		if (array_key_exists ( 'car_model_id', $this->view->params )) 
                                		{
                                			$this->view->car_model_id = (int)$this->view->params ['car_model_id'];
                                		}
    }

    public function datasearchresultsAction()
    {
        $obs = array ('supplier_admin_user_name ' => "WebUser" );
                                		$sps = new Default_Model_SparePartSuppliers ( $obs );
                                		Default_Model_SparePartSuppliersMapper::setIdentity ( $sps );
                                		
                                		$this->view->request = $this->getRequest ();
                                		$this->view->headMeta ()->appendHttpEquiv ( 'Content-Type', 'text/html; charset=utf-8' );
                                		$this->view->params = $this->_getAllParams ();
                                		$q = "";
                                		if (array_key_exists ( 'q', $this->view->params )) {
                                			$q = $this->view->params ['q'];
                                		}
                                		if (array_key_exists ( 'limit', $this->view->params )) {
                                			$limit = ( int ) $this->view->params ['limit'];
                                			if (! is_integer ( $limit )) {
                                				$trace .= "Limit was not an integer '$limit' ";
                                				$limit = 48;
                                			
                                		//die('Limit must be an integer, it was '.var_dump($car_model_id));	
                                			}
                                			if ($limit < 1)
                                				$limit = 49;
                                			$limit = " LIMIT " . $limit;
                                		}
                                		
                                		$db = $this->getDB ();
                                		/* 'spare_part_price_id' => 1,
                                                    'name' => 'Udst�dningsr�r',
                                                    'description' => 'Dette er udst�dningsr�ret til en gammel Folkevogn � � � � � �',
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
                                		if (array_key_exists ( 'q', $this->view->params )) {
                                			$q = $this->view->params ['q'];
                                			$where_and .= " and `name` LIKE '%$q%' ";
                                		}
                                		
                                		if (array_key_exists ( 'LIMIT', $this->view->params )) {
                                			$limit = ( int ) $this->view->params ['limit'];
                                			if (! is_integer ( $limit )) {
                                				$trace .= "Limit was not an integer '$limit' ";
                                				$limit = 98;
                                			
                                		//die('Limit must be an integer, it was '.var_dump($car_model_id));	
                                			}
                                			if ($limit < 1)
                                				$limit = 99;
                                			$limit = " LIMIT " . $limit;
                                		}
                                		
                                		if (array_key_exists ( 'car_model_id', $this->view->params )) {
                                			$id = " v.spare_part_price_id ";
                                			$car_model_id = ( int ) $this->view->params ['car_model_id'];
                                			if (! is_integer ( $car_model_id )) {
                                				$trace .= "'Car_model_id must be an integer, it was '$car_model_id' ";
                                				$where_and .= " and `car_model_name` = '$car_model_name' \n ";
                                			} else { // hack but works --  car_model_id is an integer
                                				$where_and .= " and `car_model_id` = '$car_model_id' \n ";
                                			}
                                		
                                		//	$where_and.= " and car_models_to_spare_part_prices.spare_part_price_id = spare_part_prices.spare_part_price_id ";
                                		//	$table_and.= " ,car_models_to_spare_part_prices  \n";
                                		}
                                		
                                		//		$sps = '`spare_part_suppliers`';
                                		//		$spp = '`spare_part_prices`';
                                		//$sql = "SELECT  name, description, spare_part_image_url, price_inc_vat, producer_make_name, part_placement,supplier_part_number from spare_part_prices LIMIT 30;";
                                		$sql = "SELECT $id as id," . " v.* " . //	. "spare_part_prices.spare_part_price_id as id, spare_part_prices.name as name "
                                		//	.", description, spare_part_image_url "
                                		//	.", spare_part_url, part_placement, supplier_name, car_model_name"
                                		//	.", price_inc_vat, producer_make_name, part_placement, supplier_part_number, supplier_name\n" 
                                		" FROM $view_name v " . "WHERE 1=1 \n" . //		    . " and spare_part_prices.spare_part_supplier_id = "
                                		//			.  "  and spare_part_prices.spare_part_price_id is not null "	
                                		$where_and . $limit;
                                		//die($sql);
                                		try {
                                			$this->view->sql = $sql;
                                			$results = $db->fetchAll ( $sql );
                                			$this->view->exception = false;
                                		} catch ( exception $e ) {
                                			$html = "<h2>sql</h2><br>" . nl2br ( $sql );
                                			$html .= "<br>Error<br>" . nl2br ( $e );
                                			//die($html);
                                			$res = array ('id' => 'error', 'name' => nl2br ( $e ), 'description' => $sql );
                                			$results = array ($res );
                                			$this->view->exception = true;
                                		}
                                		$count = count ( $results );
                                		if ($count == 0) {
                                			$res = array ('id' => 'info', 'name' => "Inden priser fundet, da vi s&oslash;gte efter '" . $q . "'", 'description' => $sql );
                                			$results = array ($res );
                                		}
                                		if (! is_integer ( $count )) {
                                			die ( "Count was not an integer??" + var_export ( $count, true ) );
                                		
                                		}
                                		if (Default_Model_XmlHttpRequestMapper::$last_object != null) {
                                			$lo = Default_Model_XmlHttpRequestMapper::$last_object;
                                			$lo->setSqlUsed ( $sql );
                                			$q .= "";
                                			$lo->setQ ( $q );
                                			$lo->setDataRowsReturned ( $count );
                                			//$lo->set()
                                			$lo->save ();
                                		}
                                		$this->view->results = $results;
    }

    public function wordCleanerAction()
    {
        // action body
                                		$dbAdapter = Zend_Registry::get ( "db" );
                                		
                                		$query = "select * from tag_names_with_spp_count_v;";
                                		$query = "select * from tag_names_with_spp_count_mv where tag_type_id is null;";
                                		$query = "select * from tag_names_with_spp_count_mv m, tags t where t.tag_id = m.tag_id and t.tag_type_id is null;";
                                		$this->view->words = $dbAdapter->fetchAll ( $query );
                                		
                                		$query = "select tag_type_id,description from tag_types;";
                                		$this->view->tag_type_pairs = $dbAdapter->fetchPairs ( $query );
    }

    public function ajaxHtmlAction()
    {
        /* @var ajaxContext Zend_Controller_Action_Helper_AjaxContext */
                                		/*$ajaxContext = $this->_helper->getHelper ( 'AjaxContext' );
                                		$ajaxContext->addActionContext ( 'ajax-html', 'json' )->addActionContext ( 'ajaxHtml', 'json' )->initContext ();
                                		$ajaxContext->disableLayouts();*/
    									$this->view->format = $this->_getParam('format');
                                		if($this->view->format!='html'){
                                			$this->_helper->layout()->disableLayout();
                                		}
                                		$this->view->layout = $this->_helper->layout();
                                		$ach = new AjaxCommandHandler ( $this->_getAllParams () );
                                		$this->view->data = $ach->run();
    }

    public function makeModelCleanerAction()
    {
        // action body     					
        		/* @var dbAdapter Zend_Db_Adapter_Mysqli */
        		$dbAdapter = Zend_Registry::get ( "db" );
        		$orderby = $this->getRequest ()->getParam ( 'orderby', 'car_make_id' );
        		
        		$select_car_makes = $dbAdapter->select ()->from ( 'car_makes' )->order ( $orderby );
        		$this->view->orderby = $orderby;
        		$query = "select * from car_makes";
        		
        		$this->view->car_makes = $dbAdapter->fetchAll ( $select_car_makes );
        		$this->view->car_make_pairs = $dbAdapter->fetchPairs ( 'select * from car_makes_v' );
        		
        		$this->view->enums = $this->getEnumOptions ( 'car_makes', 'state_enum' );
        		
        		$query = "select * from car_models";
        		$this->view->car_models = $dbAdapter->fetchAll ( $query );
    }

    public function modelsCleanerAction()
    {
        // action body     					
        		$this->_helper->layout ()->disableLayout ();		
        		/* @var dbAdapter Zend_Db_Adapter_Mysqli */
        		$dbAdapter = Zend_Registry::get ( "db" );
        		$car_make_id = (int)$this->getRequest ()->getParam ( 'car_make_id');
        		if($car_make_id!=''){
        			$select_car_models = $dbAdapter->select()->from ( 'car_models_sorted_v' )
        					->where(' car_make_id = ? ',$car_make_id);
        					
        			$this->view->car_models = $dbAdapter->fetchAll ( $select_car_models );
        			$this->view->car_make_pairs = $dbAdapter->fetchPairs ( 'select * from car_makes_v' );
        			$this->view->car_models_pairs = $dbAdapter->fetchPairs ( 'select  car_model_id, car_model_name from car_models_v where car_make_id = '.$car_make_id);
        		
        		//	$this->view->car_models_pairs = $dbAdapter->fetchPairs ( 'select * from car_models_v' );
        			//Zend_Debug::dump($this->view->car_make_pairs,'pairs',true);
        			//Zend_Debug::dump($this->view->car_make_pairs[104],'pairs - 104',true);
        			if(!array_key_exists($car_make_id, $this->view->car_make_pairs)){
        				error("Unknown car_make_id $car_make_id. Use correct id".Zend_Debug::dump($this->view->car_make_pairs,'Valid IDs',false));
        			}
        			$car_make_name=$this->view->car_make_pairs[$car_make_id];			
        			$this->view->car_make_name = $car_make_name;
        			$this->view->enums = $this->getEnumOptions ( 'car_makes', 'state_enum' );	
        			include_once 'Bildelspriser/3rdParty/BilmodelDk.php';
        			$this->view->bilmodel_dk = new Bildelspriser_3rdParty_BilmodelDk();
        			//Zend_Debug::dump($this->view->bilmodel_dk);
        			$bilmodel_bid = null;
        			if(array_key_exists($car_make_name, $this->view->bilmodel_dk->brands_by_name)){
        				$bilmodel_bid = $this->view->bilmodel_dk->brands_by_name[$car_make_name];
        				//echo "Brand found $bilmodel_bid - $car_make_name";
        			}
        			else{
        				echo "Brand $car_make_name not found using keys ".array_keys($this->view->bilmodel_dk->brands_by_name);
        			}		
        			if($bilmodel_bid){
        				$bilmodel_dk_models = $this->view->bilmodel_dk->getModelsByBrandId($bilmodel_bid);
        				//Zend_Debug::dump($bilmodel_dk_models,'Array $bilmodel_dk_models',true);
        				$this->view->bilmodel_dk_models = $bilmodel_dk_models;
        			}
        			else
        			{
        				die("Res was '$bilmodel_bid' when searching for '".$this->view->car_make_pairs[$car_make_id]
        					."' using ".Zend_Debug::dump($car_make_id,'car_make_id',false).' as key');
        			}
        		}
        		else
        			die("Car_make_id ($car_make_id) was missing<br>".Zend_Debug::dump($this->getRequest()-> getParams(),'Request',false));
    }

    public static function getEnumOptions($table, $column)
    {
        // build select to read column definition for selected column
        		$sql = " SHOW COLUMNS ";
        		$sql .= "         FROM " . $table;
        		$sql .= "         LIKE '" . $column . "'";
        		$dbAdapter = Zend_Registry::get ( "db" );
        		
        		$result = $dbAdapter->fetchRow ( $sql );
        		
        		// user regular expression to get option list from result
        		preg_match ( '=\((.*)\)=is', $result ["Type"], $options );
        		
        		// replace single quotes
        		$options = str_replace ( "'", "", $options [1] );
        		
        		// explode string into an array
        		$options = explode ( ",", $options );
        		
        		return $options;
    }

    public function modelCleanerAction()
    {
        // action body     					
        		$this->_helper->layout ()->disableLayout ();
        		/* @var dbAdapter Zend_Db_Adapter_Mysqli */
        		$dbAdapter = Zend_Registry::get ( "db" );
        		$car_make_id = $this->getRequest ()->getParam ( 'car_make_id');
        		if(is_null($car_make_id))
        			error("I was called without a car make - please always call me with a car make id");
        		$orderby = $this->getRequest ()->getParam ( 'orderby', 'car_make_id' );
        		
        		$select_car_makes = $dbAdapter->select ()->from ( 'car_makes' )->order ( $orderby );
        		$this->view->orderby = $orderby;
        		$query = "select * from car_makes";
        		
        		$this->view->car_makes = $dbAdapter->fetchAll ( $select_car_makes );
        		$this->view->car_make_pairs = $dbAdapter->fetchPairs ( 'select * from car_makes_v' );
        		
        		$this->view->enums = $this->getEnumOptions ( 'car_makes', 'state_enum' );
        		
        		$query = "select * from car_models";
        		$this->view->car_models = $dbAdapter->fetchAll ( $query );       
            	$this->view->car_model_pairs = $dbAdapter->fetchPairs ( 'select * from car_models_v where car_make_id = '.$car_make_id);
        		$this->view->bilmodel_dk = new Bildelspriser_3rdParty_BilmodelDk();
        		$res = array_search($this->view->car_make_pairs[$car_make_id], $this->view->bilmodel_dk);
        		Zend_Debug::dump($res,'Array Search Result',true);
        		if($res){
        			$bilmodel_dk_models = $this->view->bilmodel_dk->getModelsByBrandId($res);
        			Zend_Debug::dump($bilmodel_dk_models,'Array $bilmodel_dk_models',true);
        		}
    }

    public function procRunnerAction()
    {
         $db =  self::getDB();
         $select_procs = $db->select()->from('routines',array('SPECIFIC_NAME','ROUTINE_TYPE'),'information_schema');
         $result = $select_procs->query()->fetchAll();
         //Zend_Debug::dump($result,'Prcedures',true);
		 $this->view->proc_names = $result;
		 $proc_name = $this->getRequest()->getParam('proc_name');
		 $proc_args = $this->getRequest()->getParam('proc_args');
		 $rout_type = $this->getRequest()->getParam('rout_type');		 
		if($proc_name){
			$sql=null;
			switch ($rout_type) {
				case 'FUNCTION':
					$sql = "Select $proc_name($proc_args) ;";
				;
				break;
				
				case 'PROCEDURE':
					$sql = "call $proc_name($proc_args) ";
				;
				break;
				default:
					error('Unknowroutine type '.$rout_type);
				break;
			}
			//die($sql);
			$stmt = $db->query($sql);
			$this->view->proc_result = $stmt;
			//Zend_Debug::dump($stmt,'stmte',true);		 	
    	}    
    }
    
}


/**
 * 
 * Enter description here ...
 * @param array $a
 * @param integer $selected_key The value of the HTML Options 
 * @param string $selected_value The text shown
 * @param string $first_entry_text The text shown as first line, representing null
 */
function html_key_val_to_options(array $a, $selected_key , $selected_value,$first_entry_text='-Vælg Kategori'){
	$html = '<option value=\'-1\'>'.$first_entry_text;
	$selected = '';
	foreach($a as $k=>$v){
		if($k==$selected_key || $v == $selected_value)
			$selected = ' selected ';
		$html.="<option value='$k' $selected >$v</option>";
		$selected = "";
	}
	return $html;
}


class AjaxCommandHandler {
	var $_allParams = null; 
	/* @var $_db Zend_Db_Adapter_Mysqli */
	var $_db = null;
	var $_cmd = null;
	public function __construct($allParams) {
		$this->_allParams = $allParams; //Assoc array;
		$this->_db = Zend_Registry::get ( "db" );
		if (isset ( $this->_allParams ['cmd'] )) {
			$this->_cmd = $this->_allParams ['cmd'];
			if (! isset ( $this->_cmd )) {
				throw new exception ( 'Command still not set' );
			}
		} else {
			throw new exception ( "CMD not found" . Zend_Debug::dump ( $this, 'All Params', true ) );
		}
	}
	
	public function run() {
		$cmd_str = 'cmd' . $this->_cmd;
		if (array_search ( $cmd_str, get_class_methods ( get_class ( $this ) ) )) {
			try{
				return eval ( 'return $this->' . $cmd_str . '();' );
			}
			catch(exception $e){
				return array ('msg' => $e->getMessage(), 'exception' => $e->__toString(), 'error' => true); 
			}
		} else {
			$msg = "Method '$cmd_str' not implemented on " . get_class ( $this ) . "<br>" . implode ( ', ', get_class_methods ( get_class ( $this ) ) );
			return array ('error' => true, 'msg' => $msg );
		}
	}
	
	public function param($param_name){
		if(!array_key_exists($param_name, $this->_allParams))
			throw new exception ('Parameter missing - '.$param_name);
		return $this->_allParams[$param_name];		
	}
	
	public function int_param($param_name){
		$p = $this->param($param_name);
		$i = intval($p,10);
		if($p<>strval($i)){
			throw new exception ("integer conversion error $p <> $i");
		}
		return $i;
	}
	
	public function cmdHelloWorld() {
		return array ('msg' => 'Hello World' );
	
	}
	
	public function cmdSaveTagType() {
		$p_tag_id = $this->int_param('tag_id');
		$p_tag_type_id = $this->int_param('tag_type_id');
		$n = $this->_db->update('tags',array('tag_type_id'=>$p_tag_type_id),'tag_id = ' . $p_tag_id);		
		return array('msg'=>'OK '.$n.' rows updated ('."( $p_tag_id , $p_tag_type_id )");
	}
	
	public function cmdSetCarMakeState(){
		$p_car_make_id = $this->int_param('car_make_id');
		$p_state_enum = $this->param('state_enum');
		$n = $this->_db->update('car_makes',array('state_enum'=>$p_state_enum),'car_make_id = ' . $p_car_make_id);		
		return array('msg'=>'OK '.$n.' rows updated ('."($p_car_make_id,$p_state_enum)");
	}

	public function cmdSetCarModelState(){
		$p_car_model_id = $this->int_param('car_model_id');
		$p_state_enum = $this->param('state_enum');
		$n = $this->_db->update('car_models',array('state_enum'=>$p_state_enum),'car_model_id = ' . $p_car_model_id);		
		return array('msg'=>'OK '.$n.' rows updated ('."($p_car_model_id,$p_state_enum)");
	}

	public function cmdsetPurchasedState(){
		///index/ajaxHtml/cmd/setPurchasedState/jt_id/$jt_id/state/$key
		$p_jt_id = $this->int_param('jt_id');
		$p_state = $this->param('state');
		$jt = new Default_Model_DynJumpTo();
		$jt->load($p_jt_id);
		//Zend_Debug::dump($jt,'JT',true);
		$jt->part_purchased = $p_state;
		$n = $jt->update();
		//$n = $this->_db->update('car_models',array('state_enum'=>$p_state_enum),'car_model_id = ' . $p_car_model_id);		
		return array('msg'=>'OK '.$n.' rows updated ('."($p_jt_id,p_state=$p_state) - Tak for din tilbagemelding  !");
	}
	
	public function cmdSetCarMakeMainId(){
		$p_car_make_id = $this->int_param('car_make_id');
		$p_car_make_main_id = $this->param('car_make_main_id');
		if(-1 == $p_car_make_main_id)
			$p_car_make_main_id = null;
		$n = $this->_db->update('car_makes',array('car_make_main_id'=>$p_car_make_main_id),'car_make_id='.$p_car_make_id);		
		return array('msg'=>'OK '.$n.' rows updated ('."($p_car_make_id,$p_car_make_main_id)");
	}
	
	public function cmdSetCarModelMainId(){
		$p_car_model_id = $this->int_param('car_model_id');
		$p_car_model_main_id = $this->param('car_model_main_id');
		if(-1 == $p_car_model_main_id)
			$p_car_model_main_id = null;
		$n = $this->_db->update('car_models',array('car_model_main_id'=>$p_car_model_main_id),'car_model_id='.$p_car_model_id);		
		return array('msg'=>'OK '.$n.' rows updated ('."($p_car_model_id,$p_car_model_main_id)");
	}	
	public function cmdError(){
		$p_error_type = $this->param('type');
		$p_error_text = $this->param('text');
		$p_user_agent_id = 1;
		$n = $this->_db->insert('ui_errors',array(
								 'error_type'=>$p_error_type
								,'error_text'=>$p_error_text
								,'user_agent_id'=>$p_user_agent_id));
		return array('msg'=>'OK '.$n.' row inserted ('."($p_error_type,$p_error_text)");
	}
	
	public function cmdListLastTransactions(){
		$data[] = array('id'=>'12345','url'=>'http://bildelspriser.dk/bla.bla..'); 	
		$data[] = array('id'=>'12345','url'=>'http://bildelspriser.dk/bla.bla..'); 	
		$data[] = array('id'=>'12345','url'=>'http://bildelspriser.dk/bla.bla..'); 	
		return array('msg'=>'OK','data'=>$data);
	}
}

class AjaxModels {
	public function __construct() {
		$this->debug = false;
	}
	var $debug;
	public function setDebug() {
		$this->debug = true;
		$this->debugOut ( 'setDebug():: Debug is enabled' );
	}
	public function debugOut($msg) {
		if ($this->debug)
			echo "</br>" . $msg;
	}
}

class CarModelsModel extends AjaxModels {
	public function query($params) {
		/** this code depends on the following SQL to be processed first to ignore case.
		 * ALTER TABLE  `car_makes` 
		 * CHANGE  `car_make_name`  `car_make_name` VARCHAR( 255 ) 
		 * CHARACTER SET utf8 COLLATE utf8_danish_ci NOT NULL */
		
		$mapper = MapperFactory::getCmoMapper();
		$select = $mapper->createSelect ();
		$select = IndexController::getDB()->select();
		//$select = IndexController::getDB()->select(array ('id' => 'car_model_id', 'name' => 'car_model_name' ));
		$select->from ( array('car_models_v' ) 
					, array ('id' => 'car_model_id', 'name' => 'model_cleansed_name' ) )
					//, array ('id' => 'car_model_id', 'name' => 'car_model_name' ) );
					->where(' `state_enum` not in (\'Alternativ\',\'Slettet\') ');
		
		if (is_array ( $params ) && array_key_exists ( 'q', $params )) {
			$params ['car_model_name'] = $params ['q'];
		}
		
		if (is_array ( $params ) && array_key_exists ( 'car_model_name', $params )) {
			$this->debugOut ( 'So you now know model to look for?' );
			$car_model_name = str_replace ( "'", "", $params ['car_model_name'] );
			$car_model_name = str_replace ( "*", "", $car_model_name ); // dont know why it appends a star?
			$this->debugOut ( "And that is a good choice  " . $car_model_name . " " );
			$select->where ( "  `car_model_name` like '%$car_model_name%' " );
		}
		
		if (is_array ( $params ) && array_key_exists ( 'car_make_id', $params )) {
			$this->debugOut ( 'So you now know car_make to look for?' );
			$car_make_id = str_replace ( "'", "", $params ['car_make_id'] );
			$this->debugOut ( "And that is a good choice  " . $car_make_id . " " );
			$select->where ( "  `car_make_id` = '$car_make_id' " );
		}
		$select->order ( "model_cleansed_name" );
		$ar = IndexController::getDB()->fetchAssoc($select);
		return $ar;
		$ar = $mapper->fetchAll_Array ( $select );
		return $ar;
	}
}

class CarMakesModel extends AjaxModels {
	public function query($params) {
		/** this code depends on the following SQL to be processed first to ignore case.
		 * ALTER TABLE  `car_makes` 
		 * CHANGE  `car_make_name`  `car_make_name` VARCHAR( 255 ) 
		 * CHARACTER SET utf8 COLLATE utf8_danish_ci NOT NULL */
		
		$mapper = MapperFactory::getCmaMapper();
		//$mapper = Default_Model_CarMakesMapper::getInstance();
		$select = $mapper->createSelect ();
		$select->from ( array ('cm' => 'car_makes' ), array ('id' => 'car_make_id', 'name' => 'car_make_name' ) );
		$select->where(' `state_enum` not in (\'Alternativ\',\'Slettet\') ');
		

		if (is_array ( $params ) && array_key_exists ( 'q', $params )) {
			$params ['car_make_name'] = $params ['q'];
		}
		
		if (is_array ( $params ) && array_key_exists ( 'car_make_name', $params )) {
			$this->debugOut ( 'So you now know what to look for?' );
			$car_make_name = str_replace ( "'", "", $params ['car_make_name'] );
			$car_make_name = str_replace ( "*", "", $car_make_name ); // dont know why it appends a star?
			$this->debugOut ( "And that is a good choice  " . $car_make_name . " " );
			$select->where ( "  `car_make_name` like '%$car_make_name%' " );
		}
		$select->order ( "car_make_name" );
		$ar = $mapper->fetchAll_Array ( $select );
		return $ar;
	}
}

function FixUrl($url, $type, Default_Model_SparePartPrices $spp) {
	global $_SERVER;
	$script_name = trim ( ' ' . $_SERVER ["SCRIPT_NAME"] . ' ' );
	$env = getenv('APPLICATION_ENV');
	switch($env){
		case 'development': $base_path = "/bdp2010/bdpgui/"; break;
		case 'test': $base_path="";break;
		case 'production':$base_path='';break;
		case '':$base_path='';break;
		default:
			error("Unknown environment fetched in FixUrl '$env' ");
	}
	//$base_path = str_ireplace('/public/index.php','',$script_name,1);
	if(strpos($url,'biltema')){
		return biltema_FixImgUrl($url, $type, $spp);
	}
	$base_url = "not_set";
	$host_name = 'hot_set';
	if('/'==$url[0]){
		$base_url = $spp->getSpare_part_url();
		$first_slash=strpos($base_url, '.dk/');
		$url = ($host_name=substr($base_url, 0, $first_slash + 4)).$url;
	}
	//Zend_Debug::dump($url,'after hack');	
	//"http://bildelspriser.dk/bdp2010/bdpgui//media/bdp_default_image_200_200.jpg?no_url_given_to_fix_url"
	$std_img = $base_path . '/media/bdp_default_image_200_200.jpg';
	if (empty ( $url )) {
		return $std_img . '?no_url_given_to_fix_url';
	}
	if (strpos ( strtolower ( $url ), 'autostumper' ) > - 1) {
		// autostumper is currently down?
		if (strpos ( strtolower ( $type ), 'img' ) > - 1) {
			return $std_img . '?org_url=' . urlencode ( $url );
		}
		return '/index/info/type/supplier_web_down/message/' . urlencode ( 'Webshoppen "Autostumper.dk" virker ikke i øjeblikket.' );
	}	
//	$url1 = Zend_View_Helper_SparePartPricePrinter::prepareUrl($url, $spp->getSpare_part_supplier_id());
	echo "<!-- fix url $url type $type \nhost_name = $host_name \nbase_url $base_url\n$url -->";
	return $url;
		//die('x'.strpos(strtolower($url),'autostumper').$url);
}

function biltema_FixImgUrl($url, $type, Default_Model_SparePartPrices $spp){
	//<img src="http://www.biltema.dk/da/Bil%2d%2d%2dMC/Motor/Brandstofsystem/Ford/Escort/52200-ELEKTRISK-BRANDSTOFPUMPE/" style="padding:20px;margin:20px;border:5px solid orange;" alt="alt" title="Titel" height="200px" width="200px">
	if(!strpos($url,'biltema'))
		error("Wrong function - only use this for Biltema.dk");
	if(strpos($url,'.jpg') || strpos($url,'.jepg') || strpos($url,'.gif') || strpos($url,'.png'))
		return $url;
	$spp_supplier_part_nr = $spp->getSupplier_part_number();
	if(strlen($spp_supplier_part_nr)>9 || strlen($spp_supplier_part_nr)<4)
		error("Forkert længde - $spp_supplier_part_nr ".strlen($spp_supplier_part_nr));
	//63-311009_s.jpg
	//http://www.biltema.dk/ProductImages/63/small/63-311009_s.jpg
	$size_full = 'large';
	$size_short = 'l';
	$url =   'http://www.biltema.dk/ProductImages/'.substr($spp_supplier_part_nr,0,2).'/'.$size_full
			.'/'.substr($spp_supplier_part_nr,0,2).'-'.substr($spp_supplier_part_nr,2).'_'.$size_short.'.jpg';
	return $url;	
}

class utf8{
	public static function needsEncoding($string){
		$wrong_chars = "[\xC2-\xDF]";
		return true;
	}

	public static function right_code($input){
		/*$input_encoded = utf8_encode($input);
		$input_decoded = utf8_decode($input);
		$len_input = strlen($input);
		$len_encoded = strlen($input_encoded);
		$len_decoded = strlen($input_decoded);
		echo "<hr><ul>rightcode ($input)<li> len_input =$len_input $input  <li>len_encoded=$len_encoded $input_encoded <li>len_decoded=$len_decoded $input_decoded </ul>";
		*/
		//if($len_input == $)
		/*if( $len_input == $len_encoded ){
			return $input;
		}
		else
			return $input_decoded;*/
		/*if(len($input) = len($input_encoded)){
			return $input;
		}*/
		//return $input;
		return self::recursiveDecoding($input);
		if (detectUTF8($input)) {
			echo "<li>UTF8 detected";
            return $input;       
        } else {
        	echo "<li>UTF8 NOT detected";
            return cp1251_utf8($input);
        }
		$conv =@iconv($input, 'UTF-8', 'UTF-8');
		if ($conv == $input){ 
			echo "Good UTF-8! - $input ";
			return $input;
		}
		else {
			echo "Nope. - $input " ;
			return $conv;
		}
		/*if(self::needsEncoding($input)){
			return utf8_encode($input);
		}
		else
			return $string;*/
	}
	
	static function recursiveDecoding($strUtf8){
		//for($pos=strlen($strUtf8)-1; $pos >= 0 ; $pos--){
        for($pos=strlen($strUtf8)-1; $pos >= 0 ; $pos--){
        	$char = $strUtf8[$pos]; 
        	/*
        	$charm1 =  substr($strUtf8, $pos-1,1); 
        	$ocm1 = ord($charm1);
        	$char = substr($strUtf8, $pos,1); 
        	$oc = ord($char);
        	$charp1 = substr($strUtf8, $pos+1,1); 
        	$ocp1 = ord($charp1);
        	//echo "<br>ord($char) = $charm1: $ocm1 - $char: $oc - $charp1: $ocp1 ";*/
            if (ord($char) > 162) {
            	//$strUtf8 = substr($strUtf8, 0, $pos).substr($strUtf8, $pos+1);
            	/*
            	 echo "<hr>bizar char($char)  found in $strUtf8 <br>  $charm1   $char $charp1 ".ord($char);
            	echo '<br>echo decoding'.$strUtf8.' to '.utf8_decode($strUtf8) ;
            	echo '<br>echo encoding'.$strUtf8.' to '.utf8_encode($strUtf8).'<br>' ;
            	echo '<br>detect enc'.mb_detect_encoding($strUtf8);*/
            	switch(ord($char)){
            		case 165://'¥'
            		case 166://'¦'
            		case 184://'¸'
					//echo "OK We decode";
            			return self::recursiveDecoding(utf8_decode($strUtf8));
            			break;   
            		case 194: //'Â'
            		case 195: //'Ã'
            			//echo "OK We return as is";
            			return $strUtf8;       
            		case 229://å 
            		case 230://æ 
            		case 248://ø 
            		case 216://Ø 
            		case 197://Å 
            		case 198://Æ 
            		case 233://é - burde være ë i citroën	
            		case 235://ë - som i citroën	
            			continue;
            			//echo "OK we leave it as it is, and continue";
            			//return self::recursiveDecoding(utf8_encode($strUtf8));
            		default:
            			echo "<!--Special char ord nr '".ord($char)."' '$char'  not congf'ed in $strUtf8 --><br>";
            	}
            }
        }
        //echo "<br>No bizar chars left ".$strUtf8;
        return utf8_encode($strUtf8);
	}
}


function detectUTF8($string)
{
        return preg_match('%(?:
        [\xC3E5]        # non-overlong 2-byte
        )+%xs', $string);
}

function cp1251_utf8( $sInput )
{
    $sOutput = "";

    for ( $i = 0; $i < strlen( $sInput ); $i++ )
    {
        $iAscii = ord( $sInput[$i] );

        if ( $iAscii >= 192 && $iAscii <= 255 )
            $sOutput .=  "&#".( 1040 + ( $iAscii - 192 ) ).";";
        else if ( $iAscii == 168 )
            $sOutput .= "&#".( 1025 ).";";
        else if ( $iAscii == 184 )
            $sOutput .= "&#".( 1105 ).";";
        else
            $sOutput .= $sInput[$i];
    }
   
    return $sOutput;
}

function encoding($string){
    if (function_exists('iconv')) {   
        if (@!iconv('utf-8', 'cp1251', $string)) {
            $string = iconv('cp1251', 'utf-8', $string);
        }
        return $string;
    } else {
        if (detectUTF8($string)) {
            return $string;       
        } else {
            return cp1251_utf8($string);
        }
    }
}






























