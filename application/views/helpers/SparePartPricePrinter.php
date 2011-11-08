<?php
/**
 *
 * @author Andreas
 * @version
 */
require_once 'Zend/View/Interface.php';

/**
 * SparePartPricePrinter helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_SparePartPricePrinter
{
	static $car_model_id=null;
	static $car_variant_id=null;
	static $last_spp_id;
	private $price=null;
	private $_output;
	/**
	 * @var Zend_View_Interface
	 */
	public $view;

	/**
	 *@return Zend_View_Helper_SparePartPricePrinter
	 */
	public function sparePartPricePrinter($price) {
		if(!isset(self::$last_spp_id))
			self::$last_spp_id = 0;
		$this->setPrice($price);
		return $this;
	}
	
	public function setPrice($price){
		//echo Zend_Debug::dump($price,'Pris',true);
		$this->price = $price;
		$this->_output = null;
	}

	static function enc($string){
		if(strpos($string,'Ã')){
			warning('UTF8_Decoding was needed on string '.$string);
			return utf8_decode($string);	
		}
		return $string;
	}
	
    public function render()
    {
    	$price = $this->price;
    	$spp_id = $price['spare_part_price_id'];
    	if($spp_id==self::$last_spp_id)
    	{
    		warning("Same Sparepart price shown twice. $spp_id");
    		//return "<td>Same sparepart alreadt shown - self::$last_spp_id<td>";*/
    	}
    	self::$last_spp_id = $spp_id;
    	$img = $this->printImage($price);
		$colspan="";
		if(!$img){
			$colspan = ' colspan=2 ';
		}
    	$mod=null;
    	$name = self::enc($price['name']);
    	$description = self::enc($price['description']);
    	$supplier_part_number = self::enc($price['supplier_part_number']);
    	//Zend_Debug::dump($this,'ThePriter',true);
		if(isset(self::$car_model_id)){
			$mod = '/car_model_id/'.self::$car_model_id;
		}		
		$details_url = '/index/details/spare_part_price_id/'.$spp_id.$mod;
		$jump_to_url = '/index/jump_to/spare_part_price_id/'.$spp_id.$mod;
		$cell  = "\n\t<a href='$details_url'> <h3>".$name.'</h3></a>';
		$cell .= "\n\t<!--  spare_part_price_id $spp_id-->";
		$cell .= "\n\t<a href='$jump_to_url'>".$name.'</a>';
		$cell .= "\n\t <span class='spp_description' >".$description.'</span>';
		$cell .= "\n\t <br/>(Butikens varenr :  ".$supplier_part_number.') - ';
		$cell .= "\n\t <br>(Orginalt Varenr :  ".$price['original_part_number'].') - ';
		$cell .= "\n\t (Producent Varenr: ".$price['producer_part_number'].')';
		$cell .= "\n\t <br/><b>Pris inkl. moms : ".$price['price_inc_vat'].'</b>';
		$cell .= "\n\t <br/>Årgang: "/*utf8_encode*//*htmlspecialchars('Årgang: ')*/.$this->printYearRange($price).'<br/>';
		$cell .= "\n\t </td><td>$img";
		$cell .= "\n\t </td><td width='200px' class='bdp_td_spare_part_price' ><br/><a href='$details_url' >"
					.'<b>Pris inkl. moms : '.$this->dk_amount($price['price_inc_vat']).'</b>';
		$cell .= '<br/>Se detaljer om <br/>'.$name.'</a>';
//		$cell .= var_export($price,true); 
		$t_body = "\n<table width='100%' ><tr><td width='450px' >".$cell.'</td></tr></table>';
		$t_body = str_ireplace('<br/><br/>', '<br/>', $t_body);
		$t_body = str_ireplace('<br/><br/>', '<br/>', $t_body);
		$t_body = str_ireplace('<br/><br/>', '<br/>', $t_body);
		$t_body = str_ireplace('<br/><br/>', '<br/>', $t_body);
		$t_body = str_ireplace('<br/><br/>', '<br/>', $t_body);
		if(false and strpos($t_body,'Ã'))
		{
    		//$t_body = "UTF-8 first".utf8_decode($t_body);
    		warning('Still Unicode data issues on page\n'.$t_body);
    		$t_body = "UTF-8 right".utf8_decode($t_body);
    	}
    	if(strpos($t_body,'Ã')){
    		//$t_body = "UTF-8 second".utf8_decode($t_body);
    	}
    	$this->_output = $t_body;
    	
        return $this;
    }
	
    /**
     * Render table
     *
     * @return string
     */
    public function __toString()
    {
        if ($this->_output == null) 
        {
            try{
            	$this->render();	
            }
            catch (exception $e){
            	echo $e;
            	//die($e);
            	return 'Error'.$e;
            }
        }
        return $this->_output;
    }
	
	/**
	 * Sets the view field
	 * @param $view Zend_View_Interface
	 */
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
	
	public function dk_amount($num){
		$int = floor($num);
		$reminder = ((int)(($num - $int)*100))*0.01;
		//return $int.','.$reminder.' kr.';
		return number_format($num,2,',','.').' kr.';
	}
	
	function printChassis($ar){
		$r = '<span alt="Stel nr">Chassis nr: ';
		$range = '';
		$end_r = '</span>';	
		if(array_key_exists('chassis_no_from',$ar)){		
			if(array_key_exists('chassis_no_to',$ar)){
				return $r.$ar['chassis_no_from'].'-'.$ar['chassis_no_to'].$end_r;
			}
			return $r.$ar['chassis_no_from'].'-'.$end_r;		
		}
		if(array_key_exists('chassis_no_to',$ar)){
			return $r.'-'.$ar['chassis_no_to'].$end_r;
		}
	//	return $r.' alle '
	}
	
	function printImage($ar){
		$spp=null;
		if(is_array($ar)){
			$new_ar = array();
			$new_ar['spare_part_price_id'] = $ar['spare_part_price_id'];
			$new_ar['spare_part_url'] = $ar['spare_part_url'];
			$new_ar['spare_part_image_url'] = $ar['spare_part_image_url'];
			$new_ar['supplier_part_number'] = $ar['supplier_part_number'];
			$spp = new Default_Model_SparePartPrices($new_ar);
		}
		else 
			return;
		$img_src = FixUrl($spp->spare_part_image_url,'img',$spp);
		if(!array_key_exists('spare_part_image_url',$ar))
			return;
		$img_src = self::prepareUrl($img_src,$ar['spare_part_supplier_id']);
		return "<img onerror='report_img_broken(this);' src='$img_src' height=100px width=100px >";
	}
	
	public static function prepareUrl($url,$spp_id){
		   if($url[0]=='/'){
    	   		$sps_mapper = MapperFactory::getSpsMapper();
//    	   		$sps_mapper->fillRowCacheIfEmpty(100);
//    	   		$sps_mapper->fillObjectCacheFromRowCache();
    	   		$sps=$sps_mapper->findObject($spp_id);
    	   		//Zend_Debug::dump($sps,'SPS',true);
    	   		/** @var sps Default_Model_SparePartSuppliers */
    	   		//Zend_Debug::dump($sps->supplier_product_catalog_url,'supplier_product_catalog_url',true);
    	   		//Zend_Debug::dump($url,'$url',true);
    	   		return $sps->supplier_product_catalog_url.$url;    	   		    	   		
    	   		//return $sps->supplier_product_catalog_url.$url;    	   			
    	   }
    	   return $url; 
	}
	
	function printMonthYear($year,$month){
		if(isset($year))
			if(isset($month))
				return $year.'.'.$month;
			else
				return $year;
	}
	function printYearRange($ar){
		$yf = $ar['year_from'];
		$yt = $ar['year_to'];
		$mf = $ar['month_from'];
		$mt = $ar['month_to'];
		$yr;
		if(isset($yf)){
			$yr = printMonthYear($yf,$mf).'-';
			if(isset($yt)){
				 $yr = $yr.printMonthYear($yt,$mt);
			}
			return $yr;
		}	
		if(isset($yt))
			return printMonthYear($yt,$mt).'-';
		else
			return 'alle';
	}	
	
}

