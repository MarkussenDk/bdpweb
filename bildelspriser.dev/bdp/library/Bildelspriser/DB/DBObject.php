<?php

//namespace library\Bildelspriser\DB;

$db="";

include 'UTF8.php';

class Bildelspriser_DB_DBObject
{
  private $id = 0;
  public $table;
  public $id_name;
  /** @var $fields array containing all fields */
  public $fields = array();
  //public static $sta_tab_name;
  public  $insert_statements;
  public static $db;
  public  $value_list = null;
  public $field_list = null;
  public $insert_field_list = array();
  public $value_list_count = 0;
  public $last_sql = null;
  
  function __construct( $table, $fields,$id_name=null)
  {
   $this->table = $table;
   if(self::$db==null)
      $db = $this->getDb();
   //Kint::dump($id_name);   
   if($id_name==null)
   		$this->id_name = strtolower(substr($table,0,strlen($table)-1).'_id');
   else 
   		$this->id_name = strtolower($id_name);
   		
   //die ("primary key is".$this->id_name);
   $this->_set_Table($table);
   foreach( $fields as $key ){
      $this->fields[ $key ] = null;
      if($key != $this->id_name)
      	$this->insert_field_list[$key] = $key;
   }
   //KINT::dump($this);
  }
  
  static function getColumnInfo($table){
  	/** @var stmt Zend_Db_Statement_Mysqli */
  	$stmt = self::getDb()->query("SHOW COLUMNS FROM $table ");
  	$results = $stmt->fetchAll();
  	//Zend_Debug::dump($results,'Fields',true);
  	//die();
  	return $results;
  }

  static function getFields($table){
  	$coll_info = self::getColumnInfo($table);
  	/*Fields array(21) {
  [0] => array(6) {
    ["Field"] => string(19) "spare_part_price_id"
    ["Type"] => string(16) "int(10) unsigned"
    ["Null"] => string(2) "NO"
    ["Key"] => string(3) "PRI"
    ["Default"] => NULL
    ["Extra"] => string(14) "auto_increment"
  }
  [1] => array(6) {
    ["Field"] => string(4) "name"
    ["Type"] => string(13) "varchar(1024)"
    ["Null"] => string(2) "NO"
    ["Key"] => string(0) ""
    ["Default"] => NULL
    ["Extra"] => string(0) ""
  }*/
  	$fields = array();
  	foreach ($coll_info as $col){
  		$fields[] = $col['Field'];
  	}
  	return $fields;
  }
  
  
  
  function _set_Table($table){
  	$this->table = $table;/*
  	if(empty(self::$sta_tab_name)){
  		self::$sta_tab_name = $table;
  	}
  	elseif (self::$sta_tab_name == $table)
  		return;
  	else{
  		error("sta_tab_name was already set to ".self::$sta_tab_name.' now you are setting it to '.$table);
  	}*/
  }
  
  public static function phpDateToDbDate($phpdate){
  	return date( 'Y-m-d H:i:s', $phpdate );
  }
  
  public static function DbDateToPhpDate($dbdate){
  	return strtotime( $dbdate );
  }
  
  function __get( $key )
  {
  	//echo "<br>getting $key ";
  	//Kint::dump($this->fields);
    return $this->fields[ $key ];
  }

  function __set( $key, $value )
  {
  	echo "<!-- Set( $key, $value )-->";
    if ( array_key_exists( $key, $this->fields ) )
    {
      $this->fields[ $key ] = $value;
      return true;
    }
    else error("Set Error( $key, $value )".Zend_Debug::dump($this->fields,'Fields to be matched',false));
    return false;
  }

  function load( $id )
  {
    $row=null;
  	$db = $this->getDb();
    $res = $db->query(
  "SELECT * FROM ".$this->table." WHERE ".
   $this->id_name."=?",
      array( $id )
    );
    $rows = $res->fetchAll();
    $row = $rows[0];
    //$res->fetchInto( $row,  );
    $this->id = $id;
    foreach( array_keys( $row ) as $key )
      $this->fields[ $key ] = $row[ $key ];
  }
  
  function selectWhere($where_clause){  
  	//Bildelspriser_DB_UTF8::dumpStringAsBytes($where_clause);
  	Bildelspriser_DB_UTF8::removeUTF8Junk($where_clause);
  	if(Bildelspriser_DB_UTF8::hasUTFchars($where_clause)){
  		error("Where clause has UTF8 chars '$where_clause' ");
  	}
  	$sql = 'select * from '.$this->table.' where ';
  	if(is_array($where_clause)){
  		$a = '';
  		foreach ($where_clause as $k=>$v){
  			$sql .= $a."\n\t `$k`='$v'  ";
  			$a = ' and ';
  		}
  	}
  	else{
  		$sql.= $where_clause;
  	}
    $row=null;
  	$db = $this->getDb();
  	//echo " '$sql' ";
    $res = $db->query($sql);
    $row = $res->fetchAll();
    //kint::dump('AllRow in SelectWhere',$row);
    if(!is_array($row) || count($row)==0){
		kint::dump('No data found using SQL',$sql);
		return;
	}
    //$res->fetchInto( $row,  );
    //$this->id = $id;
    reset($row);
    $row = current($row);
    //kint::dump('Row in SelectWhere',$row);
    foreach( array_keys( $row ) as $key )
      $this->fields[ $key ] = $row[ $key ];
    if($this->id==0 ){
    	if($this->id_name && array_key_exists($this->id_name, $row)){
    		$this->id = $row[$this->id_name];
    	}
    }
  }

