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
 * Errors controlles
 */
class ErrorController extends Zend_Controller_Action
{
    /**
     * Initialize error controller
     */
    public function init()
    {
        $this->view->getHelper('topMenu')->clear();
    }

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
        // Send "404 Not Found" header
        $this->getResponse()->setHttpResponseCode(404);

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
        // Send "404 Not Found" header
        $this->getResponse()->setHttpResponseCode(404);

        $titleUrl = $this->getRequest()->getParam('title');

        $this->view->headTitle($titleUrl);
        $this->view->title = $titleUrl;
        $this->_helper->viewRenderer->setScriptAction('title-not-found');
    }

    /**
     * Shows "User not found" page
     */
    public function userNotFoundAction()
    {
        // Send "404 Not Found" header
        $this->getResponse()->setHttpResponseCode(404);

        $login = $this->getRequest()->getParam('login');

        $this->view->headTitle($login);
        $this->view->login = $login;
    }

    /**
     * Shows default not allowed message
     */
    public function notAllowedAction()
    {
        $this->getResponse()->setHttpResponseCode(403);

        $this->view->headTitle('403 Нет прав доступа',
            Zend_View_Helper_Placeholder_Container_Abstract::SET);
    }
}