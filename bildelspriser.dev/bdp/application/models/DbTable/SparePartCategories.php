<?php

// application/models/DbTable/CarMakes.php

/**
 * This is the DbTable class for the guestbook table.
 */
class Default_Model_DbTable_SparePartCategories extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name    = 'spare_part_categories';
    protected $_primary = 'spare_part_category_id';
}

