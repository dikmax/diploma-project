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
 * Author name index table model
 *
 * @author dikmax
 * @version
 */
class App_Db_Table_AuthorNameIndex extends Zend_Db_Table_Abstract
{
    /**
     * The default table name
     */
    protected $_name = 'lib_author_name_index';

    /**
     * Primery key
     */
    protected $_primary = 'lib_author_name_index_id';

    /**
     * This table supports auto-incremental key
     */
    protected $_sequence = true;

    /**
     * Foreign keys
     */
    protected $_referenceMap = array(
        'Author' => array(
            'columns'           => 'lib_author_id',
            'refTableClass'     => 'App_Db_Table_Author',
            'refColumns'        => 'lib_author_id'
        )
    );
}
