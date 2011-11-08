<?php

$db="";

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
    //$res->fetchInto( $row,  );
    //$this->id = $id;
    foreach( array_keys( $row ) as $key )
      $this->fields[ $key ] = $row[ $key ];
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
    $sth = $db->prepare( $sql );
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
  

  function update()
  {
  	//echo "In update";
    $db = $this->getDb();
    
    $sets = array();
    $values = array();
//    Kint::dump($this->fields);
    foreach( array_keys( $this->fields ) as $field )
    {
      //echo "<br> field ".Zend_Debug::dump($field,'Field',false);
      if('0'==$field ){
      	continue;
      }
      elseif(is_int($field)){
      	Kint::dump($field);
      	Kint::dump(array_keys($this->fields));
      	continue;
      }
      elseif (!isset($this->fields[$field]))
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
    $values []= $this->id;

$sql = 'UPDATE '.$this->table.' SET '.$set.
  ' WHERE '.$this->id_name.'=?';
	//$this->execute($sql,$values);
	//die ('SQL in Update<br/>'.$sql.'<br/>'.var_dump($values));
    try{$sth = $db->prepare( $sql );}
    catch(exception $e){
    	$debug=Zend_Debug::dump($sql,'Statement',FALSE);	
    	die('Exception in DBObject::prepare() while '.$sql
    		.' with values <br>'.$values
    		.'Exception:<br>'.nl2br($e)
    		.'<hr/>'.$debug);
    }
   	$this->execute( $sth, $values );
  }

  function execute($sth,$values){
  	$status = "starting";
  	$_zend_statement=null;
  	$db = $this->getDb();
  	try{
  		if(is_string($sth)){
  			$status = 'trying to create statement';
  			$_zend_statement = $db->prepare( $sth );
  		}elseif($sth instanceof Zend_Db_Statement){
  			$_zend_statement = $sth;
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
		$debug=Zend_Debug::dump($sth,'Statement',FALSE);		
		//$query = $profiler->getLastQueryProfile();
 		//echo $query->getQuery();
		die('Exception in DBObject::execute() while '.$status.' with values <br>'.$vals.'Exception:<br>'.nl2br($e).'<hr/>'.$debug);
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
  		return $this->getDb()->prepare($sql);
  	}
  	catch(exception $e){
  		die("In Prepare: SQL was<br>".$sql);
  		throw new exception("SQL Error - <br>$sql<br>" .$e);
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
