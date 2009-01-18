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
 * User neighbors model
 */
class App_User_Neighbors
{
    /**
     * User
     *
     * @var App_User
     */
    protected $_user;

    /**
     * @var App_Db_Table_UserNeighborhood
     */
    protected $_table;

    /**
     * Contructs new friends class
     */
    public function __construct(App_User $user)
    {
        $this->_user = $user;

        $this->_table = new App_Db_Table_UserNeighborhood();
    }

    /**
     * Returns list of friends
     *
     * @param int $state
     *
     * @return array of App_User
     */
    /*
    public function getFriendsList($state)
    {
        $list = $this->_table->getFriendsList($this->_user->getId(), $state);

        $ids = array();
        foreach ($list as $item) {
            $ids[] = $item['user2_id'];
        }

        $users = App_User_Factory::getInstance()->getUsers($ids);
        foreach ($users as $user) {
            $user->setFriendState($state);
        }

        return $users;
    }*/

    /**
     * Updates neighbors list
     */
    public function updateNeighborsList()
    {
        $this->_table->updateNeighborsList($this->_user->getId());
    }

    /*
     * Setters and getters
     */

    /**
     * Returns mailbox user
     *
     * @return App_User
     */
    public function getUser()
    {
        return $this->_user;
    }
}
