<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id$
 */

require_once 'Zend/Validate/Abstract.php';

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

        if (App_User_Factory::getInstance()->getUserByEmail($value) !== null) {
            $this->_error(self::EXISTS);
            return false;
        }

        return true;
    }
}
