<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id: Revision.php 70 2008-12-10 19:40:35Z dikmax $
 */

/**
 * Route for library
 *
 * @copyright  2008 Maxim Dikun
 * @version    Release: 0.0.1
 */
class App_Controller_Router_Route_Library extends Zend_Controller_Router_Route_Abstract
{
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

        if (strstr($path, 'library') === false) {
            return false;
        }

        $path = substr($path, 8);

        $result = array('controller' => 'library');

        if ($path == '') {
            $result['action'] = 'index';
            $result['extraparams'] = array();
        } else {
            $parts = array_map('urldecode', explode('/', $path));

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
        $result = array('library');

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

        return implode('/', $result);
    }
}