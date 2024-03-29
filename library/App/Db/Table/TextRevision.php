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
 * Text revision table model
 *
 * @author dikmax
 * @version
 */
class App_Db_Table_TextRevision extends App_Db_Table_Abstract
{
    /**
     * The default table name
     */
    protected $_name = 'lib_text_revision';

    /**
     * Primery key
     */
    protected $_primary = 'lib_text_revision_id';

    /**
     * This table supports auto-incremental key
     */
    protected $_sequence = true;

    /**
     * Foreign keys
     */
    protected $_referenceMap = array(
        'Text' => array(
            'columns'           => 'lib_text_id',
            'refTableClass'     => 'App_Db_Table_Text',
            'refColumns'        => 'lib_text_id'
        ),
        'TextRevisionContent' => array(
            'columns'           => 'lib_text_revision_content_id',
            'refTableClass'     => 'App_Db_Table_TextRevisionContent',
            'refColumns'        => 'lib_text_revision_content_id'
        ),
        'User' => array(
            'columns'           => 'author_id',
            'refTableClass'     => 'App_Db_Table_User',
            'refColumns'        => 'lib_user_id'
        )
    );

    /**
     * Dependent tables
     */
    protected $_dependentTables = array('App_Db_Table_Text');

    /**
     * Returns max revision id for specified text
     */
    public function getMaxRevisionNumber($textId)
    {
        $select = $this->_db->select()
            ->from($this->_name, array(new Zend_Db_Expr('max(revision)')))
            ->joinLeftUsing('lib_text_revision_content', 'lib_text_revision_content_id')
            ->where('lib_text_id = :text_id');

        return $this->_db->fetchOne($select, array(
            ':text_id' => $textId
        ));
    }

    public function getRevisionsList($textId)
    {
        $select = $this->_db->select()
            ->from($this->_name)
            ->where('lib_text_id = :text_id')
            ->order(new Zend_Db_Expr('revision DESC'));

        return $this->_db->fetchAll($select, array(
            ':text_id' => $textId
        ));
    }

    public function getRevision($textId, $revisionNumber)
    {
        $select = $this->_db->select()
            ->from($this->_name)
            ->joinLeftUsing('lib_text_revision_content', 'lib_text_revision_content_id')
            ->where('lib_text_id = :text_id')
            ->where('revision = :revision');

        return $this->_db->fetchRow($select, array(
            ':text_id' => $textId,
            ':revision' => $revisionNumber
        ));
    }
}
