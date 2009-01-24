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
     */
    public function getNeighborsList($userId)
    {
        $select = $this->_db->select()
            ->from($this->_name)
            ->where('user1_id = :user_id')
            ->order('avg ASC')
            ->order('count DESC');

        return $this->_db->fetchAll($select, array(
            ':user_id' => $userId
        ));
    }

    /**
     * Updates list of neightbors list for specified user
     */
    public function updateNeighborsList($userId)
    {
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
