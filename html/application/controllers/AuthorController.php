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
 * Author controller
 */
class AuthorController extends Zend_Controller_Action
{
    /**
     * Author's page
     */
    public function showAction()
    {
        try {
            $authorUrl = $this->getRequest()->getParam('author');
            $author = App_Library::getAuthorByUrl($authorUrl);
            $this->view->headTitle($author->getName());
            $this->view->author = $author;
            $this->view->frontImage = $author->getFrontImage();
        } catch (App_Library_Exception_AuthorNotFound $e) {
            $this->_forward('author-not-found', 'error', null, array(
                'author' => $authorUrl
            ));
        }
    }
}