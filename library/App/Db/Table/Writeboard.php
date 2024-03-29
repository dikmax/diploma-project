<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id$
 */

require_once 'App/Db/Table/Abstract.php';

/**
 * Writeboard table model
 *
 * @author dikmax
 * @version
 */
class App_Db_Table_Writeboard extends App_Db_Table_Abstract
{
    /**
     * The default table name
     */
    protected $_name = 'lib_writeboard';

    /**
     * Primery key
     */
    protected $_primary = 'lib_writeboard_id';

    /**
     * This table supports auto-incremental key
     */
    protected $_sequence = true;

    /**
     * Dependent tables
     */
    protected $_dependentTables = array(
        'App_Db_Table_Author',
        'App_Db_Table_Title',
        'App_Db_Table_User',
        'App_Db_Table_WriteboardMessage'
    );
}
