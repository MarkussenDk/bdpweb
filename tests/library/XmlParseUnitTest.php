<?php
require_once 'PHPUnit/Framework.php';

class MyXmlParseUnitTest extends PHPUnit_Framework_TestCase{
	var $spp_id;
	public function setup(){
		$spp_m = new Default_Model_SparePartSuppliersMapper();
		$spp_o = new Default_Model_SparePartSuppliers();
		$spp_m->authenticate('ExcelAndreas','ExcelAndreas',$spp_o);
		//$spp = $spp_m->fetchRow($select = " `supplier_admin_user_name` = 'ExcelAndreas' ");
		//$spp_o = $spp_m->fetchObject($select);
		//    	die();
		//$spp_id = $spp['spare_part_supplier_id'];
		$this->spp_id = $spp_o->getSpare_part_supplier_id();
		assertEx($this->spp_id,"No SparePartSupplier_id found");
		
	}
	
	public function testPushAndPop()    {
		$stack = array();        $this->assertEquals(0, count($stack));
		array_push($stack, 'foo');
		$this->assertEquals('foo', $stack[count($stack)-1]);
		$this->assertEquals(1, count($stack));
		$this->assertEquals('foo', array_pop($stack));
		$this->assertEquals(0, count($stack));   
	 }

	 public function ParseStringTest(){
	 	$p=dirname(APPLICATION_PATH).'/tests/data/price_list_file.xml';
		//print "<br> File path ".$p;
		//$p = 'C:\wamp\www\z18\soap\tests\data\price_list_file.xml';
//		print "<br> File path: ".$p;
		$xml_source = file_get_contents($p);
		$pp = new Bildelspriser_XmlImport_PriceParser($spp_o);
		echo 'ParseString Result: '.$pp->parseString($xml_source);//.'<hr/>';
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

		/*$spp_m = new Default_Model_SparePartSuppliersMapper();
		$spp_o = new Default_Model_SparePartSuppliers();
		$spp_m->authenticate('ExcelAndreas','ExcelAndreas',$spp_o);
		//$spp = $spp_m->fetchRow($select = " `supplier_admin_user_name` = 'ExcelAndreas' ");
		//$spp_o = $spp_m->fetchObject($select);
		//    	die();
		//$spp_id = $spp['spare_part_supplier_id'];
		$spp_id = $spp_o->getSpare_part_supplier_id();
		assertEx($spp_id,"No SparePartSupplier_id found");*/
		//$pp = new Bildelspriser_XmlImport_PriceParser($spp_o);
		//echo 'ParseString Result: '.$pp->parseString($xml_source).'<hr/>';
		 
		//die($pp->get_log_as_html());


		?>