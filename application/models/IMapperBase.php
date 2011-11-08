<?php
Zend_Loader_Autoloader::autoload('Bildelspriser_Base_SingletonBase');
require_once  'Bildelspriser/Base/SingletonBase.php';

class MapperFactory{
	const reg_name = 'MapperFactory';
	const sppm = 'Default_Model_SparePartPricesMapper';
	const spsm = 'Default_Model_SparePartSuppliersMapper';
	const cmam = 'Default_Model_CarMakesMapper';
	const cmom = 'Default_Model_CarModelsMapper';
	const cm2sppm   = 'Default_Model_CarModelsToSparePartPricesMapper';
	
	
	/*public static function Init(){
		if(Zend_Registry::isRegistered('reg_name')){
			error('MapperFactory was called multiple times');
		}
		Zend_Registry::set($reg_name, $this);
	}	*/
	public static function getMapper($mapper_name){
		if(!Zend_Registry::isRegistered($mapper_name)){
			$mapper_instance = new $mapper_name;
			//$mapper_instance = MapperBase::getInstance($mapper_name);
			Zend_Registry::set($mapper_name, $mapper_instance);
			return $mapper_instance;
		}
		return Zend_Registry::get($mapper_name);		
	}
	/**
	 * @return Default_Model_SparePartPricesMapper
	 */	
	public static function getSppMapper(){ return self::getMapper(self::sppm);}
	/**
	 * @return Default_Model_SparePartSuppliersMapper
	 */	
	public static function getSpsMapper(){ return self::getMapper(self::spsm);}
		/**
	 * @return Default_Model_CarMakesMapper
	 */	
	public static function getCmaMapper(){ return self::getMapper(self::cmam);}
	/**
	 * @return Default_Model_CarModelsMapper
	 */	
	public static function getCmoMapper(){ return self::getMapper(self::cmom);}	
	/**
	 * @return Default_Model_CarModelsToSparePartPricesMapper
	 */	
	public static function getCmo2SppMapper(){ return self::getMapper(self::cm2sppm);}	
}



interface IMapper{
	/**
	 * 
	 * @param $dbTable Zend_Db_Table_Abstract
	 * @return IMapper
	 */
    public function setDbTable($dbTable);
    /**
     * 
     * @return Zend_Db_Table_Abstract
     */
    public function getDbTable();
    //public function save();
    public function createSelect();
}

class MapperBase extends Bildelspriser_Base_SingletonBase implements IMapper{
	/** A reference to the Data Base Table.
	 * 
	 * @var Zend_Db_Table_Abstract
	 */	
	protected $_dbTable;
	/**
	 * 
	 * @var string Rows to postpone as string.
	 */
	private static $_rows_to_postpone;
	private static $_count_rows_to_postpone;
	public static $_cached_rows;
	public static $_cached_objects_indexed_by_pk;
	/**
	 * 
	 * @var Zend_Db_Abstract
	 */
	protected static $_dbAdapter;
	var $_primaryKeyValue;
	/**
	 * The ClassName of the DBTable class. eg. Default_Model_Car_Makes.
	 * @var string
	 */
	var $_dbTableName;

	/**
	 * 
	 * @var Array of Strings with the natural primary keys of the table.
	 */
	var $_unique_vars;
	
	public function __construct($primaryKeyValue = NULL){
		$this->_primaryKeyValue = $primaryKeyValue;
		$this->_count_rows_to_postpone = 0;
		$this->_rows_to_postpone = "";
	}
		
	public function __destruct(){
		//die("in destruction".strlen($this->_rows_to_postpone));
		if($this->_count_rows_to_postpone<0){
			$this->SavePostponedValuesToFile('c:\local.dat');
			die("Had to save the files manually in a destruct call");
		}
		//else log("Desctructed " . get_class($this));
	}
	/**
	 * 
	 * Enter description here ...
	 * @param integer $id Find a rowset
	 * @return Zend_Db_Table_Rowset
	 */
	public function find($id){
		//if(array_key_exists($id, self::$_cached_objects_indexed_by_pk))
		//$this->_spare_part_supplier_id
		try{		
			return $this->getDbTable()->find($id);
		}
		catch(Exception $e){
			echo ("It failed<br>".nl2br($e).'<hr>');
			Zend_Debug::dump($id,'ID before find',true);		
			Zend_Debug::dump($this->getDbTable(),'DbTable before find',true);
			die();			
		}
	}
	
