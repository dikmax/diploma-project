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
 * User neighborhood table model
 *
 * @author dikmax
 * @version
 */
class App_Db_Table_UserNeighborhood extends App_Db_Table_Abstract
{
    /**
     * The default table name
     */
    protected $_name = 'lib_user_neighborhood';

    /**
     * Primery key
     */
    protected $_primary = array(
        'user1_id',
        'user2_id');

    /**
     * This table doesn't supports auto-incremental key
     */
    protected $_sequence = false;

    /**
     * Foreign keys
     */
    protected $_referenceMap = array(
        'User' => array(
            'columns'           => 'user1_id',
            'refTableClass'     => 'App_Db_Table_User',
            'refColumns'        => 'lib_user_id'
        ),
        'Neighbor' => array(
            'columns'           => 'user2_id',
            'refTableClass'     => 'App_Db_Table_User',
            'refColumns'        => 'lib_user_id'
        )
    );

    /**
     * Returns list of friends
     *
     * @param int $userId
     * @param int $currentUserId
     *
     * @return array
     */
    public function getNeighborsList($userId, $currentUserId)
    {
        if (is_numeric($currentUserId)) {
            // We have current user id
            $select = "SELECT n.user2_id user_id, f.state "
                . "FROM lib_user_neighborhood n "
                . "LEFT JOIN lib_user_friendship f ON n.user2_id = f.user2_id AND f.user1_id = :current_user_id "
                . "WHERE n.user1_id = :user_id";
            return $this->_db->fetchAll($select, array(
                ':user_id' => $userId,
                ':current_user_id' => $currentUserId
            ));
        } else {
            // We haven't current user id
            $select = "SELECT user2_id user_id "
                . "FROM lib_user_neighborhood n "
                . "WHERE n.user1_id = :user_id";
            return $this->_db->fetchAll($select, array(
                ':user_id' => $userId
            ));
        }
    }

    /**
     * Updates list of neightbors list for specified user
     *
     * @param int $userId
     */
    public function updateNeighborsList($userId)
    {
        require_once 'App/Db/Table/UserBookshelf.php';
        $table = new App_Db_Table_UserBookshelf();
        $neighbors = $table->getNeighbors($userId);

        $this->delete($this->_db->quoteInto('user1_id = ?', $userId));

        foreach ($neighbors as $neighbor) {
            $this->insert(array(
                'user1_id' => $userId,
                'user2_id' => $neighbor['lib_user_id'],
                'avg' => $neighbor['avg'],
                'count' => $neighbor['count']
            ));
        }
    }
}
