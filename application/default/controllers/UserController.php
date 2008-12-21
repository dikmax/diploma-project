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
 * User controller.
 *
 * @copyright  2008 Maxim Dikun
 * @version    Release: 0.0.1
 */
class UserController extends Zend_Controller_Action
{
    /**
     * Showing user
     *
     * @var App_User
     */
    protected $_user;

    /**
     * Login parameter cache
     *
     * @var string
     */
    protected $_login;

    /**
     * Not found error
     *
     * @var boolean
     */
    protected $_error = false;

    /**
     * Top menu view helper
     *
     * @var App_View_Helper_TopMenu
     */
    protected $_topMenu;

    /**
     * Initialization of controller
     */
    public function init()
    {
        // Setting login
        $this->_login = $this->getRequest()->getParam('login');
        $this->view->login = $this->_login;
        $this->view->headTitle($this->_login);

        $this->_topMenu = $this->view->getHelper('topMenu');
        $this->_topMenu->addItem('profile', 'Профиль',
            $this->_helper->url->url(array('action' => 'profile')));
        $this->_topMenu->addItem('bookshelf', 'Книжная полка',
            $this->_helper->url->url(array('action' => 'bookshelf')));
        $this->_topMenu->addItem('blog', 'Блог',
            $this->_helper->url->url(array('action' => 'blog')));
    }

    public function initUser()
    {
        try {
            $this->_user = App_User_Factory::getInstance()->getUserByLogin($this->_login);
        } catch (App_User_Exception $e) {
            $this->_forward('user-not-found', 'error', null, array(
                'login' => $this->_login
            ));
            $this->_error = true;
        }
    }

    /**
     * Shows user main profile page
     */
    public function profileAction()
    {
        $this->initUser();
        if ($this->_error) {
            return;
        }
        $this->view->headTitle("профиль");
        $this->_topMenu->selectItem('profile');

        $this->view->user = $this->_user;
        $this->view->writeboard = $this->_user->getWriteboard();
    }

    /**
     * Redirecting to user blog
     */
    public function blogAction()
    {
        $this->_topMenu->selectItem('blog');
        $this->_forward("show", "blog");
    }

    /**
     * Writing user bookshelf
     */
    public function bookshelfAction()
    {
        $this->initUser();
        if ($this->_error) {
            return;
        }

        $this->_topMenu->selectItem('bookshelf');

        $bookshelf = $this->_user->getBookshelf();

        $cloud = new App_Tag_Cloud(array(
            'reader' => $bookshelf,
            'writer' => $this->view->getHelper('cloudRenderer')
        ));
        $cloud->process();

        $this->view->user = $this->_user;
        $this->view->bookshelf = $bookshelf;
        $this->view->titles = $bookshelf->getTitles();
    }

    public function mailAction()
    {
        if (!$this->view->getHelper('isAllowed')->isAllowed('mail', 'view')) {
            $this->_forward('not-allowed', 'error');
            return;
        }

        $this->initUser();
        if ($this->_error) {
            return;
        }
    }
}