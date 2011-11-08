<?php
/**
 *
 * @author Andreas
 * @version 
 */
require_once 'Zend/View/Interface.php';

/**
 * DkText helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_DkText {	
	var $search = array('å','ø','æ','Å','Ø','Æ');
	var $replace = array('&aring;','&oslash;','&aelig;','&Aring;','&Oslash;','&AElig;');
	/**
	 * @var Zend_View_Interface 
	 */
	public $view;
	
	/**
	 * 
	 */
	public function dkText($string) {
		$this->init();
		return str_replace($this->search, $this->replace, $string);
	}
	
	private function init(){
		if($this->search==null){
			$search = array('å','ø','æ','Å','Ø','Æ');
			$replace = array('&aring;','&oslash;','&aelig;','&Aring;','&Oslash;','&AElig;');
		}
		
	}
	
	/**
	 * Sets the view field 
	 * @param $view Zend_View_Interface
	 */
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
}
