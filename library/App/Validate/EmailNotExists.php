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
 * Email not exists validator
 */
class App_Validate_EmailNotExists extends Zend_Validate_Abstract
{
    const EXISTS = 'exists';

    protected $_messageTemplates = array(
        self::EXISTS => 'Данный email уже используется'
    );

    public function isValid($value)
    {
        $value = (string) $value;
        $this->_setValue($value);

        try {
            App_User_Factory::getInstance()->getUserByEmail($value);
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
