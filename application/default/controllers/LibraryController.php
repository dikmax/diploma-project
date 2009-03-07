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
                $this->view->url(array(
                    'action' => 'overview',
                    'author' => $this->_authorUrl,
                    'title' => $this->_titleUrl
                )));
        }

        if ($this->_type === self::AUTHOR_PAGE) {
            $this->_topMenu->addItem('wiki', 'Биография',
                $this->view->url(array(
                    'action' => 'wiki',
                    'author' => $this->_authorUrl
                )));
            $this->_topMenu->addItem('books', 'Книги',
                $this->view->url(array(
                    'action' => 'books',
                    'author' => $this->_authorUrl
                )));
        }

        if ($this->_type === self::TITLE_PAGE) {
            $this->_topMenu->addItem('wiki', 'Описание',
                $this->view->url(array(
                    'action' => 'wiki',
                    'author' => $this->_authorUrl,
                    'title' => $this->_titleUrl
                )));
            $this->_topMenu->addItem('similar', 'Похожие книги',
                $this->view->url(array(
                    'action' => 'similar',
                    'author' => $this->_authorUrl,
                    'title' => $this->_titleUrl
                )));
        }
    }

    /**
     * Add top right wiki menu
     */
    protected function initWikiMenu()
    {
        $this->_topMenu->addItem('edit', 'Править',
            $this->view->url(array(
                'action' => 'wiki-edit',
                'author' => $this->_authorUrl,
                'title' => $this->_titleUrl
            )), true);
        $this->_topMenu->addItem('history', 'История',
            $this->view->url(array(
                'action' => 'wiki-history',
                'author' => $this->_authorUrl,
                'title' => $this->_titleUrl
            )), true);
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
            $this->_author = App_Library_Author::getByName($this->_authorUrl);

            if ($this->_author === null) {
                $this->_forward('author-not-found', 'error', null, array(
                    'author' => $this->_authorUrl
                ));
                $this->_error = true;
                return;
            }
        }

        if ($this->_titleUrl !== null) {
            $this->_title = App_Library_Title::getByName($this->_author, $this->_titleUrl);

            if ($this->_title === null) {
                $this->_forward('title-not-found', 'error', null, array(
                    'author' => $this->_authorUrl,
                    'title' => $this->_titleUrl
                ));
                $this->_error = true;
                return;
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
        if ($this->_type === self::AUTHOR_PAGE || $this->_type === self::TITLE_PAGE) {
            $this->initAuthorAndTitle();
            if ($this->_error) {
                return;
            }
        } else {
            $this->_redirect($this->_helper->url->url());
        }
        $this->view->type = $this->_type;
        $this->_topMenu->selectItem('overview');

        if ($this->_author) {
            $authorName = $this->_author->getName();
            $this->view->headTitle($authorName);
            $this->view->author = $this->_author;
            $this->view->authorName = $authorName;
        }

        // Actual processing
        if ($this->_title) {
            $user = App_User_Factory::getSessionUser();
            if ($user instanceof App_User) {
                $user->getBookshelf()->getMark($this->_title);
            }

            $titleName = $this->_title->getName();
            $this->view->headTitle($titleName);
            $this->view->title = $this->_title;
            $this->view->titleName = $titleName;

            $this->view->frontDescription = $this->_title->getFrontDescription();
        } else if ($this->_author) {
            $this->view->frontImage = $this->_author->getFrontImage();

            $this->view->frontDescription = $this->_author->getFrontDescription();
        }
    }

    /**
     * Shows similar titles page
     */
    public function similarAction()
    {
        if ($this->_type === self::TITLE_PAGE) {
            $this->initAuthorAndTitle();
            if ($this->_error) {
                return;
            }
        } else {
            $this->_redirect($this->_helper->url->url());
        }
        $this->view->type = $this->_type;

        $this->_topMenu->selectItem('similar');

        $authorName = $this->_author->getName();
        $this->view->headTitle($authorName);
        $this->view->author = $this->_author;
        $this->view->authorName = $authorName;

        $titleName = $this->_title->getName();
        $this->view->headTitle($titleName);
        $this->view->title = $this->_title;
        $this->view->titleName = $titleName;

        $this->view->similarTitles = $this->_title->getSimilarTitles();

        $this->view->headTitle('Похожие книги');
    }

    /**
     * Shows full wiki page
     */
    public function wikiAction()
    {
        if ($this->_type === self::AUTHOR_PAGE || $this->_type === self::TITLE_PAGE) {
            $this->initAuthorAndTitle();
            if ($this->_error) {
                return;
            }
        } else {
            $this->_redirect($this->_helper->url->url());
        }
        $this->view->type = $this->_type;

        $this->initWikiMenu();
        $this->_topMenu->selectItem('wiki');

        if ($this->_author) {
            $authorName = $this->_author->getName();
            $this->view->headTitle($authorName);
            $this->view->author = $this->_author;
            $this->view->authorName = $authorName;
        }

        // Actual processing
        if ($this->_title) {
            $titleName = $this->_title->getName();
            $this->view->headTitle($titleName);
            $this->view->title = $this->_title;
            $this->view->titleName = $titleName;

            $this->view->text = $this->_title->getText();
        } else if ($this->_author) {
            $this->view->text = $this->_author->getText();
        }

        $this->view->headTitle('Информация');
    }

    /**
     * Edit wiki page
     */
    public function wikiEditAction()
    {
        if ($this->_type === self::AUTHOR_PAGE || $this->_type === self::TITLE_PAGE) {
            $this->initAuthorAndTitle();
            if ($this->_error) {
                return;
            }
        } else {
            $this->_redirect($this->_helper->url->url());
        }
        $this->view->type = $this->_type;

        $this->initWikiMenu();
        $this->_topMenu->selectItem('wiki');
        $this->_topMenu->selectItem('edit', true);

        if ($this->_author) {
            $authorName = $this->_author->getName();
            $this->view->headTitle($authorName);
            $this->view->author = $this->_author;
            $this->view->authorName = $authorName;
        }
        if ($this->_title) {
            $titleName = $this->_title->getName();
            $this->view->headTitle($titleName);
            $this->view->title = $this->_title;
            $this->view->titleName = $titleName;

            $this->view->text = $this->_title->getText();
        } else {
            $this->view->text = $this->_author->getText();
        }
        $this->view->headTitle('Информация')
                   ->headTitle('Редактирование');
    }

    /**
     * Save wiki action
     */
    public function wikiSaveAction()
    {
        if ($this->_type === self::AUTHOR_PAGE || $this->_type === self::TITLE_PAGE) {
            $this->initAuthorAndTitle();
            if ($this->_error) {
                return;
            }
        } else {
            $this->_redirect($this->_helper->url->url());
        }

        $text = $this->getRequest()->getParam('text');
        if ($this->_title) {
            if ($text == $this->_title->getText()) {
                // Text doesn't change. Nothing to do
            } else {
                $this->_title->setText($text);
            }
        } else {
            if ($text == $this->_author->getText()) {
                // Text doesn't change. Nothing to do
            } else {
                $this->_author->setText($text);
            }
        }
        $this->_redirect($this->view->url(array(
            'author' => $this->_authorUrl,
            'title' => $this->_titleUrl,
            'action' => 'overview'
        )));
    }

    /**
     * Wiki history action
     */
    public function wikiHistoryAction()
    {
        if ($this->_type === self::AUTHOR_PAGE || $this->_type === self::TITLE_PAGE) {
            $this->initAuthorAndTitle();
            if ($this->_error) {
                return;
            }
        } else {
            $this->_redirect($this->_helper->url->url());
        }
        $this->view->type = $this->_type;

        $this->initWikiMenu();
        $this->_topMenu->selectItem('wiki');
        $this->_topMenu->selectItem('history', true);

        if ($this->_author) {
            $authorName = $this->_author->getName();
            $this->view->headTitle($authorName);
            $this->view->author = $this->_author;
            $this->view->authorName = $authorName;
        }
        if ($this->_title) {
            $titleName = $this->_title->getName();
            $this->view->headTitle($titleName);
            $this->view->title = $this->_title;
            $this->view->titleName = $titleName;
        }

        $extraparams = $this->getRequest()->getParam('extraparams', array());

        if (!isset($extraparams[0])) {
            // Revisions list
            if ($this->_title) {
                $this->view->revisions = $this->_title->getDescription()->getRevisionsList();
            } else {
                $this->view->revisions = $this->_author->getDescription()->getRevisionsList();
            }
        } else {
            // Revision content
            if (!is_numeric($extraparams[0])) {
                $this->_redirect($this->_helper->url->url());
            }

            $revisionNum = (int)$extraparams[0];

            if ($this->_title) {
                $revision = $this->_title->getDescription()->getRevision($revisionNum);
            } else {
                $revision = $this->_author->getDescription()->getRevision($revisionNum);
            }

            if (!$revision) {
                $this->_redirect($this->_helper->url->url());
            }

            if (!isset($extraparams[1])) {
                //show revision
                $this->view->revisionNum = $revisionNum;
                $this->view->revision = $revision;
                $this->_helper->viewRenderer->setScriptAction('wiki-show-revision');
            } else {
                switch ($extraparams[1]) {
                    case 'rollback':
                        // Rollback revision
                        $this->wikiRollbackRevisionAction($revision);
                        break;
                    default:
                        if (is_numeric(($extraparams[1]))) {
                            // Compare revisions
                            $oldRevisionNum = (int)$extraparams[1];

                            if ($this->_title) {
                                $oldRevision = $this->_title->getDescription()->getRevision($oldRevisionNum);
                            } else {
                                $oldRevision = $this->_author->getDescription()->getRevision($oldRevisionNum);
                            }

                            $this->wikiRevisionCompareAction($revision, $oldRevision);
                        } else {
                            // TODO redirect to 404
                            $this->_redirect($this->view->url());
                        }
                }
            }
        }
    }

    /**
     * Revision rollback
     *
     * @param App_Text_Revision $revision revision to rollback
     */
    private function wikiRollbackRevisionAction(App_Text_Revision $revision)
    {
        if ($this->_title) {
            $text = $this->_title->getDescription();
        } else {
            $text = $this->_author->getDescription();
        }

        $text->rollbackToRevision($revision);

        $this->_redirect($this->view->libraryUrl('wiki-history'));
    }

    /**
     * Compare revisions
     *
     * @param App_Text_Revision $newRevision
     * @param App_Text_Revision $oldRevision
     */
    private function wikiRevisionCompareAction(App_Text_Revision $newRevision, App_Text_Revision $oldRevision)
    {
        $result = App_Diff::diff($oldRevision->getContent(), $newRevision->getContent());

        $this->view->compareResult = $result;
        $this->view->oldRevisionNum = $oldRevision->getRevision();
        $this->view->newRevisionNum = $newRevision->getRevision();

        $this->_helper->viewRenderer->setScriptAction('wiki-compare-revision');
    }

    /**
     * Reserved for showing specific text revision
     */
    private function wikiShowRevisionAction()
    {
        throw new App_Exception("This method shouldn't be called directly");
    }

    /**
     * Books action
     */
    public function booksAction()
    {
        $this->_topMenu->selectItem('books');
    }
}