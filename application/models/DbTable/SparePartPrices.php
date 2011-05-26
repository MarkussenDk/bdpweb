<?php

// application/models/DbTable/CarMakes.php

/**
 * This is the DbTable class for the guestbook table.
 */
//throw new Exception ("SparePArtLoader");


class Default_Model_DbTable_SparePartPrices extends Zend_Db_Table_Abstract
{
    // Table name 
    protected $_name    = 'spare_part_prices';
    protected $_primary = 'spare_part_price_id';
}	