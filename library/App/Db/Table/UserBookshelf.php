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
 * User bookshelf table model
 *
 * @author dikmax
 * @version
 */
class App_Db_Table_UserBookshelf extends Zend_Db_Table_Abstract
{
    /**
     * The default table name
     */
    protected $_name = 'lib_user_bookshelf';

    /**
     * Primery key
     */
    protected $_primary = 'lib_user_bookshelf_id';

    /**
     * This table supports auto-incremental key
     */
    protected $_sequence = true;

    /**
     * Foreign keys
     */
    protected $_referenceMap = array(
        'User' => array(
            'columns'           => 'lib_user_id',
            'refTableClass'     => 'App_Db_Table_User',
            'refColumns'        => 'lib_user_id'
        )
    );
}
