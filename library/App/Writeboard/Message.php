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
 * Writeboard message model
 */
class App_Writeboard_Message extends App_Acl_Resource_Abstract
{
    /**
     * Index for database table <code>lib_writeboard_message</code>
     *
     * @var int
     */
    protected $_libWriteboardMessageId;
    
    /**
     * Id of writeboard
     *
     * @var int
     */
    protected $_writeboardId;
    
    /**
     * Instance of writeboard
     *
     * @var App_Writeboard
     */
    protected $_writeboard;
    
    /**
     * Message content
     *
     * @var string
     */
    protected $_message;
    
    /**
     * Date and time of message
     *
     * @var App_Date
     */
    protected $_messageDate;
    
    /**
     * User who leave message
     *
     * @var App_User
     */
    protected $_writeboardWriter;
    
    /**
     * Constructs writeboard object
     *
     * @param array $construct
     * Available indexes
     * <ul>
     *   <li><code>lib_writeboard_message_id</code>: database id.
     *       It must be set to null if not exists in database (<b>int</b>)</li>
     *   <li><code>id</code>: alias for <code>lib_writeboard_message_id</code>
     *       (<b>int</b>)</li>
     *   <li><code>lib_writeboard_id</code>: id of writeboard (<b>int</b>)</li>
     *   <li><code>writeboard</code>: id or instance of writeboard
     *       (<b>App_Writeboard|int</b>)</li>
     *   <li><code>message</code>: message content (<b>string</b>)</li>
     *   <li><code>message_date</code>: message date and time
     *       (<b>int|string|array|App_Date</b>)</li>
     *   <li><code>writeboard_writer</code>: instance or id of writer
     *       (<b>App_User|int</b>)</li>
     * </ul>
     */
    public function __construct($construct)
    {
        // Id
        if (isset($construct['lib_writeboard_message_id'])) {
            $this->_libWriteboardMessageId = $construct['lib_writeboard_message_id'];
        } elseif (isset($construct['id'])) {
            $this->_libWriteboardMessageId = $construct['id'];
        } else {
            $this->_libWriteboardMessageId = null;
        }
        
        // Writeboard
        if (isset($construct['lib_writeboard_id'])) {
            $this->_writeboardId = $construct['lib_writeboard_id'];
            if (isset($construct['writeboard']) && $construct['writeboard'] instanceof App_Writeboard) {
                $this->_writeboard = $construct['writeboard'];
            } else {
                $this->_writeboard = null;
            }
        } else if (isset($construct['writeboard'])) {
            if ($construct['writeboard'] instanceof App_Writeboard) {
                $this->_writeboard = $construct['writeboard'];
                $this->_writeboardId = $this->_writeboard->getId();
            } else if (is_numeric($construct['writeboard'])) {
                $this->_writeboardId = $construct['writeboard'];
                $this->_writeboard = null;
            } else {
                throw new App_Writeboard_Message_Exception("'writeboard' index "
                    . "must be int or instance of App_Writeboard");
            }
        } else {
            throw new App_Writeboard_Message_Exception("Can't create message "
                . "deattached from writeboard.");
        }
        
        // Message
        $this->_message = isset($construct['message']) ? $construct['message'] : '';
        
        // Message date and time
        if (isset($construct['message_date'])) {
            $this->_messageDate = new App_Date($construct['message_date']);
        } else {
            $this->_messageDate = App_Date::now();
        }
        
        // Writer
        if (isset($construct['writeboard_writer'])) {
            if ($construct['writeboard_writer'] instanceof App_User) {
                $this->_writeboardWriter = $construct['writeboard_writer'];
            } else if (is_numeric($construct['writeboard_writer'])) {
                // TODO this paramerer must accept array instead of int
                
                // TODO allow to create simplified instances or smth
                $this->_writeboardWriter = App_User_Factory::getInstance()->getUser($construct['writeboard_writer']);
                /*
                $this->_writeboardWriter = new App_User(array(
                    "lib_user_id" => $construct['writeboard_writer'],
                    "readonly" => true
                ));*/
            } else {
                throw new App_Writeboard_Message_Exception("'writeboard_writer' "
                    . "index must be intanceof App_User or int");
            }
        } else {
            $this->_writeboardWriter = null;
        }
        
        $this->registerResource();
    }

    /**
     * Writes writeboard message to database
     */
    public function write()
    {
        $db = Zend_Registry::get("db");
        
        if ($this->_libWriteboardMessageId === null) {
            // Creating new writeboard message
            $data = array('lib_writeboard_id' => $this->_writeboardId,
                          'writeboard_writer' => $this->_writeboardWriter->getId(),
                          'message' => $this->_message,
                          'message_date' => $this->_messageDate->toMysqlString());
            $db->insert('lib_writeboard_message', $data);
            $this->unregisterResource();
            $this->_libWriteboardId = $db->lastInsertId();
            $this->registerResource();
        } else {
            // Update writeboard message
            // TODO Write update writeboard message
        }
    }
    
    // Setters and getters
    
    /**
     * Returns database id
     *
     * @return int
     */
    public function getLibWriteboardMessageId()
    {
        return $this->_libWriteboardMessageId;
    }
    
    /**
     * Sets new database id and registers in ACL
     *
     * @param int $id
     */
    protected function setLibWriteboardMessageId($id)
    {
        $this->unregisterResource();
        $this->_libWriteboardMessageId = $id;
        $this->registerResource();
    }
    
    /**
     * Returns database id (alias for <code>getLibWriteboardMessageId</code>)
     *
     * @return int
     */
    public function getId()
    {
        return $this->_libWriteboardMessageId;
    }
    
    /**
     * Return id of writeboard
     *
     * @return int
     */
    public function getWriteboardId()
    {
        return $this->_writeboardId;
    }
    
    /**
     * Returns writeboard
     *
     * @return App_Writeboard
     */
    public function getWriteboard()
    {
        return $this->_writeboard;
    }
    
    /**
     * Returns message content
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->_message;
    }
    
    /**
     * Returns message date/time
     *
     * @return App_Date
     */
    public function getMessageDate()
    {
        return $this->_messageDate;
    }
    
    /**
     * Return message creator
     *
     * @return App_User
     */
    public function getWriteboardWriter()
    {
        return $this->_writeboardWriter;
    }
    
    // Zend_Acl_Resource_Interface implementation
    
    /**
     * Returns the string identifier of the Resource
     *
     * @return string
     */
    public function getResourceId()
    {
        if ($this->_libWriteboardMessageId !== null) {
            return "writeboard-message-" . $this->_libWriteboardMessageId;
        }
        return  "writeboard-message-new";
    }
    
    /**
     * Returns resource parent (for registering)
     *
     * @return string|Zend_Acl_Resource_Interface
     */
    protected function getResourceParentId()
    {
        if ($this->_writeboard === null || !($this->_writeboard instanceof App_Writeboard)) {
            return "writeboard";
        }
        return $this->_writeboard;
    }

    /**
     * Registers resource in ACL system
     */
    public function registerResource()
    {
        parent::registerResource();
        $acl = Zend_Registry::get('acl');
        $acl->allow($this->_writeboardWriter, $this, 'delete');
    }
}