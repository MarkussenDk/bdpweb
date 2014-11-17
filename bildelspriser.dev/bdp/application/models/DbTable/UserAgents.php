<?php

// application/models/DbTable/UserAgents.php

/**
 * This is the DbTable class for the User Agents table.
 */
class Default_Model_DbTable_UserAgents extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name    = 'user_agents';
    protected $_primary = 'user_agent_id';
}


?>