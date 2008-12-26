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
 * User not exists validator
 */
class App_Validate_UserNotExists extends Zend_Validate_Abstract
{
    const EXISTS = 'exists';

    protected $_messageTemplates = array(
        self::EXISTS => 'Пользователь с таким логином уже существует'
    );

    public function isValid($value)
    {
        $value = (string) $value;
        $this->_setValue($value);

        try {
            App_User_Factory::getInstance()->getUserByLogin($value);
            $exists = true;
        } catch (App_User_Exception $e) {
            $exists = false;
        }

        if ($exists) {
            $this->_error(self::EXISTS);
            return false;
        }

        return true;
    }
}
