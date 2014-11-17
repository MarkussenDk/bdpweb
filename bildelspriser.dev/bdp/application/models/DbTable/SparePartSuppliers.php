<?php

// application/models/DbTable/SparePartSuppliers.php

/**
 * This is the DbTable class for the guestbook table.
 */
//throw new exception("Who loads me?");	
class Default_Model_DbTable_SparePartSuppliers extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name    = 'spare_part_suppliers';
    protected $_primary = 'spare_part_supplier_id';
}



?>