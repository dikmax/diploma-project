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
 * Tag table model
 *
 * @author dikmax
 * @version
 */
class App_Db_Table_Tag extends App_Db_Table_Abstract
{
    /**
     * The default table name
     */
    protected $_name = 'lib_tag';

    /**
     * Primery key
     */
    protected $_primary = 'lib_tag_id';

    /**
     * This table supports auto-incremental key
     */
    protected $_sequence = true;

    /**
     * Dependent tables
     */
    protected $_dependentTables = array(
        'App_Db_Table_AuthorHasTag',
        'App_Db_Table_ChannelItemHasTag',
        'App_Db_Table_TitleHasTag'
    );

}
