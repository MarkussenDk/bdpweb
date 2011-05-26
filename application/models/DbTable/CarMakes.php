<?php

// application/models/DbTable/CarMakes.php

/**
 * This is the DbTable class for the guestbook table.
 */
class Default_Model_DbTable_CarMakes extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name    = 'car_makes';
    protected $_primary = 'car_make_id';
}


?>