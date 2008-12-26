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
 * User friends model
 */
class App_User_Friends
{
    const STATE_UNDEFINED = 0;
    const STATE_APPROVED = 1;
    const STATE_REQUEST_SENT = 2;
    const STATE_REQUEST_RECEIVED = 3;
    const STATE_DECLINED = 4;

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
    public function getFriendsList($state)
    {
        $list = $this->_table->getFriendsList($this->_user->getId(), $state);

        $ids = array();
        foreach ($list as $item) {
            $ids[] = $item['user2_id'];
        }

        return App_User_Factory::getInstance()->getUsers($ids);
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
