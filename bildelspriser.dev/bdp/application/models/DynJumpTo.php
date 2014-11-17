<?php


class Default_Model_DynJumpTo extends Default_Model_DynBase{
	static $db;
	public $dk_parts_purchased_names;
	public function __construct(array $options = null){
		/*class Default_Model_
		 * 
		 */
		$fields = array(
		 'spare_part_supplier_id'
		, 'spare_part_price_id'
		, 'price_inc_vat' //'Yes','No-UnAnswered','No-OtherPrice','No-NotAvailable','No-OutOfStock','No-JustBrowsing'
		, 'part_purchased'
		, 'other_no_reason'
		, 'xml_http_request_id'
		, 'created'
		);
		
		
		$this->dk_parts_purchased_names = array(
			'Yes' => 'Ja, varen er bestilt',
			'No-UnAnswered' => 'Nej',
			'No-OtherPrice' => 'Nej, butikken havde en anden pris, så jeg ville ikke købe',
			'No-NotAvailable' => 'Nej, butikken havde ikke varen alligevel',
			'No-OutOfStock' => 'Nej, butikken havde varen på lager og kunne ikke skaffe',
			'No-JustBrowsing' => 'Nej, jeg ville bare kigge på priserne - ikke købe noget'
		);
		
		parent::__construct($options,'','jump_to_shop',$fields,'jump_to_shop_id');		
	}
	
	

	public function getPartPurchasedEnumValues(){
		$enum_options = IndexController::getEnumOptions($this->table, 'part_purchased');
		return $enum_options;
	}
}