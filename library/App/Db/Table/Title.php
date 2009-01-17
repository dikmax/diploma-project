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
 * Title table model
 *
 * @author dikmax
 * @version
 */
class App_Db_Table_Title extends App_Db_Table_Abstract
{
    /**
     * The default table name
     */
    protected $_name = 'lib_title';

    /**
     * Primery key
     */
    protected $_primary = 'lib_title_id';

    /**
     * This table supports auto-incremental key
     */
    protected $_sequence = true;

    /**
     * Foreign keys
     */
    protected $_referenceMap = array(
        'Text' => array(
            'columns'           => 'description_text_id',
            'refTableClass'     => 'App_Db_Table_Text',
            'refColumns'        => 'lib_text_id'
        ),
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
        'App_Db_Table_AuthorHasTitle',
        'App_Db_Table_TitleHasTag',
        'App_Db_Table_UserBookshelf'
    );
}
