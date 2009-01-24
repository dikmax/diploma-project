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
class App_Db_Table_UserBookshelf extends App_Db_Table_Abstract
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

    /**
     * Returns mark for specific user and title
     *
     * @param int $userId
     * @param int $titleId
     *
     * @return int
     */
    public function getMark($userId, $titleId)
    {
        $select = $this->_db->select()
            ->from($this->_name, 'relation')
            ->where('lib_user_id = :lib_user_id')
            ->where('lib_title_id = :lib_title_id')
            ->where('relation BETWEEN 1 AND 5');
        $mark = $this->_db->fetchOne($select, array(
            ':lib_user_id' => $userId,
            ':lib_title_id' => $titleId
        ));
        if ($mark !== false) {
            $mark = (int)$mark - 3;
        }
        return $mark;
    }

    /**
     * Sets mark for specific user and title
     *
     * @param int $userId
     * @param int $titleId
     * @param int $mark
     */
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

    /**
     * Removes mark for specific user and title
     *
     * @param int $userId
     * @param int $titleId
     */
    public function removeMark($userId, $titleId)
    {
        $this->delete($this->_db->quoteInto('lib_user_id = ? ', $userId)
            . $this->_db->quoteInto('AND lib_title_id = ? ', $titleId)
            . 'AND relation BETWEEN 1 AND 5');
    }

    /**
     * Returns list of all marks by user
     *
     * @param int $userId
     * @param boolean $markCast convert mark from range 1 to 5 to range -2 to 2
     *
     * @return array ($titleId => $mark)
     */
    public function getMarks($userId, $markCast = true)
    {
        $select = $this->_db->select()
            ->from($this->_name, array('lib_title_id', 'relation'))
            ->where('lib_user_id = :lib_user_id')
            ->where('relation BETWEEN 1 AND 5');

        $pairs = $this->_db->fetchPairs($select, array(':lib_user_id' => $userId));

        if ($markCast) {
            $keys = array_keys($pairs);
            $size = sizeof($keys);
            for ($i=0; $i < $size; ++$i) {
                $pairs[$keys[$i]] -= 3;
            }
        }

        return $pairs;
    }

    /**
     * Returns marks count by user
     *
     * @param int $userId
     *
     * @return int
     */
    public function getMarksCount($userId)
    {
        $select = $this->_db->select()
            ->from($this->_name, new Zend_Db_Expr('count(*)'))
            ->where('lib_user_id = :lib_user_id')
            ->where('relation BETWEEN 1 AND 5');

        return $this->_db->fetchOne($select, array(':lib_user_id' => $userId));
    }

    /**
     * Returns list of user neighbors (very slow)
     *
     * @param int $userId
     *
     * @return array
     */
    public function getNeighbors($userId)
    {
        /*
         * Desired result
         *
         * SELECT b.lib_user_id, count(*) `count`, AVG(ABS(ub.relation - b.relation)) `avg`
         * FROM lib_user_bookshelf ub
         * INNER JOIN lib_user_bookshelf b ON b.lib_title_id = ub.lib_title_id
         *     AND b.lib_user_id <> ub.lib_user_id AND b.relation BETWEEN 1 AND 5
         * WHERE ub.lib_user_id = :lib_user_id
         *     AND ub.relation BETWEEN 1 AND 5
         * GROUP BY b.lib_user_id
         * HAVING `count` > 10
         * ORDER BY `avg` ASC, `count` DESC
         * LIMIT 100
         */

        $select = $this->_db->select()
            ->from(array('ub' => $this->_name), array(
                'count' => new Zend_Db_Expr('count(*)'),
                'avg' => new Zend_Db_Expr('AVG(ABS(ub.relation - b.relation))')));
        $select->joinInner(array('b' => $this->_name), 'b.lib_title_id = ub.lib_title_id '
                . 'AND b.lib_user_id <> ub.lib_user_id AND b.relation BETWEEN 1 AND 5',
                array('b.lib_user_id'))
            ->where('ub.lib_user_id = :lib_user_id')
            ->where('ub.relation BETWEEN 1 AND 5')
            ->group('b.lib_user_id')
            ->having('`count` > 10')
            ->order('avg ASC')
            ->order('count DESC')
            ->limit(50);
        // In realworld data We will need to remove limit and add having `avg` < 1

        $result = $this->_db->fetchAll($select, array(
            ':lib_user_id' => $userId
        ));

        return $result;
    }

    /**
     * Returns list of similar titles (very slow)
     *
     * @param int $titleId
     *
     * @return array
     */
    public function getSimilarTitles($titleId)
    {
        /*
         * Desired result
         *
         * SELECT b.lib_title_id, count(*) `count`, AVG(ABS(ub.relation - b.relation)) `avg`
         * FROM lib_user_bookshelf ub
         * INNER JOIN lib_user_bookshelf b ON b.lib_user_id = ub.lib_user_id
         *     AND b.lib_title_id <> ub.lib_title_id AND b.relation BETWEEN 1 AND 5
         * WHERE ub.lib_title_id = 13
         *     AND ub.relation BETWEEN 1 AND 5
         * GROUP BY b.lib_title_id
         * HAVING `count` > 10
         * ORDER BY `avg` ASC, `count` DESC
         * LIMIT 100
         */

        $select = $this->_db->select()
            ->from(array('ub' => $this->_name), array(
                'count' => new Zend_Db_Expr('count(*)'),
                'avg' => new Zend_Db_Expr('AVG(ABS(ub.relation - b.relation))')));
        $select->joinInner(array('b' => $this->_name), 'b.lib_user_id = ub.lib_user_id '
                . 'AND b.lib_title_id <> ub.lib_title_id AND b.relation BETWEEN 1 AND 5',
                array('b.lib_title_id'))
            ->where('ub.lib_title_id = :lib_title_id')
            ->where('ub.relation BETWEEN 1 AND 5')
            ->group('b.lib_title_id')
            ->having('`count` > 10')
            ->order('avg ASC')
            ->order('count DESC')
            ->limit(50);
        // In realworld data We will need to remove limit and add having `avg` < 1

        $result = $this->_db->fetchAll($select, array(
            ':lib_title_id' => $titleId
        ));

        return $result;
    }

    /**
     * Updates suggested books
     *
     * @param int $userId
     */
    public function updateSuggestedBooks($userId)
    {
        $this->delete($this->_db->quoteInto('lib_user_id = ?', $userId)
            . $this->_db->quoteInto(' AND relation = ?', App_User_Bookshelf::RELATION_SUGGESTED_BOOK));

        $marks = $this->getMarks($userId, false);
        $marksCount = count($marks);

        /*
         * Desired result
         *
         * SELECT b.lib_title_id, count(*) `count`, AVG(b.relation) `avg`
         * FROM lib_user_neighborhood n
         * INNER JOIN lib_user_bookshelf b ON b.lib_user_id = n.user2_id AND relation BETWEEN 1 AND 5
         * WHERE user1_id = 8
         * GROUP BY b.lib_title_id
         * ORDER BY `avg` DESC, `count` DESC
         * LIMIT 3500
         */
        $select = $this->_db->select()
            ->from(array('n' => 'lib_user_neighborhood'),
                array(
                    'count' => new Zend_Db_Expr('count(*)'),
                    'avg' => new Zend_Db_Expr('AVG(b.relation)')));
        $select->joinInner(array('b' => $this->_name),
                'b.lib_user_id = n.user2_id AND b.relation BETWEEN 1 AND 5',
                'b.lib_title_id')
            ->where('user1_id = :user_id')
            ->group('b.lib_title_id')
            ->having('`count` > 1')
            ->order('avg DESC')
            ->order('count DESC')
            ->limit($marksCount + 100);
        $stmt = $this->_db->prepare($select);
        $stmt->execute(array(
            ':user_id' => $userId
        ));
        $addedCount = 0;
        while (($row = $stmt->fetch(Zend_Db::FETCH_ASSOC)) !== false) {
            if (!isset($marks[$row['lib_title_id']])) {
                $this->insert(array(
                    'lib_user_id' => $userId,
                    'lib_title_id' => $row['lib_title_id'],
                    'relation' => App_User_Bookshelf::RELATION_SUGGESTED_BOOK
                ));
                ++$addedCount;
                if ($addedCount === 100) {
                    $stmt->closeCursor();
                    break;
                }
            }
        }
    }
}
