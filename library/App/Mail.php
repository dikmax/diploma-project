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

    /**
     * Returns list of threads with specific state
     *
     * @param int $state
     *
     * @return array
     */
    public function getThreadsList($state)
    {
        $table = new App_Db_Table_MailThread();

        $userId = $this->_user->getId();
        $list = $table->getThreadsList($userId, $state);

        $result = array();
        if ($list) {
            // Prepare users
            $users = array();
            foreach ($list as $item) {
                $users[] = $item['user1_id'] == $userId
                         ? $item['user2_id']
                         : $item['user1_id'];
            }
            $users = App_User_Factory::getInstance()->getUsers($users);
            $users[$userId] = $this->_user;

            foreach ($list as $item) {
                $item['date'] = App_Date::fromMysqlString($item['date']);
                $item['user1'] = $users[$item['user1_id']];
                $item['user2'] = $users[$item['user2_id']];
                $result[] = new App_Mail_Thread($item);
            }
        }

        return $result;
    }

    /**
     * Returns specific thread
     *
     * @param int $threadId id of the thread
     *
     * @return App_Mail_Thread or null if thread not found or not allowed
     */
    public function getThread($threadId)
    {
        $table = new App_Db_Table_MailThread();

        $thread = $table->getThread($this->_user->getId(), $threadId);

        if (!$thread) {
            return null;
        }

        $thread['date'] = App_Date::fromMysqlString($thread['date']);
        return new App_Mail_Thread($thread);
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
