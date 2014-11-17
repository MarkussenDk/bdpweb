<?php

// application/models/DbTable/CarMakes.php

/**
 * This is the DbTable class for the CarMakes or the Car Brands. Eg. Volvo.
 */
class Default_Model_DbTable_CarModels extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name    = 'car_models';
    protected $_primary = 'car_model_id';
}


?>