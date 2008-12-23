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
 * Mail thread model
 */
class App_Mail_Thread
{
    /**
     * Index for database table <code>lib_mail_thread</code>
     *
     * @var int
     */
    protected $_libMailThreadId;

    /**
     * First user of the thread
     *
     * @var App_User
     */
    protected $_user1;

    /**
     * Id of first user of the thread
     *
     * @var int
     */
    protected $_user1Id;

    /**
     * Second user of the thread
     *
     * @var App_User
     */
    protected $_user2;

    /**
     * Id of second user of the thread
     *
     * @var int
     */
    protected $_user2Id;

    /**
     * Thread subject
     */
    protected $_subject;

    /**
     * Database table for Thread
     *
     * @var App_Db_Table_MailThread
     */
    protected $_threadTable;

    /**
     * Database table for message
     *
     * @var App_Db_Table_MailMessage
     */
    protected $_messageTable;

    /**
     * Constructs new mail thread
     *
     * @param array $construct
     * Available indices:
     * <ul>
     *   <li><code>lib_mail_thread_id</code>: database id (<b>int</b>)</li>
     *   <li><code>id</code>: alias for <code>lib_mail_thread_id</code> (<b>int</b>)</li>
     *   <li><code>user1</code>: first user of thread (<b>App_User</b>)</li>
     *   <li><code>user2</code>: second user of thread (<b>App_User</b>)</li>
     *   <li><code>user1_id</code>: first user id of thread (<b>int</b>)</li>
     *   <li><code>user2_id</code>: second user id of thread (<b>int</b>)</li>
     *   <li><code>subject</code>: subject of the thread (<b>string</b>)</li>
     * </ul>
     */
    public function __construct(array $construct = array())
    {
        // Id
        if (isset($construct['lib_mail_thread_id'])) {
            $this->_libMailThreadId = $construct['lib_mail_thread_id'];
        } elseif (isset($construct['id'])) {
            $this->_libMailThreadId = $construct['id'];
        } else {
            $this->_libMailThreadId = null;
        }

        // User1
        if (isset($construct['user1_id'])) {
            $this->_user1Id = $construct['user1_id'];
        } else if (isset($construct['user1']) && $construct['user1'] instanceof App_User) {
            $this->_user1 = $construct['user1'];
            $this->_user1Id = $this->_user1->getId();
        } else {
            throw new App_Mail_Thread_Exception('user1 not defined');
        }

        // User2
        if (isset($construct['user2_id'])) {
            $this->_user2Id = $construct['user2_id'];
        } else if (isset($construct['user2']) && $construct['user2'] instanceof App_User) {
            $this->_user2 = $construct['user2'];
            $this->_user2Id = $this->_user2->getId();
        } else {
            throw new App_Mail_Thread_Exception('user2 not defined');
        }

        // Subject
        $this->_subject = isset($construct['subject'])
                        ? $construct['subject']
                        : '';

        $this->_threadTable = new App_Db_Table_MailThread();
        $this->_messageTable = new App_Db_Table_MailMessage();
    }

    /**
     * Writes thread
     */
    public function write()
    {
       if ($this->_libMailThreadId === null) {
            // Writing new
            $this->_libMailThreadId = $this->_threadTable->insert(array(
                'user1_id' => $this->_user1Id,
                'user2_id' => $this->_user2Id,
                'subject' => $this->_subject
            ));
       } else {
           // Update
       }
    }

    /**
     * Adds new message to thread
     *
     * @param boolean $isFirstUser is message from first user to second
     * @param string $message
     */
    public function addMessage($isFirstUser, $message)
    {
        if ($this->_libMailThreadId === null) {
            throw new App_Mail_Thread_Exception('This thread isn\'t saved');
        }
        $this->_messageTable->insert(array(
            'lib_mail_thread_id' => $this->_libMailThreadId,
            'from_user1' => $isFirstUser,
            'message' => $message,
            'date' => new Zend_Db_Expr('NOW()'),
            'is_new' => 1
        ));
    }
}
