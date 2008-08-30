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
        $authorUrl = $this->getRequest()->getParam('author');
        $author = App_Library_Author::getAuthorByUrl($authorUrl);
        
        if ($author === false) {
            $this->view->headTitle($authorUrl);
            $this->view->author = $authorUrl;
            $this->_helper->viewRenderer->setScriptAction('author-not-found');
            return;
        }
        $this->view->headTitle($author->getName());
        $this->view->author = $author;
        $this->view->frontImage = $author->getFrontImage();
    }
    
    /**
     * Book title page
     */
    public function titleAction()
    {
        $authorUrl = $this->getRequest()->getParam('author');
        $author = App_Library_Author::getAuthorByUrl($authorUrl);
        if ($author === false) {
            $this->view->headTitle($authorUrl);
            $this->view->author = $authorUrl;
            $this->_helper->viewRenderer->setScriptAction('author-not-found');
            return;
        }
        $this->view->headTitle($author->getName());
        $this->view->author = $author;
        
        $titleUrl = $this->getRequest()->getParam('title');
        $title = App_Library_Title::getTitleByUrl($titleUrl, $author);
        if ($title === false) {
            $this->view->headTitle($titleUrl);
            $this->view->title = $titleUrl;
            $this->_helper->viewRenderer->setScriptAction('title-not-found');
            return;
        }
        $this->view->headTitle($title->getName());
        $this->view->title = $title;
    }
}