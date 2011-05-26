<?php
Zend_Loader_Autoloader::autoload('Bildelspriser_Base_SingletonBase');
require_once  'Bildelspriser/Base/SingletonBase.php';

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
		return $this->getDbTable()->find($id);
	}
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
    		self::$_dbAdapter = $this->getDbTable()->getAdapter(); 
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
    		echo "<br><b>Exception occured<b><br> -- SQL: '$select' ";
    		var_dump($e,$select); 
    	}
    	return $resultSet->toArray();
    }	
    
    /**
     * 
     * @param $select
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function fetchAll($select){
        try{
        	$resultSet = $this->getDbTable()->fetchAll($select);
    	}
    	catch(Exception $e){
    		echo "<br><b>Exception occured<b><br> -- SQL: '$select' ";
    		var_dump($e,$select); 
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
    
}
