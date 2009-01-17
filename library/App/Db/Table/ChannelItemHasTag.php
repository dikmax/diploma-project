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
 * Channel item has tag table model
 *
 * @author dikmax
 * @version
 */
class App_Db_Table_ChannelItemHasTag extends App_Db_Table_Abstract
{
    /**
     * The default table name
     */
    protected $_name = 'lib_channel_item_has_tag';

    /**
     * Primery key
     */
    protected $_primary = array(
        'lib_channel_item_id',
        'lib_tag_id');

    /**
     * This table doesn't supports auto-incremental key
     */
    protected $_sequence = false;

    /**
     * Foreign keys
     */
    protected $_referenceMap = array(
        'ChannelItem' => array(
            'columns'           => 'lib_channel_item_id',
            'refTableClass'     => 'App_Db_Table_Author',
            'refColumns'        => 'lib_channel_item_id'
        ),
        'Tag' => array(
            'columns'           => 'lib_tag_id',
            'refTableClass'     => 'App_Db_Table_Tag',
            'refColumns'        => 'lib_tag_id'
        )
    );
}
