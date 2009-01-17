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
 * Author has title table model
 *
 * @author dikmax
 * @version
 */
class App_Db_Table_AuthorHasTitle extends App_Db_Table_Abstract
{
    /**
     * The default table name
     */
    protected $_name = 'lib_author_has_title';

    /**
     * Primery key
     */
    protected $_primary = array(
        'lib_author_id',
        'lib_title_id');

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
        'Title' => array(
            'columns'           => 'lib_title_id',
            'refTableClass'     => 'App_Db_Table_Title',
            'refColumns'        => 'lib_title_id'
        )
    );
}