	public function findObject($pk){
		if(!(   isset( self::$_cached_objects_indexed_by_pk) && 
				array_key_exists($pk, self::$_cached_objects_indexed_by_pk))){
			$zend_row = $this->find($pk);
			$model_name= $this->getModelName();
			if($zend_row->count()==0)
				return null;
			$obj = new $model_name($zend_row->current()->toArray());
			//Zend_Debug::dump($obj,'Created a new object in findObject - cache was empty',true);
			self::$_cached_objects_indexed_by_pk[$pk] = $obj;
		}
		return self::$_cached_objects_indexed_by_pk[$pk];
	}
	
	var $postponed_buffer_limit=1000;
	public function PostponeSave($data){
		$guid = uniqid();
		$newline = "\n\r";
		$this->_guid = $guid;
		$this->_count_rows_to_postpone++;
		if(is_array($data)){
			$this->_rows_to_postpone .= join(";",$data).';'.$guid.$newline;
			return;
		}
		else if(is_string($data))
		{
			$this->_rows_to_postpone .= $newline.';'.$guid.$newline;
			return;
		}
		throw new Exception("Invalid data sent to PostponeSave - must be array " + var_export($data,true));
	}
	
	public function SavePostponedValuesToDataBaseViaFile($filename){
		$this->SavePostponedValuesToFile($filename);
		$this->LoadDataToTable($filename);
		$this->_count_rows_to_postpone = 0;
		$this->_rows_to_postpone = "";		
		
	}
	
	public function SavePostponedValuesToFile($filename){
		$f = $filename;
		echo "<br/>Saving $f .";
		file_put_contents($f,$this->_rows_to_postpone);
		echo " - saved";
		$this->_rows_to_postpone = "";
		$this->_count_rows_to_postpone = 0;
				
	}
	
