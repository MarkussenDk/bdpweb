<?php
/** Filename: /application/models/Base.php
 * 
 */
require_once('Base.php');
// for inspiration to this class - see
// http://framework.zend.com/docs/quickstart/create-a-model-and-database-table

class state_enum{
	const _suggestion = 'Forslag'; 	
	const _public = 'Offentlig';
	const _alternative = 'Alternativ';
	const _deleted = 'Slettet'; 	
}

class Default_Model_BaseWithTraceability //extends Default_Model_Base
{ 	
	private $_created=null;
	private $_created_by=null;
	private $_updated=null;
	private $_updated_by=null;
	private $_mapper;
	private $_mapper_name;
	private $_current_user_name; // Active User
	private $_guid; // used to keep reference with delayed writes; // as a replacement primary key.
	private $_dirty_flag;
	public $_user_name_required;
	private static $_rows;
	public $class_vars;
	public $object_vars;
	/**
	 * 
	 * @var Zend_Db_Table_Row
	 */
	private $_zend_db_table_row;
	protected $_options;
	
    public function __construct(array $options = null,$mapper_name=null)
    {
        $this->_user_name_required = true; // by default username is required to save things - exept for anonymous things
    	//echo ("in Base with trace".get_class($this));
    	if(is_string($mapper_name)){
        	$this->_mapper_name = $mapper_name;
        }
		if(!isset($this->class_vars)){
			 $this->class_vars = get_class_vars($this->getMapper()->getModelName());
		}
		if (is_array($options)) {
            $this->setOptions($options);
        }
        $this->object_vars = get_object_vars($this);
        /*$cls =$this->getMapper()->getModelName();
        $this_list = Zend_Debug::dump(get_class_vars($cls),'__CONSTRUCTOR_TIMEClassVars:THIS '.$cls,false);
		$this_list = Zend_Debug::dump($this->class_vars,'_Constructor time '.$cls.'vars');
        die($this_list);*/
        $db = $this->getMapper()->getDbAdapter();

    }   
   
	public function checkForExistanceAndSetPrimaryKey(array &$data){
		//$unique_vars = array('supplier_part_number','price_inc_vat','spare_part_supplier_id');
		$unique_vars = $this->getMapper()->getUnique_vars();
		if(empty($unique_vars)){
			return false;
		}
		$where_clause_ar = array();
		$where_post_fix = ' = ? ';
        is_array($unique_vars)
        	or error('Variable \$unique_vars can only be an array.');
		$table = $this->getMapper()->getDbTable();
		$stmt =  $table->select();  
		//	->from($table,'spare_part_price_id')      
			//->where($where_clause_ar)        
//			->order('bug_id ASC')        
//			->limit(1, 0)  	
		
		foreach ($unique_vars as $var){
			if(isset($data[$var]	)){			
				$val = $data[$var];
				//if empty - it just means tha 
				//assertEx($val," $var $val was empty - check the constructor of your mapper class ".$this->_mapper_name);
				//$where_clause_ar[$var.$where_post_fix] = $val;
//				$where_string = ""
				$stmt->where($var.$where_post_fix,$val);
			}
			else
				return false;
		}
		$pkNames=$this->getPrimaryKeyName();
		$stmt->order('created'); // we dont care aabout history - take the oldest
		//assertEx("","where arryay ".var_export($where_clause_ar,true));
		//$select->from($table, array('bug_id', 'bug_description'))
		$rowset=$table->fetchAll($stmt);
		//$row = $table->fetchRow($stmt);//Old way
		$row = $rowset->current();
		$count = $rowset->count();
		
		if(isset($row)){
			$this->_zend_db_table_row = $row;
			$ar = $row->toArray();
			//echo "pkName '$pkName' <br/>";
			foreach($pkNames as $pkName){
				$pk_id = $ar[$pkName];
				//$spp_id = $ar['spare_part_price_id'];
				$data[$pkName] = $ar[$pkName];
				assertEx($pk_id, "PrimaryKey $pkName was not found?");
			}
			//var_dump($pkName);
			//var_dump($ar);
			//return true;
		}
		else return false;
		$rowset->next();
		$row = $rowset->current();
		while($row){
			//die("In the row ".var_export($row,true));
			//$row = $table->fetchRow($stmt);			
			$this->_zend_db_table_row = $row;
			$row->delete();
			/*
			$ar = $row->toArray();
			$pk_id = $ar[$pkName];
			$spp_id = $ar['spare_part_price_id'];
			$spn = $ar['supplier_part_number'];
			echo "<br/>$spn  spp_id was $spp_id ";
			//$data['spare_part_price_id'] = $ar['spare_part_price_id'];
			assertEx($spp_id, "SPP was not found?");	*/		
			$rowset->next();
			$row = $rowset->current();
		}
		return true;
	}
    
