<?php


/**
 * @author Andreas
 *
 *
 */
class Default_Model_SparePartPriceSearcer  {
	static $_db;
	var $_car_model_id;
	var $_car_make_id;
	var $_car_variant_name; // To be developed later
	var $_year;
	var $_month;
	var $_chassis;
	var $_error_message;
	var $_records_returned;
	var $_limit;
	var $_page;
	var $_q;
	var $_results;
	var $_found_spare_part_prices_results;
	var $_user_message;
	var $_sql;
	var $_order_by;
	var $_suggested_names;
	var $_found_spare_part_categories;
	
	function __construct($car_model_id){		
		$this->_car_model_id = $car_model_id;
		$this->_limit = 30;
		$this->_order_by = ' price_inc_vat ';
	}
	
	function setCarMakeName($car_make_name){
		die(" setCarMakeName not implemented");
	}

	function getDB(){
		if(!isset(self::$_db)){
			$mapper = Default_Model_SparePartPricesMapper::getInstance('Default_Model_SparePartPricesMapper');
			self::$_db = $mapper->getDbAdapter();
		}
		return self::$_db;		
	}
	
	function safeFetchAll($sql){
		try{
			return $this->getDB()->fetchAll($sql);
		}
		catch(Zend_Db_Statement_Mysqli_Exception $ex){
			$message = "SQL ERROR!<br/>".$sql
						. "\n".$ex->getMessage()
						. "\n".$ex->getTraceAsString();
						;
			die(nl2br($message));
		}
		catch(Zend_Exception $ze){
			$message = "Other Zend_Exception<br/> ".nl2br($sql)
					."<br/>".$ze->getMessage();
			die($message);
		}
	}
	
	function constructNamesSQL($where=''){
		$where_and = " ";
		if($where!=''){
			$where_and .= $where;
		}
		$limit = ' limit ' . $this->_limit. ' ';
		$view_name = " sps_spp_cm2spp_cmo_v ";
//		$id = " concat(IF(v.car_model_id IS NULL,' ',v.car_model_id),'-', v.spare_part_price_id) "; 
		$trace = "";		
		if($this->_car_model_id)
			$where_and .= " and `v`.`car_model_id` = '$this->_car_model_id'  ";
		$group_by = ' group by 1 ';
		$sql = "SELECT `v`.`name`, count(`spare_part_price_id`) as antal " 
		    . " FROM $view_name v "
		    . "WHERE 1=1 \n"
		    . $where_and
		    . $group_by
		//    . $limit
		;
		$this->_sql = $sql;
		return $sql;
	}	
	
	function constructSQL($q,$soundex=0){
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
		$limit = ' limit ' . $this->_limit. ' ';
		$view_name = "sps_spp_cm2spp_cmo_v";
		
		// IF(supplier_city IS NULL, 'n/a',supplier_city) 
		$id = " concat(IF(v.car_model_id IS NULL,' ',v.car_model_id),'-', v.spare_part_price_id) "; 
		$trace = "";		
		if(isset($q)){
			if(1==$soundex){
				//MYSQL: strcmp(soundex('text'), soundex('test'));
				$where_and.= " and soundex(`name`) like soundex('$q') ";			
			}
			else
				$where_and .= " and `name` LIKE '%$q%' ";
		}
		$limit = 100;
		if(isset($this->_limit)){
			if(!is_integer($this->_limit)){
				//$trace .= "Limit was not an integer '$limit' ";
				$limit = 50;
				//die('Limit must be an integer, it was '.var_dump($car_model_id));	
			}
			if($limit < 1)
				$limit = 60;
			$limit = " LIMIT ".$limit.' ';
		}
		
		if(isset($this->_car_model_id)){
			//$id = " v.spare_part_price_id ";
			$car_model_id = $this->_car_model_id;			
			if(!is_integer($car_model_id)){
				$trace .= "'Car_model_id must be an integer, it was '$car_model_id' ";	
				die("Car_make_id was not an integer $car_model_id ");
				//$where_and .= " and `car_model_name` = '$car_model_id' \n ";
			}
			else { // hack but works --  car_model_id is an integer
				$where_and .= " and `car_model_id` = $car_model_id \n ";
			}
		}
		
		$sql = "SELECT "//$id as id," 
			. " v.* "
		    . "\nFROM $view_name v "
		    . "\nWHERE 1=1 \n"
		    . $where_and
		    . ' order by '.$this->_order_by
		    . $limit  		    
		    ;
		    $this->_sql = $sql;
		//echo "SQL</br>".nl2br($sql).'<hr>';
		return $sql;
	}

