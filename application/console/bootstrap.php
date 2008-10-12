<?php
// Setup enviroment for commandline processing

set_include_path('.' . PATH_SEPARATOR . '../../library' . PATH_SEPARATOR . '../../application/default/models/' . PATH_SEPARATOR . get_include_path());

// Set up autoload.
require_once "Zend/Loader.php"; 
Zend_Loader::registerAutoload(); 

require_once '../Initializer.php';
 
// Change to 'production' parameter under production environemtn
$init = new Initializer('console development');    

$init->initApplication();
