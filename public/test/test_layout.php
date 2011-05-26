<?php

class layout_test{
	public  $content;
}

class page_test{
	public function __construct(){
		
		include_once('..\..\application\layouts\scripts\layout.phtml');
	}

	public function doctype(){
		return "DocType";
	}
	
	public function layout(){
		$l = new layout_test();
		$l->content = "Content set in test_layout.phpxxx xxx xxxx xxx xxxx xxxx xxxxx xxxxx xxxxx xxxxx xxxx xxxx xxxx xxx xxx"	;
		$l->content .= "Content set in test_layout.php<br/><br/><br/><br/><br/><br/><br/><br/>"	;
		$l->content .= "Content set in test_layout.php<br/><br/><br/><br/><br/><br/><br/><br/>"	;
		return $l;	
	}
	
	public function headTitle(){
		return "";
	}
	
	public function headLink(){
		return "";
	}
	public function headStyle(){
		return "";
	}
	public function headScript(){
		return "";
	}
		
}

$pt = new page_test();