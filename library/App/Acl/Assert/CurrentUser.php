<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id$
 */

require_once 'Zend/Acl/Assert/Interface.php';

/**
 * Class for checking is current page belongs to current user
 */
class App_Acl_Assert_CurrentUser implements Zend_Acl_Assert_Interface
{
    /**
     * Assertion
     *
     * @param Zend_Acl $acl
     * @param Zend_Acl_Role_Interface $role
     * @param Zend_Acl_Resource_Interface $resource
     * @param string $privilege
     * @return boolean
     */
    public function assert(Zend_Acl $acl, Zend_Acl_Role_Interface $role = null,
                           Zend_Acl_Resource_Interface $resource = null, $privilege = null)
    {
        $pageOwner = Zend_Controller_Front::getInstance()->getRequest()->getParam('login');
        $user = App_User_Factory::getSessionUser();
        if ($user !== null) {
            if ($user->getLogin() == $pageOwner) {
                return true;
            }
        }
        return false;
    }
}