  function create_insert_statement($ar_fields){
  
  }
  
  function insert()
  {
    $db = $this->getDb();

    $fields = $this->id_name.', ' ;
    $fields .= join( ", ", array_keys( $this->fields ) );
    $inspoints = array( "0" );
    foreach( array_keys( $this->fields ) as $field )
      $inspoints []= "?";
    $inspt = join( ", ", $inspoints );

	$sql = "INSERT INTO ".$this->table. 
	   " ( $fields ) VALUES ( $inspt )";

    $values = array();
    foreach( array_keys( $this->fields ) as $field )
      $values []= $this->fields[ $field ];
	$lid =null;
	bdp::info("Insert sql - </br>'$sql' ");
    $sth = $this->prepare( $sql );
    if($sth){
    	$lid = $sth->execute($values);
    }
    else{
    	throw new Exception("Error - statement not valid ?<br>SQL = '$sql' ");
    }
    $this->id = $db->lastInsertId();
    bdp::info('Inserted ID was '.$this->id);
    return $this->id;
  }

  public function insert_add_row($row){
  	$val_str = '';
  	$sep='';
  	if(sizeof($this->fields)<1){
  		error('DBO not properly initialized: this->fields is empty'.Kint::dump($this));
  	}
  	//kint::dump('INSERT ROW',$row);
  	//kint::dump($this->insert_field_list);
  	foreach($this->insert_field_list as $key=>$value){
  		//echo "<br>field = $key => $value";
  		$val = '';
  		$field = strtoupper($key);
	  	if(array_key_exists($field, $row)){
  	  		$val_str.=$sep."'".$v=$row[$field]."'";
			//echo " added to '$v' VALUE LIST ";
  		}else{
  			//echo " was null ";
			$val_str.=$sep.'NULL'; // null values
  		}
		$sep = ',';		
  	}
  	$strlen_new_row = strlen($val_str);
  	$strlen_all_rows = strlen($this->value_list);
  	//MAX_PREPARED_STMT_COUNT from 
  	if(($strlen_all_rows+$strlen_new_row+400)>16000)
  	{
  		bdp::info("DBO::Old Rows = $strlen_all_rows - New row = $strlen_new_row will be to big.");
  		self::insert_execute_statement();
  	}
  	else 
  		bdp::info("DBO::Old Rows = $strlen_all_rows - New row = $strlen_new_row will NOT be to big.");
  	if($sep==''){
		Kint::dump($row);
		Kint::dump($this->fields);
		error('No rows found in insert_add_row '.Kint::dump($this));
	}
	$this->value_list_count++;
	if($this->value_list=='')
		$this->value_list = $val_str;
	else
		$this->value_list .= ")\n\t,(".$val_str;	
	//bdp::log('Adding: '.$val_str);
  }
  
  
  
  public function reset_field_list(){
  	$this->field_list = null;
  }
 
  public function insert_execute_statement(){
    $nl = "\n";
	if(is_null($this->value_list))
	{
		info("The value list is empty - nothing to save");
		return;
	}
	$field_list = implode(',', $this->insert_field_list); 
  	$sql = "INSERT INTO ".$this->table." ( $field_list ) \nVALUES \n\t($this->value_list)";  	
  	Kint::dump($sql);  	
  	//error('Stopping first run to inspect');
  	$this->execute($sql,null); //values is null, since all values are added to the string ;
  	$c = $this->value_list_count;
  	$this->value_list_count=0;
  	$this->value_list = '';
  	return $c;
  }
  
  function update_row($row){
  	if(array_key_exists($this->id_name, $row))
  		unset($row[$this->id_name]);
  	foreach($row as $key=>$val){
  		$this->$key = $val;
  	}
  	//kint::dump('Updating row ',$row);
  	$this->update();	  	
  }
  

