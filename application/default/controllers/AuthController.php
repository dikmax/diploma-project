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
        $form = new App_Form_Auth_Login($this->_helper->url('login', 'auth'));

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $db = Zend_Registry::get('db');
                $auth = new Zend_Auth_Adapter_DbTable($db, 'lib_user', 'login',
                    'password', 'MD5(?)');
                $auth->setIdentity($form->getValue('login'))
                    ->setCredential($form->getValue('password'));
                $result = $auth->authenticate();

                if ($result->isValid()) {
                    $user = App_User_Factory::getInstance()->getUserByLogin($form->getValue('login'));
                    App_User_Factory::setSessionUser($user);
                    $this->_helper->redirector->gotoRouteAndExit(
                        array('controller' => 'user',
                            'action' => 'profile',
                            'login' => $form->getValue('login')),
                        'user'
                    );
                } else {
                    $form->getElement('login')->addErrors($result->getMessages());
                }
            }

        }
        $this->view->form = $form;
        $this->_helper->layout->setLayout('full-width');
    }

    /**
     * Logging out
     */
    public function logoutAction()
    {
        Zend_Session::destroy();
        $this->_redirect($this->_helper->url('index', 'index'));
    }

    /**
     * Preparing data to login block.
     * Mustn't be called directly
     */
    public function showLoginAction()
    {
        $user = App_User_Factory::getSessionUser();

        $this->view->isLoggedIn = $user !== null;
        if ($user !== null) {
            $this->view->login = $user->getLogin();
        }
        $this->view->form = new App_Form_Auth_Login($this->_helper->url('login', 'auth'));
    }

    /**
     * Shows registration form
     */
    public function registrationAction()
    {
        $user = App_User_Factory::getSessionUser();

        if ($user !== null) {
            $this->_redirect($this->url->url(array(
                'controller' => 'user',
                'action' => 'profile',
                'login' => $user->getLogin()
            ), 'user'));
        }

        $form = new App_Form_Auth_Registration();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                App_User_Factory::getInstance()->registerUser($form->getValues());
                $this->_redirect($this->_helper->url('index', 'index'));
                // TODO show registration done message
            }
        }
        $this->view->form = $form;
    }
}