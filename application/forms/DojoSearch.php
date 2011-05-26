<?php
// inspiration http://zendgeek.blogspot.com/2009/07/creating-nice-dojo-form-in-zend.html
class Default_Form_DojoSearch extends Zend_Dojo_Form
{
	public function init()
	{
		$this->setMethod('get');
		$this->setAttribs(array(
		'name' => 'DojoSearch'
		));



		
		
	/*	$this->addElement('ComboBox', 'myAutoCompleteField', array(
		    'label'     => 'My autocomplete field:',
		
		    // The javascript identifier for the data store:
		    'storeId'   => 'autocompleter',
		
		    // The class type for the data store:
		    'storeType' => 'custom.AutocompleteReadStore',
		
		    // Parameters to use when initializint the data store:
		    'storeParams' => array(
		        'url'           => '/index/autocompletecarmake',
		        'requestMethod' => 'get',
		    ),
		));		*/
		
/*		$this->addElement(           
			 'FilteringSelect',   
		     'fs_car_make',
		            array(                
		            'label' => 'Bil Maerke',
//		             'value' => 'blue',
					'storeType' => 'custom.AutocompleteReadStore',                
		             'autocomplete'=>true,                
//		             'multiOptions' => $this->_selectOptions,
					'storeParams' => array(
							        'url'           => '/index/autocompletecarmake',
							        'requestMethod' => 'get',
							    ),
		             )
        );*/
        
		$onchange = "function(value){  alert('new Value' + value');   }";
		
        $this->addElement('FilteringSelect', 'fs1_car_make', array(
    'label'     => 'Fabrikat',
     'onChange'	=> 'fs1_car_make_changed();return false;',   
     'JsId'	=> 'fs1_car_make'
     //'onClick'	=> 'alert("Clicked");return false;'   
        ));

        $this->addElement('FilteringSelect', 'fs1_car_model', array(
    'label'     => 'Model',
    //'text'		=> 'Vaelg maerke foerst',    
//	'disabled' => 'true',
     'onChange'	=> 'fs1_car_model_changed();return false;',
    'JsId'	=> 'fs1_car_model'        
        ));
        
        /*
         * 
         * 
        $this->addElement(           
			 'FilteringSelect',   
		     'fs_car_model',
		            array(                
		            'label' => 'Bil Model',
//		             'value' => 'blue',                
		             'autocomplete'=>true,                
//		             'multiOptions' => $this->_selectOptions,
		             )
        );*/

		
		$this->addElement(
		'TextBox',
		'tx_free_text_search',
			array(
			'value' => '',
			'label' => 'Bildel eller resevedel - fritekst - fx "skiver"',
			'trim' => true,
			'onSubmit'=> 'console.log("onSubmit");btn_subcmitButton();return false;',
			//'onKeyUp'=> 'console.log("onKeyUp");btn_submitButton();return false;',
                        'onChange'=> 'console.log("onChange");btn_submitButton();return false;',
	//		'propercase' => fal
			)

		);
		
		$this->addElement('button','search',array(
			'required'	=>false,
			'ignore'	=>true,
 			'onSubmit'	=> 'alert("Submitted");return false;',		
 			'onClick'	=> 'btn_submitButton();return false;',		
			'label'		=> "Find"
		));
		
		//$this->addElement('','')
		
	}
}

