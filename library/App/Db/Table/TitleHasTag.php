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
 * Title has tag table model
 *
 * @author dikmax
 * @version
 */
class App_Db_Table_TitleHasTag extends App_Db_Table_Abstractss
{
    /**
     * The default table name
     */
    protected $_name = 'lib_title_has_tag';

    /**
     * Primery key
     */
    protected $_primary = array(
        'lib_user_id',
        'lib_tag_id',
        'lib_title_id');

    /**
     * This table doesn't supports auto-incremental key
     */
    protected $_sequence = false;

    /**
     * Foreign keys
     */
    protected $_referenceMap = array(
        'Tag' => array(
            'columns'           => 'lib_tag_id',
            'refTableClass'     => 'App_Db_Table_Tag',
            'refColumns'        => 'lib_tag_id'
        ),
        'Title' => array(
            'columns'           => 'lib_title_id',
            'refTableClass'     => 'App_Db_Table_Title',
            'refColumns'        => 'lib_title_id'
        ),
        'User' => array(
            'columns'           => 'lib_user_id',
            'refTableClass'     => 'App_Db_Table_User',
            'refColumns'        => 'lib_user_id'
        )
    );
}
