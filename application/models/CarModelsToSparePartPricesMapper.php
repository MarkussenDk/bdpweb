<?php

// application/models/CarModelsToSparePartPricesMapper.php

require_once 'CarModelsToSparePartPrices.php';
require_once 'IMapperBase.php';

class Default_Model_CarModelsToSparePartPricesMapper extends MapperBase
{
	public function __construct(){
		$this->setDbTable('Default_Model_DbTable_CarModelsToSparePartPrices');
		$this->setUnique_vars(array('CAR_MODEL_ID'
					,'SPARE_PART_PRICE_ID'
					//,'price_inc_vat' // removed until the stuff works with the vat matching.
					,'SPARE_PART_SUPPLIER_ID'));		
		parent::__construct();
	}

	
	public function save(Default_Model_CarModelsToSparePartPrices  $cmo2spp)
    {
    	global $_SERVER;
    	$cmo2spp->validateForSave();
    	$data = $cmo2spp->toArray();
    	$id =  $cmo2spp->getSpare_part_price_id();
    	if(!isset($id)){    		
    		Bildelspriser_XmlImport_PriceParser::getInstance()->log("<br/>Postpoing save");
    		echo "postponing save";
    		$this->PostponeSave($data);
    		return;
    	}			
    	//die("It there . ".$data['created_by']);
        //die("cmmtospp = ".var_export($data,true));
    	//echo "<br>After the delete";
        //var_dump_array($data,"<br>Data Default_Model_CarModelsToSparePartPrices");
        //$dbt =  $this->getDbTable();
        //echo "<hr>Type of ".get_class($dbt); returns "Default_Model_DbTable_CarModelsToSparePartPrices"
        //echo "<br>Type of ".print_r($dbt,true);
        //$is = $dbt->insert($data);
       // echo "<br>After ins ".print_r($db,true);
        //echo "<br>Ins = ".$is;
        
    	
    	
    	$where =  ' `car_model_id` =' .$cmo2spp->getCar_model_id().
        							 ' and `spare_part_price_id`='.$cmo2spp->getSpare_part_price_id();
		
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
        //echo "<br>After ins ".print_r($db,true);
        //echo "<br>ins = ".print_r($data,true);
        return $this->getDbTable()->insert($data);
    	
    	
        if(logger::$log_level < 20 )
        	logger::log('inserting Carmodel'.print_r($data,true));
        $num_rows_updated = $this->getDbTable()->update($data,$where);
        if($num_rows_updated != 1)
        	echo 
        	//--throw new exception
        	("Unknown number of rows updated '$num_rows_updated' with where '$where'");
        return $cmo2spp->getSpare_part_price_id();
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

    public function fetchAll($select=null)
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

