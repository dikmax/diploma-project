<?php
/**
 * Books social network
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id:$
 */

// Setup environment for command-line processing

$paths = array(
    dirname(__FILE__),
    realpath(dirname(__FILE__) . '/../../library')
);

set_include_path(implode(PATH_SEPARATOR, $paths));

// Set up autoload.
require_once "Zend/Loader.php";

Zend_Loader::registerAutoload();

require_once dirname(__FILE__) . '/../Initializer.php';

// Change to 'production' parameter under production environment
$init = new Initializer('console development');

$init->initApplication();