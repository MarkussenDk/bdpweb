<?php
/** Filename: /application/models/Base.php
 * 
 */

// for inspiration to this class - see
// http://framework.zend.com/docs/quickstart/create-a-model-and-database-table

class Default_Model_Base //extends car_makes
{ 	
/*	private $_created=null;
	private $_created_by=null;
	private $_updated=null;
	private $_updated_by=null;*/
	private $_mapper;
	private $_mapper_name;
	
    public function __construct(array $options = null,$mapper_name=null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
        if($mapper_name){
        	$this->_mapper_name = $mapper_name;
        }
    }
	
    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst(strtolower($key));
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
            else{
            	$class = get_class($this);
            	throw new Exception("Method '{$class}->{$method}' not implemented - Value used was '$value' <br>Options was =  "
            		.nl2br(var_export($options,true)));
            	
            }
        }
        return $this;
    }   
    
	
   /* public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid car_makes property"'.$name.'"');
        }
        $this->$method($value);
    }*/

    public function __get($name)
    {//$name holds the name of the undefined attributes getting called.
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Base:Invalid Base property - "'.$name.'"'.var_export($this,true));
        }
        return $this->$method();
    }


    public function setMapper($mapper)
    {
        $this->_mapper = $mapper;
        return $this;
    }

/*    public function getMapper()
    {
        if (null === $this->_mapper) {
            $this->setMapper(new ${$this->_mapper_name}());
        }
        return $this->_mapper;
    }*/
    public function getMapper()
    {
        if (null === $this->_mapper) {
        	if(null === $this->_mapper_name){
        		throw new Exception("Mapper name was not defined - must be set in the constructor as second parameter on the child class");       		
        	}
        	//die($this->_mapper_name);
        	$mapper = new $this->_mapper_name;
            $this->setMapper($mapper);
            //var_dump()
        }
        return $this->_mapper;
    }
    
    
    public function save()
    {
        $this->getMapper()->save($this);
    }

    public function find($id)
    {
        $this->getMapper()->find($id, $this);
        return $this;
    }
    
     public function fetchAll($select=null)
    {
        $mapper=$this->getMapper();
        if(!isset($mapper)){
        	throw new exception ("Mapper not set");
        } 
        else
    	return $this->getMapper()->fetchAll($select);
    }
    
    public function fetchAll_Array($select)
    {
        $mapper=$this->getMapper();
        if(!isset($mapper)){
        	return "Mapper not set";
        	die("mapper");
        } 
        else
        	//die("Mapper was set");
    	return $this->getMapper()->fetchAll_Array($select);
    }
}

