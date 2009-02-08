<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id$
 */

require_once 'App/Db/Table/UserNeighborhood.php';
require_once 'App/User.php';

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
     * @return array of App_User
     */
    public function getNeightborsList()
    {
        $currentUser = App_User_Factory::getSessionUser();

        $list = $this->_table->getNeighborsList($this->_user->getId(),
            $currentUser === null ? null : $currentUser->getId());

        $ids = array();
        foreach ($list as $item) {
            $ids[] = $item['user_id'];
        }

        $users = App_User_Factory::getInstance()->getUsers($ids);

        // Updating relations to current user
        if ($currentUser !== null) {
            foreach ($list as $item) {
                $users[$item['user_id']]->setFriendState((int)$item['state']);
            }
        }

        return $users;
    }

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
