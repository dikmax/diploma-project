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
 * Text revision content table model
 *
 * @author dikmax
 * @version
 */
class App_Db_Table_TextRevisionContent extends App_Db_Table_Abstract
{
    /**
     * The default table name
     */
    protected $_name = 'lib_text_revision_content';

    /**
     * Primery key
     */
    protected $_primary = 'lib_text_revision_content_id';

    /**
     * This table supports auto-incremental key
     */
    protected $_sequence = true;

    /**
     * Dependent tables
     */
    protected $_dependentTables = array('App_Db_Table_TextRevision');
}
