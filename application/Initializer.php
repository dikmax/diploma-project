<?php
/**
 * Books social network
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id$
 */

/**
 *
 * Initializes configuration depending on the type of environment
 * (test, development, production, etc.)
 *
 * This can be used to configure environment variables, databases,
 * layouts, routers, helpers and more
 *
 */
class Initializer extends Zend_Controller_Plugin_Abstract
{
    /**
     * @var boolean Option for disable cacheing acl
     */
    protected $_aclNoCache = true;

    /**
     * @var Zend_Cache_Core
     */
    protected $_cache;

    /**
     * @var Zend_Config
     */
    protected static $_config;

    /**
     * @var string Current environment
     */
    protected $_env;

    /**
     * @var boolean Is current enviroment console
     */
    protected $_envConsole;

    /**
     * @var boolean Is current enviroment development
     */
    protected $_envDevelopment;

    /**
     * @var Zend_Controller_Front
     */
    protected $_front;

    /**
     * @var string Path to application root
     */
    protected $_root;

    /**
     * Application start microtime
     *
     * @var string
     */
    protected $_startMicrotime;

    /**
     * Constructor
     *
     * Initialize environment, root path, and configuration.
     *
     * @param  string $env
     * @param  string|null $root
     * @return void
     */
    public function __construct($env, $root = null)
    {
        $this->_startMicrotime = microtime(true);

        $this->_setEnv($env);
        if (null === $root) {
            $root = realpath(dirname(__FILE__) . '/../');
        }
        $this->_root = $root;

        $this->initPhpConfig();

        if (!$this->_envConsole) {
            $this->_front = Zend_Controller_Front::getInstance();
        }

        // set the test environment parameters
        if ($this->_envDevelopment) {
            // Enable all errors so we'll know when something goes wrong.
            error_reporting(E_ALL | E_STRICT);
            ini_set('display_startup_errors', 1);
            ini_set('display_errors', 1);

            if (!$this->_envConsole) {
                $this->_front->throwExceptions(true);

                $this->_aclNoCache = true;
            }
        }
    }

    /**
     * Initialize environment
     *
     * @param  string $env
     * @return void
     */
    protected function _setEnv($env)
    {
        $this->_env = $env;

        $this->_envConsole = strpos($env, 'console') !== false;
        $this->_envDevelopment = strpos($env, 'development') !== false;
    }

    /**
     * Initialize php
     *
     * @return void
     */
    public function initPhpConfig()
    {
        date_default_timezone_set('Europe/Minsk');
    }

    /**
     * Route startup
     *
     * @return void
     */
    public function routeStartup()
    {
        $this->initApplication();
    }

    /**
     * @see Zend_Controller_Plugin_Abstract::dispatchLoopShutdown()
     *
     */
    public function dispatchLoopShutdown ()
    {
        if ($this->_env == "development") {
            Zend_Wildfire_Plugin_FirePhp::send('Execution time: '
                . (string)round(microtime(true) - $this->_startMicrotime, 5) . ' sec');
            Zend_Wildfire_Plugin_FirePhp::send('Memory usage: '
                . memory_get_peak_usage(true) . ' bytes');
        }
    }

    public function initApplication()
    {
        $this->initEnviroment();
        $this->initSession();
        $this->initCache();
        $this->initDb();
        if (!$this->_envConsole) {
            $this->initAcl();
            $this->initHelpers();
            $this->initView();
            $this->initPlugins();
            $this->initRoutes();
            $this->initControllers();
        }
    }

    public function initEnviroment()
    {
        Zend_Locale::setDefault("ru_RU");

        // Config
        self::$_config = new Zend_Config(require 'config.php');
        Zend_Registry::set('config', self::$_config);
    }

    public function initSession()
    {
        Zend_Session::setOptions(self::$_config->session->toArray());
        Zend_Session::start();
        $session = new Zend_Session_Namespace();
        Zend_Registry::set('session', $session);

        if (!isset($session->initialized)) {
            Zend_Session::regenerateId();
            $session->initialized = true;
        }
    }

    /**
     * Initialize caching
     *
     * @return void
     */
    public function initCache()
    {
        $frontendOptions = array(
            'automatic_serialization' => true
        );
        $backendOptions  = array(
            'cache_dir' => $this->_root . '/cache/'
        );
        $this->_cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
        // TODO delete this registry key
        Zend_Registry::set('cache', $this->_cache);
    }

    /**
     * Initialize data bases
     *
     * @return void
     */
    public function initDb()
    {
        // App_Date speedup
        App_Date::setOptions(array('cache' => $this->_cache));

        // Connection to database
        $db = Zend_Db::factory(self::$_config->database);
        Zend_Db_Table_Abstract::setDefaultAdapter($db);
        Zend_Db_Table_Abstract::setDefaultMetadataCache($this->_cache);

        $db->getConnection()->exec("SET NAMES UTF8");

        Zend_Registry::set('db', $db);

        if ($this->_env == "development") {
            $profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
            $profiler->setEnabled(true);
            $db->setProfiler($profiler);
        }
    }

