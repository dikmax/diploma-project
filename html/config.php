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
        'bookstitle' => array(
            'route' => 'books/:author/:title',
            'defaults' => array(
                'controller' => 'books',
                'action' => 'title',
                'author' => '',
                'title' => ''
            )
        ),
        'booksauthor' => array(
            'route' => 'books/:author',
            'defaults' => array(
                'controller' => 'books',
                'action' => 'author'
            ),
  
        ),
        'booksmain' => array(
            'type' => 'Zend_Controller_Router_Route_Static',
            'route' => 'books',
            'defaults' => array(
                'controller' => 'books',
                'action' => 'main'
            )
        ),
    )
);