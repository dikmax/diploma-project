<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id$
 */

require_once 'App/Date.php';
require_once 'App/Mail.php';
require_once 'App/User/Bookshelf.php';
require_once 'App/User/Friends.php';
require_once 'App/User/Neighbors.php';
require_once 'App/User/OtherFriends.php';
require_once 'App/Writeboard.php';
require_once 'Zend/Acl/Role/Interface.php';

/**
 * User model
 *
 * @copyright  2008 Maxim Dikun
 * @version    Release: 0.0.1
 */
class App_User implements Zend_Acl_Role_Interface
{
    /*
     * Sex constants
     */
    const SEX_UNDEFINED = 0;
    const SEX_MALE = 1;
    const SEX_FEMALE = 2;

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
     * Real name
     *
     * @var string
     */
    protected $_realName;

    /**
     * User's sex
     *
     * @var int
     */
    protected $_sex;

    /**
     * About
     *
     * @var string
     */
    protected $_about;

    /**
     * Userpic uploaded
     *
     * @var boolean
     */
    protected $_userpic;

    /**
     * Path to userpic
     *
     * @var string
     */
    protected $_userpicUrl;

    /**
     * Url to userpic
     *
     * @var string
     */
    protected $_userpicPath;

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
     *
     * @var App_User_Neighbors
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
     *   <li><code>real_name</code>: real name (<b>string</b>)</li>
     *   <li><code>sex</code>: sex (<b>int</b>)</li>
     *   <li><code>about</code>: about (<b>string</b>)</li>
     *   <li><code>userpic</code>: is userpic uploaded (<b>boolean</b>)</li>
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

        // Real name
        $this->_realName = isset($construct['real_name'])
                         ? $construct['real_name']
                         : '';

        // Sex
        $this->_sex = isset($construct['sex'])
                    ? $construct['sex']
                    : self::SEX_UNDEFINED;

        // About
        $this->_about = isset($construct['about'])
                      ? $construct['about']
                      : '';

        // Userpic
        $this->_userpic = isset($construct['userpic'])
                        ? (boolean)$construct['userpic']
                        : false;
        $this->_userpicPath = null;
        $this->_userpicUrl = null;

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
        require_once 'App/Exception.php';
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

            require_once 'App/Db/Table/User.php';
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
            require_once 'App/Db/Table/User.php';
            $userTable = new App_Db_Table_User();

            $userTable->update(array(
                'real_name' => $this->_realName,
                'sex' => $this->_sex,
                'about' => $this->_about,
                'userpic' => $this->_userpic
            ), $userTable->getAdapter()->quoteInto('lib_user_id = ?', $this->_libUserId));
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
     * Returns user real name
     *
     * @return string
     */
    public function getRealName()
    {
        return $this->_realName;
    }

    /**
     * Sets user real name
     *
     * @param string $realName
     */
    public function setRealName($realName)
    {
        $this->_realName = $realName;
    }

    /**
     * Returns user sex
     *
     * @return int
     */
    public function getSex()
    {
        return $this->_sex;
    }

    /**
     * Sets user sex
     *
     * @param int $sex
     */
    public function setSex($sex)
    {
        if ($sex == self::SEX_UNDEFINED || $sex == self::SEX_MALE
            || $sex == self::SEX_FEMALE)
        {
            $this->_sex = $sex;
        }
    }

    /**
     * Return user about info
     *
     * @return string
     */
    public function getAbout()
    {
        return $this->_about;
    }

    /**
     * Sets user about info
     *
     * @param string $about
     */
    public function setAbout($about)
    {
        $this->_about = $about;
    }

    /**
     * Returns is userpic uploaded
     *
     * @return boolean
     */
    public function getUserpic()
    {
        return $this->_userpic;
    }

    /**
     * Sets userpic state
     *
     * @param boolean $userpic
     */
    public function setUserpic($userpic)
    {
        $this->_userpic = $userpic;
        $this->_userpicUrl = null;
    }

    /**
     * Returns path to userpic.
     *
     * It always return returns path where userpic should be, doesn't matter uploaded or not
     *
     * @return string
     */
    public function getUserpicPath()
    {
        if ($this->_userpicPath === null) {
            $hi = $this->_libUserId >> 10;
            $lo = $this->_libUserId & 1023;
            $this->_userpicPath = Zend_Registry::get('publicPath')
                . '/images/userpic/' . $hi . '/' . $lo . '.jpg';
        }

        return $this->_userpicPath;
    }

    /**
     * Returns userpic url
     *
     * @return string
     */
    public function getUserpicUrl()
    {
        if ($this->_userpicUrl === null) {
            if ($this->_userpic) {
                $hi = $this->_libUserId >> 10;
                $lo = $this->_libUserId & 1023;
                $this->_userpicUrl = '/images/userpic/' . $hi . '/' . $lo . '.jpg';
            } else {
                $this->_userpicUrl = '/images/default_user.png';
            }
        }

        return $this->_userpicUrl;
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
