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
            $this->_redirect($this->_helper->url->url(array('action' => 'login'), 'auth'));
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
                $this->_helper->url->url(array(
                    'action' => 'list'
                )));
            $this->_topMenu->addItem('friend-requests', 'Предложения дружбы',
                $this->_helper->url->url(array(
                    'action' => 'friend-requests'
                )));
        }
    }

    public function listAction()
    {
        $this->_topMenu->selectItem('list');

        $friends = $this->_user->getFriends();

        $list = $friends->getFriendsList(App_User_Friends::STATE_APPROVED);

        $this->view->friends = $list;
    }

    public function friendRequestsAction()
    {
        $this->_topMenu->selectItem('friend-requests');

        $friends = $this->_user->getFriends();

        $list = $friends->getFriendsList(App_User_Friends::STATE_REQUEST_RECEIVED);

        $this->view->friends = $list;
    }
}
