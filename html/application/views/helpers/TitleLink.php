<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id:$
 */

/**
 * Helper for writing book title with authors
 */
class App_View_Helper_TitleLink extends Zend_View_Helper_Abstract 
{
    public function titleLink(App_Library_Title $title)
    {
        $authors = $title->getAuthorsIndex();
        
        $firstAuthorUrl = '';
        $authorLinks = array();
        foreach($authors as $authorUrl => $authorName) {
            if ($firstAuthorUrl === '') {
                $firstAuthorUrl = $authorUrl;
            }
            $authorLinks[] = '<a href="' . $this->view->url(array('author' => $authorUrl), 'libraryauthor') . '">'
                . $authorName .'</a>';
        }
        
        return implode(', ', $authorLinks) . ' - '
            . '<a href="' . $this->view->url(array('author' => $firstAuthorUrl,
                'title' => $title->getUrl()), 'librarytitle') . '">'
            . $title->getName() . '</a>';
    }
}