    public function setOptions(array $in_options)
    {
    	//kint::dump('set',$in_options);
    	$options = $in_options;
    	if($in_options instanceof Zend_Db_Table_Row){
    		$options = $in_options->toArray();
    	}
		$exists=$this->checkForExistanceAndSetPrimaryKey($options);
    	$this->_options = array();	
    	if(!is_array($this->object_vars))
    		$this->object_vars = get_object_vars($this);
    	$class_vars = get_class_vars(get_class($this));
    	$methods = get_class_methods($this);
    	$log = "setOptions $exists ".$this->_mapper_name;
        foreach ($options as $key => $value_raw) {
        	//$log .= "<br/> $key => $value_raw ".strpos($key, '_id');
        	if(2>strpos($key, '_id') && (strpos($value_raw,'Ã') || strpos($value_raw,'Â'))  ){ // anything but primary keys
	        	$val_dec = utf8_decode($value_raw);	        	
        		$val_enc = utf8_encode($value_raw);
	        	$value = $val_enc;
	        	/*if($val_enc != $val_dec){
	        		$log.="<br/><h2>$key-  Now for some UNICODE FUN </h2>";
	        	}*/
	        	if(strlen($value) > strlen($val_dec) ){
	        		$value = $val_dec;
	        	}
	        	$enc_data = array( 'normal' => $value , 'enc'=> $val_enc , 'decod'=>$val_dec);
	        	$table = '<table>';
	        	/*foreach($enc_data as $key => $val)
	        		$table .= '<tr><td><b>'.$key.'</b></td><td>'.$val.'</td></tr>';*/
	        		$table .= '<tr><td><b>normal</b></td><td>'.$value.'</td></tr>';
	        		$table .= '<tr><td><b>enc</b></td><td>'.$val_enc.'</td></tr>';
	        		$table .= '<tr><td><b>decod</b></td><td>'.$val_dec.'</td></tr>';
	        		$table .= "</table>";
	        	$log.=$table; 
	        	//$log .= "<table><tr><td><b>encoded</b> $val_enc - decoded $val_dec - normal $value ";
        	}
        	else
        		$value = $value_raw;
        	if(array_key_exists($key,$this->class_vars)){ // search for public direct vars e.g. key
        		//Zend_Debug::dump($class_vars,'LocalClassVars');
        		//Zend_Debug::dump($class_vars,'ClassVars');
        		$this->$key = $value;
        		continue;
        	}
            if(array_key_exists('_'.$key,$this->class_vars)){ // search for public sub vars e.g. _key
        		$this->{'_'.$key} = $value;
        		continue;
        	}
        	if(array_key_exists('_'.$key,$this->object_vars)){ // search for public sub vars e.g. _key
        		$this->{'_'.$key} = $value;
        		continue;
        	}
        	$method = 'set' . ucfirst(strtolower($key));
            if (in_array($method, $methods)) {
                $this->$method($value);
            }            
            else{
            	$class = get_class($this);            	
            	throw new Exception("Method '".$class."::"
            		.$method."' not implemented on  - "
            		."\nValue used was '$value' - '$key' "
            		.". \n<br>The options array is ".nl2br(var_export($options,true)));            	
            }
            $log.="\n end($key=>$value)";
        }
        bdp::log($log);
/*      $user_name = Default_Model_SparePartSuppliersMapper::getIdentityAsString();
        $class = get_class($this);
        assertEx($user_name,"No UserName was found in class $class, perhabs you havent authenticated on the SPS.");
        $this->_current_user_name = $user_name;*/
        //$this->setUpdated_by($user_name);
        return $this;
    }
		
