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
 * Title controller
 */
class TitleController extends Zend_Controller_Action
{
    /**
     * Book title page
     */
    public function showAction()
    {
        try {
            $authorUrl = $this->getRequest()->getParam('author');
            $author = App_Library::getAuthorByUrl($authorUrl);
            $this->view->headTitle($author->getName());
            $this->view->author = $author;
            
            $titleUrl = $this->getRequest()->getParam('title');
            $title = App_Library::getTitleByUrl($author, $titleUrl);
            $this->view->headTitle($title->getName());
            $this->view->title = $title;
        } catch (App_Library_Exception_AuthorNotFound $e) {
            $this->_forward('author-not-found', 'error', null, array(
                'author' => $authorUrl
            ));
        } catch (App_Library_Exception_TitleNotFound $e) {
            $this->_forward('title-not-found', 'error', null, array(
                'author' => $authorUrl,
                'title' => $titleUrl
            ));
        }
    }

    /**
     * Shows full wiki page
     */
    public function wikiAction()
    {
        try {
            $authorUrl = $this->getRequest()->getParam('author');
            $author = App_Library::getAuthorByUrl($authorUrl);
            
            $titleUrl = $this->getRequest()->getParam('title');
            $title = App_Library::getTitleByUrl($author, $titleUrl);
            
            $this->view->headTitle($author->getName())
                       ->headTitle($title->getName())
                       ->headTitle('Информация');
            $this->view->author = $author;
            $this->view->title = $title;
        } catch (App_Library_Exception_AuthorNotFound $e) {
            $this->_forward('author-not-found', 'error', null, array(
                'author' => $authorUrl
            ));
        } catch (App_Library_Exception_TitleNotFound $e) {
            $this->_forward('title-not-found', 'error', null, array(
                'author' => $authorUrl,
                'title' => $titleUrl
            ));
        }
    }
    
    /**
     * Edit wiki page
     */
    public function wikiEditAction()
    {
        try {
            $authorUrl = $this->getRequest()->getParam('author');
            $author = App_Library::getAuthorByUrl($authorUrl);
            
            $titleUrl = $this->getRequest()->getParam('title');
            $title = App_Library::getTitleByUrl($author, $titleUrl);
            
            $this->view->headTitle($author->getName())
                       ->headTitle($title->getName())
                       ->headTitle('Информация')
                       ->headTitle('Редактирование');
            $this->view->author = $author;
            $this->view->title = $title;
        } catch (App_Library_Exception_AuthorNotFound $e) {
            $this->_forward('author-not-found', 'error', null, array(
                'author' => $authorUrl
            ));
        } catch (App_Library_Exception_TitleNotFound $e) {
            $this->_forward('title-not-found', 'error', null, array(
                'author' => $authorUrl,
                'title' => $titleUrl
            ));
        }
    }
    
    /**
     * Save wiki action
     */
    public function wikiSaveAction()
    {
        try {
            $authorUrl = $this->getRequest()->getParam('author');
            $author = App_Library::getAuthorByUrl($authorUrl);
            
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
                'author' => $authorUrl
            ));
        }
    }
    
}