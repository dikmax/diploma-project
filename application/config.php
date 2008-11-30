<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id$
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
    'session' => array(
        'save_path' => '/home/dikmax/workspace/librarian/sessions',
        'use_only_cookies' => true,
        'remember_me_seconds' => 864000
    ),
    'mainChannelId' => 1
);