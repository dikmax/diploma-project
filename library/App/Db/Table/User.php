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
 * User table model
 *
 * @author dikmax
 * @version
 */
class App_Db_Table_User extends Zend_Db_Table_Abstract
{
    /**
     * The default table name
     */
    protected $_name = 'lib_user';

    /**
     * Primery key
     */
    protected $_primary = 'lib_user_id';

    /**
     * This table supports auto-incremental key
     */
    protected $_sequence = true;
}
