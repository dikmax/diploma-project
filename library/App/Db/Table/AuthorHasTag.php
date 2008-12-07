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
 * Author has tag table model
 *
 * @author dikmax
 * @version
 */
class App_Db_Table_AuthorHasTag extends Zend_Db_Table_Abstract
{
    /**
     * The default table name
     */
    protected $_name = 'lib_author_has_tag';

    /**
     * Primery key
     */
    protected $_primary = array(
        'lib_user_id',
        'lib_tag_id',
        'lib_author_id');

    /**
     * This table doesn't supports auto-incremental key
     */
    protected $_sequence = false;

    /**
     * Foreign keys
     */
    protected $_referenceMap = array(
        'Author' => array(
            'columns'           => 'lib_author_id',
            'refTableClass'     => 'App_Db_Table_Author',
            'refColumns'        => 'lib_author_id'
        ),
        'Tag' => array(
            'columns'           => 'lib_tag_id',
            'refTableClass'     => 'App_Db_Table_Tag',
            'refColumns'        => 'lib_tag_id'
        ),
        'User' => array(
            'columns'           => 'lib_user_id',
            'refTableClass'     => 'App_Db_Table_User',
            'refColumns'        => 'lib_user_id'
        )
    );
}
