<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id:$
 */

require_once 'App/Console/Controller/Action/Abstract.php';

/**
 * Console controller
 *
 * @copyright  2008 Maxim Dikun
 * @version    Release: 0.0.1
 */
class App_Console_Controller
{
    /**
     * @var string
     */
    protected $_controllersFolder;

    /**
     * @var string
     */
    protected $_controllersClassPrefix;

    /**
     * @var array All controllers found in specified folder
     */
    protected $_registeredControllers;

    /**
     * Constructs console controller
     *
     * @param array $options
     */
    public function __construct ($controllersDirectory = '', $controllersClassPrefix = '')
    {
        $this->_controllersFolder = $controllersDirectory;
        $this->_controllersClassPrefix = $controllersClassPrefix;

        if (strrchr($this->_controllersClassPrefix, '_') !== '_' && $this->_controllersClassPrefix !== '') {
            $this->_controllersClassPrefix .= '_';
        }

        $this->_registeredControllers = null;
    }

    /**
     * Scan folder and registers all controllers
     */
    protected function scanControllersFolder()
    {
        $this->_registeredControllers = array();

        foreach (new DirectoryIterator($this->_controllersFolder) as $value) {
            if (!$value->isDir()) {
                $filename = $value->getFilename();
                if (preg_match("/Controller\\.php$/", $filename) !== false) {
                    $className = $this->_controllersClassPrefix
                               . preg_replace("/\\.php/", '', $filename);
                    Zend_Loader::loadClass($className, $this->_controllersFolder);

                    $reflectionClass = new ReflectionClass($className);

                    if (!$reflectionClass->isSubclassOf(
                            new ReflectionClass('App_Console_Controller_Action_Abstract')
                        ))
                    {
                        require_once 'App/Console/Controller/Exception.php';
                        throw new App_Console_Controller_Exception($className
                            . ' must be subclass of App_Console_Controller_Action_Abstract');
                    }

                    $this->_registeredControllers[$className] = array(
                        'long' => $reflectionClass->getMethod('getLongActionName')->invoke(null),
                        'short' => $reflectionClass->getMethod('getShortActionName')->invoke(null),
                        'description' => $reflectionClass->getMethod('getDescription')->invoke(null)
                    );
                }
            }
        }
    }

    /**
     * Returns options list for Zend_Console_Getopt
     *
     * @return array
     */
    public function getOptionsList()
    {
        $registeredControllers = $this->getRegisteredControllers();

        $result = array();
        foreach ($registeredControllers as $controller => $params) {
            if (!$controller['long']) {
                require_once 'App/Console/Controller/Exception.php';
                throw new App_Console_Controller_Exception('Long description for '
                    . $controller . ' must be defined.');
            }

            if (!$params['short']) {
                $key = $params['long'];
            } else {
                $key = $params['long'] . '|' . $params['short'];
            }

            $result[$key] = $params['description'];
        }
        return $result;
    }

    /**
     * Executes action by long name
     *
     * @param string $longAction
     */
    public function executeAction($longAction)
    {
        $registeredControllers = $this->getRegisteredControllers();

        $found = false;
        foreach ($registeredControllers as $controller => $params) {
            if ($params['long'] === $longAction) {
                $found = true;
                $controllerClass = new ReflectionClass($controller);
                $controllerObject = $controllerClass->newInstance();
                $controllerObject->init();
                $controllerObject->process();
                break;
            }
        }

        if (!$found) {
            require_once 'App/Console/Controller/Exception.php';
            throw new App_Console_Controller_Exception('Action ' . $longAction
                . ' isn\'t registered');
        }
    }

    /**
     * Returns folder with controllers
     *
     * @return string
     */
    public function getControllersFolder()
    {
        return $this->_controllersFolder;
    }

    /**
     * Sets folder with controllers
     *
     * @param string $controllersFolder
     */
    public function setControllersFolder($controllersFolder)
    {
        $this->_controllersFolder = $controllersFolder;
        $this->_registeredControllers = null;
    }

    /**
     * Returs list of registered controllers
     *
     * @return array
     */
    public function getRegisteredControllers()
    {
        if ($this->_registeredControllers === null) {
            $this->scanControllersFolder();
        }
        return $this->_registeredControllers;
    }
}