	/* public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid car_makes property"'.$name.'"');
        }
        $this->$method($value);
    }*/
	
	public function __get($name) { //$name holds the name of the undefined attributes getting called.
		//$cls = $this->getMapper()->getModelName();
		//Zend_Debug::dump(get_class_vars($cls),$cls.'Class Vars  '.$name,true);
		
		if (array_key_exists($name, $this->class_vars )) { // search for public direct vars e.g. key
			return $this->$name;
		}
		if (array_key_exists( '_' . $name, $this->class_vars )) { // search for public sub vars e.g. _key
			return $this->{'_' . $name};
		}
//		kint::dump('__get('.$name.'):OBJ_VARS'.get_class($this),get_object_vars($this));
//		die();
		//if(array_key_exists('_' . $name, get_object_vars($this) /*$this->object_vars*/)){
		//	kint::dump("DUMPING with Fancy approach $name .",$this);
		//	return $this->${'_'.$name};
		//}
		$method = 'get' . ucfirst ( $name );
		if (('mapper' == $name) || ! method_exists ( $this, $method )) {
			$class_name = get_class ( $this );
			//--die('X');
				
			$this_list = Zend_Debug::dump('ClassVars: '.__class__,get_class_vars($class_name),false);
			$this_list = Zend_Debug::dump('ClassVars: '.__class__,get_class_vars(__CLASS__),false);
			$this_list = Zend_Debug::dump('ObjectVars: THIS '.__class__,get_object_vars($this),false);
			$this_list .= Zend_Debug::dump($this,'This',false);
			$method_list = Zend_Debug::dump(get_class_methods($this),'Class Methods in '.__CLASS__,false);
			
			//die();
			throw new Exception ( 'BaseWithTrace: Invalid Base property - "' . $name . '" - Method requested : "' . $class_name . '->' . $method . '()"'.$method_list.$this_list  )//.var_export($this,true)
			;
		}
		return $this->$method ();
    }

    final public function getCreated_by(){
    	if(null == $this->_created_by){
    		$this->_created_by = $this->getCurrent_user_name();
    		assertEx($this->_created_by, "Current username was empty in getCreatedBy");
    		
    	}
    	return $this->_created_by;
    }
    
  /*  final public function get_created_by(){
    	return $this->getCreated_by();
    }*/
    
    final public function getCurrent_user_name(){
    	$user_name=null;
    	if(empty($this->_current_user_name)){
    		try{
	       		$user_name = Default_Model_SparePartSuppliersMapper::getIdentityAsString(false);
	       	}
	       	catch(Exception $e){
	       		if($this->_user_name_required){
	       			throw $e;
	       		}
	       		else
	       			 $user_name = 'anonymous'; // call get web user name function - that uses GUI.
	       	}
	       	$class = get_class($this);
	        assertEx($user_name,"No UserName was found in class $class, perhabs you havent authenticated on the SPS.");    	
			$this->_current_user_name = $user_name;
    	}    	
    	return $this->_current_user_name;
    }
        
    final public function getCreated(){
    	return $this->_created; // Data
    }
    
    final public function getUpdated_by(){
        if(null == $this->_updated_by){
    		$this->_updated_by = $this->_current_user_name;
    	}
    	return $this->_updated_by;
    }
        
    final public function getUpdated(){
    	return $this->_updated; // Date
    }
    
    
    final public function setCreatedBy($text){
    	$this->_created_by = (string)$text;
    	return $this;
    }

    final public function setUpdated_by($text){
    	$this->_updated_by = (string)$text;
    	return $this;
    }    
    
