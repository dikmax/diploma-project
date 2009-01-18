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
 * Factory for saving users for later reuse
 *
 * @copyright  2008 Maxim Dikun
 * @version    Release: 0.0.1
 */
class App_User_Factory
{
    /**
     * Singletone instance
     *
     * @var App_User_Factory
     */
    private static $_instance = null;

    /**
     * Users cache
     *
     * @var array
     */
    protected $_users;

    /**
     * Users table
     *
     * @var App_Db_Table_User
     */
    protected $_table;

    /**
     * Max user id
     *
     * @var int
     */
    protected $_maxUserId;

    /**
     * Constructs users factory object
     */
    private function __construct()
    {
        $this->_users = array();
        $this->_table = new App_Db_Table_User();
        $this->_maxUserId = null;
    }

    /**
     * Prevents from cloning
     */
    private function __clone()
    {
        throw new App_User_Factory_Exception("Clone isn't allowed");
    }

    /**
     * Returns singleton instance
     *
     * @return App_User_Factory
     */
    public static function getInstance()
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /**
     * Returns session user
     *
     * @return App_User
     */
    public static function getSessionUser()
    {
        $factory = self::getInstance();
        $session = Zend_Registry::get('session');
        if (!isset($session->userId)) {
            $session->userId = false;
            return null;
        }
        if (is_numeric($session->userId)) {
            return $factory->getUser($session->userId);
        }
        return null;
    }

    /**
     * Sets session user
     *
     * @param App_User $user user to set
     */
    public static function setSessionUser($user)
    {
        $session = Zend_Registry::get('session');
        if ($user === false || !($user instanceof App_User)) {
            $session->userId = false;
        } else {
            $session->userId = $user->getId();
        }

    }

    /**
     * Returns user with specified id or condition from database
     *
     * @param int|string $cond user id or string with condition. If there is
     *     more than one row in result, it returns first one.
     * @param string $value value to quote into condition
     *
     * @return App_User or <code>null</code> if not found
     *
     * @throws App_User_Factory_Exception
     */
    public function getUser($cond, $value = null)
    {
        if (is_numeric($cond) && isset($this->_users[$cond])
            && $this->_users[$cond] instanceof App_User)
        {
            // User with specified id found in cache
            return $this->_users[$cond];
        }

        if (is_numeric($cond)) {
            $rows = $this->_table->find((int)$cond);
            if (count($rows) == 0) {
                return null;
            }
            $row = $rows->current();
        } else if (is_string($cond)) {
            $row = $this->_table->fetchRow(
                $this->_table->select()->where($cond, $value)
            );
            if ($row === null) {
                return null;
            }
        } else {
            throw new App_User_Factory_Exception('First parameter to '
                . 'App_User_Factory::get() must be int or string');
        }

        $row = $row->toArray();
        $userId = $row['lib_user_id'];
        if (isset($this->_users[$userId]) && $this->_users[$userId] instanceof App_User) {
            // User with specified condition found in cache
            return $this->_users[$userId];
        }

        $row['registration_date'] = App_Date::fromMysqlString($row['registration_date']);
        $row['login_date'] = App_Date::fromMysqlString($row['login_date']);
        $this->_users[$userId] = new App_User($row);
        return $this->_users[$userId];
    }

    /**
     * Returns user with specified login from database
     *
     * @param string $login User login
     * @return App_User
     */
    public function getUserByLogin($login)
    {
        return $this->getUser('login = ?', $login);
    }

    /**
     * Returns user with specified email from database
     *
     * @param string $email User login
     * @return App_User
     */
    public function getUserByEmail($email)
    {
        return $this->getUser('email = ?', $email);
    }

    /**
     * Returns users by ids list
     *
     * @return array
     */
    public function getUsers(array $ids)
    {
        $ids = array_unique($ids);

        // Assigning already retrieved
        $result = array();
        $new = array();
        foreach ($ids as $id) {
            if (isset($this->_users[$id])) {
                $result[$id] = $this->_users[$id];
            } else {
                $new[] = (int)$id;
            }
        }

        if (count($new) > 0) {
            $newUsers = $this->_table->find($new);

            foreach ($newUsers as $row) {
                $row = $row->toArray();
                $userId = $row['lib_user_id'];
                $row['registration_date'] = App_Date::fromMysqlString($row['registration_date']);
                $row['login_date'] = App_Date::fromMysqlString($row['login_date']);
                $this->_users[$userId] = new App_User($row);
                $result[$userId] = $this->_users[$userId];
            }
        }

        return $result;
    }

    public function addUser(App_User $user)
    {
        $userId = $user->getId();

        if ($userId !== null) {
            $this->_users[$userId] = $user;
        }
    }

    /**
     * Registers new user
     *
     * @param array $params
     *
     * @return App_User
     */
    public function registerUser(array $params)
    {
        $user = new App_User(array(
            'login' => $params['login'],
            'password' => md5($params['password']),
            'email' => $params['email']
        ));
        $user->write();
        $this->addUser($user);

        return $user;
    }

    /**
     * Return max user id
     *
     * @return int
     */
    public function getMaxUserId()
    {
        if ($this->_maxUserId === null) {
             $this->_maxUserId = $this->_table->getMaxUserId();
        }

        return $this->_maxUserId;
    }
}