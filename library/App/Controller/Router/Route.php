<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id$
 */

require_once 'Zend/Controller/Router/Route/Abstract.php';
require_once 'Zend/Config.php';

/**
 * Route for library
 *
 * @copyright  2008 Maxim Dikun
 * @version    Release: 0.0.1
 */
class App_Controller_Router_Route extends Zend_Controller_Router_Route_Abstract
{
    /**
     * Holds route values
     *
     * @var array
     */
    protected $_values;

    /**
     * Constructs new route object
     */
    public function __construct()
    {
    }

    /**
     * Instantiates route based on passed Zend_Config structure
     *
     * @param Zend_Config $config Configuration object
     */
    public static function getInstance(Zend_Config $config)
    {
        return new self();
    }

    /**
     * Matches a user submitted path with a previously defined route.
     * Assigns and returns an array of defaults on a successful match.
     *
     * @param Zend_Controller_Request_Http $request Path used to match against this routing map
     * @return array|false An array of assigned values or a false on a mismatch
     */
    public function match($request)
    {
        $path = trim($request->getRequestUri(), '/');

        $parts = array_map('urldecode', explode('/', $path));

        $head = array_shift($parts);
        $result = array('controller' => $head);

        switch ($head) {
            case 'auth':
            case 'writeboard':
                if (count($parts) > 1) {
                    return false;
                }
                $result['action'] = isset($parts[0]) ? $parts[0] : 'index';
                break;

            case 'ajax':
                if (count($parts) != 1) {
                    return false;
                }
                $result['action'] = $parts[0];
                break;

            case 'bookshelf':
                if (count($parts) > 1) {
                    return false;
                }
                $result['action'] = isset($parts[0]) ? $parts[0] : 'show';
                break;

            case 'friends':
                if (count($parts) > 2) {
                    return false;
                }
                $result['action'] = isset($parts[0]) ? $parts[0] : 'list';
                $result['user'] = isset($parts[1]) ? $parts[1] : '';
                break;

            case 'mail':
                if (count($parts) > 2) {
                    return false;
                }
                $result['action'] = isset($parts[0]) ? $parts[0] : 'active';
                $result['param'] = isset($parts[1]) ? $parts[1] : '';
                break;

            case 'library':
                if (count($parts) === 0) {
                    $result['action'] = 'index';
                    $result['extraparams'] = array();
                } else {
                    $actionFound = false;
                    foreach ($parts as $part) {
                        if ($actionFound) {
                            $result['extraparams'][] = $part;
                        } elseif ($part[0] == '~') {
                            $actionFound = true;
                            $result['action'] = mb_substr($part, 1);
                            $result['extraparams'] = array();
                        } elseif (!isset($result['author'])) {
                            $result['author'] = $part;
                        } elseif (!isset($result['title'])) {
                            $result['title'] = $part;
                        } else {
                            return false;
                        }
                    }
                }

                if (!isset($result['action'])) {
                    $result['action'] = 'overview';
                }
                break;

            case 'user':
                if (count($parts) > 2) {
                    return false;
                }
                $result['login'] = isset($parts[0]) ? $parts[0] : '';
                $result['action'] = isset($parts[1]) ? $parts[1] : 'profile';
                break;

            default: // Default path
                if (count($parts) > 0) {
                    return false;
                }
                $result = array(
                    'controller' => 'index',
                    'action' => $head
                );
                break;
        }

        $this->_values = $result;
        return $result;
    }

    /**
     * Assembles a URL path defined by this route
     *
     * @param array $data An array of name (or index) and value pairs used as parameters
     * @return string Route path with user submitted parameters
     */
    public function assemble($data = array(), $reset = false, $encode = false)
    {
        // Add values from initial state
        if (!$reset && is_array($this->_values)) {
            foreach ($this->_values as $key => $value) {
                if (!isset($data[$key])) {
                    $data[$key] = $value;
                }
            }
        }

        $result = array($data['controller']);

        switch ($data['controller']) {
            case 'index':
                $result = $data['action'] === 'index' ? array() : array($data['action']);
                break;

            case 'auth':
            case 'writeboard':
                if (isset($data['action']) && $data['action'] !== 'index') {
                    $result[] = $data['action'];
                }
                break;

            case 'ajax':
                if (!isset($data['action'])) {
                    require_once 'Zend/Controller/Router/Exception.php';
                    throw new Zend_Controller_Router_Exception('action is not specified');
                }
                $result[] = $data['action'];
                break;

            case 'bookshelf':
                if ($data['action'] !== 'show') {
                    $result[] = $data['action'];
                }
                break;

            case 'friends':
                if (isset($data['action']) && $data['action'] !== 'list') {
                    $result[] = $data['action'];
                    if (isset($data['user']) && $data['user'] !== '') {
                        $result[] = $data['user'];
                    }
                }
                break;

            case 'mail':
                if (isset($data['action']) && $data['action'] !== 'active') {
                    $result[] = $data['action'];
                }
                if (isset($data['param']) && $data['param'] !== '') {
                    if ($data['action'] === 'active') {
                        $result[] = 'active';
                    }
                    $result[] = $data['param'];
                }

                break;

            case 'user':
                if (!$data['login']) {
                    require_once 'Zend/Controller/Router/Exception.php';
                    throw new Zend_Controller_Router_Exception('login is not specified');
                }
                $result[] = $data['login'];
                if (isset($data['action']) && $data['action'] !== 'profile') {
                    $result[] = $data['action'];
                }
                break;

            case 'library':
                if (isset($data['author']) && $data['author'] != '') {
                    $result[] = urlencode($data['author']);

                    if (isset($data['title']) && $data['title'] != '') {
                        $result[] = urlencode($data['title']);
                    }
                }

                $addExtraParams = false;

                if (count($result) === 1) {
                    if (isset($data['action']) && $data['action'] !== 'index') {
                        $result[] = '~' . urlencode($data['action']);
                        $addExtraParams = true;
                    }
                } else {
                    if (isset($data['action']) && $data['action'] !== 'overview') {
                        $result[] = '~' . urlencode($data['action']);
                        $addExtraParams = true;
                    }
                }

                if ($addExtraParams && isset($data['extraparams']) && is_array($data['extraparams'])) {
                    foreach ($data['extraparams'] as $param) {
                        $result[] = urlencode($param);
                    }
                }
                break;

            default:
                require_once 'Zend/Controller/Router/Exception.php';
                throw new Zend_Controller_Router_Exception('Unknown controller ' . $data['controller']);
        }

        return '/' . implode('/', $result);
    }
}