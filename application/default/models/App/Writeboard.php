<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id:$
 */

/**
 * Writeboard model
 */
class App_Writeboard extends App_Acl_Resource_Abstract
{
    /**
     * Index for database table <code>lib_writeboard</code>
     *
     * @var int
     */
    protected $_libWriteboardId;

    /**
     * Where writeboard belongs
     *
     * @var string
     */
    protected $_ownerDescription;

    /**
     * Writeboard messages
     *
     * @var array
     */
    protected $_messages = null;

    /**
     * Writeboard messages count
     *
     * @var int
     */
    protected $_messagesCount = null;

    /**
     * Is writeboard changed
     *
     * @var boolean
     */
    protected $_changed = false;

    /**
     * Constructs writeboard object
     *
     * @param array $construct
     * Available indexes
     * <ul>
     *     <li><code>lib_writeboard_id</code>: database id. It must be set to null
     *         if not exists in database (<b>int</b>)</li>
     *     <li><code>id</code>: alias for <code>lib_writeboard_id</code> (<b>int</b>)</li>
     *     <li><code>owner_description</code>: where writeboard belongs (<b>string</b>)</li>
     * </ul>
     */
    public function __construct($construct)
    {
        // Id
        if (isset($construct['lib_writeboard_id'])) {
            $this->_libWriteboardId = $construct['lib_writeboard_id'];
        } elseif (isset($construct['id'])) {
            $this->_libWriteboardId = $construct['id'];
        } else {
            $this->_libWriteboardId = null;
            $this->_changed = true;
        }

        if (isset($construct['owner_description'])) {
            $this->_ownerDescription = (string)$construct['owner_description'];
        } else {
            if ($this->_libWriteboardId === null) {
                throw new App_Writeboard_Exception("Description is required "
                    . "for new writeboards");
            }
            $this->_ownerDescription = '';
        }

        $this->registerResource();
    }

    /**
     * Writes writeboard to database
     */
    public function write()
    {
        $db = Zend_Registry::get("db");

        if ($this->_libWriteboardId === null) {
            // Creating new writeboard
            $data = array('owner_description' => $this->_ownerDescription);
            $db->insert('lib_writeboard', $data);
            $this->setLibWriteboardId($db->lastInsertId());
        } else if ($this->_changed) {
            // Update writeboard
            $data = array('owner_description' => $this->_ownerDescription);
            $db->update('lib_writeboard', $data,
                $db->quoteInto('lib_writeboard_id = ?', $this->_libWriteboardId));
        }
    }

    /**
     * Add new message to writeboard
     *
     * @param string $message Message content
     */
    public function addMessage($message)
    {
        $user = App_User_Factory::getSessionUser();
        if ($user === false) {
            throw new App_Writeboard_Message_Exception('Can\'t create writeboard message without user');
        }
        $acl = Zend_Registry::get('acl');
        $aclRole = Zend_Registry::get('aclRole');
        if (!($acl->isAllowed($aclRole, $this, 'add'))) {
            throw new App_Writeboard_Exception('You have no permission to write in writeboard');
        }

        $writeboardMessage = new App_Writeboard_Message(array(
            'writeboard' => $this,
            'message' => $message,
            'writeboard_writer' => $user
        ));

        $writeboardMessage->write();
    }

    /**
     * Remove message from writeboard
     *
     * @param int $messageid Id of message
     */
    public function removeMessage($messageid)
    {
        $acl = Zend_Registry::get('acl');
        $aclRole = Zend_Registry::get('aclRole');

        $messages = $this->getMessages();
        if (!isset($messages[$messageid])) {
            throw new App_Writeboard_Exception("Message with id=$messageid doesn't exists");
        }

        if (!$acl->isAllowed($aclRole, $messages[$messageid], 'delete')) {
            throw new App_Writeboard_Exception("You have no permission for delete message $messageid");
        }

        $db = Zend_Registry::get('db');

        $db->delete('lib_writeboard_message',
            $db->quoteInto('lib_writeboard_message_id = ?', $messageid));
    }

    // Setters and getters

    /**
     * Returns database id
     *
     * @return int
     */
    public function getLibWriteboardId()
    {
        return $this->_libWriteboardId;
    }

    /**
     * Sets new database id and registers in ACL
     *
     * @param int $id
     */
    protected function setLibWriteboardId($id)
    {
        $this->unregisterResource();
        $this->_libWriteboardId = $id;
        $this->registerResource();
    }

    /**
     * Returns database id (alias for <code>getLibWriteboardId</code>)
     *
     * @return int
     */
    public function getId()
    {
        return $this->_libWriteboardId;
    }

    /**
     * Returns owner description if it was readed from database or added manually
     * May not be useful
     *
     * @return string
     */
    public function getOwnerDescription()
    {
        return $this->_ownerDescription;
    }

    /**
     * Sets new owner description
     *
     * @param string $ownerDescription
     */
    public function setOwnerDescription($ownerDescription)
    {
        if (!is_string($ownerDescription)) {
            throw new App_Writeboard_Exception('Owner description must be string');
        }

        if ($this->_ownerDescription !== $ownerDescription) {
            $this->_ownerDescription = $ownerDescription;
            $this->_changed = true;
        }
    }

    /**
     * Reads from database and returns messages
     *
     * @return array
     */
    public function getMessages()
    {
        if ($this->_messages === null) {
            $db = Zend_Registry::get('db');

            $items = $db->fetchAll('SELECT lib_writeboard_message_id, '
                   . 'writeboard_writer, message, message_date '
                   . 'FROM lib_writeboard_message '
                   . 'WHERE lib_writeboard_id = :lib_writeboard_id '
                   . 'ORDER BY message_date DESC',
                   array(':lib_writeboard_id' => $this->_libWriteboardId));
            $this->_messagesCount = count($items);

            // Extracting users
            $users = array();
            foreach ($items as $item) {
                $users[] = $item['writeboard_writer'];
            }

            $users = App_User_Factory::getInstance()->getUsers($users);

            $result = array();
            foreach ($items as $item) {
                $item['writeboard'] = $this;
                $item['writeboard_writer'] = $users[$item['writeboard_writer']];
                $item['message_date'] = App_Date::fromMysqlString($item['message_date']);
                $result[$item['lib_writeboard_message_id']] = new App_Writeboard_Message($item);
            }
            $this->_messages = $result;
        }
        return $this->_messages;
    }

    /**
     * Reads from database and returns count of messages
     *
     * @return int
     */
    public function getMessagesCount()
    {
        if ($this->_messagesCount === null) {
            $db = Zend_Registry::get('db');

            $count = $db->fetchOne('SELECT count(*) '
                   . 'FROM lib_writeboard_message '
                   . 'WHERE lib_writeboard_id = :lib_writeboard_id',
                   array(':lib_writeboard_id' => $this->_libWriteboardId));
            $this->_messagesCount = $count;
        }
        return $this->_messagesCount;
    }

    /**
     * Returns the string identifier of the Resource
     *
     * @return string
     */
    public function getResourceId()
    {
        if ($this->_libWriteboardId !== null) {
            return "writeboard-" . $this->_libWriteboardId;
        }
        return "writeboard-new";
    }

    /**
     * Returns resource parent (for registering)
     *
     * @return string
     */
    protected function getResourceParentId()
    {
        return "writeboard";
    }
}