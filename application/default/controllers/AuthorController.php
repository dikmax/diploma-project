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
 * Author controller
 */
class AuthorController extends Zend_Controller_Action
{
    /**
     * Top menu view helper
     *
     * @var App_View_Helper_TopMenu
     */
    protected $_topMenu;

    /**
     * Author name
     *
     * @var string
     */
    protected $_author;

    const OVERVIEW_ACTION = 'Обзор';
    const BIOGRAPHY_ACTION = 'Биография';
    const BOOKS_ACTION = 'Книги';

    public function init()
    {
        $this->_author = $this->getRequest()->getParam('author');

        $this->_topMenu = $this->view->getHelper('topMenu');
        $this->_topMenu->addItem('overview', 'Обзор',
            $this->_helper->url->url(array(
                'action' => 'show',
                'author' => $this->_author
            ), 'libraryauthor'));
        $this->_topMenu->addItem('bio', 'Биография',
            $this->_helper->url->url(array(
                'action' => 'wiki',
                'author' => $this->_author
            ), 'libraryauthoraction'));
        $this->_topMenu->addItem('books', 'Книги',
            $this->_helper->url->url(array(
                'action' => 'books',
                'author' => $this->_author
            ), 'libraryauthoraction'));
    }

    /**
     * Add top right wiki menu
     */
    protected function addWikiTopMenu()
    {
        $this->_topMenu->addItem('edit', 'Править',
            $this->_helper->url->url(array(
                'action' => 'wiki-edit',
                'author' => $this->_author
            ), 'libraryauthoraction'), true);
        $this->_topMenu->addItem('history', 'История',
            $this->_helper->url->url(array(
                'action' => 'wiki-history',
                'author' => $this->_author
            ), 'libraryauthoraction'), true);
    }

    /**
     * Author's page
     */
    public function overviewAction()
    {
        $this->_topMenu->selectItem('overview');
        try {
            $author = App_Library_Author::getByName($this->_author);
            $this->view->headTitle($author->getName());
            $this->view->author = $author;
            $this->view->frontImage = $author->getFrontImage();
        } catch (App_Library_Exception_AuthorNotFound $e) {
            $this->_forward('author-not-found', 'error', null, array(
                'author' => $this->_author
            ));
        }
    }

    /**
     * Shows full wiki page
     */
    public function wikiAction()
    {
        $this->addWikiTopMenu();
        $this->_topMenu->selectItem('bio');
        try {
            $author = App_Library_Author::getByName($this->_author);
            $this->view->headTitle($author->getName())
                       ->headTitle('Информация');
            $this->view->author = $author;
        } catch (App_Library_Exception_AuthorNotFound $e) {
            $this->_forward('author-not-found', 'error', null, array(
                'author' => $this->_author
            ));
        }
    }

    /**
     * Edit wiki page
     */
    public function wikiEditAction()
    {
        $this->addWikiTopMenu();
        $this->_topMenu->selectItem('bio');
        $this->_topMenu->selectItem('edit', true);
        try {
            $author = App_Library_Author::getByName($this->_author);
            $this->view->headTitle($author->getName())
                       ->headTitle('Информация')
                       ->headTitle('Редактирование');
            $this->view->author = $author;
        } catch (App_Library_Exception_AuthorNotFound $e) {
            $this->_forward('author-not-found', 'error', null, array(
                'author' => $this->_author
            ));
        }
    }

    /**
     * Save wiki action
     */
    public function wikiSaveAction()
    {
        try {
            $author = App_Library_Author::getByName($this->_author);

            $text = $this->getRequest()->getParam('text');

            if ($text == $author->getText()) {
                // Text doesn't change. Nothing to do
            } else {
                $author->setText($text);
            }
            $this->_redirect($this->view->url(array(
                'action' => 'wiki')));
        } catch (App_Library_Exception_AuthorNotFound $e) {
            $this->_forward('author-not-found', 'error', null, array(
                'author' => $this->_author
            ));
        }
    }

    /**
     * Wiki history action
     */
    public function wikiHistoryAction()
    {
        $this->addWikiTopMenu();
        $this->_topMenu->selectItem('bio');
        $this->_topMenu->selectItem('history', true);
    }

    /**
     * Books action
     */
    public function booksAction()
    {
        $this->_topMenu->selectItem('books');
    }
}