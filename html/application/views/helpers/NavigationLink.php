<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id:$
 */

/**
 * Helper for writing navigation links in left panel
 */
class App_View_Helper_NavigationLink extends Zend_View_Helper_Abstract
{
    public function navigationLink($resource, $name, $url)
    {
        if ($resource) {
            $acl = Zend_Registry::get('acl');
            $aclRole = Zend_Registry::get('aclRole');
            if (!$acl->isAllowed($aclRole, $resource, 'view')) {
                return '';
            }
        }
        return '<a class="navigation-link" href="' . $url . '">'
            . $name
            . '</a>';
    }
}
