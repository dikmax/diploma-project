<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id$
 */

/**
 * Helper for linking inside library
 */
class App_View_Helper_LibraryUrl extends Zend_View_Helper_Abstract
{
    /**
     * Return link inside library
     *
     * @param string $action Controller action name
     * @param string $author override author name
     * @param string $title override title name
     * @param array $extraparams url extra params
     *
     * @return string
     */
    public function libraryUrl($action = null, $author = null, $title = null, $extraparams = null)
    {
        $params = array();

        if ($action !== null) {
            $params['action'] = $action;
        }

        if ($author === null) {
            if ($this->view->authorName != null) {
                $params['author'] = $this->view->authorName;
            }
        } else if ($author !== false) {
            $params['author'] = $author;
        }

        if ($title === null) {
            if ($this->view->titleName != null) {
                $params['title'] = $this->view->titleName;
            }
        } else if ($title !== false) {
            $params['title'] = $title;
        }

        if ($extraparams !== null) {
            $params['extraparams'] = $extraparams;
        }
        return $this->view->url($params, 'library');
    }
}