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
 * User friendship table model
 *
 * @author dikmax
 * @version
 */
class App_Db_Table_UserFriendship extends App_Db_Table_Abstract
{
    /**
     * The default table name
     */
    protected $_name = 'lib_user_friendship';

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
        'Friend' => array(
            'columns'           => 'user2_id',
            'refTableClass'     => 'App_Db_Table_User',
            'refColumns'        => 'lib_user_id'
        )
    );

    /**
     * Returns list of friends
     *
     * @param int $userId
     * @param int $state
     *
     * @return array
     */
    public function getFriendsList($userId, $state)
    {
        $select = $this->_db->select()
            ->from($this->_name)
            ->where('user1_id = :user_id')
            ->where('state = :state');

        return $this->_db->fetchAll($select, array(
            ':user_id' => $userId,
            ':state' => $state
        ));
    }

    /**
     * Returns list of friends for not current user and their relation to current
     *
     * @param int $userId
     * @param int $currentUserId
     *
     * @return array
     */
    public function getOtherFriendsList($userId, $currentUserId)
    {
        if (is_numeric($currentUserId)) {
            // We have current user id
            $select = "SELECT f.user2_id user_id, f2.state "
                . "FROM lib_user_friendship f "
                . "LEFT JOIN lib_user_friendship f2 ON f.user2_id = f2.user2_id AND f2.user1_id = :current_user_id "
                . "WHERE f.user1_id = :user_id AND f.state = :state";
            return $this->_db->fetchAll($select, array(
                ':user_id' => $userId,
                ':current_user_id' => $currentUserId,
                ':state' => App_User_Friends::STATE_APPROVED
            ));
        } else {
            // We haven't current user id
            $select = "SELECT f.user2_id user_id "
                . "FROM lib_user_friendship f "
                . "WHERE f.user1_id = :user_id AND f.state = :state";
            return $this->_db->fetchAll($select, array(
                ':user_id' => $userId,
                ':state' => App_User_Friends::STATE_APPROVED
            ));
        }
    }

    /**
     * Update status between users
     *
     * @param int $user1Id
     * @param int $user2Id
     * @param int $state1 Status user1 -> user2
     * @param int $state2 Status user2 -> user1
     */
    public function setState($user1Id, $user2Id, $state1, $state2)
    {
        $user1Id = (int)$user1Id;
        $user2Id = (int)$user2Id;
        $state1 = (int)$state1;
        $state2 = (int)$state2;
        $replace = $this->_db->prepare('REPLACE INTO ' . $this->_name . ' (user1_id, user2_id, state) '
                 . 'VALUES (:user1, :user2, :state)');
        $replace->execute(array(
            ':user1' => $user1Id,
            ':user2' => $user2Id,
            ':state' => $state1
        ));
        $replace->execute(array(
            ':user1' => $user2Id,
            ':user2' => $user1Id,
            ':state' => $state2
        ));
    }
}
