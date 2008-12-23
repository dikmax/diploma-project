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

    /**
     * Creates new thread and writes it to database
     *
     * @return App_Mail_Thread
     */
    public function createNewThread(App_User $toUser, $subject, $message)
    {
        $thread = new App_Mail_Thread(array(
            'user1' => $this->_user,
            'user2' => $toUser,
            'state_user1' => App_Mail_Thread::STATE_SENT,
            'state_user2' => App_Mail_Thread::STATE_ACTIVE,
            'subject' => $subject
        ));
        $thread->write();

        $thread->addMessage(true, $message);

        return $thread;
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
