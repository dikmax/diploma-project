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
 * Mail model
 */
class App_Mail
{
    /**
     * User
     *
     * @var App_User
     */
    protected $_user;

    /**
     * Contructs new mailbox class
     */
    public function __construct(App_User $user)
    {
        $this->_user = $user;
    }

    /*
     * Setters and getters
     */

    /**
     * Returns mailbox user
     */
    public function getUser()
    {
        return $this->_user;
    }
}
