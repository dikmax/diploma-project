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
 * Text table model
 *
 * @author dikmax
 * @version
 */
class App_Db_Table_Text extends App_Db_Table_Abstract
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

    /**
     * Returns text with revision
     *
     * @param int $textId text id
     *
     * @return array
     */
    public function findTextWithRevision($textId)
    {
        $select = $this->_db->select()
            ->from($this->_name)
            ->joinLeftUsing('lib_text_revision', 'lib_text_revision_id')
            ->joinLeft('lib_text_revision_content',
                'lib_text_revision.lib_text_revision_content_id = '
                . 'lib_text_revision_content.lib_text_revision_content_id')
            ->where('`lib_text`.`lib_text_id` = :text_id');

        return $this->_db->fetchRow($select, array(
            ':text_id' => $textId
        ));
    }
}