    final public function setUpdated($date){
    	$this->_updated = $date;
    	return $this;
    }    

    final public function setCreated($date){
    	$this->_created = $date;
    	return $this;
    }    
    
    final public function setCreated_by($user_name){
    	$this->_created_by = $user_name;
    }
    
    final public function setMapper($mapper)
    {
        $this->_mapper = $mapper;
        return $this;
    }

	/**
	 * 
	 * @return MapperBase 
	 */
    final public function getMapper()
    {
    	if (null === $this->_mapper) {
        	if(null === $this->_mapper_name){
        		throw new Exception("Mapper name was not defined - must be set in the constructor as second parameter on the child class");       		
        	}
        	//die($this->_mapper_name);
        	$mapper = new $this->_mapper_name;
            $this->setMapper($mapper);
            //var_dump()
        }
        if($this->_mapper instanceof MapperBase){
        	return $this->_mapper;
        }
        else
        	throw new Exception("The mapper was not a mapper".gettype($this->_mapper));
        return null;
    }
    
    final public function getPrimaryKeyName(){
   		return $this->getMapper()->getPrimaryKeyName();
    }
    
    final public function getPrimaryKeyValue(){
    	$pkname = $this->getPrimaryKeyName();
    	//$l = sizeof($pkname);
    	assertEx( sizeof($pkname)== 1,"PK name contiains more than one Primary key? ".var_export($pkname,true)."<br>'".sizeof($pkname).'"');
    	$pkname_string = end($pkname);
    	assertEx(is_string($pkname_string)," Pk_name_string was not a string ".var_export($pkname_string,true));
    	return $this->$pkname_string;
    }
    
    /**
     * @return boolean Returns TRUE if the primarykey is set and different form 0
     */
    final public function isPrimaryKeySet(){
    	$pk_val = $this->getPrimaryKeyValue();
    	return ((isset($pk_val)) && $pk_val <> 0); 
    }
    
    public function SaveStrategyType(){
    	if($this->_mapper_name == 'Default_Model_SparePartPricesMapper'){
    		return "PostPonedSave";
    	}
    	else
    		return "NormalSave";
    }
    
  /*  public function PostPonedSave($array){
    	$this->getMapper()->PostponeSave($data);
    }*/
    
   /* public function __toString()
    {
    	$class_info = 'Class'.get_class($this)."<ul>";
    	$this->class_vars = get_object_vars($this);
    	foreach($this->class_vars as $key){
    		$class_info .= "<li>key</li>";
    	}
    	$class_info .='<ul/>';
    	throw new exception("tostring called");
    	return $class_info;
    	//return //var_export($this,true);
    }*/
    
    /**
     * 
     * @return primary_key_value
     */
    public function save()
    {
        $verbose = false;
    	$this->_current_user_name = $this->getCurrent_user_name();
    	$mapper=$this->getMapper();
        //assert($mapper,"Mapper must be set");
        //die("type was")
        //var_dump_array($mapper,"Mapper");
    	try{
    		//throw new exception("");
    		//throw new exception ("Type was ".gettype($mapper)." \nThe var export ".var_export($mapper,true));
    		//$type = gettype($mapper);
    		//$mapper=$this->getMapper();
    		$mapper instanceof MapperBase
    			or error("Mapper was does not inherit a Mapper_base, Coding error! - Contact support!");
    		//$id_array = array();
    		$id=$mapper->save($this);
    		if( is_scalar($id))		
    			if($id <= 0) //PK 
    			{    				
    				throw new exception('PK should be higher than 0 it was '.$id);
    			}
    		else {
				/*$id_array = id;
				$id = first()
    			throw new exception('PK was array '.print_r($id,true));*/
    		}
    		if($verbose){
	    		print "<hr>(baseWithTrace::Save()-ID was  ";
	    		print_r($id);
	    		print ' <br> primary key name ';
	    		print_r($this->getPrimaryKeyName());
    		}
    		return $id;	
    		//return $this->getMapper()->save($this);
        }
        catch(Zend_Db_Exception $zend_db_ex){
        	$class = get_class($this);        	
        	$vars = get_class_vars($class);
        	$var_info = "Public variables : ";
        	foreach($vars as $key=>$value){
        		$var_info .= "\n    -  $key = '$value'";    
        	}
        	$class_info = "\n Class : $class "." With ".$var_info;
        }        
        catch(Exception $e){
        	$type = "";
        	if(is_object($mapper)){
        		$type = get_class($mapper);
        	}
        	else $type = gettype($mapper);       	        	
        	//throw new exception ("Mapper type was '$type' \n The var export ".get_class($mapper) ." <br>". $e->getMessage());
        	//$e->
        	throw $e;       	
        	
        }
        //return $this->_mapper->save($this);
        //return self::getMapper()->save($this);
    }
    
