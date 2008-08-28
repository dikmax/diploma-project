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
        $author = $this->getRequest()->getParam('author');
        $this->view->headTitle($author);
        
        $title = $this->getRequest()->getParam('title');
        $this->view->headTitle($title);
        
        $this->view->author = $author;
        $this->view->title = $title;
    }
}