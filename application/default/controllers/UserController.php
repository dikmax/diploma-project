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
    const PROFILE_ACTION = 'Профиль';
    const BOOKSHELF_ACTION = 'Книжная полка';
    const BLOG_ACTION = 'Журнал';

    /**
     * Login parameter cache
     *
     * @var string
     */
    protected $_login;

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
        $this->_topMenu
             ->addItem(self::PROFILE_ACTION, $this->_helper->url->url(array('action' => 'profile')))
             ->addItem(self::BOOKSHELF_ACTION, $this->_helper->url->url(array('action' => 'bookshelf')))
             ->addItem(self::BLOG_ACTION, $this->_helper->url->url(array('action' => 'blog')));
    }

    /**
     * Shows user main profile page
     */
    public function profileAction()
    {
        $this->view->headTitle("профиль");
        $this->_topMenu->selectItem(self::PROFILE_ACTION);

        $user = App_User_Factory::getInstance()->getUserByLogin($this->_login);

        $this->view->user = $user;
        $this->view->writeboard = $user->getWriteboard();
    }

    /**
     * Redirecting to user blog
     */
    public function blogAction()
    {
        $this->_topMenu->selectItem(self::BLOG_ACTION);
        $this->_forward("show", "blog");
    }

    /**
     * Writing user bookshelf
     */
    public function bookshelfAction()
    {
        $this->_topMenu->selectItem(self::BOOKSHELF_ACTION);

        $user = App_User_Factory::getInstance()->getUserByLogin($this->_login);
        $bookshelf = $user->getBookshelf();

        $cloud = new App_Tag_Cloud(array(
            'reader' => $bookshelf,
            'writer' => $this->view->getHelper('cloudRenderer')
        ));
        $cloud->process();

        $this->view->user = $user;
        $this->view->bookshelf = $bookshelf;
        $this->view->titles = $bookshelf->getTitles();
    }
}