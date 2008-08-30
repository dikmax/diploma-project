<?php
/** Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';

/**
 * Errors controlles
 */
class ErrorController extends Zend_Controller_Action
{
    /**
     * Generic error page
     */
    public function errorAction()
    {
    }
    
    /**
     * Shows "Author not found" page
     */
    public function authorNotFoundAction()
    {
        $authorUrl = $this->getRequest()->getParam('author');
        
        $this->view->headTitle($authorUrl);
        $this->view->author = $authorUrl;
        $this->_helper->viewRenderer->setScriptAction('author-not-found');
    }
    
    /**
     * Shows "Title not found" page
     */
    public function titleNotFoundAction()
    {
        $titleUrl = $this->getRequest()->getParam('title');
        
        $this->view->headTitle($titleUrl);
        $this->view->title = $titleUrl;
        $this->_helper->viewRenderer->setScriptAction('title-not-found');
    }
}