<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id:$
 */

//phpinfo(); die;

define('HTTP_PATH', dirname(__FILE__));
define('DEBUG', true);
define('SHOW_QUERIES', false);
define('ACL_NO_CACHE', false);

if (DEBUG) {
    error_reporting(E_ALL | E_STRICT);
    ini_set('display_startup_errors', true);
    ini_set('display_errors', true);
}

/*set_include_path('./library' . PATH_SEPARATOR . './application/models'
    . PATH_SEPARATOR . get_include_path());*/
set_include_path(HTTP_PATH . "/library" . PATH_SEPARATOR
    . HTTP_PATH . '/application/models' . PATH_SEPARATOR
    . get_include_path());

date_default_timezone_set('Europe/Minsk');

require_once "Zend/Loader.php";
Zend_Loader::registerAutoload();

Zend_Session::start();

Zend_Locale::setDefault("ru_RU");

// Config
$config = new Zend_Config(require 'config.php');
Zend_Registry::set('config', $config);

// Setup database caching
$frontendOptions = array(
    'automatic_serialization' => true
);
$backendOptions  = array(
    'cache_dir' => './cache/'
);
$cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);

// App_Date speedup
App_Date::setOptions(array('cache' => $cache));

// Connection to database
$db = Zend_Db::factory($config->database);
Zend_Db_Table_Abstract::setDefaultAdapter($db);
Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);

$db->getConnection()->exec("SET NAMES UTF8");

Zend_Registry::set('db', $db);

if (DEBUG) {
    $profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
    $profiler->setEnabled(true);
    $db->setProfiler($profiler);
}
// ACL
require_once "./acl.php";

// Registering and auto initing some action helpers
Zend_Controller_Action_HelperBroker::addPath('./application/controllers/helpers',
    'App_Controller_Action_Helper');
// TODO remove or uncomment
//Zend_Controller_Action_HelperBroker::getStaticHelper("Breadcrumbs");

// Set default options for views
$view = new Zend_View();
$view->addHelperPath(HTTP_PATH . '/application/views/helpers', 'App_View_Helper');
$viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
$viewRenderer->setView($view);
$view->headTitle('Librarian')
     ->setSeparator(' / ');
$doctypeHelper = new Zend_View_Helper_Doctype();
$doctypeHelper->doctype('XHTML1_STRICT');
Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);

Zend_Layout::startMvc(array('layoutPath' => HTTP_PATH . '/application/views/layouts',
                            'layout' => 'default'));
// Request dispatching
$frontController = Zend_Controller_Front::getInstance();
$frontController->setControllerDirectory(array(
    'default' => HTTP_PATH . '/application/controllers'
));

if (DEBUG) {
    $frontController->throwExceptions(true);
}

// Configuring router
$frontController->getRouter()->addConfig($config, 'routes');

$frontController->dispatch();

// Show debug information at the end of the page
if (DEBUG) {
    $profiler = $db->getProfiler();
    echo '<hr /><div align="right">Запросов к БД: ' . $profiler->getTotalNumQueries()
        . '<br />' . 'Общее время запросов: ' . $profiler->getTotalElapsedSecs() . '</div>';
    
    if (SHOW_QUERIES) {
        echo "Запросы:<br />";
        foreach ($profiler->getQueryProfiles() as $query) {
            //echo $db->quoteInto($query->getQuery(), $query->getQueryParams()) . "<br />";
            echo $query->getQuery() . "<br />";
            var_dump ($query->getQueryParams());
        }
    }
}

// Closing connections
$db->closeConnection();