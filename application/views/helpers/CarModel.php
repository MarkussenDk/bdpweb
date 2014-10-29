<?php
/**
 *
 * @author Andreas
 * @version
 */
require_once 'Zend/View/Interface.php';

/**
 * CarModel helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_CarModel
{

	/**
	 * @var Zend_View_Interface
	 */
	public $view;
	
	public $car_model;

	/**
	 *
	 */
	public function carModel() {
		// TODO Auto-generated Zend_View_Helper_CarModel::carModel() helper
		return null;
	}

	/**
	 * Sets the view field
	 * @param $view Zend_View_Interface
	 */
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
}

