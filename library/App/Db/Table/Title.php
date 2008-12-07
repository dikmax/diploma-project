<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id$
 */

/**
 * Title table model
 *
 * @author dikmax
 * @version
 */
class App_Db_Table_Title extends Zend_Db_Table_Abstract
{
    /**
     * The default table name
     */
    protected $_name = 'lib_title';

    /**
     * Primery key
     */
    protected $_primary = 'lib_title_id';

    /**
     * This table supports auto-incremental key
     */
    protected $_sequence = true;

    /**
     * Dependent tables
     */
    //protected $_dependentTables = array('App_Db_Table_UserBookshelf');

}