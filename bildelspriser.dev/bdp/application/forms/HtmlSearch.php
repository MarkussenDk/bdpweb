<?php
// inspiration http://zendgeek.blogspot.com/2009/07/creating-nice-dojo-form-in-zend.html
class Default_Form_HtmlSearch extends Zend_Form
{
	public function init()
	{
		$this->setMethod('get');
		$this->setAttribs(array('name' => 'HtmlSearch'));
		$this->setAttrib('accept-charset', 'utf-8'); 

		//$onchange = "function(value){  alert('new Value' + value');   }";
		
		/*$element = new Zend_Form_Element_Select('foo', array(
    'multiOptions' => array(
        'foo' => 'Foo Option',
        'bar' => 'Bar Option',
        'baz' => 'Baz Option',
        'bat' => 'Bat Option',  ) ));
		$this->addElement($element);*/
		
		$zfes = new Zend_Form_Element_Select('sel');
		$this->addElement($zfes);
		$zfes->setName('make_select');
		$zfes->setLabel(utf8_encode('Mærke'));
		$zfes->setAttrib('style',"width: 170px");
		$zfes->setAttrib('id','make_select');
		$zfes->setAttrib('onchange','handleBrandChange(this);');
		$makes = Default_Model_CarMakesMapper::getMakes(array(0=>utf8_encode("Vælg mærke")));
		//die(nl2br(print_r($makes)));
		$zfes->setMultiOptions((array)$makes);		
        $a = array();
        $zfes = new Zend_Form_Element_Select('model');
		$this->addElement($zfes);
		$zfes->setName('model_select');
		$zfes->setAttrib('style',"width: 170px");
		$zfes->setLabel(utf8_encode('Model'));
		$zfes->setMultiOptions($a);
		//$zfes->setAttrib('disabled','true');
		$zfes->setMultiOptions(array(utf8_encode('Vælg mærke først')));
		$zfet = new Zend_Form_Element_Hidden('model_select_hidden');
		$this->addElement($zfet);
		
		
	/*
        $this->addElement('Select', 'fs1_car_model', array(
    'label'     => 'Model',
     'width'     => '200px',
        'values' => $a,
     'style'     =>"width: 300px",
  //width="300" style="width: 300px">
  
    'text'		=> "test" /  *'Vælg mærke først'* /,    
    //    	$this->view->form = " ".str_replace('Mærke','Mrke',$form);
        
	'disabled' => 'true',
     'onChange'	=> 'fs1_car_model_changed();return false;',
    'JsId'	=> 'fs1_car_model'        
        ))
        ;
        $this->getElement('fs1_car_model')->setMultiOptions(array(1=>"Vaelg maerke forst"));*/
        
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
		'text',
		'tx_free_text_search',
			array(
			'value' => '',
			'label' => 'Varenavn - fritekst - fx "skive"',
			'trim' => true,
			'style'=> 'width:170px',
			//'onSubmit'=> 'console.log("onSubmit");btn_subcmitButton();return false;',
			//'onKeyUp'=> 'console.log("onKeyUp");btn_submitButton();return false;',
          //              'onChange'=> 'console.log("onChange");btn_submitButton();return false;',
	//		'propercase' => fal
			)

		);
		
		$this->addElement('button','search',array(
		//	'required'	=>false,
		//	'ignore'	=>true,
 		//	'onSubmit'	=> 'alert("Submitted");return false;',		
 		//	'onClick'	=> 'btn_submitButton();return false;',		
			'label'		=> "Find",
			'id'		=> 'btn_find',
			'name'		=> 'btn_find'		
		));
		
		//$this->addElement('','')
		
	}
}

