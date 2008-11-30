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

    private function __construct()
    {
        $this->_users = array();
    }

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
     * @return App_User
     *
     * @throws App_User_Exception
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

        $db = Zend_Registry::get('db');

        $select = $db->select();
        $select->from('lib_user',
                      array('lib_user_id', 'login', 'password', 'email',
                            'registration_date', 'login_date', 'login_ip',
                            'lib_writeboard_id'));

        if (is_numeric($cond)) {
            $select->where("lib_user_id = ?", $cond);
        } else if (is_string($cond)) {
            $select->where($cond, $value)
                   ->limit(1);
        } else {
            throw new App_User_Factory_Exception('First parameter to '
                . 'App_User_Factory::get() must be int or string');
        }

        $row = $db->fetchRow($select);

        if ($row === false) {
            if (is_numeric($cond)) {
                throw new App_User_Exception('User with id=' . $cond . ' doesn\'t exists');
            } else {
                throw new App_User_Exception('User with requested condition doesn\' found.');
            }
        }

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
     * @param string $login User login
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
        $res = array();
        $new = array();
        foreach ($ids as $id) {
            if (isset($this->_users[$id])) {
                $res[$id] = $this->_users[$id];
            } else {
                $new[] = (int)$id;
            }
        }

        if (count($new) > 0) {
            $db = Zend_Registry::get('db');

            $newusers = $db->fetchAll('SELECT lib_user_id, login, password, '
                      . 'email, registration_date, login_date, login_ip, '
                      . 'lib_writeboard_id '
                      . 'FROM lib_user '
                      . 'WHERE lib_user_id IN (' . implode(', ', $new). ')');

            foreach ($newusers as $row) {
                $userId = $row['lib_user_id'];
                $row['registration_date'] = App_Date::fromMysqlString($row['registration_date']);
                $row['login_date'] = App_Date::fromMysqlString($row['login_date']);
                $this->_users[$userId] = new App_User($row);
                $res[$userId] = $this->_users[$userId];
            }
        }

        return $res;
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
    }
}