<?php
/**
 * Books social network
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id$
 */

require_once 'App/User/Factory.php';
require_once 'Zend/Registry.php';

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
     * View
     *
     * @var App_View
     */
    protected $_view;

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
        Zend_Registry::set('rootPath', $root);
        Zend_Registry::set('tempPath', $root . '/tmp');
        if (!$this->_envConsole) {
            Zend_Registry::set('publicPath', $root . '/public');
        }

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
        if ($this->_envDevelopment) {
            require_once 'Zend/Wildfire/Plugin/FirePhp.php';
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
        if ($this->_envConsole) {
            // Set user with id 1 as session user
            $user = App_User_Factory::getInstance()->getUser(1);
            App_User_Factory::setSessionUser($user);
        }
        $this->initAcl();
        if (!$this->_envConsole) {
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
        require_once 'Zend/Config.php';
        self::$_config = new Zend_Config(require 'config.php');
        Zend_Registry::set('config', self::$_config);
    }

    public function initSession()
    {
        require_once 'Zend/Session.php';
        require_once 'Zend/Session/Namespace.php';
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
        if ($this->_envDevelopment) {
            $backendOptions['cache_file_umask'] = 0666;
        }

        require_once 'Zend/Cache/Backend/File.php';
        require_once 'Zend/Cache/Core.php';
        require_once 'Zend/Cache.php';
        $this->_cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
        // TODO delete this registry key
        Zend_Registry::set('cache', $this->_cache);

        // App_Date speedup
        require_once 'App/Date.php';
        App_Date::setOptions(array('cache' => $this->_cache));
    }

    /**
     * Initialize data bases
     *
     * @return void
     */
    public function initDb()
    {
        // Connection to database
        require_once 'Zend/Db.php';
        require_once 'Zend/Db/Adapter/Pdo/Mysql.php';
        require_once 'Zend/Db/Table/Abstract.php';
        $db = Zend_Db::factory(self::$_config->database);
        Zend_Db_Table_Abstract::setDefaultAdapter($db);
        Zend_Db_Table_Abstract::setDefaultMetadataCache($this->_cache);

        $db->getConnection()->exec("SET NAMES UTF8");

        Zend_Registry::set('db', $db);

        if ($this->_envDevelopment && !$this->_envConsole) {
            require_once 'Zend/Db/Profiler/Firebug.php';
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
        require_once 'Zend/Acl.php';
        require_once 'Zend/Acl/Role.php';
        require_once 'Zend/Acl/Resource.php';
        require_once 'App/Acl/Assert/CurrentUser.php';

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
        require_once 'Zend/Controller/Action/HelperBroker.php';
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
        require_once 'App/View.php';
        $this->_view = new App_View();
        $this->_view->addHelperPath($this->_root . '/application/default/views/helpers', 'App_View_Helper');

        require_once 'Zend/Controller/Action/Helper/ViewRenderer.php';
        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
        $viewRenderer->setView($this->_view);
        $this->_view->headTitle('Librarian')
             ->setSeparator(' / ');

        require_once 'Zend/View/Helper/Doctype.php';
        $doctypeHelper = new Zend_View_Helper_Doctype();
        $doctypeHelper->doctype('XHTML1_STRICT');
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);

        require_once 'ZendX/JQuery.php';
        ZendX_JQuery::enableView($this->_view);
        $jQuery = $this->_view->jQuery();

        $jQuery->setLocalPath('/scripts/jquery.js')
            ->setUiLocalPath('/scripts/jquery.ui.js')
            ->uiDisable()
            ->addJavascriptFile('/scripts/init.js');

        // Bootstrap layouts
        require_once 'Zend/Layout.php';
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

        require_once 'App/Controller/Router/Route.php';
        $route = new App_Controller_Router_Route();
        $this->_view->setRoute($route);

        $router->addRoute('default', $route);
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
