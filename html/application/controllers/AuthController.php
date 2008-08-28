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
 * Authentication controller.
 *
 * @copyright  2008 Maxim Dikun
 * @version    Release: 0.0.1
 */
class AuthController extends Zend_Controller_Action
{
    /**
     * Logging in
     */
    public function loginAction()
    {
        if ($this->getRequest()->getParam('login') !== null &&
            $this->getRequest()->getParam('password') !== null) {
            $db = Zend_Registry::get('db');
            $auth = new Zend_Auth_Adapter_DbTable($db, 'lib_user', 'login',
                'password', 'MD5(?)');
            $auth->setIdentity($this->getRequest()->getParam('login'))
                ->setCredential($this->getRequest()->getParam('password'));
            $result = $auth->authenticate();
            
            if ($result->isValid()) {
                $user = App_User_Factory::getInstance()->getUserByLogin($this->getRequest()->getParam('login'));
                App_User_Factory::setSessionUser($user);
                $this->_helper->redirector->gotoRouteAndExit(
                    array('controller' => 'user',
                          'action' => 'profile',
                          'login' => $this->getRequest()->getParam('login')),
                    'user'
                );
            }
            // Login is invalid
            // TODO Output localized string
            $messages = $result->getMessages();
            $this->view->reason = $messages[0];
            $this->_helper->layout->setLayout('full-width');
        }
    }
    
    /**
     * Logging out
     */
    public function logoutAction()
    {
        Zend_Session::destroy();
        $this->_helper->redirector->gotoAndExit('index', 'index');
    }

    /**
     * Preparing data to login block.
     * Mustn't be called directly
     */
    public function showLoginAction()
    {
        $user = App_User_Factory::getSessionUser();
        
        $this->view->isLoggedIn = $user !== false;
        if ($user !== false) {
            $this->view->login = $user->getLogin();
        }
    }
}