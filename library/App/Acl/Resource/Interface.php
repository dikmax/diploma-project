<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id$
 */

require_once 'Zend/Acl/Resource/Interface.php';

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