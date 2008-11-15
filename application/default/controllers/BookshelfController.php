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
 * Bookshelf controller.
 *
 * @copyright  2008 Maxim Dikun
 * @version    Release: 0.0.1
 */
class BookshelfController extends Zend_Controller_Action
{
    /**
     * Current user
     *
     * @var App_User
     */
    protected $_user;

    /**
     * Initializes bookshelf controller
     */
    public function init()
    {
        $this->_user = App_User_Factory::getSessionUser();

        if ($this->_user === null) {
            $this->_redirect($this->view->url(array('action' => 'show-login'), 'auth'));
        }
    }

    /**
     * Shows bookshelf
     */
    public function showAction()
    {
        $this->view->user = $this->_user;
    }

    /**
     * Shows bookshelf add form
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

        $this->_user->getBookshelf()->addTitle($title);

        die('Handler must be here!');
    }
}
