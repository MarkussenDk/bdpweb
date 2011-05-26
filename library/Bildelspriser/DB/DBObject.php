<?php

$db="";

class Bildelspriser_DB_DBObject
{
  private $id = 0;
  private $table;
  private $id_name;
  private $fields = array();
  public static $sta_tab_name;
  public static $insert_statements;
  public static $db;

  function __construct( $table, $fields )
  {
   $this->table = $table;
   $this->id_name = substr($table,0,strlen($table)-1).'_id';
   //die ("primary key is".$this->id_name);
   $this->_set_Table($table);
   foreach( $fields as $key )
      $this->fields[ $key ] = null;
  }

  function _set_Table($table){
  	if(empty(self::$sta_tab_name)){
  		self::$sta_tab_name = $table;
  	}
  	else{
  		echo "sta_tab_name was already set to ".self::$sta_tab_name.' now you are setting it to '.$table;
  	}
  }
  
  public static function phpDateToDbDate($phpdate){
  	return date( 'Y-m-d H:i:s', $phpdate );
  }
  
  public static function DbDateToPhpDate($dbdate){
  	return strtotime( $mysqldate );
  }
  
  function __get( $key )
  {
    return $this->fields[ $key ];
  }

  function __set( $key, $value )
  {
  	echo "Set( $key, $value )";
    if ( array_key_exists( $key, $this->fields ) )
    {
      $this->fields[ $key ] = $value;
      return true;
    }
    else echo "Set Error( $key, $value )";
    return false;
  }

  function load( $id )
  {
    $db = $this->getDb();
    $res = $db->query(
  "SELECT * FROM ".$this->table." WHERE ".
   $this->id_name."=?",
      array( $id )
    );
    $res->fetchInto( $row, DB_FETCHMODE_ASSOC );
    $this->id = $id;
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
	echo "<br/>Insert sql - $sql<br/>";
    $sth = $db->prepare( $sql );
    if($sth){
    	$lid = $sth->execute($values);
    }
    else{
    	throw new Exception("Error - statement not valid ?<br>SQL = '$sql' ");
    }

    $this->id = $db->lastInsertId();
    return $this->id;
  }

  function update()
  {
  	echo "In update";
    $db = $this->getDb();
    
    $sets = array();
    $values = array();
    foreach( array_keys( $this->fields ) as $field )
    {
      $sets []= $field.'=?';
      $values []= $this->fields[ $field ];
    }
    $set = join( ", ", $sets );
    $values []= $this->id;

$sql = 'UPDATE '.$this->table.' SET '.$set.
  ' WHERE '.$this->id_name.'=?';
	$this->execute($sql,$values);
	/*echo ('SQL in Update'.$sql.var_dump($values));
    $sth = $db->prepare( $sql );
    $this->execute( $sth, $values );*/

  }

  function execute($sth,$values){
  	$status = "starting";
  	$_zend_statement;
  	$db = $this->getDb();
  	try{
  		if(is_string($sth)){
  			$status = 'trying to create statement';
  			$_zend_statement = $db->prepare( $sth );
  		}elseif(is_a($sth,'Zend_Db_Statement')){
  			$_zend_statement = $sth;
  	  	}
  	  	else{
  	  		throw new Exception("Unknown type ".get_class($sth));  	  		
  		}
  		$status='executing statement';
  		$_zend_statement->execute($values);  			
  	}
	catch(exception $e){
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
		die('Exception in DBObject::execute() while '.$status.' with values <br>'.$vals.'Exception:<br>'.$e);
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
  function getDb(){
  	if(self::$db){
  		return self::$db;
  	}
  	else{
  		$db = new Zend_Db();
  		assertEx($db,"Could not create new Zend_DB() in DBObject::getDb().");
  		return self::$db = $db; 		
  	}
  }  
}
