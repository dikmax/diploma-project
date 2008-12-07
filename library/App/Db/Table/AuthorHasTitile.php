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
     * Dependent tables
     */
    //protected $_dependentTables = array('App_Db_Table_UserBookshelf');

}
