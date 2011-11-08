<?php
/**
 *
 * @author Andreas
 * @version 
 */
require_once 'Zend/View/Interface.php';

/**
 * ExtLink helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_ExtLink {
	
	/**
	 * @var Zend_View_Interface 
	 */
	public $view;
	
	/**
	 * 
	 */
	public function extLink($url,$text,$target='_another_window',$title=null) {
		// TODO Auto-generated Zend_View_Helper_ExtLink::extLink() helper
		return "<a href='$url' target='$target' title='$title'>$text</a>";
	}
	
	/**
	 * Sets the view field 
	 * @param $view Zend_View_Interface
	 */
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
}
