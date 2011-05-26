<?php

// application/models/CarModelsToSparePartPricesMapper.php

require_once 'CarModelsToSparePartPrices.php';
require_once 'IMapperBase.php';

class Default_Model_CarModelsToSparePartPricesMapper extends MapperBase
{
	public function __construct(){
		$this->setDbTable('Default_Model_DbTable_CarModelsToSparePartPrices');
		parent::__construct();
	}

	public function save(Default_Model_CarModelsToSparePartPrices  $cmo2spp)
    {
    	global $_SERVER;
    	$cmo2spp->validateForSave();
    	$data = array(
            'car_model_id'   		 => $cmo2spp->getCar_model_id(), 
            'spare_part_price_id'    => $id =  $cmo2spp->getSpare_part_price_id(), 
    	    'year_from'    => $cmo2spp->getYear_from(), 
            'month_from'    => $cmo2spp->getMonth_from(), 
    	    'year_to'    => $cmo2spp->getYear_to(), 
            'month_to'	  => $cmo2spp->getMonth_to(),
    	    'year_to'    => $cmo2spp->getYear_to(), 
            'chassis_no_from'	  => $cmo2spp->getChassis_no_from(),
    		'chassis_no_to'	  => $cmo2spp->getChassis_no_to(),
            'created' 		  => date('Y-m-d H:i:s') ,
            'created_by' 		  => $cmo2spp->getCreated_by(),
    		'price_parser_run_id' => Bildelspriser_XmlImport_PriceParser::$_price_parser_run_id)
    	;
    	if(!isset($id)){
    		
    		/*Bildelspriser_XmlImport_PriceParser::getInstance()->log("<br/>Postpoing save");
    		$this->PostponeSave($data);
    		return;*/
    	}	
    	//die("It there . ".$data['created_by']);
        //die("cmmtospp = ".var_export($data,true));
        /**
         * If it exists, delete it to ensure that there only exists one relationship between one model and one spare_part_price_id
         * It seems unlikely that there is "holes" in the periods where a spare part match a car.
         */
    	$sql = "";
    	//echo "<br>Before the delete";
        try{
    	$this->getDbTable()->delete($sql= " `car_model_id` =" .$cmo2spp->getCar_model_id().
        							 " and `spare_part_price_id`=".$cmo2spp->getSpare_part_price_id());
		
        }
        catch(Exception $e){
        	echo "<br/>sql = $sql<br/>Exception ".$e;
        }
    	//echo "<br>After the delete";
        //var_dump_array($data,"<br>Data Default_Model_CarModelsToSparePartPrices");
		
        //$dbt =  $this->getDbTable();
        //echo "<hr>Type of ".get_class($dbt); returns "Default_Model_DbTable_CarModelsToSparePartPrices"
        //echo "<br>Type of ".print_r($dbt,true);
        //$is = $dbt->insert($data);
       // echo "<br>After ins ".print_r($db,true);
        //echo "<br>Ins = ".$is;
        echo "<br>ins = ".print_r($data,true);
        return $this->getDbTable()->insert($data);
    }

   
 /*   public function find($id, Default_Model_CarModelsToSparePartPrices  $cmo2spp)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        //$cmo2spp->setAllFromGenClass($result->First());
        $row = $result->current();
        $cmo2spp->setId($row->id)
        //          ->setEmail($row->email)
                  ->setComment($row->comment)
                  ->setCreated($row->created);
                 
    }*/

    public function fetchAll($select)
    {
       	$resultSet = $this->getDbTable()->fetchAll($select);
    	$entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Default_Model_CarModelsToSparePartPrices($row->toArray());
            $entries[] = $entry;
            
        }
        return $entries;
    }    
}

