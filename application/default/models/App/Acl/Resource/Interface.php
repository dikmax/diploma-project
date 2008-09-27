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
 * Interface for defining application resources
 */
interface App_Acl_Resource_Interface extends Zend_Acl_Resource_Interface
{
    /**
     * Registers resource in ACL system
     */
    public function registerResource();
    
    /**
     * Unregisters resource from ACL system
     */
    public function unregisterResource();
}