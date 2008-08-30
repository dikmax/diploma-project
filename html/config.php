<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id:$
 */

return array(
    'database' => array(
        'adapter' => 'pdo_mysql',
        'params' => array(
            'host'     => 'localhost',
            'username' => 'root',
            'password' => '',
            'dbname'   => 'librarian'
        )
    ),
    'mainChannelId' => 1,
    'routes' => array(
        'user' => array(
            'route' => 'user/:login/:action',
            'defaults' => array(
                'controller' => 'user',
                'action' => 'profile',
                'login' => ''
            )
        ),
        'librarytitle' => array(
            'route' => 'library/:author/:title',
            'defaults' => array(
                'controller' => 'title',
                'action' => 'show'
            )
        ),
        'libraryauthor' => array(
            'route' => 'library/:author',
            'defaults' => array(
                'controller' => 'author',
                'action' => 'show'
            ),
  
        ),
        'librarymain' => array(
            'type' => 'Zend_Controller_Router_Route_Static',
            'route' => 'library',
            'defaults' => array(
                'controller' => 'index',
                'action' => 'library'
            )
        ),
    )
);