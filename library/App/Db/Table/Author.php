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
 * Author table model
 *
 * @author dikmax
 * @version
 */
class App_Db_Table_Author extends App_Db_Table_Abstract
{
    /**
     * The default table name
     */
    protected $_name = 'lib_author';

    /**
     * Primary key
     */
    protected $_primary = 'lib_author_id';

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
        'App_Db_Table_AuthorImage',
        'App_Db_Table_AuthorName',
        'App_Db_Table_AuthorNameIndex',
        'App_Db_Table_AuthorHasTitle',
        'App_Db_Table_AuthorHasTag'
    );

    /**
     * Returns max author id
     *
     * @return int
     */
    public function getMaxAuthorId()
    {
        $select = $this->_db->select()
            ->from($this->_name, new Zend_Db_Expr('max(lib_author_id)'));

        return $this->_db->fetchOne($select);
    }

    /**
     * Returns author by name
     *
     * @param string $authorName
     *
     * @return array
     */
    public function getAuthorByName($authorName)
    {
        return $this->_db->fetchRow('SELECT a.`lib_author_id`, a.`name`, '
            .     'a.`description_text_id`, a.`front_description`, '
            .     'a.`lib_writeboard_id` '
            . 'FROM `lib_author_name` n '
            . 'LEFT JOIN `lib_author` a USING (lib_author_id) '
            . 'WHERE n.name = :name', array(':name' => $authorName));
    }
}
