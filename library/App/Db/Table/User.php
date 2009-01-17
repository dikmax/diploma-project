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
class App_Db_Table_User extends App_Db_Table_Abstract
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

    /**
     * Foreign keys
     */
    protected $_referenceMap = array(
        'Writeboard' => array(
            'columns'           => 'lib_writeboard_id',
            'refTableClass'     => 'App_Db_Table_Writeboard',
            'refColumns'        => 'lib_writeboard_id'
        )
    );

    /**
     * Dependent tables
     */
    protected $_dependentTables = array(
        'App_Db_Table_AuthorHasTag',
        'App_Db_Table_TextRevision',
        'App_Db_Table_TitleHasTag',
        'App_Db_Table_UserBookshelf',
        'App_Db_Table_WriteboardMessage'
    );

}