  function update($where_clause=null)
  {
  	//echo "In update($where_clause) ";
    $db = $this->getDb();
    
    $sets = array();
    $values = array();
//    Kint::dump($this->fields);
    foreach( array_keys( $this->fields ) as $field )
    {
      //echo "<br> field ".Kint::dump($field,'Field',false);
      if(!is_null($where_clause) && $field == $this->id_name){
      	echo "Skipping primary key '$field' & $this->id_name since there is another WC '$where_clause' ";
      	continue;
      }	
      if('0'==$field ){
      	continue;
      }
      elseif(is_int($field)){
      	//Kint::dump($field);
      	//Kint::dump(array_keys($this->fields));
      	continue;
      }
      elseif (!isset($this->fields[$field]))
      {
      	//echo "<br> Empty field $field ";
      	continue;
      }
      elseif ($field == 'updated')
      {
      	//echo "<br> Empty field $field ";
      	continue;
      }      
      else{	
      	$sets []= $field.'=?';
      	$values []= $this->fields[ $field ];
      }
    }
    $set = join( ", ", $sets );
    
    if(is_null($where_clause)){
    	if($this->id<0)
    		die(kint::dump('ERROR: this->id was not set on DBObject',$this));
    	//$values []= $this->id;
    	$where_clause = $this->id_name.'='.$this->id;    	
    }
    if(!str_contains($set, 'updated')){
    	$set .= ',updated = null ';
    }
	$sql = 'UPDATE '.$this->table.' SET '.$set.
  	' WHERE '.$where_clause;
	//$this->execute($sql,$values);
	//die ('SQL in Update<br/>'.$sql.'<br/>'.var_dump($values));
    //Kint::dump('UpdateSQL',$sql);
    //die('TEST');
    $cmd = 'prepare';
	try{
		$sth = $db->prepare( $sql );
		$cmd = 'execute';
		$this->execute( $sth, $values );
	}
    catch(exception $e){
    	$debug=Zend_Debug::dump('Statement with error',$sql);	
    	die(__LINE__.'Exception in DBObject::'.$cmd.'() while cmd='.$cmd.' SQL='.$sql
    		.' with values <br>'.$values
    		.__LINE__.'Exception:<br>'.nl2br($e)
    		.'<hr/>'.$debug);
    }   	
  }

  function execute($sth,$values){
  	/** @var $_zend_statement Zend_Db_Statement_Mysqli */
  	$status = "starting";
  	$_zend_statement=null;
  	$sql = null;
  	$db = $this->getDb();
  	try{
  		if(is_string($sth)){
  			$this->last_sql = $sth;
  			$status = 'trying to create statement (sql='.$sth.')';
  			$_zend_statement = $db->prepare( $sth );  			
  		}elseif($sth instanceof Zend_Db_Statement){
  			$_zend_statement = $sth;
  			$sql = $this->last_sql;
  	  	}
  	  	else{
  	  		throw new Exception("Unknown type ".get_class($sth));  	  		
  		}
  		$status='executing statement';
  		$_zend_statement->execute($values);  			
  	}
	catch(exception $e){
		$vals='<null>';
		if($values){
			kint::dump($values);
			$vals = "<table border=1px ><tr><td>Row</td><td>value</td><td>dataType</td></tr>";
			if(is_array($values))
				foreach ($values as $key=>$val){
					$type = gettype($val);
					if(is_object($val)){
						$type.=" of ".get_class();
					}
					$vals.="<tr><td>$key</td><td>$val</td><td>$type</td></tr>";		
				}
			$vals .= "</table>";
		}
		//$debug=Zend_Debug::dump('SQL Statement',$sql,$sth,'Statement',FALSE);		
		//$query = $profiler->getLastQueryProfile();
 		//echo $query->getQuery();
		$str_sth = "";
		if(is_string($sth)) $str_sth = $sth;
		elseif (is_object($sth)) { $str_sth = 'Object of type '.get_class($sth);
			/** @var $sth Zend_Db_Statement_Mysqli */
			//$str_sth .= '<br>ERROR_INFO from statement: '. ;
			$str_sth = $this->last_sql;
			kint::dump($sth->errorInfo());
		}
		echo(__LINE__.'Exception in DBObject::execute(sth='.$str_sth.') while '.$status
				.' with values <br>'.$vals.'Exception:<br>'.nl2br($e).'<hr/>');
	}
  }
  
  function delete()
  {
    $sql = 'DELETE FROM '.$this->table.' WHERE '.$this->table_id.'=?';
    $this->execute($sql,array( $this->id ));
    /*$db->execute( $sth,
      array( $this->id ) );*/
  }

  function delete_all()
  {
    $db = $this->getDb();
    $sth = $this->prepare( 'DELETE FROM '.$this->table );
    $db->execute( $sth );
  }
  
  function prepare($sql){
  	try{
  		$this->last_sql = $sql;
  		return $this->getDb()->prepare($sql);
  	}
  	catch(exception $e){
  		error("Exception: In Prepare: SQL was<br>".$sql.'<br>'.nl2br($e));
		return;
  		throw $e;
  		throw new exception("In Prepare - SQL Error - <br>$sql<br>",0,$e);
  	}  	
  }
  /**
   * @return Zend_Db_Adapter_Mysqli
   */
  public static function getDb(){
  	if(self::$db){
  		return self::$db;
  	}
  	else{
  		$db = Zend_Registry::get('db');
  		assertEx($db,"Could not create new Zend_DB() in DBObject::getDb().");
  		return self::$db = $db; 		
  	}
  }  
}
