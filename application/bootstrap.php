<?php
$paths = array(
    realpath(dirname(__FILE__) . '/../library'),
    realpath(dirname(__FILE__) . '/../application/default/models/')
);

set_include_path(implode(PATH_SEPARATOR, $paths));

// Set up autoload.
require_once "Zend/Loader.php";
Zend_Loader::registerAutoload();

require_once 'Initializer.php';

// Prepare the front controller.
$frontController = Zend_Controller_Front::getInstance();

// Change to 'production' parameter under production enviroment
$frontController->registerPlugin(new Initializer('development'));

// Dispatch the request using the front controller.
$frontController->dispatch();
