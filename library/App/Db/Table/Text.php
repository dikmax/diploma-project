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
 * Text table model
 *
 * @author dikmax
 * @version
 */
class App_Db_Table_Text extends Zend_Db_Table_Abstract
{
    /**
     * The default table name
     */
    protected $_name = 'lib_text';

    /**
     * Primery key
     */
    protected $_primary = 'lib_text_id';

    /**
     * This table supports auto-incremental key
     */
    protected $_sequence = true;

    /**
     * Foreign keys
     */
    protected $_referenceMap = array(
        'TextRevision' => array(
            'columns'           => 'lib_text_revision_id',
            'refTableClass'     => 'App_Db_Table_Text',
            'refColumns'        => 'lib_text_revision_id'
        )
    );

    /**
     * Dependent tables
     */
    protected $_dependentTables = array(
        'App_Db_Table_Author',
        'App_Db_Table_ChannelItem',
        'App_Db_Table_TextRevision',
        'App_Db_Table_Title'
    );
}
