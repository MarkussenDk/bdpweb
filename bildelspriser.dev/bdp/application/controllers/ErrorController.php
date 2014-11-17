<?php

class ErrorController extends Zend_Controller_Action
{
    public function init()
    {
    	$xh = new Default_Model_XmlHttpRequest();
    	//$xh->setCreatedBy($user_name);
    	$xh->getMapper()->save($xh);

    	$view = $this->view;
    	//die(var_dump($view));
    	Zend_Dojo::enableView($view);
    	$view->dojo()->setDjConfigOption('usePlainJson',true)
    				 ->addLayer('/js/bdp/main.js');
    				 //->addJavascript('bdp.main.init();');*/
    }  
	
    public function errorAction()
    {
        $xh = new Default_Model_XmlHttpRequest();
    	//$xh->setCreatedBy($user_name);
    	$xh->getMapper()->save($xh);
    	
    	$errors = $this->_getParam('error_handler');
        $ar_errors = array();
        $msg="";
		if(count($errors)>1){
			$msg = "multiple errors - ".count($errors).'</br>';
			$ar_errors = $errors; 
			//$errors = end($errors);			
		}        
        //die('<pre>'.var_dump($errors,'true').'</pre>');
       switch ($errors->type) { 
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        
                // 404 error -- controller or action not found
           //	     $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = '404 Page not found';
                break;
            default:
                // application error 
         //       $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = '500 Application error';
				
                break;
        }
        //$this->view->message  = $msg.$errors->message;
        $this->view->exception = $errors->exception;
//        $this->view->ar_errors = $ar_errors;
        
        // pass the request to the view
        $this->view->request   = $errors->request; 
        
        echo "\n<!--In ErrorControler->error handler end -->";
        }


}

