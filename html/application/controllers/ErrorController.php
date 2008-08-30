<?php
/** Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';

class ErrorController extends Zend_Controller_Action
{
    public function errorAction()
    {
    }
    
    public function authorNotFoundAction()
    {
        $authorUrl = $this->getRequest()->getParam('author');
        
        $this->view->headTitle($authorUrl);
        $this->view->author = $authorUrl;
        $this->_helper->viewRenderer->setScriptAction('author-not-found');
    }
    
    public function titleNotFoundAction()
    {
        $titleUrl = $this->getRequest()->getParam('title');
        
        $this->view->headTitle($titleUrl);
        $this->view->title = $titleUrl;
        $this->_helper->viewRenderer->setScriptAction('title-not-found');
    }
}