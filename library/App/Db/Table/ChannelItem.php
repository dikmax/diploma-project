<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id$
 */

require_once 'App/Db/Table/Abstract.php';

/**
 * Channel item table model
 *
 * @author dikmax
 * @version
 */
class App_Db_Table_ChannelItem extends App_Db_Table_Abstract
{
    /**
     * The default table name
     */
    protected $_name = 'lib_channel_item';

    /**
     * Primery key
     */
    protected $_primary = 'lib_channel_item_id';

    /**
     * This table supports auto-incremental key
     */
    protected $_sequence = true;

    /**
     * Foreign keys
     */
    protected $_referenceMap = array(
        'Channel' => array(
            'columns'           => 'lib_channel_id',
            'refTableClass'     => 'App_Db_Table_Channel',
            'refColumns'        => 'lib_channel_id'
        ),
        'Text' => array(
            'columns'           => 'item_text_id',
            'refTableClass'     => 'App_Db_Table_Text',
            'refColumns'        => 'lib_text_id'
        )
    );

    /**
     * Dependent tables
     */
    protected $_dependentTables = array('App_Db_Table_ChannelItemHasTag');
}
