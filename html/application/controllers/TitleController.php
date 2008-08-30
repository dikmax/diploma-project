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
 * Title controller
 */
class TitleController extends Zend_Controller_Action
{
    /**
     * Book title page
     */
    public function showAction()
    {
        try {
            $authorUrl = $this->getRequest()->getParam('author');
            $author = App_Library::getAuthorByUrl($authorUrl);
            $this->view->headTitle($author->getName());
            $this->view->author = $author;
            
            $titleUrl = $this->getRequest()->getParam('title');
            $title = App_Library::getTitleByUrl($author, $titleUrl);
            $this->view->headTitle($title->getName());
            $this->view->title = $title;
        } catch (App_Library_Exception_AuthorNotFound $e) {
            $this->_forward('author-not-found', 'error', null, array(
                'author' => $authorUrl
            ));
        } catch (App_Library_Exception_TitleNotFound $e) {
            $this->_forward('title-not-found', 'error', null, array(
                'author' => $authorUrl,
                'title' => $titleUrl
            ));
        }
    }
}