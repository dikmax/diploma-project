<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id:$
 */

require_once 'App/Db/Table/Abstract.php';

/**
 * Title has tag table model
 *
 * @author dikmax
 * @version
 */
class App_Db_Table_AuthorImageMark extends App_Db_Table_Abstractss
{
    /**
     * The default table name
     */
    protected $_name = 'lib_author_image_mark';

    /**
     * Primery key
     */
    protected $_primary = array(
        'lib_author_image_id',
        'lib_user_id');

    /**
     * This table doesn't supports auto-incremental key
     */
    protected $_sequence = false;

    /**
     * Foreign keys
     */
    protected $_referenceMap = array(
        'AuthorImage' => array(
            'columns'           => 'lib_author_image_id',
            'refTableClass'     => 'App_Db_Table_AuthorImage',
            'refColumns'        => 'lib_author_image_id'
        ),
        'User' => array(
            'columns'           => 'lib_user_id',
            'refTableClass'     => 'App_Db_Table_User',
            'refColumns'        => 'lib_user_id'
        )
    );
}
