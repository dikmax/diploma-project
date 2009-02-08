<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id$
 */

require 'Zend/Validate/Abstract.php';

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

        if (App_User_Factory::getInstance()->getUserByLogin($value) !== null) {
            $this->_error(self::EXISTS);
            return false;
        }

        return true;
    }
}
