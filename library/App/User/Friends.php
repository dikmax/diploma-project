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
    const STATE_CANCELED = 5;

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

    /**
     * Sends request
     *
     * @param App_User $user
     */
    public function sendRequest(App_User $user)
    {
        $current = $this->_table->find($user->getId(), $this->_user->getId());
        if (count($current) === 0) {
            $state = 0;
        } else {
            $state = $current[0]['state'];
        }
        if ($state == self::STATE_APPROVED || $state == self::STATE_REQUEST_RECEIVED) {
            return;
        }
        if ($state == self::STATE_REQUEST_SENT) {
            $this->_table->setState($this->_user->getId(), $user->getId(),
                self::STATE_APPROVED, self::STATE_APPROVED);
            return;
        }
        $this->_table->setState($this->_user->getId(), $user->getId(),
            self::STATE_REQUEST_SENT, self::STATE_REQUEST_RECEIVED);
    }

    /**
     * Accepts request
     *
     * @param App_User $user
     */
    public function acceptRequest(App_User $user)
    {
        $current = $this->_table->find($this->_user->getId(), $user->getId());
        if (count($current) === 0) {
            $state = 0;
        } else {
            $state = $current[0]['state'];
        }
        if ($state != self::STATE_REQUEST_RECEIVED) {
            return;
        }
        $this->_table->setState($this->_user->getId(), $user->getId(),
            self::STATE_APPROVED, self::STATE_APPROVED);
    }

    /**
     * Declines request
     *
     * @param App_User $user
     */
    public function declineRequest(App_User $user)
    {
        $current = $this->_table->find($this->_user->getId(), $user->getId());
        if (count($current) === 0) {
            $state = 0;
        } else {
            $state = $current[0]['state'];
        }
        if ($state != self::STATE_REQUEST_RECEIVED) {
            return;
        }
        $this->_table->setState($this->_user->getId(), $user->getId(),
            self::STATE_DECLINED, self::STATE_DECLINED);
    }

    /**
     * Cancels request
     *
     * @param App_User $user
     */
    public function cancelRequest(App_User $user)
    {
        $current = $this->_table->find($this->_user->getId(), $user->getId());
        if (count($current) === 0) {
            $state = 0;
        } else {
            $state = $current[0]['state'];
        }
        if ($state != self::STATE_REQUEST_SENT) {
            return;
        }
        $this->_table->setState($this->_user->getId(), $user->getId(),
            self::STATE_CANCELED, self::STATE_CANCELED);
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
