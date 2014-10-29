<?php

class Bildelspriser_XmlImport_FastParser {
	public $dbo;
	public $filename;
	public $allowed_element_name;
	public static $spp_saver;
	public $price_parser_run_id;
	/** var $overwrite_all Fast or Full*/
	public $overwrite_all; 
	function __construct($filename) {
		$this->overwrite_all = false;	
		$steps[] = 'add_to_db';		
		$this->filename = $filename;
		/*$table= 'spare_part_prices';
		$fields = Bildelspriser_DB_DBObject::getFields($table);
		echo "Dumping fileds";
		Kint::dump($fields);*/
		/*$this->dbo = new Bildelspriser_DB_DBObject($table, $fields);
		$this->dbo->selectWhere($where_clause);
		if(@$this->dbo->filesize){
			echo "<br>File Found ";
			echo "<br>FileSize: ".$filesize = $this->dbo->filesize;
			echo "<br>Lateest_Status: ".$latest_status = $this->dbo->latest_status;
		}
		else{
			$this->dbo->filesize = filesize($filename);
			$this->dbo->filename = $filename;
			$this->dbo->modified = filemtime($filename);
			$this->dbo->latest_status = ''		
		}*/				
	}
	
	function add_to_db(){
		
	}
	
	function startElement($parser, $name, $attrs) {
		if($name!='SPARE_PART_PRICE')
			RETURN;
	   /*global $depth;
	   global $stack;
	   global $tree;*/
	  
	   $element = array();
	   $callback = 'startElement_'.$name;
	   if(!method_exists($this, $callback))
	   		error("No callback called $callback <br> for element <b>$name</b>");
   		$this->$callback($attrs);	 
	   /*$html = "\nFound &lt;$name/&gt; with ";
	   foreach ($attrs as $key => $value) {	   		
	       $html.="$key = $value, ";	       
	   }
	
	   echo nl2br($html);*/
	   /*end($stack);
	   $stack[key($stack)][strtolower($name)] = &$element;
	   $stack[strtolower($name)] = &$element;
	   
	   $depth++;*/
	}
	
	function startElement_SPARE_PART_PRICE($attrs){
		if(array_key_exists('REFERENCE', $attrs) && $attrs['REFERENCE']=='true')
			return;
		$spp_saver = self::$spp_saver;
		//$this->checkArrayForUTF8Errors($attrs);
		if($spp_saver==null){
			bdp::log("Creating a SparePartPriceSaver - this should only happen once");
			$spp_saver = new Bildelspriser_XmlImport_SparePartPriceSaver();
			self::$spp_saver = $spp_saver;
		}
		/*
		$html = "";
		   foreach ($attrs as $key => $value) {	   		
		       $html.="$key = $value, ";	       
		   }
		echo '<br>'.htmlspecialchars("<spare_part_price ".$html.'>');*/
		$attrs['PRICE_PARSER_RUN_ID'] = $this->price_parser_run_id;
		$spp_saver->addSppRow($attrs);
		                     flush();
	}
	
	function endElement($parser, $name) {
	   global $depth;
	   global $stack;
		echo "Ending </$name >";
	   //array_pop($stack);
	   //$depth--;
	}
	
	function parseSparePartPrices(){
		$parser = xml_parser_create('UTF-8');
		xml_set_object($parser, $this);
		xml_set_element_handler($parser,'startElement',null) or 
			die(' xml_set_element_handler failed ');
		//xml_parser_set_option($parser, 'XML_OPTION_CASE_FOLDING', true);
		$file_str='';
		$fp=fopen($this->filename, 'r');
		$this->allowed_element_name = 'spare_part_price';
		$i=200;
		bdp::log("<hr>starting looping on file (Loops allowed $i) ");
		//return;
		$buffer_size=0.5*1024; 
		$buffer_size=1024*1024;
		while(!feof($fp)) {
			$content = fread($fp, $buffer_size);
			$is_final = false;
			if(strlen($content)!=$buffer_size)
				$is_final = true;
			bdp::log("<br>$i:before first parse");	
			//xml_parse($parser,$content,$is_final);			
		    if (!xml_parse($parser,$content,feof($fp))) {
		      $lines[]=explode("\n", $content);
		      Kint::dump($lines);
		      $nr = xml_get_current_line_number($parser);
	//	      $lines_str = '<h2>XML Dump</H2><div class="code">'.$lines[$nr-1].'<br><b>'.$lines[$nr].'<b><br>'.$lines[$nr+1].'</div>';
     		  error(sprintf("XML error: %s at line %d",
     	              xml_error_string(xml_get_error_code($parser)),
                   xml_get_current_line_number($parser)));
   			}			
			bdp::log("<br>after first parse ($i) )");
			if(($i--)<0){
				bdp::info("<br>ended via ");
				return;
			}
		}
		self::$spp_saver->saveRowsToDb();
		xml_parser_free($parser);
		fclose($fp);
		bdp::info("Parsing done");
	}

}

