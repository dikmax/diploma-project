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
 * Writeboard message table model
 *
 * @author dikmax
 * @version
 */
class App_Db_Table_WriteboardMessage extends Zend_Db_Table_Abstract
{
    /**
     * The default table name
     */
    protected $_name = 'lib_writeboard_message';

    /**
     * Primery key
     */
    protected $_primary = 'lib_writeboard_message_id';

    /**
     * This table supports auto-incremental key
     */
    protected $_sequence = true;

    /**
     * Foreign keys
     */
    protected $_referenceMap = array(
        'User' => array(
            'columns'           => 'writeboard_writer',
            'refTableClass'     => 'App_Db_Table_User',
            'refColumns'        => 'lib_user_id'
        ),
        'Writeboard' => array(
            'columns'           => 'lib_writeboard_id',
            'refTableClass'     => 'App_Db_Table_Writeboard',
            'refColumns'        => 'lib_writeboard_id'
        )
    );
}
