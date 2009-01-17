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
        'ChannelItem' => array(
            'columns'           => 'user1_id',
            'refTableClass'     => 'App_Db_Table_User',
            'refColumns'        => 'lib_user_id'
        ),
        'Tag' => array(
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
