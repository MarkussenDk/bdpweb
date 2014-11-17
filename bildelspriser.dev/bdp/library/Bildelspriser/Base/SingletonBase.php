<?php
/**
 *
 * @category   Bildelspriser
 * @package    Base
 * @subpackage SingletonBase
 * @copyright  Copyright (c) 2005-2008 Andreas Markussen
 * @version    1.0
 * @license    
 */

/**
 * SingletonBase
 * 
 * @uses       
 * @package    
 * @subpackage 
 * @copyright  
 * @license    
 */
require_once 'Bildelspriser/all.inc.php';

//if (function_exists ( 'get_called_class' )) {
/*function get_called_class_52() {
	$t = debug_backtrace ();
	$t = $t [0];
	//throw new exception(var_export($t,true));
	if (isset ( $t ['object'] ) && $t ['object'] instanceof $t ['class'])
		return get_class ( $t ['object'] );
	return false;
}*/
//}


//if (function_exists ( 'get_called_class' )) 

/*	function get_called_class_522() {
		$c=0;
		$res="";
		foreach ( debug_backtrace () as $trace ) {
			if (isset ( $trace ['object'] ))
			{
				echo "<br>t: ".$c++.' '.get_class ( $trace ['object'] );
				if ($trace ['object'] instanceof $trace ['class']){
						//return get_class ( $trace ['object'] );
						if(strpos(get_class ( $trace ['object'] ),'Mapper')>1){
							$res = get_class ( $trace ['object'] );
							echo " - used";
						}
						
				}				
			}
		}
		if($res){
			debug_print_backtrace();
		}
		//$res = 'Default_Model_SparePartSuppliersMapper';
		assertEx($res<>"","Class not found?");
		return $res;
		return false;
	}
*/

/******************************** 
 * Retro-support of get_called_class() 
 * Tested and works in PHP 5.2.4 
 * http://www.sol1.com.au/ 
 ********************************/
///*if (! function_exists ( 'get_called_class' )) {
	function get_called_class_hc($bt = false, $l = 1) {
		if (! $bt)
			$bt = debug_backtrace ();
		if (! isset ( $bt [$l] ))
			throw new Exception ( "Cannot find called class -> stack level too deep." );
		if (! isset ( $bt [$l] ['type'] )) {
			throw new Exception ( 'type not set' );
		} else
			switch ($bt [$l] ['type']) {
				case '::' :
					$lines = file ( $bt [$l] ['file'] );
					$i = 0;
					$callerLine = '';
					do {
						$i ++;
						$callerLine = $lines [$bt [$l] ['line'] - $i] . $callerLine;
					} while ( stripos ( $callerLine, $bt [$l] ['function'] ) === false );
					preg_match ( '/([a-zA-Z0-9\_]+)::' . $bt [$l] ['function'] . '/', $callerLine, $matches );
					if (! isset ( $matches [1] )) {
						// must be an edge case. 
						throw new Exception ( "Could not find caller class: originating method call is obscured." );
					}
					switch ($matches [1]) {
						case 'self' :
						case 'parent' :
							return get_called_class_hc ( $bt, $l + 1 );
						default :
							return $matches [1];
					}
				// won't get here. 
				case '->' :
					switch ($bt [$l] ['function']) {
						case '__get' :
							// edge case -> get class of calling object 
							if (! is_object ( $bt [$l] ['object'] ))
								throw new Exception ( "Edge case fail. __get called on non object." );
							$class_name = get_class ( $bt [$l] ['object'] );
							unset($bt);
							return $class_name;
						default :
							$class_name = "".$bt [$l] ['class'];
							unset($bt);
							return $class_name;
					}
				
				default :
					throw new Exception ( "Unknown backtrace method type" );
			}
	}
//}

// http://bugs.php.net/bug.php?id=42830
/**
 * Class Singleton is a generic implementation of the singleton design pattern.
 *
 * Extending this class allows to make a single instance easily accessible by
 * many other objects.
 *
 * @author     Quentin Berlemont <quentinberlemont@gmail.com>
 */
abstract class Bildelspriser_Base_SingletonBase {
	/**
	 * Prevents direct creation of object.
	 *
	 * @param  void
	 * @return void
	 */
	protected function __construct() {
	}
	
	/**
	 * Prevents to clone the instance.
	 *
	 * @param  void
	 * @return void
	 */
	final private function __clone() {
	}
	
	/**
	 * Gets a single instance of the class the static method is called in.
	 *
	 * See the {@link http://php.net/lsb Late Static Bindings} feature for more
	 * information.
	 *
	 * @param  void
	 * @return object Returns a single instance of the class.
	 */
	final static public function getInstance($class_name) {
		
		//die ( var_export ( debug_backtrace (), true ) );
		static $instance = null;
		//return $instance ?:  $instance = new static;
		$class = "";
		//throw new exception ( "Ho$class" );
		
		if ($instance == null ) {
			//$class = get_called_class();
			//$class = __CLASS__;
			//$class = get_class($this);// ou get_class($this).
			try{
				if(isset($class_name)){
					$instance = new $class_name();
				}
				else{
					$class = get_called_class_hc();
			
					//debug
					echo ("Singleton::getInstance() - Creating a new $class");
					$instance = new $class();
				}//echo "-after class-";
			}
			catch(exception $ex){
				debug_print_backtrace();
				error($ex->getMessage().'<hr>'.$ex->getTraceAsString());
			}
			//$instance = new __CLASS__();
			//throw new Exception("I am trying");
		}
		else
			debug("Singleton::getInstance() - reusing $class");
		assertEx(isset($instance),"getInstance error - ".$class);
		//throw new Exception($class.' - '.get_class($instance));
		//echo "-i�1-$class";
		unset($class);
		return $instance;
		//return $instance ?:  $instance = new $class();
	//return $instance ?:  $instance = new static;
	}
}
