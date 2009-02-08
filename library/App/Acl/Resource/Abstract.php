<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id$
 */

require_once 'App/Acl/Resource/Interface.php';

/**
 * Abstract class for defining application resources
 */
abstract class App_Acl_Resource_Abstract implements App_Acl_Resource_Interface
{
    /**
     * Registers resource in ACL system
     */
    public function registerResource()
    {
        Zend_Registry::get('acl')->add($this, $this->getResourceParentId());
    }

    /**
     * Unregisters resource from ACL system
     */
    public function unregisterResource()
    {
        Zend_Registry::get('acl')->remove($this);
    }

    /**
     * Returns resource parent (for registering)
     *
     * @return string
     */
    protected function getResourceParentId()
    {
        return null;
    }
}