    /**
     * Initialize acl (Access Control List)
     *
     * @return void
     */
    public function initAcl()
    {
        if ($this->_aclNoCache || !($acl = $this->_cache->load('acl'))) {
            $acl = new Zend_Acl();

            // Creating roles
            $acl->addRole(new Zend_Acl_Role('guest'))
                ->addRole(new Zend_Acl_Role('user'), 'guest')
                ->addRole(new Zend_Acl_Role('admin'), 'user');

            // Creating resources
            $acl->add(new Zend_Acl_Resource('profile'))
                ->add(new Zend_Acl_Resource('mail'))
                ->add(new Zend_Acl_Resource('writeboard'))
                ->add(new Zend_Acl_Resource('blog'))
                ->add(new Zend_Acl_Resource('wiki'))
                ->add(new Zend_Acl_Resource('bookshelf'));

            // Creating permissions
            $acl->allow('guest', 'profile', 'view');
            $acl->allow('user', 'profile', 'edit', new App_Acl_Assert_CurrentUser());
            $acl->allow('user', 'mail', 'view', new App_Acl_Assert_CurrentUser());

            $acl->allow('guest', 'writeboard', 'view');
            $acl->allow('user', 'writeboard', 'add');

            $acl->allow('guest', 'bookshelf', 'view');

            $acl->allow('guest', 'blog', 'view');
            $acl->allow('user', 'blog', 'edit');

            $acl->allow('user', 'wiki', 'edit');
            $acl->allow('admin', 'wiki', 'edit');

            $acl->allow('user', 'wiki', 'rollback');

            // Set caching
            $this->_cache->save($acl, 'acl');
        }
        Zend_Registry::set('acl', $acl);

        // Detection of acl role
        $user = App_User_Factory::getSessionUser();
        if ($user === null) {
            $aclRole = 'guest';
        } else {
            $aclRole = $user;
            if (!($acl->hasRole($aclRole))) {
                $aclRole->registerRole();
            }
        }
        Zend_Registry::set('aclRole', $aclRole);
    }

    /**
     * Initialize action helpers
     *
     * @return void
     */
    public function initHelpers()
    {
        // register the default action helpers
        Zend_Controller_Action_HelperBroker::addPath('../application/default/helpers',
            'App_Controller_Action_Helper');
    }

    /**
     * Initialize view
     *
     * @return void
     */
    public function initView()
    {
        $view = new Zend_View();
        $view->addHelperPath($this->_root . '/application/default/views/helpers', 'App_View_Helper');
        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
        $viewRenderer->setView($view);
        $view->headTitle('Librarian')
            ->setSeparator(' / ');
        $doctypeHelper = new Zend_View_Helper_Doctype();
        $doctypeHelper->doctype('XHTML1_STRICT');
        $view->getPluginLoader('helper')->load('UsersList');
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);

        // Bootstrap layouts
        Zend_Layout::startMvc(array(
            'layoutPath' => $this->_root .  '/application/default/layouts',
            'layout' => 'default'
        ));

    }

    /**
     * Initialize plugins
     *
     * @return void
     */
    public function initPlugins()
    {

    }

    /**
     * Initialize routes
     *
     * @return void
     */
    public function initRoutes()
    {
        $router = $this->_front->getRouter();
        $router->removeDefaultRoutes();

        $routes = $this->_cache->load('routes');
        if ($routes === false) {
            $router->addRoute('default',
                new Zend_Controller_Router_Route(':action', array(
                    'controller' => 'index',
                    'action' => 'index'
                ))
            );

            $router->addRoute('auth',
                new Zend_Controller_Router_Route('auth/:action', array(
                    'controller' => 'auth',
                    'action' => 'index'
                ))
            );

            $router->addRoute('ajax',
                new Zend_Controller_Router_Route('ajax/:action', array(
                    'controller' => 'ajax'
                ))
            );

            $router->addRoute('writeboard',
                new Zend_Controller_Router_Route('writeboard/:action', array(
                    'controller' => 'writeboard',
                    'action' => 'index'
                ))
            );

            $router->addRoute('bookshelf',
                new Zend_Controller_Router_Route('bookshelf/:action', array(
                    'controller' => 'bookshelf',
                    'action' => 'show'
                ))
            );

            $router->addRoute('mail',
                new Zend_Controller_Router_Route('mail/:action/:param', array(
                    'controller' => 'mail',
                    'action' => 'active',
                    'param' => ''
                ))
            );

            $router->addRoute('friends',
                new Zend_Controller_Router_Route('friends/:action/:user', array(
                    'controller' => 'friends',
                    'action' => 'list',
                    'user' => ''
                ))
            );

            $router->addRoute('user',
                new Zend_Controller_Router_Route('user/:login/:action', array(
                    'controller' => 'user',
                    'action' => 'profile',
                    'login' => ''
                ))
            );

            $router->addRoute('library', new App_Controller_Router_Route_Library());

            $this->_cache->save($router->getRoutes(), 'routes', array(), 86400);
        } else {
            $router->addRoutes($routes);
        }
    }

    /**
     * Initialize Controller paths
     *
     * @return void
     */
    public function initControllers()
    {
        $this->_front->addControllerDirectory($this->_root . '/application/default/controllers', 'default');
    }

    /**
     * @return string
     */
    public function getRoot()
    {
        return $this->_root;
    }
}
?>
