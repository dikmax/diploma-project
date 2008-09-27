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
        'default' => array(
            'route' => ':action',
            'defaults' => array(
                'controller' => 'index',
                'action' => 'index'
            )
        ),
        'auth' => array(
            'route' => 'auth/:action',
            'defaults' => array(
                'controller' => 'auth',
                'action' => 'index'
            )
        ),
        'ajax' => array(
            'route' => 'ajax/:action',
            'defaults' => array(
                'controller' => 'ajax'
            )
        ),
        'writeboard' => array(
            'route' => 'writeboard/:action',
            'defaults' => array(
                'controller' => 'writeboard',
                'action' => 'index'
            )
        ),
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
            ),
            'reqs' => array(
                'title' => '^[^~].*'
            )
        ),
        'librarytitleaction' => array(
            'type' => 'Zend_Controller_Router_Route_Regex',
            'route' => 'library/([^/]*)/([^/]*)/~([^/]*)',
            'defaults' => array(
                'controller' => 'title',
            ),
            'map' => array(
                1 => 'author',
                2 => 'title',
                3 => 'action'
            ),
            'reverse' => 'library/%s/%s/~%s'
        ),
        'libraryauthor' => array(
            'route' => 'library/:author',
            'defaults' => array(
                'controller' => 'author',
                'action' => 'show'
            ),
        ),
        'libraryauthoraction' => array(
            'type' => 'Zend_Controller_Router_Route_Regex',
            'route' => 'library/([^/]*)/~([^/]*)',
            'defaults' => array(
                'controller' => 'author'
            ),
            'map' => array(
                1 => 'author',
                2 => 'action'
            ),
            'reverse' => 'library/%s/~%s'
        )
    )
);