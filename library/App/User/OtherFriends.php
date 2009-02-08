<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id$
 */

require_once 'App/Db/Table/UserFriendship.php';
require_once 'App/User.php';

/**
 * Not current user friends model
 */
class App_User_OtherFriends
{
    /**
     * User
     *
     * @var App_User
     */
    protected $_user;

    /**
     * @var App_Db_Table_UserFriendship
     */
    protected $_table;

    /**
     * Contructs new friends class
     */
    public function __construct(App_User $user)
    {
        $this->_user = $user;

        $this->_table = new App_Db_Table_UserFriendship();
    }

    /**
     * Returns list of friends
     *
     * @param int $state
     *
     * @return array of App_User
     */
    public function getFriendsList()
    {
        $currentUser = App_User_Factory::getSessionUser();

        $list = $this->_table->getOtherFriendsList($this->_user->getId(),
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
