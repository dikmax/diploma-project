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
 * Helper for writing book title with authors
 */
class App_View_Helper_TitleLink extends Zend_View_Helper_Abstract
{
    public function titleLink(App_Library_Title $title)
    {
        $authors = $title->getAuthorsIndex();

        $firstAuthorName = '';
        $authorLinks = array();
        foreach($authors as $authorName) {
            if ($firstAuthorName === '') {
                $firstAuthorName = $authorName;
            }
            $authorLinks[] = '<a href="' . $this->view->url(array('author' => $authorName), 'library') . '">'
                . $authorName .'</a>';
        }

        return implode(', ', $authorLinks) . ' - '
            . '<a href="' . $this->view->url(
                array('author' => $firstAuthorName,
                      'title' => $title->getName()
                ), 'library') . '">'
            . $title->getName() . '</a>';
    }
}