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
 * User controller.
 *
 * @copyright  2008 Maxim Dikun
 * @version    Release: 0.0.1
 */
class UserController extends Zend_Controller_Action
{
    /**
     * Login parameter cache
     *
     * @var string
     */
    protected $_login;

    /**
     * Initialization of controller
     */
    public function init()
    {
        // Setting login
        $this->_login = $this->getRequest()->getParam('login');
        $this->view->login = $this->_login;
        $this->view->headTitle($this->_login);
    }

    /**
     * Shows user main profile page
     */
    public function profileAction()
    {
        $this->view->headTitle("профиль");

        $user = App_User_Factory::getInstance()->getUserByLogin($this->_login);

        $this->view->user = $user;
        $this->view->writeboard = $user->getWriteboard();
    }

    /**
     * Redirecting to user blog
     */
    public function blogAction()
    {
        $this->_forward("show", "blog");
    }

    /**
     * Shows library add form
     */
    public function addAction()
    {

    }

    /**
     * Process add book to library
     */
    public function addProcessAction()
    {
        $authorName = $this->getRequest()->getParam('author');
        $titleName = $this->getRequest()->getParam('title');

        try {
            $author = App_Library_Author::getByName($authorName);
        } catch (App_Library_Exception_AuthorNotFound $e) {
            $author = null;
        }

        if ($author === null) {
            // New author: creating
            $author = new App_Library_Author(array(
                'name' => $authorName,
            ));

            $author->write();
        }

        try {
            $title = App_Library_Title::getByName($author, $titleName);
        } catch (App_Library_Exception_TitleNotFound $e) {
            $title = null;
        }

        if ($title === null) {
            // New title: creating
            $title = new App_Library_Title(array(
                'name' => $titleName,
                'authors' => array($author),
                'authors_index' => $author->getName()
            ));

            $title->write();
        }

        die('Handler must be here!');
    }
}