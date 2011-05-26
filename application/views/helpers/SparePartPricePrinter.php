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

	/**
	 * @var Zend_View_Interface
	 */
	public $view;

	/**
	 *
	 */
	public function sparePartPricePrinter($price) {
		//$img = printImage($price);
		$colspan="";
		if(!$img){
		//	$colspan = ' colspan=2 '
		}
		$details_url = '/index/details/spare_part_price_id/'.$price['spare_part_price_id'];
		$jump_to_url = '/index/jump_to/spare_part_price_id/'.$price['spare_part_price_id'];
		$cell  = "\n\t<h3>".$price['name'].'</h3>';
		$cell .= "\n\t<!--  spare_part_price_id ".$price['spare_part_price_id'].'-->';
		$cell .= "\n\t<a href='/index/jump_to/spare_part_price_id/".$price['spare_part_price_id']."'>".$price['name'].'</a>';
		$cell .= "\n\t ".$price['description'];
		$cell .= "\n\t <br/>(Butikens varenr :  ".$price['supplier_part_number'].') - ';
		$cell .= "\n\t (Orginalt Varenr :  ".$price['original_part_number'].') - ';
		$cell .= "\n\t (Producent Varenr: ".$price['producer_part_number'].')';
		$cell .= "\n\t <br/><b>Pris inkl. moms : ".$price['price_inc_vat'].'</b>';
		$cell .= /*utf8_encode*/htmlspecialchars('<br/>Årgang: ').$this->printYearRange($price).'<br/>';
		//$cell .= "\n\t </td><td>".
		$cell .= "\n\t </td><td class='bdp_td_spare_part_price' ><br/><b>Pris inkl. moms : ".$this->dk_amount($price['price_inc_vat']).'</b>';
		$cell .= '<br/><a href="'.$details_url.'">Se detaljer</a>';
//		$cell .= var_export($price,true); 
		$t_body = "\n<tr class='bdp_tr_spare_part_price' ><td class='bdp_td_spare_part_price' >".$cell.'</td><td>';
		return ($t_body);
	}

	/**
	 * Sets the view field
	 * @param $view Zend_View_Interface
	 */
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
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
		if(!array_key_exists('spare_part_image_url',$ar))
			return;
		$img_src = $ar['spare_part_image_url'];
		return "<img src='$img_src' height=100px width=100px >";
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
	
	function dk_amount($num){
		$int = floor($num);
		$reminder = ((int)(($num - $int)*100))*0.01;
		//return $int.','.$reminder.' kr.';
		return number_format($num,2,',','.').' kr.';
	}
	
}

