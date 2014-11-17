<?php

//namespace library\Bildelspriser\DB;

/**
 *
 * @author Andreas
 *        
 */
class Bildelspriser_DB_UTF8 {
	// TODO - Insert your code here
	static function  hasUTFchars($value){
		if(is_array($value)){
			foreach($value as $key=>$ar_value){
				if(self::hasUTFchars($ar_value)){
					return true;
				}
			}
			return false;
		}
		$len = strlen($value);
		for($i=0;$i<$len;$i++){
			//		echo "<br/>val[$i]=".$value[$i].' = '.ord($value[$i]);
			if(ord($value[$i])>=192){
				//			kint::dump('Byte is found above 192 "'.ord($value[$i]).'" in key '.$key.'='.$value );
				//			kint::dump('Next byte is "'.ord($value[$i+1]).'" in key '.$key.'='.$value );
				//kint::dump('inCheckArray',$value);
				return true;
			}
		}
		return false;
	}
	
	static function removeUTF8Junk(&$value){
		//self::dumpStringAsBytes($value);
		if(is_array($value)){
			foreach($value as $key=>$ar_value){
				$value[$key] = self::removeUTF8Junk($value[$key]);
			}			
			return $value;
		}
		$value = str_replace(chr(160), ' ',$value);
		//self::dumpStringAsBytes($value);
		return $value;
	}
	
	static function dumpStringAsBytes($value){
		if(is_array($value)){
			foreach($value as $key=>$ar_value){
				echo '<h3>'.$key.'<h3/>';
				self::dumpStringAsBytes($ar_value);
			}
			return;
		}
		$len = strlen($value);
		for($i=0;$i<$len;$i++){
			echo "<br/>val[$i]=".$value[$i].' = '.ord($value[$i]);
			if(ord($value[$i])>=192){
							kint::dump('Byte is found above 192 "'.ord($value[$i]).'" '.$value );
							kint::dump('Next byte is "'.ord($value[$i+1]).'" '.$value );
				//kint::dump('inCheckArray',$value);
			}
		}		
	}
	
	static function checkArrayForUTF8Errors(&$attrs){
		Kint::dump('CheckForArray Start',$attrs);
		foreach($attrs as $key=>$value){
			if(self::hasUTFchars($value)){
				$attrs[$key] = $value = trim(utf8_encode($value));
			}
		}
	}
}

?>