	function runSearch($q){
		$try_with_soundex=0;
		if($this->tryRunSearch($q,$try_with_soundex)){
			$this->_found_spare_part_prices_results = $this->_results;
			return $this->_results;
		}
		$er_pos = @stripos($q,'er',2);
		if($er_pos>2){ //check for multiple in danish - eg. skiver vs. skive
			$str_len = strlen($q);
			//echo "\n<!-- pos was $er_pos len was $str_len -->";
			if($str_len = (2+$er_pos)){
				$q=substr($q,0,$str_len-1);
				//echo "<!-- q is now $q -->";
					if($this->tryRunSearch($q,$try_with_soundex)){
					$this->_found_spare_part_prices_results = $this->_results;
					return $this->_results;
				}
			}			
		}
		$try_with_soundex=1;
		$this->_user_message = "\n<br>Søger med soundex efter '$q' ";
		if($this->tryRunSearch($q,$try_with_soundex)){
			$this->_found_spare_part_prices_results = $this->_results;
			return $this->_results;
		}		
	}

	function tryRunSearch($q,$use_soundex){
		$this->_error_message = '';
		$db = $this->getDB();		
		try{    
			$sql = $this->constructSQL($q,$use_soundex);
			$this->_results = $this->safeFetchAll($sql);
			$this->_records_returned = count($this->_results);
			//echo "\n<!-- tryRunSearch q=$q,  soundex=$use_soundex results $this->_records_returned rows -->";
			if($this->_records_returned > 0)
				return true;
		}
		catch(Zend_Db_Exception $zde){
			//die($zde);
			$count = count($this->_results);
			$this->_zend_db_exception .= $zde;	
			$this->_error_message .= $zde;
			$this->_user_message.=$zde;	
			//die($this->_error_message);
			throw($zde);	
		}
		return false;		
	}
	
	function suggestSoundexNames(){
		// and soundex(`name`) like concat(soundex('BRAMSESKIVER'),'%');
		$len = strlen($this->_q)-1;
		$names = array();
		if($len>5){
			$len = 5;
		}while($len>-1){
			$where = " and soundex(`name`) like concat(substr(soundex('".$this->_q."'),1,$len),'%')"; 
			$sql = $this->constructNamesSQL($where);
			$names = $this->safeFetchAll($sql);
			//echo "<br>$this->_q $len " . sizeof($names);
			if(sizeof($names)){
				//echo "Names found " . implode(', ',$names);// implode($names,', ');
				return $names;
			}
			$len--;
		}
		return $names;
	}
	
	function allNames(){
		// and soundex(`name`) like concat(soundex('BRAMSESKIVER'),'%');
		$names = array();
		$sql = $this->constructNamesSQL('');
		$names = $this->safeFetchAll($sql);
		return $names;
	}
	
	function checkSearch(){
		if(isset($this->_results)){
			$count = count($this->_results);
			if($count>0){
				// Records found - but is it prices?
				$first = $this->_results[0];
				if(array_key_exists('spare_part_price_id',$first)){
					$count = count($this->_found_spare_part_prices_results);	
					if(Default_Model_XmlHttpRequestMapper::$last_object != null){
						$lo = Default_Model_XmlHttpRequestMapper::$last_object;
						$lo->setSqlUsed($this->_sql);
						//$q .= "";
						$lo->setQ($this->_q);
						$lo->_user_name_required = false; // could be anonymous user
			            $lo->setDataRowsReturned($count);
						$lo->save();
					}					
				}
				else{
					$this->_error_message .= "<br>carSearcerError - spare_part_price_id missing";
					throw new Zend_Exception($this->_error_message);
				}
				return true;
			}			
			else
				return false;
		}
		else{
			return false;
		}
	}
	
	function layoutSuggestedNames($ar_suggested_names){
		$s = "";
		$i =0;		
		$a_end = "</a>";
		if(count($ar_suggested_names)>0){
			echo "<i>Vi kunne ikke finde '$this->_q', mente du..</i>";
		}
		else
			return;
		foreach($ar_suggested_names as $name){
			$n = $name['name'];
			$a_start = "<a href='javascript:setQandRun(\"$n\");' onDDclick='setQandRun(\"$n\");return false;' >";
		
			$s.= "\n<br>".$a_start.$n.$a_end.'('.$name['antal'].')';
		}
		return "<div class='bdpSuggestedNames'>$s</div";
	}
	
	function searchPrices($q){
		$this->_q = $q;
		$db = $this->getDB();
		$this->runSearch($q);
		//$this->_found_spare_part_categories = $this->_suggested_names;
		if($this->checkSearch()){
			return; // all fine
		}
		//$this->_q = substr()
		$this->_suggested_names = $this->suggestSoundexNames();
		//$this->_found_spare_part_categories .= "Lad os finde nogle andre vare til dig";
		//$this->_user_message = "Vi kunne ikke finde vare der passede til søgningen '$q', men her er der nogle andre ting til din bil.";
		
		die($this->layoutSuggestedNames($this->_suggested_names));
		//$this->runSearch('');
	}// end of function searchPrices();
}// end of class