<?php
class Default_Form_Login extends Zend_Form
{
    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');

        // Add an email element
        $this->addElement('text', 'username', array(
            'label'      => 'Admin User Name:',
            'required'   => true,
            'filters'    => array('StringTrim')/*,
            'validators' => array(
                'EmailAddress',
            )*/
        ));

        // Add the comment element
        $this->addElement('password', 'password', array(
            'label'      => 'Password:',
            'required'   => true,
            'filters'    => array('StringTrim')
            /*'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, 20))
                )*/
        ));

        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Login',
        ));

/*        // And finally add some CSRF protection
        $this->addElement('hash', 'csrf', array(
            'ignore' => true,
        ));*/
    }
}
