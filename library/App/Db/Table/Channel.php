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
 * Channel table model
 *
 * @author dikmax
 * @version
 */
class App_Db_Table_Channel extends App_Db_Table_Abstract
{
    /**
     * The default table name
     */
    protected $_name = 'lib_channel';

    /**
     * Primery key
     */
    protected $_primary = 'lib_channel_id';

    /**
     * This table supports auto-incremental key
     */
    protected $_sequence = true;

    /**
     * Dependent tables
     */
    protected $_dependentTables = array('App_Db_Table_ChannelItem');
}
