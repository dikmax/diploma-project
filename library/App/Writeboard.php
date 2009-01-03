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
     * lib_writeboard_message table
     *
     * @var App_Db_Table_WriteboardMessage
     */
    protected $_table;

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

        $this->_table = new App_Db_Table_WriteboardMessage();

        $this->registerResource();
    }

    /**
     * Writes writeboard to database
     */
    public function write()
    {
        if ($this->_libWriteboardId === null) {
            // Creating new writeboard
            $table = new App_Db_Table_Writeboard();
            $insertId = $table->insert(array(
                'owner_description' => $this->_ownerDescription
            ));
            $this->setLibWriteboardId($insertId);
        } else if ($this->_changed) {
            // Update writeboard
            $table = new App_Db_Table_Writeboard();
            $table->update(array('owner_description' => $this->_ownerDescription),
                $table->getAdapter()->quoteInto('lib_writeboard_id = ?', $this->_libWriteboardId));
        }
    }

    /**
     * Add new message to writeboard
     *
     * @param string $message Message content
     *
     * @return App_Writeboard_Message new message
     */
    public function addMessage($message)
    {
        $user = App_User_Factory::getSessionUser();
        if ($user === null) {
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

        return $writeboardMessage;
    }

    /**
     * Remove message from writeboard
     *
     * @param int $messageid Id of message
     */
    public function removeMessage($messageId)
    {
        $acl = Zend_Registry::get('acl');
        $aclRole = Zend_Registry::get('aclRole');

        if ($this->_messages !== null) {
            $message = isset($this->_messages[$messageId])
                ? $this->_messages[$messageId]
                : null;
        } else {
            $messages = $this->_table->find($messageId);
            if (!$messages->offsetExists(0)
                || $messages[0]['lib_writeboard_id'] != $this->_libWriteboardId) {
                $message = null;
            } else {
                $item = $messages[0]->toArray();
                $item['writeboard'] = $this;
                $item['writeboard_writer'] = App_User_Factory::getInstance()->getUser($item['writeboard_writer']);
                $item['message_date'] = App_Date::fromMysqlString($item['message_date']);
                $message = new App_Writeboard_Message($item);
            }
        }
        if (!$message) {
            throw new App_Writeboard_Exception("Message with id=$messageId doesn't exists");
        }

        if (!$acl->isAllowed($aclRole, $message, 'delete')) {
            throw new App_Writeboard_Exception("You have no permission for delete message $messageId");
        }

        $this->_table->delete(
            $this->_table->getAdapter()->quoteInto('lib_writeboard_message_id = ?', $messageId)
        );

        if ($this->_messages !== null) {
            unset($this->_messages[$messageId]);
        }
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
            $items = $this->_table->getMessages($this->_libWriteboardId);
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
            $this->_messagesCount = $this->_table->getMessagesCount($this->_libWriteboardId);
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