class Bildelspriser_XmlImport_SparePartPriceSaver extends Default_Model_DynBase{
	static $buffer_size=100;
	static $spare_part_supplier_id=null;
	static $pp = null;

	public function __construct(array $options = null){
		/*class Default_Model_
		 * 
		 */
		$table = 'spare_part_prices';
		$fields = parent::getFields($table);
		//Kint::dump($fields);
		$pp = Bildelspriser_XmlImport_PriceParser::$_instance;
		parent::__construct($options,'',$table,$fields);	
	} 
	
	function log($msg){
		bdp::log($msg);
	}
	
	function addSppRow($row){
		//echo "<br><b>addSppRow($row)</b>  Adding row ".implode(':>', $row);
		$spare_part_price_id = null;
		$natual_key = null;
		$row['CREATED'] = phpDateToDbDate(time());
		$row['SPARE_PART_SUPPLIER_ID'] = self::$spare_part_supplier_id;
		$lower_attribs = array_change_key_case($row,CASE_LOWER);
		$row['PRICE_INC_VAT'] = DkFloatToMySQL($row['PRICE_INC_VAT']);
		if(is_null(self::$spare_part_supplier_id)){
			error(' Bildelspriser_XmlImport_SparePartPriceSaver must be initialized with a $spare_part_supplier_id - it was null ');
		}
		if(Default_Model_SparePartPricesMapper::existsInCache($lower_attribs, $spare_part_price_id, $natual_key))
		{
			//bdp::log("Spare_Part_Price $spare_part_price_id exists in cache ". $lower_attribs['supplier_part_number']);
			//$row['spare_part_price_id']=$spare_part_price_id;
			$row=array_change_key_case($row,CASE_LOWER);
			$this->update_name_from_row($row);
			//bdp::log("Now updated");
			return;			
		}
		else {
			bdp::log("Spare_Part_Price $spare_part_price_id does not exist in cache ". implode(' - ', $lower_attribs));	
		}
		$this->insert_add_row($row);
		self::$buffer_size=10;
		if(	($c = $this->value_list_count) >=
		 	($bs = self::$buffer_size)){
			$strlen=strlen($this->value_list);
		 	bdp::log("STRLEN WAS $strlen - therefore we call saveRowsToDb()");
		 	bdp::log("Array_count was $c and buffer_size was $bs - therefore we call saveRowsToDb()");
			$count = $this->saveRowsToDb();
		}
	}
	
	function saveRowsToDb(){
		return parent::insert_execute_statement();
	}


	function update_name_from_row($row){
		$spare_part_supplier_id = $row['spare_part_supplier_id'];
		$supplier_part_number = $row['supplier_part_number'];
		if($spare_part_supplier_id < 1){
			kint::dump($row);
			die('In update row - spare_part_supplier_id was missing ');			
		}
		if(!isset($supplier_part_number)){
			kint::dump($row);
			die('In update row - supplier_part_number was missing ');			
		}		
		if(Bildelspriser_DB_UTF8::hasUTFchars($supplier_part_number)){
			$supplier_part_number = utf8_decode($supplier_part_number);
		}
		$where_clause = " spare_part_supplier_id = $spare_part_supplier_id AND supplier_part_number = '$supplier_part_number' ";
		Bildelspriser_DB_UTF8::removeUTF8Junk($where_clause);
		$this->selectWhere($where_clause);
		//kint::dump('Update_Name_From_Row - AfterSelect',$this,$where_clause,$row['name'],$row);
		bdp::log('Just set name to '.$row['name'].' it was '.$this->name);
		$this->name = $row['name'];
		//kint::dump('ROW in UPD2',$this->fields);
		$this->spare_part_price_id = $this->fields['spare_part_price_id'];
		if($this->spare_part_price_id>0)
			$where_clause = " spare_part_price_id='$this->spare_part_price_id' ";
		$this->update($where_clause);
	}
}

?>