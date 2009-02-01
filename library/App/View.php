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
 * View with integrated helpers
 */
class App_View extends Zend_View
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
            if ($this->authorName != null) {
                $params['author'] = $this->authorName;
            }
        } else if ($author !== false) {
            $params['author'] = $author;
        }

        if ($title === null) {
            if ($this->titleName != null) {
                $params['title'] = $this->titleName;
            }
        } else if ($title !== false) {
            $params['title'] = $title;
        }

        if ($extraparams !== null) {
            $params['extraparams'] = $extraparams;
        }
        return $this->url($params, 'library');
    }

    /**
     * Helper for writing navigation links in left panel
     *
     * @param string $resource
     * @param string $name
     * @param string $url
     *
     * @return string
     */
    public function navigationLink($resource, $name, $url)
    {
        if ($resource) {
            $acl = Zend_Registry::get('acl');
            $aclRole = Zend_Registry::get('aclRole');
            if (!$acl->isAllowed($aclRole, $resource, 'view')) {
                return '';
            }
        }
        return '<a class="navigation-link" href="' . $url . '">'
            . $name
            . '</a>';
    }

    /**
     * Helper for writing book title with authors
     *
     * @var App_Library_Title $title
     *
     * @return string
     */
    public function titleLink(App_Library_Title $title)
    {
        $authors = $title->getAuthorsIndex();

        $firstAuthorName = '';
        $authorLinks = array();
        foreach($authors as $authorName) {
            if ($firstAuthorName === '') {
                $firstAuthorName = $authorName;
            }
            // TODO Link generation is very slow
            $authorLinks[] = '<a href="' . $this->libraryUrl(null, $authorName) . '">'
                . $authorName .'</a>';
        }

        return implode(', ', $authorLinks) . ' - '
            . '<a href="' . $this->libraryUrl(null, $firstAuthorName, $title->getName()) . '">'
            . $title->getName() . '</a>';
    }
}
