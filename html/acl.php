<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id:$
 */

define('ACL_CACHE_ID', 'acl');

if (ACL_NO_CACHE || !($acl = $cache->load(ACL_CACHE_ID))) {
    $acl = new Zend_Acl();

    // Creating roles
    $acl->addRole(new Zend_Acl_Role('guest'))
        ->addRole(new Zend_Acl_Role('user'), 'guest')
        ->addRole(new Zend_Acl_Role('admin'), 'user');
    
    // Creating resources
    $acl->add(new Zend_Acl_Resource('profile'))
        ->add(new Zend_Acl_Resource('writeboard'))
        ->add(new Zend_Acl_Resource('blog'))
        ->add(new Zend_Acl_Resource('wiki'));
    
    // Creating permissions
    $acl->allow('guest', 'profile', 'view');
    $acl->allow('user', 'profile', 'edit', new App_Acl_Assert_CurrentUser());
    
    $acl->allow('guest', 'writeboard', 'view');
    $acl->allow('user', 'writeboard', 'add');
    
    $acl->allow('guest', 'blog', 'view');
    $acl->allow('user', 'blog', 'edit');
    
    $acl->allow('user', 'wiki', 'edit');
    $acl->allow('admin', 'wiki', 'edit');
    
    // Set caching
    $cache->save($acl, ACL_CACHE_ID);
}
Zend_Registry::set('acl', $acl);

// Detection of acl role
$user = App_User_Factory::getSessionUser();
if ($user === false) {
    $aclRole = 'guest';
} else {
    $aclRole = $user;
    if (!($acl->hasRole($aclRole))) {
        $aclRole->registerRole();
    }
}
Zend_Registry::set('aclRole', $aclRole);

?>