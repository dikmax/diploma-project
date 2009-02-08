<?php
$paths = array(
    realpath(dirname(__FILE__) . '/../library')
);

set_include_path(implode(PATH_SEPARATOR, $paths));

// Set up autoload.
require_once "Zend/Loader.php";
Zend_Loader::registerAutoload();

// Set plugin caching
$classFileIncCache = dirname(__FILE__) .  '/../cache/pluginLoaderCache.php';
if (file_exists($classFileIncCache)) {
    include_once $classFileIncCache;
}

require_once 'Zend/Loader/PluginLoader.php';
Zend_Loader_PluginLoader::setIncludeFileCache($classFileIncCache);

require_once 'Initializer.php';
require_once 'Zend/Controller/Front.php';

// Prepare the front controller.
$frontController = Zend_Controller_Front::getInstance();

// Change to 'production' parameter under production enviroment
$frontController->registerPlugin(new Initializer('development'));

// Dispatch the request using the front controller.
$frontController->dispatch();
