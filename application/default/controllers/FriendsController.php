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
 * model
 */
class FriendsController extends Zend_Controller_Action
{
    /**
     * Current user
     *
     * @var App_User
     */
    protected $_user;

    /**
     * Top menu
     *
     * @var App_View_Helper_TopMenu
     */
    protected $_topMenu;

    /**
     * Initialize controller
     */
    public function init()
    {
        $this->_user = App_User_Factory::getSessionUser();
        if (!$this->_user) {
            // Not logged in. Redirect to login page
            $this->_redirect($this->_helper->url->url(array(
                'controller' => 'auth',
                'action' => 'login')));
            return;
        }

        if ($this->_user) {
            $this->initMenu();
        }
    }

    /**
     * Initialize menu
     */
    protected function initMenu()
    {
        $this->_topMenu = $this->view->getHelper('topMenu');

        if ($this->_topMenu->isEmpty()) {
            $this->_topMenu->addItem('list', 'Друзья',
                $this->view->url(array(
                    'action' => 'list',
                    'user' => ''
                )));
            $this->_topMenu->addItem('friend-requests', 'Предложения дружбы',
                $this->view->url(array(
                    'action' => 'requests',
                    'user' => ''
                )));
        }
    }

    /**
     * Redirects to friends list
     */
    protected function redirectToList()
    {
        $this->_redirect($this->view->url(array(
            'action' => 'list',
            'user' => ''
        )));
    }

    /**
     * Shows list of friends
     */
    public function listAction()
    {
        $this->_topMenu->selectItem('list');

        $friends = $this->_user->getFriends();

        $list = $friends->getFriendsList(App_User_Friends::STATE_APPROVED);

        $this->view->friends = $list;
    }

    /**
     * Shows lists of friendship requests
     */
    public function requestsAction()
    {
        $this->_topMenu->selectItem('friend-requests');

        $friends = $this->_user->getFriends();

        $receivedRequests = $friends->getFriendsList(App_User_Friends::STATE_REQUEST_RECEIVED);
        $this->view->receivedRequests = $receivedRequests;

        $sentRequests = $friends->getFriendsList(App_User_Friends::STATE_REQUEST_SENT);
        $this->view->sentRequests = $sentRequests;
    }

    /**
     * Show request confirm
     */
    public function confirmAction()
    {
        $confirmUser = $this->getRequest()->getParam('user');

        if (!$confirmUser) {
            $this->redirectToList();
            return;
        }

        $form = new App_Form_Friends_Confirm();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                if ($form->getValue('confirm')) {
                    $friends = $this->_user->getFriends();
                    $user = App_User_Factory::getInstance()->getUserByLogin($confirmUser);
                    if ($user === null) {
                        $this->redirectToList();
                        return;
                    }
                    $friends->sendRequest($user);
                    $this->redirectToList();
                    return;
                } else if ($form->getValue('decline')) {
                    $this->redirectToList();
                    return;
                }
            }
        }

        $this->view->confirmUser = $confirmUser;
        $this->view->form = $form;
    }

    /**
     * Show delete confirm
     */
    public function confirmDeleteAction()
    {
        $confirmUser = $this->getRequest()->getParam('user');

        if (!$confirmUser) {
            $this->redirectToList();
            return;
        }

        $form = new App_Form_Friends_ConfirmDelete();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                if ($form->getValue('delete')) {
                    $friends = $this->_user->getFriends();
                    $user = App_User_Factory::getInstance()->getUserByLogin($confirmUser);
                    if ($user === null) {
                        $this->redirectToList();
                        return;
                    }
                    $friends->deleteFriend($user);
                    $this->redirectToList();
                    return;
                } else if ($form->getValue('cancel')) {
                    $this->redirectToList();
                    return;
                }
            }
        }

        $this->view->confirmUser = $confirmUser;
        $this->view->form = $form;
    }

    /**
     * Accept request
     */
    public function acceptAction()
    {
        $userLogin = $this->getRequest()->getParam('user');

        if (!$userLogin) {
            $this->redirectToList();
            return;
        }
        $friends = $this->_user->getFriends();
        $user = App_User_Factory::getInstance()->getUserByLogin($userLogin);
        if ($user === null) {
            $this->redirectToList();
            return;
        }
        $friends->acceptRequest($user);
        $this->_redirect($this->_helper->url->url(array(
            'action' => 'requests',
            'user' => ''
        )));
    }

    /**
     * Decline request
     */
    public function declineAction()
    {
        $userLogin = $this->getRequest()->getParam('user');

        if (!$userLogin) {
            $this->redirectToList();
            return;
        }
        $friends = $this->_user->getFriends();
        $user = App_User_Factory::getInstance()->getUserByLogin($userLogin);
        if ($user === null) {
            $this->redirectToList();
            return;
        }
        $friends->declineRequest($user);
        $this->_redirect($this->_helper->url->url(array(
            'action' => 'requests',
            'user' => ''
        )));
    }

    /**
     * Cancel request
     */
    public function cancelAction()
    {
        $userLogin = $this->getRequest()->getParam('user');

        if (!$userLogin) {
            $this->redirectToList();
            return;
        }
        $friends = $this->_user->getFriends();
        $user = App_User_Factory::getInstance()->getUserByLogin($userLogin);
        if ($user === null) {
            $this->redirectToList();
            return;
        }
        $friends->cancelRequest($user);
        $this->_redirect($this->_helper->url->url(array(
            'action' => 'requests',
            'user' => ''
        )));
    }
}
