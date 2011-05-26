<?php
$php_model_path = '../application/models/';
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once $php_model_path.'SparePartPrices.php';

class SparePartSupplierTest extends PHPUnit_Framework_TestCase{
	var $sps_id;
	var $sps;
	var $sps_mapper;
	public function testSetup(){
		$sps_m = new Default_Model_SparePartSuppliersMapper();
		$sps_o = new Default_Model_SparePartSuppliers();
		$sps_m->authenticate('ExcelAndreas','ExcelAndreas',$sps_o);
		//$sps = $sps_m->fetchRow($select = " `supplier_admin_user_name` = 'ExcelAndreas' ");
		//$sps_o = $sps_m->fetchObject($select);
		//    	die();
		//$sps_id = $sps['spare_part_supplier_id'];
		$this->sps_id = $sps_o->getSpare_part_supplier_id();
		$this->sps = $sps_o;
		$this->sps_mapper = $sps_m;
		assertEx($this->sps_id,"No SparePartSupplier_id found");
		
	}	
	public function testSetuptestbutik1_user(){
		$sps_m = new Default_Model_SparePartSuppliersMapper();
		$sps_o = new Default_Model_SparePartSuppliers();
		$sps_m->authenticate('testbutik1_user','testbutik1_password',$sps_o);
		//$sps = $sps_m->fetchRow($select = " `supplier_admin_user_name` = 'ExcelAndreas' ");
		//$sps_o = $sps_m->fetchObject($select);
		//    	die();
		//$sps_id = $sps['spare_part_supplier_id'];
		$this->sps_id = $sps_o->getSpare_part_supplier_id();
		assertEx($this->sps_id,"No SparePartSupplier_id found");
		
	}
	
	public function testFindPrice()    {
		//$spp_mapper = Default_Model_SparePartPricesMapper::getInstance('Default_Model_SparePartPricesMapper');
		$spp_mapper = new Default_Model_SparePartPricesMapper();
		$spp_rowset = $spp_mapper->find(286);
		$this->assertType('Zend_Db_Table_Rowset',$spp_rowset);
		$cmo2spp_mapper = new Default_Model_CarModelsToSparePartPricesMapper();
		$spp_object = new Default_Model_SparePartPrices($spp_rowset->current()->toArray());
		$this->assertTrue($spp_object->Price_inc_vat>10,'Amount must be set - bigger than 10');
		$cmo_array=$spp_object->getCmo2Spp();
				
	 }

	 public function testParseString(){
	 	/*$p=dirname(APPLICATION_PATH).'/tests/data/price_list_file.xml';
		//print "<br> File path ".$p;
		//$p = 'C:\wamp\www\z18\soap\tests\data\price_list_file.xml';
//		print "<br> File path: ".$p;
		$xml_source = file_get_contents($p);
		$pp = new Bildelspriser_XmlImport_PriceParser($sps_o);
		echo 'ParseString Result: '.$pp->parseString($xml_source);//.'<hr/>';*/
	 }
	 
	 public function teardown(){
	 	
	 	
	 }
	 
}
		 
		 


		//print "<br>In XmlParseUnitTest - #1";
		//print "<br>APPLICATION_PATH".APPLICATION_PATH;
		//$p=dirname(APPLICATION_PATH).'/tests/data/price_list_file.xml';
		//print "<br> File path ".$p;
		//$p = 'C:\wamp\www\z18\soap\tests\data\price_list_file.xml';
//		print "<br> File path: ".$p;
		//$xml_source = file_get_contents($p);
		//print "<br> File Lenght: " . strlen($xml_source). " Bytes";
		//$fp = fopen($p,'r');
		//$xml_source = fread($fp,10000000);

		//phpinfo();
		//print "<br/><hr/>".htmlentities($xml_source,ENT_QUOTES);
		//    	die("");

		//print "<pre>".$xml_source."</PRE>";
		//assertEx($xml_source,"XML Source must not be empty".$xml_source);
//		print "<hr/>";

		/*$sps_m = new Default_Model_SparePartSuppliersMapper();
		$sps_o = new Default_Model_SparePartSuppliers();
		$sps_m->authenticate('ExcelAndreas','ExcelAndreas',$sps_o);
		//$sps = $sps_m->fetchRow($select = " `supplier_admin_user_name` = 'ExcelAndreas' ");
		//$sps_o = $sps_m->fetchObject($select);
		//    	die();
		//$sps_id = $sps['spare_part_supplier_id'];
		$sps_id = $sps_o->getSpare_part_supplier_id();
		assertEx($sps_id,"No SparePartSupplier_id found");*/
		//$pp = new Bildelspriser_XmlImport_PriceParser($sps_o);
		//echo 'ParseString Result: '.$pp->parseString($xml_source).'<hr/>';
		 
		//die($pp->get_log_as_html());


		?>