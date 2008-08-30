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
 * Books controller
 */
class BooksController extends Zend_Controller_Action
{
    /**
     * Main library page
     */
    public function mainAction()
    {
        
    }
    
    /**
     * Author's page
     */
    public function authorAction()
    {
        try {
            $authorUrl = $this->getRequest()->getParam('author');
            $author = App_Library::getAuthorByUrl($authorUrl);
            $this->view->headTitle($author->getName());
            $this->view->author = $author;
            $this->view->frontImage = $author->getFrontImage();
        } catch (App_Library_Exception_AuthorNotFound $e) {
            $this->view->headTitle($authorUrl);
            $this->view->author = $authorUrl;
            $this->_helper->viewRenderer->setScriptAction('author-not-found');
        }
    }
    
    /**
     * Book title page
     */
    public function titleAction()
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
            $this->view->headTitle($authorUrl);
            $this->view->author = $authorUrl;
            $this->_helper->viewRenderer->setScriptAction('author-not-found');
        } catch (App_Library_Exception_TitleNotFound $e) {
            $this->view->headTitle($titleUrl);
            $this->view->title = $titleUrl;
            $this->_helper->viewRenderer->setScriptAction('title-not-found');
        }
    }
}