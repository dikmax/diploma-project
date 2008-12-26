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
    const STATE_UNDEFINED = 0;
    const STATE_ACTIVE = 1;
    const STATE_SENT = 2;
    const STATE_ARCHIVE = 3;
    const STATE_DELETED = 4;

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
     * State of thread in user1 mailbox
     *
     * @var int
     */
    protected $_stateUser1;

    /**
     * State of thread in user2 mailbox
     *
     * @var int
     */
    protected $_stateUser2;

    /**
     * Thread subject
     *
     * @var string
     */
    protected $_subject;

    /**
     * Thread last update date
     *
     * @var App_Date
     */
    protected $_date;

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
     *   <li><code>state_user1</code>: state of thread in user1 mailbox (<b>int</b>)</li>
     *   <li><code>state_user2</code>: state of thread in user2 mailbox (<b>int</b>)</li>
     *   <li><code>subject</code>: subject of the thread (<b>string</b>)</li>
     *   <li><code>date</code>: thread last update date (<b>int|string|array|App_Date</b>)</li>
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
            $this->_user1 = isset($construct['user1']) && $construct['user1'] instanceof App_User
                          ? $construct['user1']
                          : null;
            $this->_user1Id = $construct['user1_id'];
        } else if (isset($construct['user1']) && $construct['user1'] instanceof App_User) {
            $this->_user1 = $construct['user1'];
            $this->_user1Id = $this->_user1->getId();
        } else {
            throw new App_Mail_Thread_Exception('user1 not defined');
        }

        // User2
        if (isset($construct['user2_id'])) {
            $this->_user2 = isset($construct['user2']) && $construct['user2'] instanceof App_User
                          ? $construct['user2']
                          : null;
            $this->_user2Id = $construct['user2_id'];
        } else if (isset($construct['user2']) && $construct['user2'] instanceof App_User) {
            $this->_user2 = $construct['user2'];
            $this->_user2Id = $this->_user2->getId();
        } else {
            throw new App_Mail_Thread_Exception('user2 not defined');
        }

        // State user1
        $this->_stateUser1 = isset($construct['state_user1'])
                           ? $construct['state_user1']
                           : self::STATE_UNDEFINED;

        // State user2
        $this->_stateUser2 = isset($construct['state_user2'])
                           ? $construct['state_user2']
                           : self::STATE_UNDEFINED;

        // Subject
        $this->_subject = isset($construct['subject'])
                        ? $construct['subject']
                        : '';

        // Date
        $this->_date = isset($construct['date'])
                     ? new App_Date($construct['date'])
                     : App_Date::now();

        // Initialize private variables
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
                'state_user1' => $this->_stateUser1,
                'state_user2' => $this->_stateUser2,
                'subject' => $this->_subject,
                'date' => $this->_date->toMysqlString()
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
     * @param boolean $setActive move thread to active folder
     */
    public function addMessage($isFirstUser, $message, $setActive = true)
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

        // Updating thread
        $this->_date = App_Date::now();
        $data = array('date' => $this->_date->toMysqlString());
        if ($setActive) {
             $data['state_user1'] = self::STATE_ACTIVE;
             $data['state_user2'] = self::STATE_ACTIVE;
             $this->_stateUser1 = self::STATE_ACTIVE;
             $this->_stateUser2 = self::STATE_ACTIVE;
        }
        $this->_threadTable->update(
            $data,
            $this->_threadTable->getAdapter()->quoteInto('lib_mail_thread_id = ?', $this->_libMailThreadId)
        );
    }

    /**
     * Returns messages of thread
     */
    public function getMessages() {
        if ($this->_libMailThreadId === null) {
            throw new App_Mail_Thread_Exception('This thread isn\'t saved');
        }

        $messages = $this->_messageTable->getThreadMessages($this->_libMailThreadId);

        if (!$messages) {
            return array();
        }
        return $messages;
    }

    /**
     * Mark messages as read
     *
     * @param boolean $first mark messages from first user
     */
    public function markAsRead($first) {
        if ($this->_libMailThreadId === null) {
            throw new App_Mail_Thread_Exception('This thread isn\'t saved');
        }

        $this->_messageTable->markAsRead($this->_libMailThreadId, $first);
    }

    /*
     * Setters and getters
     */

    /**
     * Returns database id
     *
     * @return int
     */
    public function getLibMailThreadId()
    {
        return $this->_libMailThreadId;
    }

    /**
     * Returns database id (alias for <code>getLibMailThreadId</code>)
     *
     * @return int
     */
    public function getId()
    {
        return $this->_libMailThreadId;
    }

    /**
     * Returns id of user1
     *
     * @return int
     */
    public function getUser1Id()
    {
        return $this->_user1Id;
    }

    /**
     * Returns user1
     *
     * @return App_User
     */
    public function getUser1()
    {
        if ($this->_user1 === null) {
            $this->_user1 = App_User_Factory::getInstance()->getUser($this->_user1Id);
        }
        return $this->_user1;
    }

    /**
     * Returns id of user2
     *
     * @return int
     */
    public function getUser2Id()
    {
        return $this->_user2Id;
    }

    /**
     * Returns user2
     *
     * @return App_User
     */
    public function getUser2()
    {
        if ($this->_user2 === null) {
            $this->_user2 = App_User_Factory::getInstance()->getUser($this->_user2Id);
        }
        return $this->_user2;
    }

    /**
     * Returns thread subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->_subject;
    }

    /**
     * Returns thread last modification date
     *
     * @return App_Date
     */
    public function getDate()
    {
        return $this->_date;
    }
}
