<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id: AuthorController.php 71 2008-12-10 21:03:29Z dikmax $
 */

/**
 * Author controller
 */
class LibraryController extends Zend_Controller_Action
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
    protected $_authorUrl;

    /**
     * Author
     *
     * @var App_Library_Author
     */
    protected $_author;

    /**
     * Title name
     *
     * @var string
     */
    protected $_titleUrl;

    /**
     * Title
     *
     * @var App_Library_Title
     */
    protected $_title;

    /**
     * Page type
     *
     * @var int
     */
    protected $_type;

    /**
     * Not found error
     *
     * @var boolean
     */
    protected $_error;

    const AUTHOR_PAGE = 1;
    const TITLE_PAGE = 2;
    const OTHER_PAGE = 0;

    public function init()
    {
        $this->_error = false;
        $this->detectType();
        $this->initMenu();
    }

    public function detectType()
    {
        $request = $this->getRequest();
        $this->_authorUrl = $request->getParam('author');
        $this->_titleUrl = $request->getParam('title');

        if ($this->_authorUrl !== null) {
            if ($this->_titleUrl !== null) {
                $this->_type = self::TITLE_PAGE;
            } else {
                $this->_type = self::AUTHOR_PAGE;
            }
        } else {
            $this->_type = self::OTHER_PAGE;
        }
    }

    public function initMenu()
    {
        $this->_topMenu = $this->view->getHelper('topMenu');

        if ($this->_type === self::AUTHOR_PAGE || $this->_type === self::TITLE_PAGE) {
            $this->_topMenu->addItem('overview', 'Обзор',
                $this->_helper->url->url(array(
                    'action' => 'overview',
                    'author' => $this->_authorUrl
                ), 'library'));
        }

        if ($this->_type === self::AUTHOR_PAGE) {
            $this->_topMenu->addItem('wiki', 'Биография',
                $this->_helper->url->url(array(
                    'action' => 'wiki',
                    'author' => $this->_authorUrl
                ), 'library'));
            $this->_topMenu->addItem('books', 'Книги',
                $this->_helper->url->url(array(
                    'action' => 'books',
                    'author' => $this->_authorUrl
                ), 'library'));
        }
    }
    /**
     * Add top right wiki menu
     */
    protected function initWikiMenu()
    {
        $this->_topMenu->addItem('edit', 'Править',
            $this->_helper->url->url(array(
                'action' => 'wiki-edit',
                'author' => $this->_authorUrl
            ), 'libraryauthoraction'), true);
        $this->_topMenu->addItem('history', 'История',
            $this->_helper->url->url(array(
                'action' => 'wiki-history',
                'author' => $this->_authorUrl
            ), 'libraryauthoraction'), true);
    }

    /**
     * Returns author
     *
     * @return App_Library_Author
     */
    protected function initAuthorAndTitle()
    {
        $this->_author = null;
        $this->_title = null;

        if ($this->_authorUrl !== null) {
            try {
                $this->_author = App_Library_Author::getByName($this->_authorUrl);
            } catch (App_Library_Exception_AuthorNotFound $e) {
                $this->_forward('author-not-found', 'error', null, array(
                    'author' => $this->_authorUrl
                ));
                $this->_error = true;
            }
        }

        if ($this->_titleUrl !== null) {
            try {
                $this->_title = App_Library_Title::getByName($this->_author, $this->_titleUrl);
            } catch (App_Library_Exception_TitleNotFound $e) {
                $this->_forward('title-not-found', 'error', null, array(
                    'author' => $this->_authorUrl,
                    'title' => $this->_titleUrl
                ));
                $this->_error = true;
            }
        }
    }

    /*
     * Actions
     */

    /**
     * Main library action
     */
    public function indexAction()
    {
        $this->view->authors = App_Library::getMostReadAuthors();
        $this->view->titles = App_Library::getMostReadTitles();
    }

    /**
     * Author's page
     */
    public function overviewAction()
    {
        if ($this->_type == self::AUTHOR_PAGE || $this->_type == self::TITLE_PAGE) {
            $this->initAuthorAndTitle();
            if ($this->_error) {
                return;
            }
        }
        $this->_topMenu->selectItem('overview');

        if ($this->_author) {
            $this->view->headTitle($this->_author->getName());
            $this->view->author = $this->_author;
            $this->view->frontImage = $this->_author->getFrontImage();
        }

        if ($this->_title) {
            $this->view->headTitle($this->_title->getName());
            $this->view->title = $this->_title;
        }
    }

    /**
     * Shows full wiki page
     */
    public function wikiAction()
    {
        $this->initWikiMenu();
        $this->_topMenu->selectItem('wiki');

        $author = $this->getAuthor();
        if ($author) {
            $this->view->headTitle($author->getName())
                       ->headTitle('Информация');
            $this->view->author = $author;
        }
    }

    /**
     * Edit wiki page
     */
    public function wikiEditAction()
    {
        $this->initWikiMenu();
        $this->_topMenu->selectItem('wiki');
        $this->_topMenu->selectItem('edit', true);

        $author = $this->getAuthor();
        if ($author) {
            $this->view->headTitle($author->getName())
                       ->headTitle('Информация')
                       ->headTitle('Редактирование');
            $this->view->author = $author;
        }
    }

    /**
     * Save wiki action
     */
    public function wikiSaveAction()
    {
        $author = $this->getAuthor();
        if ($author) {
            $text = $this->getRequest()->getParam('text');

            if ($text == $author->getText()) {
                // Text doesn't change. Nothing to do
            } else {
                $author->setText($text);
            }
            $this->_redirect($this->view->url(array(
                'action' => 'wiki')));
        }
    }

    /**
     * Wiki history action
     */
    public function wikiHistoryAction()
    {
        $this->initWikiMenu();
        $this->_topMenu->selectItem('wiki');
        $this->_topMenu->selectItem('history', true);

        $author = $this->getAuthor();
        if ($author) {
            $this->view->revisions = $author->getDescription()->getRevisionsList();
        }
    }

    /**
     * Books action
     */
    public function booksAction()
    {
        $this->_topMenu->selectItem('books');
    }
}