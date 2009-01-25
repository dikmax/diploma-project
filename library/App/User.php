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
 * User model
 *
 * @copyright  2008 Maxim Dikun
 * @version    Release: 0.0.1
 */
class App_User implements Zend_Acl_Role_Interface
{
    /**
     * Index for database table <code>lib_user</code>
     *
     * @var int
     */
    protected $_libUserId;

    /**
     * User name
     *
     * @var string
     */
    protected $_login;

    /**
     * MD5 of user password
     *
     * @var string
     */
    protected $_password;

    /**
     * User email
     *
     * @var string
     */
    protected $_email;

    /**
     * User registration date
     *
     * @var App_Date
     */
    protected $_registrationDate;

    /**
     * User last login date
     *
     * @var App_Date
     */
    protected $_loginDate;

    /**
     * User last login IP
     *
     * @var string
     */
    protected $_loginIP;

    /**
     * User personal writeboard id
     *
     * @var int
     */
    protected $_writeboardId;

    /**
     * User personal writeboard.
     * Constructs on first request
     *
     * @var App_Writeboard
     */
    protected $_writeboard;

    /**
     * User bookshelf
     *
     * @var App_User_Bookshelf
     */
    protected $_bookshelf;

    /**
     * User mailbox
     *
     * @var App_Mail
     */
    protected $_mail;

    /**
     * User friends
     *
     * @var App_User_Friends
     */
    protected $_friends;

    /**
     * Friends of not current user
     *
     * @var App_User_OtherFriends
     */
    protected $_otherFriends;

    /**
     * Users friend state
     *
     * @var int
     */
    protected $_friendState;

    /**
     * User neighbors
     */
    protected $_neighbors;

    /**
     * Constructs user object
     *
     * @param array $construct
     * Available indices:
     * <ul>
     *   <li><code>lib_user_id</code>: database id (<b>int</b>)</li>
     *   <li><code>id</code>: alias for <code>lib_user_id</code> (<b>int</b>)</li>
     *   <li><code>login</code>: user login (<b>string</b>)</li>
     *   <li><code>password</code>: user password (<b>string</b>)</li>
     *   <li><code>email</code>: user email (<b>string</b>)</li>
     *   <li><code>registration_date</code>: user registration date
     *       (<b>int|string|array|App_Date</b>)</li>
     *   <li><code>login_date</code>: user last login date
     *       (<b>int|string|array|App_Date</b>)</li>
     *   <li><code>login_ip</code>: user last login ip (<b>string</b>)</li>
     *   <li><code>lib_writeboard_id</code>: writeboard id (<b>int</b>)</li>
     * </ul>
     */
    public function __construct(array $construct = array())
    {
        // Id
        if (isset($construct['lib_user_id'])) {
            $this->_libUserId = $construct['lib_user_id'];
        } elseif (isset($construct['id'])) {
            $this->_libUserId = $construct['id'];
        } else {
            $this->_libUserId = null;
        }

        // Login
        if (isset($construct['login'])) {
            $this->_login = $construct['login'];
        } else {
            $this->_login = ''; // Not current user or guest
        }

        // Password
        if (isset($construct['password'])) {
            $this->_password = $construct['password'];
        } else {
            $this->_password = ''; // Not current user or guest
        }

        // Email
        $this->_email = isset($construct['email'])
                      ? $construct['email']
                      : '';

        // Registration date
        if (isset($construct['registration_date'])) {
            $this->_registrationDate = new App_Date($construct['registration_date']);
        } else {
            $this->_registrationDate = App_Date::now();
        }

        // Login date
        if (isset($construct['login_date'])) {
            $this->_loginDate = new App_Date($construct['login_date']);
        } else {
            $this->_loginDate = App_Date::now();
        }

        // Login ip
        if (isset($construct['login_ip']) && is_string($construct['login_ip'])) {
            $this->_loginIP = $construct['login_ip'];
        } else {
            $this->_loginIP = '0.0.0.0';
        }

        // Writeboard
        if (isset($construct['lib_writeboard_id'])) {
            $this->_writeboardId = $construct['lib_writeboard_id'];
        } else {
            $this->_writeboardId = null;
        }
        $this->_writeboard = null;
        // THINK maybe it would be useful to create 'writeboard' index in $construct

        // Bookshelf
        $this->_bookshelf = null;

        // Mailbox
        $this->_mail = null;

        // Friends
        $this->_friends = null;

        // Friends of not current user
        $this->_otherFriends = null;

        // Friend state
        $this->_friendState = null;

        // Neighbord
        $this->_neighbors = null;

        $this->registerRole();
    }

    /**
     * Serialization
     *
     * @throws App_Exception
     */
    public function __sleep()
    {
        throw new App_Exception("App_User serialization isn't allowed.");
    }

    public function __toString()
    {
        return $this->_login;
    }

