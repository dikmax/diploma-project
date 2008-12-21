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
 * Helper for checking acl isAllowed
 */
class App_View_Helper_IsAllowed extends Zend_View_Helper_Abstract
{
    /**
     * Check acl
     */
    public function isAllowed($resource, $previlege)
    {
        $acl = Zend_Registry::get('acl');
        $aclRole = Zend_Registry::get('aclRole');
        return $acl->isAllowed($aclRole, $resource, $previlege);
    }
}