    /**
     * 
     * @return primary_key_value
     */
    public function saveReturnInsertId()
    {
    	$id = (int)$this->getMapper()->save($this);
    	//var_dump_array($id);
    	if(is_int($id)){
    		//var_dump_array($id);
    		return 	$id;
    	}
    	//var_dump_array(array('$id',$id));
        return $this->getMapper()->saveReturnInsertId($this);
    }
    

    public function find($id)
    {
        $ret = $this->getMapper()->find($id, $this);
        $ar=$ret->toArray();
        if(!is_array($ar)){
        	error("Spp not found '$id'");
        	return null;
        }$this->setOptions($ar[0]);
        return $this;
    }
    
  /*  public function addAsSimpleXmlChildNode(SimpleXMLElement &$elem){
    	$child = $elem->addChild('car_makes');
    	$child->addChild('car_make_id',$this->getCar_make_id());
    	$child->addChild('car_make_name',$this->getCar_make_name());
    	$main_id = $this->getCar_make_main_id() or $this->getCar_make_id();
    	$child->addChild('car_make_main_id',$m_id);
    	$child->addChild('created',$this->getCreated());  
    	$child->addChild('updated',$this->getUpdated());
    	return $child;
    }*/

    public function fetchAll($select=null)
    {
        $mapper=$this->getMapper();
        if(!isset($mapper)){
        	throw new exception ("Mapper not set");
        } 
        else
    	return $this->getMapper()->fetchAll($select);
    }
    
    public function getCachedById($id){
    	if(empty(self::$_rows)){
    		$select = $this->getMapper()->getDbTable()->select(true);
    		//$select->from($this->getMapper()->getDbTable()->);
    		$select->limit(1000);
    		$tmp_rows=array();  		
    		//($where = null, $order = null, $count = null, $offset = null)
    		//$tmp_rows = $this->getMapper()->getDbTable()->fetchAll('1=1',$this->getPrimaryKeyName(),1000);
    		$tmp_rows = $this->fetchAll_Array($select);
    		$pk_ar = $this->getPrimaryKeyName();
    		if(count($pk_ar)==1){    			
	    		$pk_name = implode($this->getPrimaryKeyName(),'');
	    		//print_r($tmp_rows);
	    		foreach ($tmp_rows as $row){
	    			echo '<!-- <hr>caching '.$pk_name.' = '.$row[$pk_name].'-->';
	    			self::$_rows[$pk_name] = $row;
	    		}
    		}    		
    	}
    	if(is_array(self::$_rows) and  array_key_exists($id, self::$_rows)){
    		return self::$_rows[$id];
    	}
    	else{
    		return self::find($id);
    	}    	
    }
    
    public function fetchAll_Array($select)
    {
        $mapper=$this->getMapper();
        if(!isset($mapper)){
        	return "Mapper not set";
        	throw new Exception("mapper not defined");
        } 
        else
        	//die("Mapper was set");
    	return $this->getMapper()->fetchAll_Array($select);
    }
    
    public function __call($methodName, $args){
    	echo "\n\n<br>## - call";
		echo "methodName  $methodName, args=".print_r($args,true) ;
		die("Ended - unknown function called");    
    }
}