    /**
     * Writes user to database
     */
    public function write()
    {
        if ($this->_libUserId === null) {
            // Create new user
            if ($this->_writeboardId === null) {
                $writeboard = new App_Writeboard(array(
                    'owner_description' => 'New user'
                ));
                $writeboard->write();

                $this->_writeboardId = $writeboard->getId();
                $this->_writeboard = $writeboard;
            }

            $userTable = new App_Db_Table_User();
            $insertId = $userTable->insert(array(
                'login' => $this->_login,
                'password' => $this->_password,
                'email' => $this->_email,
                'registration_date' => $this->_registrationDate,
                'lib_writeboard_id' => $this->_writeboardId
            ));

            $this->setLibUserId($insertId);
            $writeboard->setOwnerDescription('User ' . $this->_libUserId);
            $writeboard->write();
        } else {
            // TODO write update user
        }
    }

    /*
     * Work with ACL
     */

    /**
     * Registers role in ACL system
     */
    public function registerRole()
    {
        if (Zend_Registry::isRegistered('acl')) {
            Zend_Registry::get('acl')->addRole($this, 'user');
        }
    }

    /**
     * Unregisters role from ACL system
     */
    public function unregisterRole()
    {
        Zend_Registry::get('acl')->removeRole($this);
    }

    /*
     * Setters and getters
     */

    /**
     * Returns database id
     *
     * @return int
     */
    public function getLibUserId()
    {
        return $this->_libUserId;
    }

    /**
     * Sets new database id and registers it in ACL
     *
     * @param int $id
     */
    protected function setLibUserId($id)
    {
        $this->unregisterRole();
        $this->_libUserId = $id;
        $this->registerRole();
    }

    /**
     * Returns database id (alias for <code>getLibUserId</code>)
     *
     * @return int
     */
    public function getId()
    {
        return $this->_libUserId;
    }

    /**
     * Returns user login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->_login;
    }

    /**
     * Returns user email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->_email;
    }

    /**
     * Returns user registration date
     *
     * @return App_Date
     */
    public function getRegistrationDate()
    {
        return $this->_registrationDate;
    }

    /**
     * Returns user last login date
     *
     * @return App_Date
     */
    public function getLoginDate()
    {
        return $this->_loginDate;
    }

    /**
     * Returns user login ip
     *
     * @return string
     */
    public function getLoginIp()
    {
        return $this->_loginIP;
    }

    /**
     * Returns user personal writeboard
     *
     * @return App_Writeboard
     */
    public function getWriteboard()
    {
        if ($this->_writeboard === null) {
            $this->_writeboard = new App_Writeboard(array(
                'lib_writeboard_id' => $this->_writeboardId
            ));
            $acl = Zend_Registry::get('acl');
            $acl->allow($this, $this->_writeboard, 'delete');
        }
        return $this->_writeboard;
    }

    /**
     * Returns user personal writeboard id
     *
     * @return int
     */
    public function getWriteboardId()
    {
        return $this->_writeboardId;
    }

    /**
     * Returns user bookshelf
     *
     * @return App_User_Bookshelf
     */
    public function getBookshelf()
    {
        if ($this->_bookshelf === null) {
            $this->_bookshelf = new App_User_Bookshelf(array('user' => $this));

            $acl = Zend_Registry::get('acl');
            $acl->allow($this, $this->_bookshelf, 'edit');
        }
        return $this->_bookshelf;
    }

    /**
     * Returns user mailbox
     *
     * @return App_Mail
     */
    public function getMail()
    {
        if ($this->_mail === null) {
            $this->_mail = new App_Mail($this);
        }

        return $this->_mail;
    }

    /**
     * Returns user friends
     *
     * @return App_User_Friends
     */
    public function getFriends()
    {
        if ($this->_friends === null) {
            $this->_friends = new App_User_Friends($this);
        }

        return $this->_friends;
    }

    /**
     * Returns user friends (as not current user)
     *
     * @return App_User_OtherFriends
     */
    public function getOtherFriends()
    {
        if ($this->_otherFriends === null) {
            $this->_otherFriends = new App_User_OtherFriends($this);
        }

        return $this->_otherFriends;
    }

    /**
     * Returns users friend state
     *
     * @return int
     */
    public function getFriendState()
    {
        return $this->_friendState;
    }

    /**
     * Sets users friends state
     *
     * @param int $friendState
     */
    public function setFriendState($friendState)
    {
        $this->_friendState = $friendState;
    }

    /**
     * Returns user neighbors
     *
     * @return App_User_Neighbors
     */
    public function getNeighbors()
    {
        if ($this->_neighbors === null) {
            $this->_neighbors = new App_User_Neighbors($this);
        }

        return $this->_neighbors;
    }
    /*
     * Zend_Acl_Role_Interface implementation
     */

    /**
     * Returns the string identifier of the Role
     *
     * @return string
     */
    public function getRoleId()
    {
        if ($this->_libUserId !== null) {
            return "user-" . $this->_libUserId;
        }
        return "user-new";
    }

}
