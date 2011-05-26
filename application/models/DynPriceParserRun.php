<?php


class Default_Model_DynPriceParserRun extends Default_Model_DynBase{
	static $db;
	public function __construct($options = null){
		/*class Default_Model_
		 * 
		 */
		$fields = array(
		  'file_base_name', 'file_create_time'
		, 'file_size', 'processing_start'
		, 'processing_end', 'status'
		, 'last_elem_type', 'last_elem_id'
		, 'created_by', 'xml_http_request_id', 'created'
		);
		
		parent::__construct($options,'','price_parser_runs',$fields);
	
	} 

}