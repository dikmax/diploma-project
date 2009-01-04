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
 * User bookshelf table model
 *
 * @author dikmax
 * @version
 */
class App_Db_Table_UserBookshelf extends Zend_Db_Table_Abstract
{
    /**
     * The default table name
     */
    protected $_name = 'lib_user_bookshelf';

    /**
     * Primery key
     */
    protected $_primary = 'lib_user_bookshelf_id';

    /**
     * This table supports auto-incremental key
     */
    protected $_sequence = true;

    /**
     * Foreign keys
     */
    protected $_referenceMap = array(
        'Title' => array(
            'columns'           => 'lib_title_id',
            'refTableClass'     => 'App_Db_Table_Title',
            'refColumns'        => 'lib_title_id'
        ),
        'User' => array(
            'columns'           => 'lib_user_id',
            'refTableClass'     => 'App_Db_Table_User',
            'refColumns'        => 'lib_user_id'
        )
    );

    /**
     * Returns titles by user id
     *
     * @param int $userId
     *
     */
    public function findTitlesByUserId($userId)
    {
        $select = $this->_db->select()
            ->from($this->_name, array())
            ->joinLeftUsing('lib_title', '`lib_title_id`')
            ->where('`lib_user_bookshelf`.`lib_user_id` = :user_id');

        return $this->_db->fetchAll($select, array(
            ':user_id' => $userId
        ));
    }

    public function setMark($userId, $titleId, $mark)
    {
        $select = $this->_db->select()
            ->from($this->_name, 'lib_user_bookshelf_id')
            ->where('lib_user_id = :lib_user_id')
            ->where('lib_title_id = :lib_title_id')
            ->where('relation BETWEEN 1 AND 5');
        $marks = $this->_db->fetchAll($select, array(
            ':lib_user_id' => $userId,
            ':lib_title_id' => $titleId
        ));
        if (!$marks) {
            // New mark
            $this->insert(array(
                'lib_user_id' => $userId,
                'lib_title_id' => $titleId,
                'relation' => $mark + 3
            ));
        } else if (count($marks) == 1) {
            // Old mark
            $this->update(array('relation' => $mark + 3),
                $this->_db->quoteInto('lib_user_bookshelf_id = ?', $marks[0]['lib_user_bookshelf_id']));
        } else if (count($marks) > 1) {
            // Hmmm. We have some extra marks. Removing
            $ids = array();
            for ($i = 1; $i < count($marks); ++$i) {
                $ids[] = $marks[$i]['lib_user_bookshelf_id'];
            }
            $this->delete('lib_user_bookshelf_id IN (' . implode(', ', $ids) . ')');

            $this->update(array('relation' => $mark + 3),
                $this->_db->quoteInto('lib_user_bookshelf_id = ?', $marks[0]['lib_user_bookshelf_id']));
        }
    }
}