	public function LoadDataToTable($filename){
		$tbl_name = "";
		/* LOAD DATA [LOW_PRIORITY | CONCURRENT] [LOCAL] INFILE 'file_name'
    [REPLACE | IGNORE]
    INTO TABLE tbl_name
    [CHARACTER SET charset_name]
    [{FIELDS | COLUMNS}
        [TERMINATED BY 'string']
        [[OPTIONALLY] ENCLOSED BY 'char']
        [ESCAPED BY 'char']
    ]
    [LINES
        [STARTING BY 'string']
        [TERMINATED BY 'string']
    ]
    [IGNORE number LINES]
    [(col_name_or_user_var,...)]
    [SET col_name = expr,...]

		 * */
		$db = $this->getDbAdapter();
		$last_ins = $db->lastInsertId($tbl_name); 
		$loadData =  "LOAD DATA LOCAL INFILE '$filename' "
					."INTO TABLE $tbl_name ";
					die($loadData);
		$db->query($loadData,null); // the second param is for place holders.			
		$count_inserted = $db->lastInsertId($tbl_name) - $last_ins;
		if($count_inserted != $this->_count_rows_to_postpone){
			throw new exception("Number of records was different from expected? " . $count_inserted
				. " and expected " . $this->_count_rows_to_postpone);
		}				
	}
	
	
    public function setDbTable($dbTable)
    {
    	//C:\wamp\www\z18\soap\application\models/DbTable/CarMakes.php
    	//throw new exception(var_export($dbTable,true));
        //print('setDbTable'.$dbTable);
    	if(isset($this->_dbTable)){
        	throw new exception("Dont set me twice");
;        }
    	if (is_string($dbTable)) {
        	/*Zend_Loader_Autoloader::autoload($dbTable,
        		array(        '/application/'
        					//,'/application/models/DbTable/'
        				      //,  'C:\\wamp\\www\\z18\\soap\\application\\'
        				            ));        */	
    		//echo "Instantiating  $dbTable() ";
        	$this->_dbTableName = $dbTable;
    		$dbTable = new $dbTable();
            //$odbTable = Zend_Loader::loadClass($dbTable)
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }
    
    final public function setUnique_vars(Array $unique_vars){
    	$this->_unique_vars = $unique_vars;
    } 
    
    final public function getUnique_vars(){
    	//assertEx($this->_unique_vars,"Unique vars was not set")
    	return $this->_unique_vars;
    }
    
    /**
     * @return string The DbTable name of the mySql class.
     */
    public function getDbTableName(){
    	if(null === $this->_dbTableName){
    		throw new Exception('No DbTable name found. This seems like a coding error. Contact support.');
    	}
    	return $this->_dbTableName;
    }

    public function getModelName(){
    	if(null === $this->_dbTableName){
    		throw new Exception('No DbTable name found. This seems like a coding error. Contact support.');
    	}
    	return str_replace('_DbTable','',$this->_dbTableName);
    }
    
    /**
     * @return Zend_Db_Table_Abstract
     */
    public function getDbTable()
    {
        if (null == $this->_dbTable) {
        	assertEx($this->_dbTableName, ' $this->_dbTableName was undefined '.var_export($this,true));            
        	$this->setDbTable($this->_dbTableName);
        }
        return $this->_dbTable;
    }    
    
    /**
     * (non-PHPdoc)
     * @return Zend_Db_Select
     */
    public function createSelect(){
    	return $this->getDbTable()->select();
    }
    
    /*public function getPrimaryKeyValue(){
    	$pk_name =$this->getDbTable()->info('primary');
    	throw new Exception ("Do it when needed - not implemented");
    	//$pk_value = $this->
    }*/
    /**
     * 
     * @return string Name of the primary key
     */
    public function getPrimaryKeyName(){
        $pk_name = $this->getDbTable()->info('primary');
        $tab_name = $this->getDbTable()->info('name');
    	assertEx($pk_name,"No primary key on the table. Define this in the constructor of the Default_Model_DbTable_$tab_name class.");
    	return $pk_name;
    }
    
    /**
     * 
     * @return Zend_Db_Adapter_Mysqli
     */
    public function getDbAdapter(){
    	if(null === self::$_dbAdapter){
    		$db = $this->getDbTable()->getAdapter(); 
    		self::$_dbAdapter = $db;
    		$db->query('SET NAMES utf8');
			$db->query('SET CHARACTER SET utf8');		
    		Zend_Registry::set('db', $db);
    		//http://stackoverflow.com/questions/921024/registering-zend-database-adapter-in-registry
    	}
    	assertEx(self::$_dbAdapter,"The DbAdapter was not retrieved from the DbTable()->getAdapter() - coding error.");    		
    	return self::$_dbAdapter;
    }
    
    /**
     * http://php.net/manual/en/function.mysql-insert-id.php
     * 
     */
	public function saveReturnInsertId($model){
		$insert = $this->save($model);
		return 1;
		if( empty($insert)){
			throw new Exception("No insert value  was given. ".var_export($model,true));
		}
		//return $insert;
		// Consider getting it alternatively?
		//$this->getDbTable()->		
	}	

    /**
     * 
     * @param $select
     * @return Array of Associative Arrays containing CarMakes 
     */
    public function fetchAll_Array($select){
        try{
        	$resultSet = $this->getDbTable()->fetchAll($select);
    	}
    	catch(Exception $e){
    		$err  = Zend_Debug::dump($select,'Select',true);
			$err .= Zend_Debug::dump($e,'Error',true);
    		die("<br><b>Exception occured<b><br> -- SQL: '$select' ");
    		 
    	}
    	return $resultSet->toArray();
    }	
    
    /**
     * 
     * @param $select
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function fetchAll($select=null){
        try{
        	$resultSet = $this->getDbTable()->fetchAll($select);
    	}
    	catch(Zend_Db_Statement_Mysqli_Exception $zdsme){
    		echo "SQL Exception";
    		Zend_Debug::dump($select,'select',true);
    		Zend_Debug::dump($zdsme,'zde',true);    		
    	}
    	catch(Exception $e){
    		$str="<br><b>Exception occured while fetchAll<b><hr> -- SQL: '$select' ";
    		$str.='<hr>Message<br>'.$e->getMessage();
    		$str.='<hr>Exception type<br>'.$e->__toString();
    		//$str.='<hr>'
    		error($str); 
    	}
    	return $resultSet;
    }
    
    /**
     * 
     * @param $select
     * @return Zend_Db_Table_Row_Abstract
     */
    public function fetchRow($select){
        try{
        	$row = $this->getDbTable()->fetchRow($select);
    	}
    	catch(Exception $e){
    		echo "<br><b>Exception occured<b><br> -- SQL: '$select' ";
    		var_dump($e,$select); 
    	}
    	return $row;
    }    
  
    function getCacheArrayName($table_name){
    	return $table_name.'_from_current_supplier';
    }
    
    function fillCacheDirect($spare_part_supplier_id){
		$start = microtime(true);
    	$mysqli=$this->getDbAdapter()->getConnection();
    	$table_name = 'spare_part_prices';
    	$array_name =  $this->getCacheArrayName($table_name);
    	$unique_vars = $this->getUnique_vars();
    	/*die("class_name ".print_r($conn).var_export(get_class_methods(get_class($conn)),true)
    	);*/
    	
    	
    	/*$conn->exec("");
    	$res = mysql_query()
    	$ar = mysql_fetch_assoc()
    	$select = $db->select(array('spare_part_price_id','spare_part_supplier_id'));
    	//echo " tableName  ".$this->getDbTableName();
    	$select->from('spare_part_prices');
    	$select->where(' spare_part_supplier_id = ? ',$spare_part_supplier_id);
    	$o =  $select->query(Zend_Db::FETCH_ASSOC);
    	//$o = $this->getDbTable()->fetchAll(' spare_part_supplier_id = '..' ',);
    	//var_dump($o);
    	$sql_grp = "SELECT COUNT(*) AS `R�kker`, `spare_part_supplier_id` FROM `spare_part_prices` GROUP BY `spare_part_supplier_id` ORDER BY `spare_part_supplier_id` LIMIT 0, 30 ";
    	*/
    	$sql = "select * from $table_name where spare_part_supplier_id = $spare_part_supplier_id ";
    	//$sql = $o;
    	//$ar = $db->fetchAll($sql);
    	//$mysqli = (mysqli)$mysqli;
    	$result = $mysqli->query($sql);
    	$s1 = microtime(true);
    	$diff_load  =  microtime_diff($start);
    	if($log)
    		$log->log("IMB: Number of $table_name (rows) ".$rows=$result->num_rows.' ');
        if ($result) {
            while ($row = $result->fetch_assoc()) {
            	$id = '';
            	foreach ($unique_vars as $key)
	            	$id .= $row['supplier_part_number'];
	    		self::$$array_name[$id] = $row;
	            /* Commented out for memory usage
	            $spp_id = $row['spare_part_supplier_id'];
	    		self::$prices[$spp_id][$id] = $row;
	    		*/
            }
            $result->close();
        }
		else{
			error("sql:<br>$sql<br>No result".$mysqli->error.$mysqli->sqlstate);	
		}
		$log = Bildelspriser_XmlImport_PriceParser::$_instance;
		if($log){  	
    		$diff_2 =  microtime_diff($s1);
    		$log->log("Direct Rows <b>'$table_name'</b> loaded $rows  from supplier id $spare_part_supplier_id ");
    		$log->log("Database - Load time = $diff_load seconds  ".(1.0*$rows/$diff_load)." rows pr sec ");
    		$log->log("PHP Processing - Foreach time = $diff_2 seconds  ".(1.0*$rows/$diff_2)." rows pr sec ");    	
		}
    }
    
	public function verifySparePartSupplierId($sps_id){
		$table_name = 'spare_part_prices';
    	$array_name =  $this->getCacheArrayName($table_name);
		if(empty(self::$$array_name)){
			try{
				$this->fillCacheDirect($sps_id);
				self::$current_supplier_id=$sps_id;
			}
			catch(exception $e){
				error("Exception in mapper ".$e);
			
			}
			return;
		}
		if(self::$current_supplier_id!=$sps_id){
			echo "Now you are chaning the supplier from ".$prices_from_current_supplier.
				" to ".$sps_id;
			self::$prices_from_current_supplier = null;
			$this->verifySparePartSupplierId($sps_id);
		}
	}
	
	public function fillRowCache($limit = 1000, $where = '' ){
		$db = $this->getDbAdapter();
		//$select = $db->select()->from($this->getDbTable()->getDefinition());
		/*$select->limit($limit);
		if($where!=''){
			$select->where($where);
		}*/
		self::$_cached_rows = $this->fetchAll();
	}
    
	public function fillRowCacheIfEmpty($limit = 1000, $where = '' ){
		if(isset(self::$_cached_rows))
			return;
		$this->fillRowCache($limit,$where);
	}
	
	public function fillObjectCacheFromRowCache(){
		if(isset(self::$_cached_rows))		
			foreach (self::$_cached_rows as $row){
				///** 
				$pk = 0;
				$model_name = $this->getModelName();
				$new_model = new $model_name($row->toArray());
				/** @var $model_name Default_Model_BaseWithTraceability */
				//Zend_Debug::dump($new_model,'New Model'.$model_name,true);
				self::$_cached_objects_indexed_by_pk[$new_model->getPrimaryKeyValue()] = $new_model;
			}		
	}
}
