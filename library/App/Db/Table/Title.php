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
        'App_Db_Table_TitleSimilar',
        'App_Db_Table_UserBookshelf'
    );

    /**
     * Returns max title id
     *
     * @return int
     */
    public function getMaxTitleId()
    {
        $select = $this->_db->select()
            ->from($this->_name, new Zend_Db_Expr('max(lib_title_id)'));

        return $this->_db->fetchOne($select);
    }

    /**
     * Returns title by name
     *
     * @param int $authorId
     * @param string $titleName
     *
     * @return array
     */
    public function getTitleByName($authorId, $titleName)
    {
        $row = $this->_db->fetchRow('SELECT t.lib_title_id, t.name, '
             .     't.authors_index, t.description_text_id, t.front_description, '
             .     't.lib_writeboard_id '
             . 'FROM lib_title t '
             . 'LEFT JOIN lib_author_has_title h USING (lib_title_id) '
             . 'WHERE h.lib_author_id = :lib_author_id AND t.name = :name',
             array(':lib_author_id' => $authorId,
                   ':name' => $titleName)
        );

        return $row;
    }
}
